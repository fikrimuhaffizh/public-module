<?php

namespace Modules\Public\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Public\Models\BuilderPageData;
use Modules\Public\Models\BuilderTemplate;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Page;

/**
 * Manajemen halaman custom (render_mode='custom', freeform GrapesJS).
 *
 * Alur: admin membuat halaman dari template starter (gjs_project disalin ke
 * cms_page_builder_data) -> edit di GrapesJS -> save-project (gjs_project +
 * html/css hasil compile, SEMUA disanitasi server-side) -> publish.
 * Halaman template (React/Inertia) tidak tersentuh di service ini.
 */
class BuilderPageService
{
    public function __construct(protected BuilderSanitizeService $sanitizer) {}

    /** Daftar template starter aktif untuk galeri "pick a template". */
    public function templateCatalog(): iterable
    {
        return BuilderTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function templateByKey(?string $key): ?BuilderTemplate
    {
        if (! $key) {
            return null;
        }

        return BuilderTemplate::query()->where('key', $key)->first();
    }

    /**
     * Membuat halaman custom baru. `$templateKey` nullable -> blank page.
     */
    public function createCustomPage(array $data, ?string $templateKey = null): Page
    {
        return DB::transaction(function () use ($data, $templateKey) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $data['render_mode'] = Page::MODE_CUSTOM;
            $data['template_key'] = $templateKey;
            $data['is_published'] = false;

            $page = Page::create($data);

            $project = ['components' => []];
            $template = $this->templateByKey($templateKey);
            if ($template && ! empty($template->gjs_project)) {
                $project = $template->gjs_project;
            }

            $this->saveProject($page, $project, '', '');

            logActivity('builder_page', "Membuat halaman custom: {$page->title}", $page);

            return $page;
        });
    }

    public function updateSettings(Page $page, array $data): bool
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $page->update($data);
        logActivity('builder_page', "Update pengaturan halaman: {$page->title}", $page);

        return true;
    }

    /**
     * Menyimpan project GrapesJS + hasil compile. Semua lewat sanitizer.
     *
     * @param  array  $project  gjs_project (editor.getProjectData())
     * @param  string  $html     editor.getHtml()
     * @param  string  $css      editor.getCss()
     */
    public function saveProject(Page $page, array $project, string $html, string $css): BuilderPageData
    {
        // NOTE (permintaan user): sanitasi & rekonstruksi SERVER SIDE dinonaktifkan
        // sementara — simpan persis apa yang dikirim editor GrapesJS, agar
        // perubahan (font/italic/center/style) tersimpan apa adanya untuk
        // debugging konsistensi data. Aktifkan kembali setelah root cause jelas.
        //
        // $project = $this->sanitizer->sanitizeProject($project);
        // $html = $this->sanitizer->sanitizeHtml($html);
        // $css = $this->sanitizer->sanitizeCss($css);
        // $projectHtml = $this->htmlFromProject($project);
        // if ($projectHtml !== '') $html = $this->sanitizer->sanitizeHtml($projectHtml);
        // $projectCss = $this->cssFromProject($project);
        // if ($projectCss !== '') $css = $this->sanitizer->sanitizeCss($projectCss);

        return DB::transaction(function () use ($page, $project, $html, $css) {
            $data = $this->dataFor($page);

            if ($data === null) {
                $data = new BuilderPageData(['page_id' => $page->getKey()]);
                $data->page_id = $page->getKey();
            }

            $data->gjs_project = $project;
            $data->html_compiled = $html;
            $data->css_compiled = $css;
            $data->compiled_at = now();
            $data->save();

            logActivity('builder_page', "Simpan project GrapesJS: {$page->title}", $data);

            return $data;
        });
    }

    /** Data builder milik sebuah halaman (atau null). */
    public function dataFor(Page $page): ?BuilderPageData
    {
        $relation = $page->builderData;

        if ($relation) {
            return $relation;
        }

        return BuilderPageData::query()->where('page_id', $page->getKey())->first();
    }

    /**
     * Rekonstruksi CSS dari aturan `styles` di project GrapysJS (level atas
     * maupun di pages[*].frames[*]). Menghasilkan CSS setara getCss() editor.
     */
    public function cssFromProject(array $project): string
    {
        $rules = [];

        foreach (($project['styles'] ?? []) as $rule) {
            if (is_array($rule)) {
                $rules[] = $rule;
            }
        }
        foreach (($project['pages'] ?? []) as $page) {
            if (! is_array($page)) {
                continue;
            }
            foreach (($page['frames'] ?? []) as $frame) {
                if (! is_array($frame)) {
                    continue;
                }
                foreach (($frame['styles'] ?? []) as $rule) {
                    if (is_array($rule)) {
                        $rules[] = $rule;
                    }
                }
            }
        }

        $groups = [];
        $order = [];
        foreach ($rules as $rule) {
            $media = trim((string) ($rule['mediaText'] ?? ''));
            $selectors = is_array($rule['selectors'] ?? null) ? $rule['selectors'] : [];
            $selText = implode(',', array_map(fn ($s) => (string) $s, $selectors));
            $style = is_array($rule['style'] ?? null) ? $rule['style'] : [];
            $decls = [];
            foreach ($style as $k => $v) {
                if ($k === '' || $v === null || $v === '') {
                    continue;
                }
                $decls[] = trim((string) $k).':'.trim((string) $v);
            }
            if ($selText === '' || ! $decls) {
                continue;
            }
            if (! isset($groups[$media])) {
                $groups[$media] = [];
                $order[] = $media;
            }
            $groups[$media][] = $selText.' { '.implode('; ', $decls).'; }';
        }

        if (! $groups) {
            return '';
        }

        // Media default ('' — aturan dasar) didahulukan, sisanya urutan kemunculan.
        usort($order, fn ($a, $b) => $a === '' ? -1 : ($b === '' ? 1 : 0));

        $css = '';
        foreach ($order as $media) {
            if ($media === '') {
                $css .= implode("\n", $groups[$media])."\n";
            } else {
                $css .= '@media '.$media." {\n".implode("\n", $groups[$media])."\n}\n";
            }
        }

        return $css;
    }

    /**
     * Rekonstruksi HTML dari pohon komponen project GrapesJS (pages → frames →
     * component). Render deterministik agar html_compiled selalu sinkron dengan
     * project — termasuk atribut `id` penanda rule selector-id sehingga override
     * font-style/align dari Style Manager tetap mengikat di halaman publik.
     */
    public function htmlFromProject(array $project): string
    {
        foreach (($project['pages'] ?? []) as $page) {
            if (! is_array($page)) {
                continue;
            }
            foreach (($page['frames'] ?? []) as $frame) {
                if (is_array($frame) && isset($frame['component']) && is_array($frame['component'])) {
                    return $this->renderNode($frame['component']);
                }
            }
        }

        return '';
    }

    protected function renderNode(array $node): string
    {
        $type = (string) ($node['type'] ?? '');
        $tag = strtolower((string) ($node['tagName'] ?? ''));

        if ($type === 'textnode') {
            return (string) ($node['content'] ?? '');
        }

        if ($type === 'video') {
            $attrs = $node['attributes'] ?? [];
            $provider = (string) ($node['provider'] ?? 'yt');
            $videoId = (string) ($node['videoId'] ?? '');
            if ($provider === 'yt' && $videoId !== '') {
                $attrs['src'] = 'https://www.youtube.com/embed/'.$videoId.'?';
            }

            return '<iframe'.$this->renderAttrs($attrs).'></iframe>';
        }

        // Barang bungkus tingkat atas (wrapper/head/html) → cukup anak-anaknya.
        if ($tag === '' || $tag === 'wrapper' || $tag === 'head' || $tag === 'html') {
            return $this->renderChildren($node['components'] ?? []);
        }

        $attrs = $node['attributes'] ?? [];
        $classes = $node['classes'] ?? [];
        if (is_array($classes) && $classes) {
            $attrs['class'] = implode(' ', array_map('strval', $classes));
        }
        $style = $node['style'] ?? null;
        if (is_array($style) && $style) {
            $decls = [];
            foreach ($style as $k => $v) {
                if ($k === '' || $v === null || $v === '') {
                    continue;
                }
                $decls[] = trim((string) $k).':'.trim((string) $v);
            }
            if ($decls) {
                $attrs['style'] = implode(';', $decls);
            }
        }

        $content = (string) ($node['content'] ?? '');
        $inner = $content !== '' ? $content : $this->renderChildren($node['components'] ?? []);

        if (in_array($tag, ['br', 'hr', 'img', 'input', 'meta', 'link', 'source'], true)) {
            return '<'.$tag.$this->renderAttrs($attrs).'>';
        }

        return '<'.$tag.$this->renderAttrs($attrs).'>'.$inner.'</'.$tag.'>';
    }

    protected function renderChildren(array $components): string
    {
        $out = '';
        foreach ($components as $component) {
            if (is_array($component)) {
                $out .= $this->renderNode($component);
            }
        }

        return $out;
    }

    protected function renderAttrs(array $attrs): string
    {
        $out = '';
        foreach ($attrs as $name => $value) {
            if ($value === null || $value === false) {
                $out .= ' '.$name;
                continue;
            }
            $out .= ' '.$name.'="'.htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8').'"';
        }

        return $out;
    }

    /** Payload lengkap untuk editor (project + html/css). */
    public function editorPayload(Page $page): array
    {
        $data = $this->dataFor($page);

        return [
            'id' => $page->encrypted_page_id,
            'title' => $page->title,
            'slug' => $page->slug,
            'render_mode' => $page->render_mode,
            'is_published' => $page->is_published,
            'gjs_project' => $data?->gjs_project ?? ['components' => []],
            'html' => $data?->html_compiled ?? '',
            'css' => $data?->css_compiled ?? '',
            'compiled_at' => $data?->compiled_at?->toIso8601String(),
            'theme' => config('builder_theme', []),
            'theme_css' => $this->themeCss(),
        ];
    }

    /**
     * CSS tema halaman (var --wbp-*) yang diinject selalu — memastikan blok
     * GrapesJS yang memakai var(--wbp-*) tetap konsisten dengan brand tenant.
     */
    public function themeCss(): string
    {
        $theme = config('builder_theme', []);
        $primary = $theme['primary'] ?? '#2563EB';
        $secondary = $theme['secondary'] ?? '#0F172A';
        $radius = is_numeric($theme['radius'] ?? null) ? ((int) $theme['radius']).'px' : ($theme['radius'] ?? '12px');
        $container = is_numeric($theme['container_width'] ?? null) ? ((int) $theme['container_width']).'px' : ($theme['container_width'] ?? '1140px');
        $rgb = $this->hexToRgb($primary);

        // Stylesheet minimal untuk halaman custom. Lebih lengkap disediakan
        // lewat public-builder.css saat preview/editor (client side).
        return <<<CSS
        :root {
            --wbp-primary: {$primary};
            --wbp-primary-rgb: {$rgb};
            --wbp-secondary: {$secondary};
            --wbp-font: '{$theme['font']}', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            --wbp-radius: {$radius};
            --wbp-container: {$container};
        }
        CSS;
    }

    /**
     * Daftar section yang bisa diseret ke kanvas editor (Block Manager GrapesJS).
     * Setiap section dirender server-side (Blade) dengan content/settings default
     * sehingga blok siap pakai (warna, layout, placeholder gambar sudah ada).
     *
     * @return array<string, array{name: string, category: string, icon: string, html: string}>
     */
    public function sectionBlocks(): array
    {
        $blocks = [];

        foreach (config('builder_sections.sections', []) as $type => $meta) {
            $content = is_array($meta['defaults']['content'] ?? null) ? $meta['defaults']['content'] : [];
            $settings = is_array($meta['defaults']['settings'] ?? null) ? $meta['defaults']['settings'] : [];
            $content = $this->fillImagePlaceholders($content);

            $html = $this->renderSection($type, $content, $settings);
            if ($html === '') {
                continue;
            }

            $blocks[$type] = [
                'name' => $meta['name'] ?? ucfirst((string) $type),
                'category' => $this->sectionCategory($meta['category'] ?? 'basic'),
                'icon' => $meta['icon'] ?? 'box',
                'html' => $html,
            ];
        }

        // Blok variant section React (Mode1..N) — hasil SSR
        // (builder-ssr/render-blocks.mjs → storage/app/builder/builder_blocks.json).
        foreach ($this->reactSectionBlocks() as $key => $block) {
            if (! isset($blocks[$key])) {
                $blocks[$key] = $block;
            }
        }

        return $blocks;
    }

    /**
     * Blok section dari library React (renderToString tiap Mode{n}.jsx).
     * File di-generate manual lewat:
     *   node Modules/Public/resources/assets/js/builder-ssr/render-blocks.mjs
     */
    protected function reactSectionBlocks(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $cache = [];
        $file = storage_path('app/builder/builder_blocks.json');
        if (! is_file($file)) {
            return $cache;
        }

        try {
            $data = json_decode(File::get($file), true);
        } catch (\Throwable) {
            return $cache;
        }

        foreach ($data['blocks'] ?? [] as $block) {
            $html = trim((string) ($block['html'] ?? ''));
            if ($html === '') {
                continue;
            }

            $type = (string) ($block['type'] ?? 'section');
            $mode = (string) ($block['mode'] ?? 'mode');
            $variantName = (string) ($block['name'] ?? (str_starts_with($mode, 'mode') ? 'Mode' : $mode));
            $category = $this->sectionDisplayName($type);

            // Label ringkas: "Mode 1", "Mode 2", dst — nama section sudah jadi
            // kategori grup blok di Block Manager GrapesJS.
            $cache[sprintf('%s-%s', $type, $mode)] = [
                'name' => $variantName,
                'category' => $category,
                'icon' => (string) ($block['icon'] ?? 'box'),
                'html' => $html,
            ];
        }

        return $cache;
    }

    /** Nama tampilan sebuah section (dipakai kategori & label blok React). */
    protected function sectionDisplayName(string $type): string
    {
        return match ($type) {
            'pageheader' => 'Page Header',
            'navbar' => 'Navbar',
            'topbar' => 'Top Bar',
            'hero' => 'Hero',
            'product' => 'Produk',
            'statistic' => 'Statistik',
            'feature' => 'Fitur',
            'testimonial' => 'Testimoni',
            'client' => 'Klien',
            'faq' => 'FAQ',
            'price' => 'Paket Harga',
            'cta' => 'Call To Action',
            'gallery' => 'Galeri',
            'pengumuman' => 'Pengumuman',
            'marquee' => 'Marquee Text',
            'footer' => 'Footer',
            default => ucfirst((string) $type),
        };
    }

    /** Render satu section builder menjadi HTML (dipakai block editor & seeder). */
    public function renderSection(string $type, array $content = [], array $settings = []): string
    {
        $meta = config("builder_sections.sections.{$type}");
        if (! $meta || empty($meta['component'])) {
            return '';
        }

        try {
            return trim((string) \Illuminate\Support\Facades\View::make('public::components.builder.'.$meta['component'])->with([
                'content' => $content,
                'settings' => $settings,
                'theme' => config('builder_theme', []),
                'section' => ['type' => $type, 'name' => $meta['name']],
                'index' => 0,
            ])->render());
        } catch (\Throwable) {
            return '';
        }
    }

    private function sectionCategory(string $key): string
    {
        return match ($key) {
            'basic' => 'Dasar',
            'marketing' => 'Pemasaran',
            'content' => 'Konten',
            'layout' => 'Layout',
            default => 'Lainnya',
        };
    }

    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return '37 99 235';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return sprintf('%d %d %d', $r, $g, $b);
    }

    /** Isi field gambar/photo yang masih null dengan placeholder SVG inline. */
    private function fillImagePlaceholders(array $content): array
    {
        $placeholder = $this->placeholderDataUri();

        if (empty($content['image'])) {
            $content['image'] = $placeholder;
        }

        foreach (['items', 'columns'] as $collectionKey) {
            if (! isset($content[$collectionKey]) || ! is_array($content[$collectionKey])) {
                continue;
            }

            foreach ($content[$collectionKey] as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (empty($item['image'])) {
                    $item['image'] = $placeholder;
                }
                if (empty($item['photo'])) {
                    $item['photo'] = $placeholder;
                }
                $content[$collectionKey][$index] = $item;
            }
        }

        return $content;
    }

    private function placeholderDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="400"><rect width="100%" height="100%" fill="#e2e8f0"/><text x="50%" y="50%" font-family="Arial" font-size="22" fill="#94a3b8" text-anchor="middle" dominant-baseline="middle">Ganti Gambar</text></svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }

    /** Status halaman untuk badge (custom flow: draft/published). */
    public function publish(Page $page): bool
    {
        return $this->setPublished($page, true);
    }

    public function unpublish(Page $page): bool
    {
        return $this->setPublished($page, false);
    }

    protected function setPublished(Page $page, bool $published): bool
    {
        return DB::transaction(function () use ($page, $published) {
            $page->is_published = $published;
            $page->save();

            Menu::query()->where('page_id', $page->getKey())->update(['is_active' => $published]);

            logActivity('builder_page', ($published ? 'Publish' : 'Unpublish')." halaman: {$page->title}", $page);

            return true;
        });
    }

    public function deletePage(Page $page): bool
    {
        return DB::transaction(function () use ($page) {
            $title = $page->title;

            Menu::query()->where('page_id', $page->getKey())->delete();
            BuilderPageData::query()->where('page_id', $page->getKey())->delete();
            $page->delete();

            logActivity('builder_page', "Hapus halaman custom: {$title}");

            return true;
        });
    }
}