<?php

namespace Modules\Public\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Sanitasi keluaran GrapesJS sebelum disimpan/diterbitkan.
 *
 * Prinsip: arbitary HTML DIIZINKAN (halaman custom bebas), tapi wajib disaring.
 *  - HTML  : HTMLPurifier — buang <script>, on* attribute, javascript: URI.
 *  - CSS   : filter regex — buang expression()/behavior/binding, url(javascript:),
 *            @import, dan komentar. Plus guard keseimbangan kurung kurawal.
 *  - Project JSON : walk komponen GrapesJS; buang komponen script, atribut
 *            on-handler, dan URI javascript aktip, lalu sanitasi nilai
 *            `content` komponen teks (HTML) + nilai style.
 *
 * Selalu dipanggil pada saat save-project, sehingga html/css yang disimpan
 * sudah aman. CSP pada rute publik menjadi pertahanan lapis kedua.
 */
class BuilderSanitizeService
{
    /** Ukuran maksimal payload HTML/CSS (bytes) — guard kasar. */
    protected const MAX_SIZE = 4 * 1024 * 1024;

    /** Elemen yang diizinkan di HTML halaman custom. */
    protected const ALLOWED_TAGS = 'section,div,span,p,a,img,br,hr,ul,ol,li,strong,em,b,i,u,s,small,sub,sup';
    protected const ALLOWED_TAGS_EXT = ',h1,h2,h3,h4,h5,h6,figure,figcaption,blockquote,pre,code,table,thead,tbody,tfoot,tr,td,th,caption,dl,dt,dd,details,summary,iframe';

    /** Atribut yang diizinkan (notasi tag.attr / *.attr). */
    protected const ALLOWED_ATTRS = [
        '*.class', '*.id', '*.style', '*.title', '*.lang', '*.dir',
        'a.href', 'a.target', 'a.rel', 'a.name',
        'img.src', 'img.alt', 'img.width', 'img.height', 'img.loading',
        'iframe.src', 'iframe.width', 'iframe.height', 'iframe.allowfullscreen', 'iframe.title', 'iframe.loading', 'iframe.allow', 'iframe.referrerpolicy',
        'td.colspan', 'td.rowspan', 'th.colspan', 'th.rowspan',
        'ol.start', 'ol.type',
    ];

    /** Domain iframe yang boleh disematkan (video embed). */
    protected const IFRAME_REGEX = '%#^(?:https?:)?//(?:www\.)?(?:youtube\.com|youtube-nocookie\.com|player\.vimeo\.com|www\.vimeo\.com|maps\.google\.com)/#%';

    /** Properti CSS modern (flex/grid/transform/dst.) yang diloloskan pada style inline. */
    protected const CSS_MODERN = [
        'display', 'position', 'top', 'right', 'bottom', 'left', 'z-index',
        'flex', 'flex-basis', 'flex-direction', 'flex-flow', 'flex-grow', 'flex-shrink', 'flex-wrap', 'order',
        'gap', 'row-gap', 'column-gap',
        'grid', 'grid-area', 'grid-auto-columns', 'grid-auto-flow', 'grid-auto-rows',
        'grid-column', 'grid-column-end', 'grid-column-gap', 'grid-column-start', 'grid-gap',
        'grid-row', 'grid-row-end', 'grid-row-gap', 'grid-row-start',
        'grid-template', 'grid-template-areas', 'grid-template-columns', 'grid-template-rows',
        'justify-content', 'justify-items', 'justify-self',
        'align-content', 'align-items', 'align-self',
        'resize', 'visibility', 'opacity', 'pointer-events', 'cursor', 'box-sizing', 'box-shadow',
        'transform', 'transform-origin', 'perspective',
        'transition', 'transition-delay', 'transition-duration', 'transition-property',
        'transition-timing-function',
        'word-break', 'word-wrap', 'text-overflow', 'text-shadow', 'zoom', 'white-space',
        'overflow', 'overflow-x', 'overflow-y', 'content', 'quotes',
        'min-width', 'max-width', 'min-height', 'max-height',
        'letter-spacing', 'line-height', 'background-clip', 'background-origin',
        'text-decoration', 'text-decoration-color', 'text-decoration-line', 'text-decoration-style',
    ];

    public function sanitizeHtml(?string $html): string
    {
        $html = (string) $html;

        if ($html === '') {
            return '';
        }

        if (mb_strlen($html) > self::MAX_SIZE) {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();
        $cacheDir = storage_path('framework/htmlpurifier');
        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cacheDir);
        $config->set('HTML.DefinitionID', 'builder-page-html-v1');
        $config->set('HTML.DefinitionRev', 2);
        $config->set('URI.DefinitionID', 'builder-page-uri-v1');
        $config->set('URI.DefinitionRev', 1);
        $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_top', '_parent']);
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);
        $config->set('HTML.AllowedElements', self::ALLOWED_TAGS.self::ALLOWED_TAGS_EXT);
        $config->set('HTML.AllowedAttributes', implode(',', self::ALLOWED_ATTRS));
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', self::IFRAME_REGEX);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'ftp' => true, 'ftps' => true, 'mailto' => true, 'tel' => true, 'telnet' => true, 'news' => true, 'nntp' => true, 'irc' => true, 'ircs' => true, 'aim' => true, 'webcal' => true, 'data' => true]);

        // Daftarkan skema data: custom sekali per proses.
        static $dataSchemeRegistered = false;
        if (! $dataSchemeRegistered) {
            \HTMLPurifier_URISchemeRegistry::instance()->register('data', new BuilderDataURIScheme());
            $dataSchemeRegistered = true;
        }
        $config->set('AutoFormat.RemoveEmpty', false);
        $config->set('AutoFormat.RemoveSpansWithoutAttributes', false);
        $config->set('CSS.AllowTricky', false);
        $config->set('CSS.MaxImgLength', null);
        // CSS: properti default HTMLPurifier (liberal, tetap divalidasi
        // per-properti) + properti modern (flexbox/grid/transform/gap/dst.)
        // yang didaftarkan di bawah. Tanpa CSS.AllowedProperties agar tidak
        // memicu warning "not supported" untuk properti modern.
        $config->set('CSS.AllowImportant', true);

        // Properti CSS modern dikenal via CSSDefinition bawaan? Tidak —
        // HTMLPurifier hanya tahu properti CSS2 legacy. Didaftarkan manual
        // dengan validator BuilderModernCssDef (longgar tapi aman).
        static $cssModernRegistered = false;
        if (! $cssModernRegistered) {
            $cssDef = $config->getCSSDefinition();
            foreach (self::CSS_MODERN as $prop) {
                $cssDef->info[$prop] = new BuilderModernCssDef();
            }
            $cssModernRegistered = true;
        }

        // Daftarkan elemen/atribut HTML5 yang belum dikenal HTMLPurifier.
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('figure', 'Block', 'Flow', 'Common');
            $def->addElement('figcaption', 'Block', 'Flow', 'Common');
            $def->addElement('details', 'Block', 'Flow', 'Common');
            $def->addElement('summary', 'Block', 'Flow', 'Common');
            $def->addAttribute('img', 'loading', 'Enum#lazy,eager,auto');
            $def->addAttribute('iframe', 'loading', 'Enum#lazy,eager,auto');
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
            $def->addAttribute('iframe', 'allow', 'Text');
            $def->addAttribute('iframe', 'referrerpolicy', 'Text');
            $def->addAttribute('iframe', 'width', 'Length');
            $def->addAttribute('iframe', 'height', 'Length');
        }

        // Data URI hanya diizinkan untuk gambar (image/*) — data:text/html dll ditolak.
        if ($uriDef = $config->maybeGetRawURIDefinition()) {
            $uriDef->addFilter(new DataImageURIFilter(), $config);
        }

        $purifier = new HTMLPurifier($config);

        return (string) $purifier->purify($html);
    }

    public function sanitizeCss(?string $css): string
    {
        $css = (string) $css;

        if ($css === '') {
            return '';
        }

        if (mb_strlen($css) > self::MAX_SIZE) {
            return '';
        }

        // Buang komentar & aturan berbahaya.
        $css = preg_replace('#/\*.*?\*/#s', '', $css) ?? '';
        $css = preg_replace('#@import[^;]*;?#is', '', $css) ?? '';
        $css = preg_replace('#expression\s*\([^)]*\)#is', '', $css) ?? '';
        $css = preg_replace('#[-a-z-]*binding\s*:[^;};]*#is', '', $css) ?? '';
        $css = preg_replace('#behavior\s*:[^;};]*#is', '', $css) ?? '';

        // url() dengan skema berbahaya.
        $css = preg_replace('#url\(\s*(["\']?)\s*(?:javascript|vbscript|data:text/html)\s*:.*?\)#is', 'none', $css) ?? '';

        // Guard: kurung kurawal harus seimbang — jika tidak, kosongkan seluruhnya.
        $openCount = substr_count($css, '{');
        $closeCount = substr_count($css, '}');
        if ($openCount !== $closeCount) {
            return '';
        }

        return trim($css);
    }

    /**
     * Sanitasi project GrapesJS (pohon komponen). Mengembalikan array yang
     * sudah dibersihkan dari komponen/atribut berbahaya.
     */
    public function sanitizeProject(?array $project): array
    {
        $project = is_array($project) ? $project : [];

        if (isset($project['components']) && is_array($project['components'])) {
            $project['components'] = $this->sanitizeComponents($project['components']);
        }

        if (isset($project['styles']) && is_array($project['styles'])) {
            $project['styles'] = $this->sanitizeStyles($project['styles']);
        }

        return $project;
    }

    protected function sanitizeComponents(array $components): array
    {
        $clean = [];

        foreach ($components as $component) {
            if (! is_array($component)) {
                continue;
            }

            $tag = strtolower((string) ($component['tagName'] ?? ''));
            $type = strtolower((string) ($component['type'] ?? ''));

            // Buang komponen aktif-jalankan kode.
            if (in_array($tag, ['script', 'style'], true) || in_array($type, ['script', 'style'], true)) {
                continue;
            }

            // Buang atribut berbahaya.
            if (isset($component['attributes']) && is_array($component['attributes'])) {
                $component['attributes'] = array_filter($component['attributes'], fn ($key) =>
                    ! str_starts_with(strtolower($key), 'on') && ! $this->containsDangerousUrl((string) $component['attributes'][$key]),
                    ARRAY_FILTER_USE_KEY
                );
            }

            // Sanitasi konten HTML (komponen teks/link menyimpan innerHTML).
            if (isset($component['content']) && is_string($component['content'])) {
                $component['content'] = $this->sanitizeHtml($component['content']);
            }

            // Sanitasi style inline.
            if (isset($component['style']) && is_array($component['style'])) {
                $component['style'] = $this->sanitizeStyleArray($component['style']);
            }

            // Rekursi ke children.
            if (isset($component['components']) && is_array($component['components'])) {
                $component['components'] = $this->sanitizeComponents($component['components']);
            }

            $clean[] = $component;
        }

        return $clean;
    }

    protected function sanitizeStyles(array $styles): array
    {
        foreach ($styles as &$style) {
            if (is_array($style) && isset($style['style']) && is_array($style['style'])) {
                $style['style'] = $this->sanitizeStyleArray($style['style']);
            }
        }

        return $styles;
    }

    protected function sanitizeStyleArray(array $style): array
    {
        foreach ($style as $key => $value) {
            if (! is_string($value)) {
                continue;
            }
            $value = preg_replace('#expression\s*\(.*\)#is', '', $value);
            $value = preg_replace('#(?:javascript|vbscript|data:text/html)\s*:#is', 'none:', $value);
            $style[$key] = (string) $value;
        }

        return $style;
    }

    protected function containsDangerousUrl(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return (bool) preg_match('#\b(?:javascript|vbscript)\s*:#i', $value);
    }
}

/**
 * Validator nilai untuk properti CSS modern yang tidak dikenali HTMLPurifier.
 * Longgar tapi aman: hanya menolak token yang bisa mengeksekusi/menyuntik
 * (expression, javascript:, vbscript:, url(data:text/html…), escape \),
 * karakter struktural ( ; { } < > " ' ), dan @import. Nilai lain — angka,
 * keyword, var(--x), calc(), minmax()/repeat(), warna, dst. — diloloskan.
 */
class BuilderModernCssDef extends \HTMLPurifier_AttrDef
{
    public function validate($string, $config, $context)
    {
        $string = trim((string) $string);
        if ($string === '') {
            return false;
        }

        if (preg_match('#(?:expression\s*\(|conditional\s*\(|behaviour|behavior\s*:|javascript\s*:|vbscript\s*:|@import|\\\\|\burl\s*\(\s*["\']?\s*data:text/html)#i', $string)) {
            return false;
        }

        if (preg_match('#[<>{};"\']#', $string)) {
            return false;
        }

        return $string;
    }
}

/**
 * Filter URI: hanya mengizinkan data: URI bertipe image/* (svg, png, jpg…).
 * Blok data:text/html, data:application/*, dsb.
 */
class DataImageURIFilter extends \HTMLPurifier_URIFilter
{
    /** @var string */
    public $name = 'BuilderDataImage';

    public function filter(&$uri, $config, $context)
    {
        if ($uri->scheme === 'data') {
            return DataImageURIFilter::isSafeImageDataUri($uri);
        }

        return true;
    }

    public static function isSafeImageDataUri(\HTMLPurifier_URI $uri): bool
    {
        $path = (string) $uri->path;
        $pos = strpos($path, ',');
        if ($pos === false) {
            return false;
        }

        $mediatype = strtolower(trim(substr($path, 0, $pos)));
        $mediatype = explode(';', $mediatype)[0];

        return in_array($mediatype, [
            'image/png', 'image/jpeg', 'image/gif',
            'image/webp', 'image/avif', 'image/svg+xml',
        ], true);
    }
}

/**
 * Skema URI `data:` custom (hanya image/*). Didaftarkan ke
 * HTMLPurifier_URISchemeRegistry sehingga mengalahkan refleksi kelas bawaan
 * `HTMLPurifier_URIScheme_data` yang tidak menerima SVG/webp/avif.
 */
class BuilderDataURIScheme extends \HTMLPurifier_URIScheme
{
    /** @var bool */
    public $browsable = true;

    /** @var bool */
    public $may_omit_host = true;

    public function doValidate(&$uri, $config, $context)
    {
        return DataImageURIFilter::isSafeImageDataUri($uri);
    }
}