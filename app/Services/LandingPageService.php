<?php

namespace Modules\Public\Services;

use Modules\Account\Models\Tenant;
use Modules\Tenant\Services\TenantConfigService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Public\Models\FAQ;
use Modules\Public\Models\Menu;
use Modules\Public\Models\LandingPageSetting;
use Modules\Public\Models\LandingSection;
use Modules\Public\Models\Page;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Pricing;
use Modules\Public\Models\Section;
use Modules\Public\Models\Slideshow;

class LandingPageService
{
    public function __construct(
        private TenantConfigService $tenantConfig,
        private ThemeRegistry $themes,
    ) {}

    public function template(?string $previewTemplate = null): string
    {
        if ($this->themes->isValid($previewTemplate)) {
            return $previewTemplate;
        }
        $selected = $this->tenantConfig->get(sys_tenant_id(), 'public', 'landing_template', config('public.landing_template', $this->themes->default()));
        return $this->themes->isValid($selected) ? $selected : $this->themes->default();
    }

    public function saveTemplate(string $template): void
    {
        $this->tenantConfig->set(sys_tenant_id(), 'public', 'landing_template', $template);
    }

    public function themeKeys(): array { return $this->themes->keys(); }
    public function themeGroups(): array { return $this->themes->categories(); }
    public function design(): ?array { return LandingPageSetting::forCurrentTenant()->design; }

    public function saveDesign(string $template, array $design): void
    {
        if (! $this->themes->isValid($template)) { abort(422, 'Tema tidak valid.'); }
        $this->saveTemplate($template);
        $settings = LandingPageSetting::forCurrentTenant();
        $settings->design = array_merge($design, ['template' => $template]);
        $settings->save();
        $this->pruneOrphanBackgrounds($design['sectionColors'] ?? []);
    }

    private function pruneOrphanBackgrounds(array $sectionColors): void
    {
        $usedUuids = collect($sectionColors)->pluck('image')->filter()
            ->map(fn (string $url) => preg_match('#/file/([0-9a-f-]{36})#i', $url, $m) ? $m[1] : null)
            ->filter()->unique()->values();
        $tenant = Tenant::find(sys_tenant_id());
        if (! $tenant) return;
        $tenant->getMedia('section_backgrounds')
            ->reject(fn ($media) => $usedUuids->contains($media->uuid))
            ->each(fn ($media) => $media->delete());
    }

    public function saveSectionSettings(array $sectionSettings): void
    {
        $tenantId = sys_tenant_id();
        foreach ($sectionSettings as $sectionKey => $settings) {
            if (! is_string($sectionKey) || ! is_array($settings) || empty($settings)) continue;
            $normalized = [];
            foreach ($settings as $k => $v) { $normalized[Str::snake($k)] = $v; }
            $settings = $normalized;
            $section = LandingSection::where('tenant_id', $tenantId)->where('section_key', $sectionKey)->first();
            if (! $section) continue;
            if (array_key_exists('active', $settings)) { $section->is_active = (bool) $settings['active']; unset($settings['active']); }
            if (array_key_exists('title', $settings)) { $section->title = $settings['title'] !== '' ? $settings['title'] : null; unset($settings['title']); }
            if (array_key_exists('pre_title', $settings)) { $section->pre_title = $settings['pre_title'] !== '' ? $settings['pre_title'] : null; unset($settings['pre_title']); }
            if (array_key_exists('subtitle', $settings)) { $section->subtitle = $settings['subtitle'] !== '' ? $settings['subtitle'] : null; unset($settings['subtitle']); }
            if (array_key_exists('limit_data', $settings)) { $section->limit_data = max(1, (int) $settings['limit_data']); unset($settings['limit_data']); }
            if (! empty($settings)) { $section->settings = array_replace($section->settings ?? [], $settings); }
            $section->save();
        }
    }

    public function shared(string $template, bool $preview = false): array
    {
        $tenant = Tenant::find(sys_tenant_id());
        $settings = LandingPageSetting::forCurrentTenant();
        return [
            'template' => $template, 'preview' => $preview, 'design' => $this->design(), 'themeOptions' => $this->themes->all(),
            'site' => [
                'name' => $tenant?->name ?: config('app.name'), 'title' => $tenant?->name ?: config('app.name'),
                'description' => $tenant?->tagline, 'tagline' => $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.',
                'address' => $settings?->address ?: $tenant?->address, 'email' => $settings?->contact_email ?: $tenant?->email,
                'phone' => $settings?->contact_phone ?: $tenant?->phone, 'whatsapp' => $settings?->whatsapp,
                'logo' => $tenant?->logoNavbarUrl() ?: $settings?->logo_url, 'logoNavbar' => $tenant?->logoNavbarUrl() ?: $settings?->logo_url,
                'logoFooter' => $tenant?->logoFooterUrl(), 'favicon' => $settings?->favicon_url ?: $tenant?->faviconUrl(),
                'homeUrl' => Route::has('public.index') ? route('public.index') : url('/'),
                'contactUrl' => route('public.contact'), 'loginUrl' => route('auth.login'),
                'social' => ['facebook' => $settings?->facebook_url, 'instagram' => $settings?->instagram_url, 'linkedin' => $settings?->linkedin_url, 'youtube' => $settings?->youtube_url],
            ],
            'seo' => ['title' => $settings?->meta_title, 'description' => $settings?->meta_description, 'keywords' => $settings?->meta_keywords],
            'menus' => $this->menus(), 'footerMenus' => $this->footerMenus(), 'sections' => $this->sectionOrder(),
        ];
    }

    public function home(string $template, bool $preview = false): array
    {
        $data = [
            ...$this->shared($template, $preview),
            'slides' => Slideshow::where('is_active', true)->orderBy('seq')->get()->map(fn (Slideshow $s) => $this->mapSlide($s))->values(),
            'announcements' => $this->announcements(6),
            'faqs' => FAQ::where('is_active', true)->orderBy('seq')->get()->map(fn (FAQ $f) => $this->mapFaq($f))->values(),
            'testimonials' => Section::ofType('testimonial')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $t) => $this->mapTestimonial($t))->values(),
            'partners' => Section::ofType('partner')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $p) => $this->mapPartner($p))->values(),
            'pages' => Page::where('is_published', true)->orderBy('title')->get()->map(fn (Page $p) => $this->pageSummary($p))->values(),
        ];
        $data['sections'] = $this->sectionOrder();
        $data['landing'] = $this->landingContent();
        return $data;
    }

    public function landingContent(): array
    {
        $cta = Section::ofType('cta')->where('is_active', true)->latest('updated_at')->first();
        return [
            'hero' => $this->heroContent(),
            'features' => Section::ofType('feature')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $i) => $this->mapFeature($i))->values(),
            'products' => Section::ofType('product')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $i) => $this->mapProduct($i))->values(),
            'statistics' => Section::ofType('statistic')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $i) => $this->mapStatistic($i))->values(),
            'clients' => Section::ofType('client')->where('is_active', true)->orderBy('sort_order')->get()->map(fn (Section $i) => $this->mapClient($i))->values(),
            'pricing' => Pricing::where('is_active', true)->orderBy('sort_order')->get()->map(fn (Pricing $i) => $this->mapPricing($i))->values(),
            'cta' => $cta ? $this->mapCta($cta) : null,
        ];
    }

    public function page(Page $page, string $template): array
    {
        return [...$this->shared($template), 'page' => [...$this->pageSummary($page), 'content' => $this->sanitizeHtml($page->content), 'content_layout' => $page->content_layout ?? 'default', 'content_width' => $page->content_width ?? 'default', 'content_bg' => $page->content_bg ?? null, 'eyebrow' => $page->eyebrow ?? null, 'excerpt' => $page->excerpt ?? Str::limit(strip_tags($page->content), 135), 'pretitle_color' => $page->pretitle_color ?? null, 'title_color' => $page->title_color ?? null, 'subtitle_color' => $page->subtitle_color ?? null]];
    }

    public function news(Pengumuman $item, string $template): array
    {
        return [...$this->shared($template), 'announcement' => [...$this->announcementSummary($item), 'content' => $this->sanitizeHtml($item->isi), 'content_layout' => $item->content_layout ?? 'default', 'content_width' => $item->content_width ?? 'default', 'content_bg' => $item->content_bg ?? null, 'eyebrow' => $item->jenis ?? 'Pengumuman', 'excerpt' => $item->excerpt ?? Str::limit(strip_tags($item->isi), 150), 'pretitle_color' => $item->pretitle_color ?? null, 'title_color' => $item->title_color ?? null, 'subtitle_color' => $item->subtitle_color ?? null, 'author' => $item->penulis?->name, 'reading_time' => max(1, ceil(str_word_count(strip_tags($item->isi)) / 200))], 'related' => $this->announcements(3, $item->getKey())];
    }

    public function newsIndex(string $template): array
    {
        return [...$this->shared($template), 'announcements' => $this->announcements(24), 'header' => ['eyebrow' => 'Kabar kampus', 'title' => 'Berita dan pengumuman', 'excerpt' => 'Ikuti perkembangan, agenda, dan informasi terbaru dari institusi.']];
    }

    private function menus(): Collection
    {
        return Menu::with(['page', 'children.page', 'children.children.page'])->whereNull('parent_id')->where('position', 'header')->where('is_active', true)->orderBy('sequence')->get()->map(fn (Menu $m) => $this->mapMenuItem($m))->prepend(['id' => 'home', 'title' => 'Beranda', 'url' => route('home'), 'target' => '_self'])->push(['id' => 'contact', 'title' => 'Hubungi Kami', 'url' => route('public.contact'), 'target' => '_self'])->values();
    }

    private function footerMenus(): Collection
    {
        return Menu::with('page')->whereNull('parent_id')->where('position', 'like', 'footer%')->where('is_active', true)->orderBy('position')->orderBy('sequence')->get()->map(fn (Menu $m) => $this->mapMenuItem($m))->values();
    }

    private function mapMenuItem(Menu $menu): array
    {
        $item = ['id' => $menu->getKey(), 'title' => $menu->title, 'url' => match ($menu->type) { 'page' => $menu->page ? route('public.page.show', ['page' => $menu->page->slug]) : '#', 'route' => $menu->url && Route::has($menu->url) ? route($menu->url) : '#', default => $menu->url ?: '#', }, 'target' => $menu->target];
        if ($menu->children && $menu->children->count() > 0) { $item['children'] = $menu->children->filter(fn (Menu $c) => $c->is_active)->map(fn (Menu $c) => $this->mapMenuItem($c))->values(); }
        return $item;
    }

    private function announcements(int $limit, ?int $except = null): Collection
    {
        return Pengumuman::where('is_published', true)->when($except, fn ($q) => $q->whereKeyNot($except))->orderByDesc('published_at')->orderByDesc('created_at')->limit($limit)->get()->map(fn (Pengumuman $i) => $this->announcementSummary($i))->values();
    }

    private function announcementSummary(Pengumuman $item): array
    {
        return ['id' => $item->getKey(), 'title' => $item->judul, 'excerpt' => Str::limit(strip_tags($item->isi), 150), 'type' => Str::headline($item->jenis), 'date' => formatTanggalIndo($item->published_at ?? $item->created_at), 'image' => $item->cover_medium_url ?: asset('images/no-image.jpg'), 'url' => route('public.news.show', $item)];
    }

    public function sectionOrder(): array
    {
        $tenantId = sys_tenant_id();
        $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        if ($existing->isEmpty()) { $this->initializeSections($tenantId); $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get(); }
        return $existing->map(fn (LandingSection $s) => ['landing_section_id' => $s->landing_section_id, 'section_key' => $s->section_key, 'section_name' => $s->section_name, 'area' => $s->area, 'title' => $s->title, 'pre_title' => $s->pre_title, 'post_title' => $s->post_title, 'subtitle' => $s->subtitle, 'is_active' => $s->is_active, 'limit_data' => $s->limit_data, 'variant' => $s->variant, 'settings' => is_string($s->settings) ? json_decode($s->settings, true) : $s->settings])->values()->toArray();
    }

    public function sections(): array
    {
        $tenantId = sys_tenant_id();
        $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        if ($existing->isEmpty()) { $this->initializeSections($tenantId); $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get(); }
        return $existing->map(fn (LandingSection $s) => [...$s->toArray(), 'encrypted_id' => $s->encrypted_landing_section_id])->values()->toArray();
    }

    public function initializeSections(int $tenantId): void { foreach (LandingSection::defaultRows($tenantId) as $row) { LandingSection::create($row); } }
    public function updateSection(LandingSection $section, array $data): void { $section->update($data); }
    public function reorderSections(string $area, array $ids): void { $tenantId = sys_tenant_id(); $i = 1; foreach ($ids as $id) { LandingSection::where('tenant_id', $tenantId)->where('landing_section_id', decryptId($id))->update(['sort_order' => $i++]); } }

    public function reorderSectionsGlobal(array $order): void
    {
        $tenantId = sys_tenant_id(); $sort = ['top' => 0, 'middle' => 0, 'bottom' => 0];
        foreach ($order as $item) {
            if (! is_array($item) || empty($item['id']) || empty($item['area'])) continue;
            $area = in_array($item['area'], ['top', 'middle', 'bottom'], true) ? $item['area'] : null;
            if ($area === null) continue;
            $sort[$area]++;
            LandingSection::where('tenant_id', $tenantId)->where('landing_section_id', decryptIdIfEncrypted($item['id']))->update(['area' => $area, 'sort_order' => $sort[$area]]);
        }
    }

    public function sectionData(LandingSection $section): array
    {
        $data = ['section' => $section];
        switch ($section->section_key) {
            case 'hero': $data['hero'] = $this->heroContent($section); break;
            case 'stats': case 'statistic': $data['stats'] = Section::ofType('statistic')->where('is_active', true)->orderBy('sort_order')->limit($section->limit_data ?? 4)->get()->map(fn (Section $i) => $this->mapStatistic($i))->values(); break;
            case 'products': case 'product': $data['products'] = Section::ofType('product')->where('is_active', true)->orderBy('sort_order')->limit($section->limit_data ?? 6)->get()->map(fn (Section $i) => $this->mapProduct($i))->values(); break;
            case 'features': case 'feature': $data['features'] = Section::ofType('feature')->where('is_active', true)->orderBy('sort_order')->limit($section->limit_data ?? 6)->get()->map(fn (Section $i) => $this->mapFeature($i))->values(); break;
            case 'testimonials': case 'testimonial': $data['testimonials'] = Section::ofType('testimonial')->where('is_active', true)->orderBy('sort_order')->limit($section->limit_data ?? 3)->get()->map(fn (Section $i) => $this->mapTestimonial($i))->values(); break;
            case 'clients': case 'client': $data['clients'] = Section::ofType('client')->where('is_active', true)->orderBy('sort_order')->limit($section->limit_data ?? 8)->get()->map(fn (Section $i) => $this->mapClient($i))->values(); break;
            case 'faq': $data['faqs'] = FAQ::where('is_active', true)->orderBy('seq')->limit($section->limit_data ?? 8)->get()->map(fn (FAQ $i) => $this->mapFaq($i))->values(); break;
            case 'pengumuman': case 'announcement': $data['announcements'] = $this->announcements($section->limit_data ?? 6); break;
            case 'cta': $cta = Section::ofType('cta')->where('is_active', true)->latest('updated_at')->first(); $data['cta'] = $cta ? $this->mapCta($cta) : null; break;
        }
        return $data;
    }

    private function pageSummary(Page $page): array { return ['id' => $page->getKey(), 'title' => $page->title, 'excerpt' => Str::limit(strip_tags($page->content), 135), 'url' => route('public.page.show', $page->slug)]; }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|textarea|button)[^>]*>.*?<\/\1>/is', '', $html);
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|textarea|button)[^>]*\/?>/is', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/is', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:/is', '$1=$2#', $html);
        $html = preg_replace('/src\s*=\s*(["\'])\s*data:(?!image)/is', 'src=$1#', $html);
        return $html;
    }

    // ─── Mappers ─────────────────────────────────────────────────────

    private function mapSlide(Slideshow $slide): array { return ['id' => $slide->getKey(), 'title' => $slide->title, 'caption' => $slide->caption, 'image' => $slide->large_url, 'link' => $slide->link]; }

    private function heroContent(?LandingSection $section = null): array
    {
        $section ??= LandingSection::where('tenant_id', sys_tenant_id())->where('section_key', 'hero')->first();
        $tenant = Tenant::find(sys_tenant_id()); $settings = LandingPageSetting::forCurrentTenant();
        $siteName = $tenant?->name ?: config('app.name'); $tagline = $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.';
        return ['title' => $section?->title ?: $siteName, 'subtitle' => $section?->subtitle, 'description' => $section?->post_title ?: $tagline, 'image' => $section?->image_url, 'buttonPrimary' => ['text' => 'Masuk', 'link' => route('auth.login')], 'buttonSecondary' => ['text' => 'Hubungi Kami', 'link' => route('public.contact')], 'whatsapp' => $settings?->whatsapp, 'microcopy' => ['Respon cepat via WhatsApp', 'Gratis konsultasi', 'Tanpa komitmen']];
    }

    private function mapFeature(Section $item): array { return ['id' => $item->getKey(), 'title' => $item->title, 'description' => $item->description, 'icon' => $item->icon, 'image' => $item->image_url]; }
    private function mapProduct(Section $item): array { return ['id' => $item->getKey(), 'name' => $item->title, 'slug' => $item->slug, 'shortDescription' => $item->getSetting('short_description'), 'description' => $item->description, 'image' => $item->image_url, 'demoUrl' => $item->getSetting('demo_url')]; }
    private function mapStatistic(Section $item): array { return ['id' => $item->getKey(), 'label' => $item->title, 'value' => $item->getSetting('value'), 'icon' => $item->icon]; }
    private function mapClient(Section $item): array { return ['id' => $item->getKey(), 'name' => $item->title, 'logo' => $item->image_url, 'website' => $item->getSetting('website')]; }
    private function mapPricing(Pricing $item): array { return ['id' => $item->getKey(), 'name' => $item->name, 'slug' => $item->slug, 'description' => $item->description, 'price' => $item->price, 'period' => $item->period, 'features' => $item->features ?? [], 'highlight' => $item->highlight]; }
    private function mapCta(Section $cta): array { return ['title' => $cta->title, 'description' => $cta->description, 'buttonText' => $cta->getSetting('button_text'), 'buttonLink' => $cta->getSetting('button_link'), 'backgroundImage' => $cta->image_url]; }
    private function mapFaq(FAQ $item): array { return ['id' => $item->getKey(), 'question' => $item->question, 'answer' => $item->answer, 'category' => $item->category]; }
    private function mapTestimonial(Section $item): array { return ['id' => $item->getKey(), 'name' => $item->title, 'position' => $item->getSetting('position'), 'organization' => $item->getSetting('organization'), 'quote' => $item->description, 'rating' => $item->getSetting('rating'), 'photo' => $item->image_url]; }
    private function mapPartner(Section $p): array { return ['id' => $p->getKey(), 'name' => $p->title, 'category' => $p->getSetting('category'), 'url' => $p->getSetting('website_url'), 'logo' => $p->image_url]; }
}
