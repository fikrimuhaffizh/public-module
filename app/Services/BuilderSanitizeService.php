<?php

namespace Modules\Public\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Sanitasi keluaran GrapesJS sebelum disimpan/diterbitkan.
 *
 * Prinsip: arbitary HTML DIIZINKAN (halaman custom bebas), tapi wajib disaring.
 *  - HTML  : HTMLPurifier — buang <script>, on* attribute, javascript: URI.
 *            Elemen HTML5/SVG/ikon yang dipakai library blok (navbar/header/
 *            footer/button/article + svg path/circle/rect/dst.) didaftarkan
 *            agar TIDAK ikut terbuang oleh whitelist.
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

    /**
     * Token cache untuk mekanisme pre/post HTMLPurifier: nilai style inline
     * dan atribut passthrough (data-*, aria-*, role, dst.) di-tokenisasi
     * sebelum purify dan di-restore setelahnya, agar gaya modern (flex/grid/
     * var(--wbp-*)/gradient/transform/dst.) dan atribut TIDAK ikut dibuang
     * HTMLPurifier. State per-panggilan — di-reset di awal sanitizeHtml().
     */
    protected array $styleTokens = [];
    protected array $classTokens = [];
    protected int $tokenSeq = 0;

    /**
     * Atribut global yang aman & umum dipakai builder (Trait panel), dipulihkan
     * setelah purify lewat token class (HTMLPurifier tidak mengenal *.attr global
     * di luar kelas Core).
     */
    protected const PASSTHROUGH_ATTRS = [
        'role', 'tabindex', 'contenteditable', 'draggable', 'spellcheck', 'translate',
        'download', 'hidden',
    ];

    /** Elemen yang diizinkan di HTML halaman custom. */
    protected const ALLOWED_TAGS = 'section,div,span,p,a,img,br,hr,ul,ol,li,strong,em,b,i,u,s,small,sub,sup';
    protected const ALLOWED_TAGS_EXT = ',h1,h2,h3,h4,h5,h6,figure,figcaption,blockquote,pre,code,table,thead,tbody,tfoot,tr,td,th,caption,dl,dt,dd,details,summary,iframe'
        . ',nav,header,footer,article,main,aside,button'
        . ',svg,path,circle,rect,line,polyline,polygon,ellipse,g,defs,use,text,tspan';

    /** Atribut yang diizinkan (notasi tag.attr / *.attr). */
    protected const ALLOWED_ATTRS = [
        '*.class', '*.id', '*.style', '*.title', '*.lang', '*.dir',
        'a.href', 'a.target', 'a.rel', 'a.name',
        'img.src', 'img.alt', 'img.width', 'img.height', 'img.loading',
        'iframe.src', 'iframe.width', 'iframe.height', 'iframe.allowfullscreen', 'iframe.title', 'iframe.loading', 'iframe.allow', 'iframe.referrerpolicy',
        'td.colspan', 'td.rowspan', 'th.colspan', 'th.rowspan',
        'ol.start', 'ol.type',
        // HTML5 interaktif / semantik bantu
        'button.type', 'button.name', 'button.value', 'button.disabled',
    ];

    /** Domain iframe yang boleh disematkan (video embed). */
    protected const IFRAME_REGEX = '%^(?:https?:)?//(?:www\.)?(?:youtube\.com|youtube-nocookie\.com|player\.vimeo\.com|www\.vimeo\.com|maps\.google\.com)/%';

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

        // State token per-panggilan (service ini di-resolve singleton per request).
        $this->styleTokens = [];
        $this->classTokens = [];
        $this->tokenSeq = 0;

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
        $config->set('HTML.DefinitionRev', 4);
        $config->set('URI.DefinitionID', 'builder-page-uri-v1');
        $config->set('URI.DefinitionRev', 1);
        $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_top', '_parent']);
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);
        $config->set('HTML.AllowedElements', self::ALLOWED_TAGS.self::ALLOWED_TAGS_EXT);
        $config->set('HTML.AllowedAttributes', implode(',', array_merge(self::ALLOWED_ATTRS, self::svgAllowedAttrs())));
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
        $config->set('CSS.AllowTricky', true);
        $config->set('CSS.MaxImgLength', null);
        // CSS: properti default HTMLPurifier (CSS2) terlalu ketat untuk nilai
        // modern (var()/gradient/calc/flexbox/grid). Ganti SEMUA validator
        // properti dengan BuilderModernCssDef (longgar tapi aman) agar nilai
        // modern lolos; atribut style juga ditokenisasi pre/purify (lihat
        // tokenizeAttributes) sehingga kontrol penuh ada di sanitizeInlineStyle.
        $config->set('CSS.AllowImportant', true);

        // Daftarkan properti CSS modern + ganti semua validator properti dengan
        // definisi longgar TANPA static guard (dijalankan per-panggilan — definisi
        // dikembalikan HTMLPurifier bisa instance/cache berbeda tiap request).
        $cssDef = $config->getCSSDefinition();
        $modernDef = new BuilderModernCssDef();
        foreach ($cssDef->info as $prop => $unused) {
            $cssDef->info[$prop] = $modernDef;
        }
        $cssDef->info['builderstyle'] = $modernDef;

        // Daftarkan elemen/atribut HTML5 yang belum dikenal HTMLPurifier.
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('figure', 'Block', 'Flow', 'Common');
            $def->addElement('figcaption', 'Block', 'Flow', 'Common');
            $def->addElement('details', 'Block', 'Flow', 'Common');
            $def->addElement('summary', 'Block', 'Flow', 'Common');

            // Elemen semantik HTML5 — dipakai library blok (navbar/header/footer/dst).
            foreach (['nav', 'header', 'footer', 'article', 'main', 'aside'] as $flow) {
                $def->addElement($flow, 'Block', 'Flow', 'Common');
            }

            $def->addElement('button', 'Inline', 'Flow', 'Common', [
                'type' => 'Enum#button,submit,reset',
                'name' => 'Text',
                'value' => 'Text',
                'disabled' => 'Bool',
            ]);

            // SVG (ikon Lucide/Tabler) — atribut case-sensitive dibetulkan di post-processing.
            $svgPaint = [
                'fill' => 'Text',
                'stroke' => 'Text',
                'stroke-width' => 'Text',
                'stroke-linecap' => 'Text',
                'stroke-linejoin' => 'Text',
                'fill-rule' => 'Text',
                'clip-rule' => 'Text',
                'opacity' => 'Text',
                'transform' => 'Text',
            ];
            $svgFlows = ['g', 'text', 'tspan'];
            $svgEmpties = ['path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse', 'use'];
            foreach ($svgFlows as $sEl) {
                $def->addElement($sEl, 'Inline', 'Flow', 'Common', $svgPaint);
            }
            $def->addElement('path', 'Inline', 'Empty', 'Common', ['d' => 'Text'] + $svgPaint);
            $def->addElement('circle', 'Inline', 'Empty', 'Common', ['cx' => 'Text', 'cy' => 'Text', 'r' => 'Text'] + $svgPaint);
            $def->addElement('rect', 'Inline', 'Empty', 'Common', ['x' => 'Text', 'y' => 'Text', 'width' => 'Text', 'height' => 'Text', 'rx' => 'Text', 'ry' => 'Text'] + $svgPaint);
            $def->addElement('line', 'Inline', 'Empty', 'Common', ['x1' => 'Text', 'y1' => 'Text', 'x2' => 'Text', 'y2' => 'Text'] + $svgPaint);
            $def->addElement('polyline', 'Inline', 'Empty', 'Common', ['points' => 'Text'] + $svgPaint);
            $def->addElement('polygon', 'Inline', 'Empty', 'Common', ['points' => 'Text'] + $svgPaint);
            $def->addElement('ellipse', 'Inline', 'Empty', 'Common', ['cx' => 'Text', 'cy' => 'Text', 'rx' => 'Text', 'ry' => 'Text'] + $svgPaint);
            $def->addElement('use', 'Inline', 'Empty', 'Common', ['href' => 'URI', 'x' => 'Text', 'y' => 'Text', 'width' => 'Text', 'height' => 'Text'] + $svgPaint);
            $def->addElement('svg', 'Inline', 'Flow', 'Common', $svgPaint + [
                'xmlns' => 'Text',
                'viewbox' => 'Text',
                'preserveaspectratio' => 'Text',
                'width' => 'Text',
                'height' => 'Text',
                'aria-hidden' => 'Text',
            ]);
            $def->addElement('defs', 'Inline', 'Flow', 'Common');

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

        // PRE purify: tokenisasi style inline + atribut passthrough.
        $html = $this->tokenizeAttributes($html);

        $purifier = new HTMLPurifier($config);

        $html = (string) $purifier->purify($html);

        // POST purify: pulihkan style/atribut yang ditokenisasi.
        $html = $this->restoreTokens($html);

        // Rapikan atribut kosong yang tersisa.
        $html = preg_replace('/\sstyle\s*=\s*(["\'])\s*\1/i', '', $html) ?? $html;
        $html = preg_replace('/\sclass\s*=\s*(["\'])\s*\1/i', '', $html) ?? $html;

        return $this->restoreSvgCasing($html);
    }

    /**
     * PRE-purify: ganti nilai style inline & atribut passthrough (data-*,
     * aria-*, role, tabindex, dst.) dengan token aman sehingga HTMLPurifier
     * tidak membuang gaya modern / atribut yang valid. Token dipulihkan di
     * restoreTokens().
     */
    protected function tokenizeAttributes(string $html): string
    {
        return preg_replace_callback('#<[a-zA-Z][^>]*>#', function (array $m): string {
            $tag = $m[0];

            // style="..." -> style="builderstyle:TOKEN"
            $tag = preg_replace_callback('/(\sstyle\s*=\s*)(["\'])(.*?)\2/is', function (array $mm): string {
                $token = $this->nextToken();
                $this->styleTokens[$token] = $this->sanitizeInlineStyle($mm[3]);
                return $mm[1].'"builderstyle:'.$token.'"';
            }, $tag) ?? $tag;

            // Atribut passthrough -> token class (untuk diselundupkan melewati purify)
            $regex = '#(\s)((?:data-[a-z0-9-]+)|(?:aria-[a-z0-9-]+)|(?:'.implode('|', self::PASSTHROUGH_ATTRS).'))=(["\'])(.*?)\3#is';
            if (preg_match_all($regex, $tag, $matches, PREG_SET_ORDER)) {
                $removals = [];
                $tokens = [];
                foreach ($matches as $one) {
                    $name = strtolower($one[2]);
                    // Metadata editor GrapesJS (data-gjs-*) tidak perlu di publik.
                    if (preg_match('#^(?:data-gjs|data-gjs-)#i', $name)) {
                        continue;
                    }
                    $token = $this->nextToken();
                    $value = $this->sanitizeAttrValue($one[4]);
                    $tokens[] = $token;
                    $this->classTokens[$token] = ['name' => $name, 'value' => $value];
                    $removals[] = $one[0];
                }
                if ($removals) {
                    foreach ($removals as $rm) {
                        $tag = str_replace($rm, '', $tag);
                    }
                    foreach ($tokens as $token) {
                        $tag = $this->appendClassToken($tag, $token);
                    }
                }
            }

            return $tag;
        }, $html) ?? $html;
    }

    /** Tambahkan token passthrough ke atribut class tag (atau buat class baru). */
    protected function appendClassToken(string $tag, string $token): string
    {
        $classToken = '__ptok_'.$token;
        if (preg_match('/(\sclass\s*=\s*["\'])([^"\']*)(["\'])/i', $tag, $cm)) {
            return str_replace($cm[0], $cm[1].trim($cm[2]).' '.$classToken.$cm[3], $tag);
        }

        return rtrim($tag, '/>').' class="'.$classToken.'"'.substr($tag, -1);
    }

    /**
     * POST-purify: pulihkan token style & atribut passthrough menjadi nilai
     * aslinya yang sudah disanitasi. Satu pass reguler per jenis token.
     */
    protected function restoreTokens(string $html): string
    {
        if ($this->styleTokens) {
            $html = preg_replace_callback('#builderstyle\s*:\s*([0-9a-f]+)#i', function (array $m): string {
                $token = $m[1];
                if (! isset($this->styleTokens[$token])) {
                    return $m[0];
                }
                return $this->safeAttrOut($this->styleTokens[$token]);
            }, $html) ?? $html;
        }

        if ($this->classTokens) {
            $html = preg_replace_callback('#<([a-zA-Z][^>]*)>#', function (array $m): string {
                $inner = $m[1];
                if (! str_contains($inner, '__ptok_')) {
                    return $m[0];
                }
                preg_match_all('/__ptok_([0-9a-f]+)/', $inner, $toks);
                if (empty($toks[1])) {
                    return $m[0];
                }
                $attrs = '';
                foreach ($toks[1] as $token) {
                    if (! isset($this->classTokens[$token])) {
                        continue;
                    }
                    $info = $this->classTokens[$token];
                    $inner = preg_replace('#\s*__ptok_'.$token.'\s*#', ' ', $inner) ?? $inner;
                    if ($info['value'] !== '') {
                        $attrs .= ' '.$info['name'].'="'.$this->safeAttrOut($info['value']).'"';
                    }
                }
                return '<'.rtrim($inner).$attrs.'>';
            }, $html) ?? $html;
        }

        return $html;
    }

    /** Escape aman untuk disisipkan ke nilai atribut (kutip ganda di-entity). */
    protected function safeAttrOut(string $value): string
    {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    }

    protected function nextToken(): string
    {
        return dechex(++$this->tokenSeq).bin2hex(random_bytes(6));
    }

    /**
     * Sanitize nilai atribut passthrough: buang URI aktif pengeksekusi,
     * sisanya dipertahankan.
     */
    protected function sanitizeAttrValue(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        if (preg_match('#(?:javascript\s*:|vbscript\s*:|data:text/html|expression\s*\()#i', $raw)) {
            return '';
        }

        return $raw;
    }

    /**
     * Sanitize nilai style inline per-deklarasi. Properti modern bebas, hanya
     * nilai yang membahayakan (injection/exec) yang dibuang. Kutip tunggal
     * diizinkan (font-family), kutip ganda & karakter struktural dilarang.
     */
    protected function sanitizeInlineStyle(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        $out = [];
        foreach ($this->splitCssDecls($raw) as $decl) {
            $decl = trim($decl);
            if ($decl === '' || ! str_contains($decl, ':')) {
                continue;
            }
            [$prop, $value] = explode(':', $decl, 2);
            $prop = trim($prop);
            $value = trim($value);
            if (! preg_match('/^(?:--[a-zA-Z0-9-]+|[a-z][a-z0-9-]*)$/', $prop)) {
                continue;
            }
            if (in_array(strtolower($prop), ['behavior', 'binding', 'expression', '-moz-binding'], true)) {
                continue;
            }
            if ($value === '' || str_contains($value, '"') || ! $this->isSafeCssValue($value)) {
                continue;
            }
            $out[] = $prop.':'.$value;
        }

        return implode(';', $out);
    }

    /**
     * Pecah deklarasi CSS pada ';', mengabaikan ';' dalam string kutip dan ';'
     * penutup entitas HTML (&#039;, &amp;, dst.) agar hasil restore sebelumnya
     * tetap utuh bila diproses ulang.
     */
    protected function splitCssDecls(string $css): array
    {
        $decls = [];
        $cur = '';
        $n = strlen($css);
        for ($i = 0; $i < $n; $i++) {
            $c = $css[$i];
            if ($c === '\'' || $c === '"') {
                $cur .= $c;
                $i++;
                while ($i < $n && $css[$i] !== $c) {
                    $cur .= $css[$i];
                    $i++;
                }
                if ($i < $n) {
                    $cur .= $css[$i];
                }
                continue;
            }
            if ($c === '&') {
                $cur .= $c;
                $i++;
                while ($i < $n && $css[$i] !== ';') {
                    $cur .= $css[$i];
                    $i++;
                }
                if ($i < $n) {
                    $cur .= $css[$i];
                }
                continue;
            }
            if ($c === ';') {
                $decls[] = $cur;
                $cur = '';
                continue;
            }
            $cur .= $c;
        }
        if ($cur !== '') {
            $decls[] = $cur;
        }

        return $decls;
    }

    /** Validasi nilai CSS: tolak eksekusi/injection, izinkan nilai modern. */
    protected function isSafeCssValue(string $value): bool
    {
        if (preg_match('#(?:expression\s*\(|conditional\s*\(|behaviou?r|javascript\s*:|vbscript\s*:|@import|\\\\|\burl\s*\(\s*["\']?\s*data:text/html)#i', $value)) {
            return false;
        }
        if (preg_match('#[\x00-\x1f<>{}]#', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Atribut SVG/ikon yang diizinkan, per elemen (HTMLPurifier tidak
     * mendukung atribut global `*.attr` di luar kelas Core/I18N).
     */
    protected static function svgAllowedAttrs(): array
    {
        $paint = ['fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule', 'opacity', 'transform'];

        $list = [];
        $elements = ['svg', 'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse', 'g', 'text', 'tspan', 'use'];
        foreach ($elements as $el) {
            foreach ($paint as $attr) {
                $list[] = $el.'.'.$attr;
            }
        }

        $list[] = 'svg.viewbox';
        $list[] = 'svg.preserveaspectratio';
        $list[] = 'svg.xmlns';
        $list[] = 'svg.width';
        $list[] = 'svg.height';
        $list[] = 'svg.aria-hidden';
        $list[] = 'path.d';
        $list[] = 'circle.cx';
        $list[] = 'circle.cy';
        $list[] = 'circle.r';
        $list[] = 'rect.x';
        $list[] = 'rect.y';
        $list[] = 'rect.width';
        $list[] = 'rect.height';
        $list[] = 'rect.rx';
        $list[] = 'rect.ry';
        $list[] = 'line.x1';
        $list[] = 'line.x2';
        $list[] = 'line.y1';
        $list[] = 'line.y2';
        $list[] = 'polyline.points';
        $list[] = 'polygon.points';
        $list[] = 'ellipse.cx';
        $list[] = 'ellipse.cy';
        $list[] = 'ellipse.rx';
        $list[] = 'ellipse.ry';
        $list[] = 'use.href';
        $list[] = 'use.x';
        $list[] = 'use.y';

        return $list;
    }

    /**
     * HTMLPurifier menulis seluruh atribut dengan huruf kecil (HTML case-insensitive),
     * tapi SVG case-sensitive (viewBox, preserveAspectRatio). Pulihkan casing pada
     * tag <svg ...> supaya ikon tetap menskala dengan benar.
     */
    protected function restoreSvgCasing(string $html): string
    {
        if (! str_contains($html, '<svg')) {
            return $html;
        }

        return preg_replace_callback('#<svg([^>]*)>#i', function (array $m): string {
            $tag = $m[1];
            $tag = preg_replace('#\bviewbox\b#i', 'viewBox', $tag) ?? $tag;
            $tag = preg_replace('#\bpreserveaspectratio\b#i', 'preserveAspectRatio', $tag) ?? $tag;

            return '<svg'.$tag.'>';
        }, $html) ?? $html;
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

        // MIGRASI CSS legacy: ubahan style "Desktop" (device lama widthMedia '992px')
        // tersimpan sebagai @media (max-width: 992px) sehingga tidak tampil di
        // layar > 992px. Pindahkan isinya ke level dasar. (Tablet=768px, Mobile=480px
        // tidak tersentuh; nilai 992px kini khusus legacy desktop.)
        $css = self::unwrapLegacyDesktopMedia($css);

        // Buang deklarasi berbahaya SELURUHNYA (properti + nilai, termasuk kurung
        // penutup) agar tidak menyisakan karakter yatim yang merusak render.
        $css = $this->stripDangerDeclarations($css);

        // Rapikan sisa `;` kosong / aturan kosong.
        $css = preg_replace('#\s*;\s*}#', '}', $css) ?? $css;
        $css = preg_replace('#\{\s*;#', '{', $css) ?? $css;

        // Guard: kurung kurawal & kurung biasa harus seimbang — jika tidak,
        // kosongkan seluruhnya (dead code CSS tidak boleh merusak halaman).
        // Hitungan sadar-string/url() agar `content:"}"`, `url(svg…)`, dsb.
        // tidak memicu false-positive yang menghapus SEMUA CSS.
        if (! $this->cssIsBalanced($css)) {
            return '';
        }

        return trim($css);
    }

    /**
     * Periksa keseimbangan kurung kurawal & kurung biasa dengan memAKAI pemindai
     * sadar-konteks: string kutip, entitas, dan url(...) dilewati.
     */
    protected function cssIsBalanced(string $css): bool
    {
        $depth = 0;
        $paren = 0;
        $n = strlen($css);
        for ($i = 0; $i < $n; $i++) {
            $c = $css[$i];
            if ($c === '\'' || $c === '"') {
                $i++;
                while ($i < $n && $css[$i] !== $c) {
                    if ($css[$i] === '\\') {
                        $i++;
                    }
                    $i++;
                }
                continue;
            }
            if ($c === '{') {
                $depth++;
            } elseif ($c === '}') {
                $depth--;
            } elseif ($c === '(') {
                $paren++;
            } elseif ($c === ')') {
                $paren--;
            }
            if ($depth < 0 || $paren < 0) {
                return false;
            }
        }

        return $depth === 0 && $paren === 0;
    }

    /**
     * Pindahkan isi aturan `@media (max-width: 992px)` ke level dasar (tanpa
     * media query). 992px hanya dipakai device Desktop legacy → aman di-unwrap.
     * Memakai penyeimbang kurung kurawal agar blok berisi banyak aturan
     * (rule berlapis) tidak hancur.
     */
    protected static function unwrapLegacyDesktopMedia(string $css): string
    {
        $segments = preg_split('#@media\s*\(\s*max-width\s*:\s*992px\s*\)\s*\{#i', $css, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($segments) <= 1) {
            return preg_match('#@media\s*\(\s*max-width\s*:\s*992px\s*\)\s*\{#i', $css) ? '' : $css;
        }

        $out = '';
        for ($i = 0; $i < count($segments); $i++) {
            if ($i === 0) {
                $out .= $segments[$i];
                continue;
            }

            $tail = $segments[$i];
            $depth = 1;
            $len = strlen($tail);
            $j = 0;
            while ($j < $len && $depth > 0) {
                $ch = $tail[$j];
                if ($ch === '{') {
                    $depth++;
                } elseif ($ch === '}') {
                    $depth--;
                }
                $j++;
            }

            // Isi blok media = tail sebelum `}` penutup (minimal sisa karakter).
            $inner = substr($tail, 0, max(0, $j - 1));
            $rest = substr($tail, $j);
            $out .= $inner."\n".$rest;
        }

        return $out;
    }

    /**
     * Hapus deklarasi CSS berbahaya secara utuh: url(javascript:...),
     * expression(...), behavior:, dan -binding:.
     */
    protected function stripDangerDeclarations(string $css): string
    {
        // url() dengan skema aktif (javascript/vbscript/data:text/html).
        $css = preg_replace('#[-_a-zA-Z][-_a-zA-Z0-9]*\s*:\s*url\s*\(\s*["\']?\s*(?:javascript|vbscript|data:text/html)\s*:[^;{}]*(?:\([^;{}]*\))?[^;{}]*\)\s*;?#i', '', $css) ?? $css;

        // expression( ... ) sebagai nilai properti.
        $css = preg_replace('#[-_a-zA-Z][-_a-zA-Z0-9]*\s*:\s*expression\s*\((?:[^();]|\([^()]*\))*\)\s*;?#i', '', $css) ?? $css;

        // expression( ... ) tanpa properti (penyelundupan langsung).
        $css = preg_replace('#expression\s*\((?:[^();]|\([^()]*\))*\)#i', '', $css) ?? $css;

        // behavior: / *-binding:.
        $css = preg_replace('#[-_a-zA-Z][-_a-zA-Z0-9]*(?:behavior|-binding|binding)\s*:[^;}]*[;}]?#i', '', $css) ?? $css;

        return $css;
    }

    /**
     * Sanitasi project GrapesJS (pohon komponen). Mengembalikan array yang
     * sudah dibersihkan dari komponen/atribut berbahaya.
     */
    public function sanitizeProject(?array $project): array
    {
        $project = is_array($project) ? $project : [];

        // MIGRASI device Desktop legacy: sebelum perbaikan device config,
        // ubahan style "Desktop" tersimpan sebagai `mediaText: (max-width: 992px)`
        // (device Desktop memakai widthMedia '992px'). Akibatnya di layar > 992px
        // perubahan tidak terlihat. Di sini aturan itu dipindahkan ke level dasar
        // (mediaText kosong). Tablet=768px & Mobile=480px tidak tersentuh.
        if (isset($project['styles']) && is_array($project['styles'])) {
            foreach ($project['styles'] as $k => $rule) {
                if (is_array($rule) && isset($rule['mediaText'])
                    && is_string($rule['mediaText']) && $rule['mediaText'] !== ''
                    && preg_match('#max-width:\s*992px#i', $rule['mediaText'])) {
                    $project['styles'][$k]['mediaText'] = '';
                }
            }
        }

        if (isset($project['components']) && is_array($project['components'])) {
            $project['components'] = $this->sanitizeComponents($project['components']);
        }

        if (isset($project['styles']) && is_array($project['styles'])) {
            $project['styles'] = $this->sanitizeStyles($project['styles']);
        }

        // GrapesJS ≥0.21 menyimpan pohon komponen & CSS di `pages[*].frames[*]`
        // (bukan lagi `components`/`styles` level atas). Tanpa ini, sanitasi
        // komponen/style SKIP total pada format baru sehingga project yang
        // disimpan bisa membawa script/on-handler mentah.
        if (isset($project['pages']) && is_array($project['pages'])) {
            $project['pages'] = $this->sanitizePages($project['pages']);
        }

        return $project;
    }

    /** Sanitasi pohon halaman GrapesJS baru: pages → frames → component/styles. */
    protected function sanitizePages(array $pages): array
    {
        foreach ($pages as &$page) {
            if (! is_array($page)) {
                continue;
            }

            foreach (($page['frames'] ?? []) as &$frame) {
                if (! is_array($frame)) {
                    continue;
                }

                if (isset($frame['component']) && is_array($frame['component'])) {
                    $components = $this->sanitizeComponents([$frame['component']]);
                    $frame['component'] = $components[0] ?? [];
                }

                if (isset($frame['styles']) && is_array($frame['styles'])) {
                    $styles = $this->sanitizeStyles($frame['styles']);
                    $frame['styles'] = $styles;

                    // Migrasi legacy Desktop (@media max-width: 992px) juga
                    // diterapkan ke styles berformat baru agar konsisten.
                    foreach ($frame['styles'] as $k => $rule) {
                        if (is_array($rule) && isset($rule['mediaText'])
                            && is_string($rule['mediaText']) && $rule['mediaText'] !== ''
                            && preg_match('#max-width:\s*992px#i', $rule['mediaText'])) {
                            $frame['styles'][$k]['mediaText'] = '';
                        }
                    }
                }
            }
            unset($frame);
        }
        unset($page);

        return $pages;
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