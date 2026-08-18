<?php

namespace Modules\Public\Services;

use Modules\Account\Models\Tenant;
use Modules\Account\Services\TenantConfigService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Public\Models\FAQ;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Client;
use Modules\Public\Models\Cta;
use Modules\Public\Models\Feature;
use Modules\Public\Models\LandingPageSetting;
use Modules\Public\Models\LandingSection;
use Modules\Public\Models\Page;
use Modules\Public\Models\Partner;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Product;
use Modules\Public\Models\Slideshow;
use Modules\Public\Models\Statistic;
use Modules\Public\Models\Testimonial;

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

        $selected = $this->tenantConfig->get(
            sys_tenant_id(),
            'public',
            'landing_template',
            config('public.landing_template', $this->themes->default())
        );

        return $this->themes->isValid($selected) ? $selected : $this->themes->default();
    }

    public function saveTemplate(string $template): void
    {
        $this->tenantConfig->set(sys_tenant_id(), 'public', 'landing_template', $template);
    }

    /** Daftar key tema valid (untuk Rule::in di validasi). */
    public function themeKeys(): array
    {
        return $this->themes->keys();
    }

    /** Tema dikelompokkan per kategori (institutional / umkm) untuk UI CMS. */
    public function themeGroups(): array
    {
        return $this->themes->categories();
    }

    /**
     * Desain tersimpan (dari tombol "Terapkan ke landing" di /preview).
     * Format: { template, paletteKey, font, card, nav, button, radius, dark,
     * customCss, sectionVariants, sectionColors } — sama seperti state
     * customizer frontend, dipakai landing asli (non-preview) sebagai basis.
     */
    public function design(): ?array
    {
        return LandingPageSetting::forCurrentTenant()->design;
    }

    /**
     * Simpan tema aktif + desain landing per-tenant.
     * Template disimpan ke konfigurasi tenant (landing_template), desain
     * penuh (palet, font, variant & warna section) ke kolom JSON settings.
     */
    public function saveDesign(string $template, array $design): void
    {
        if (! $this->themes->isValid($template)) {
            abort(422, 'Tema tidak valid.');
        }

        $this->saveTemplate($template);

        $settings = LandingPageSetting::forCurrentTenant();
        $settings->design = array_merge($design, ['template' => $template]);
        $settings->save();

        // Hapus media latar section yang tidak lagi direferensikan desain
        // tersimpan, agar file tidak menumpuk di collection 'section_backgrounds'.
        $this->pruneOrphanBackgrounds($design['sectionColors'] ?? []);
    }

    /**
     * Hapus media latar section (collection 'section_backgrounds') yang tidak
     * lagi direferensikan oleh desain tersimpan. Dipanggil setiap desain
     * diterapkan ke landing agar file tidak menumpuk.
     *
     * @param array<string, array> $sectionColors [section_key => colors patch]
     */
    private function pruneOrphanBackgrounds(array $sectionColors): void
    {
        // UUID media yang masih dipakai lewat URL /file/{uuid}/...
        $usedUuids = collect($sectionColors)
            ->pluck('image')
            ->filter()
            ->map(fn (string $url) => preg_match(
                '#/file/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})#i',
                $url,
                $matches
            ) ? $matches[1] : null)
            ->filter()
            ->unique()
            ->values();

        $tenant = Tenant::find(sys_tenant_id());
        if (! $tenant) {
            return;
        }

        $tenant->getMedia('section_backgrounds')
            ->reject(fn ($media) => $usedUuids->contains($media->uuid))
            ->each(fn ($media) => $media->delete());
    }

    /**
     * Simpan pengaturan per-section (mis. navbar → show_topbar) ke settings
     * section DB, di-merge dengan settings yang sudah ada.
     * Dipanggil dari "Terapkan ke landing" (saveDesign) di /preview.
     *
     * @param array<string, array> $sectionSettings [section_key => settings patch]
     */
    public function saveSectionSettings(array $sectionSettings): void
    {
        $tenantId = sys_tenant_id();

        foreach ($sectionSettings as $sectionKey => $settings) {
            if (! is_string($sectionKey) || ! is_array($settings) || empty($settings)) {
                continue;
            }

            // Frontend memakai camelCase (showTopbar); DB memakai snake_case.
            $normalized = [];
            foreach ($settings as $k => $v) {
                $normalized[Str::snake($k)] = $v;
            }
            $settings = $normalized;

            $section = LandingSection::where('tenant_id', $tenantId)
                ->where('section_key', $sectionKey)
                ->first();

            if (! $section) {
                continue;
            }

            // Kolom khusus dipisah dari settings JSON.
            if (array_key_exists('active', $settings)) {
                $section->is_active = (bool) $settings['active'];
                unset($settings['active']);
            }
            if (array_key_exists('title', $settings)) {
                $section->title = $settings['title'] !== '' ? $settings['title'] : null;
                unset($settings['title']);
            }
            if (array_key_exists('pre_title', $settings)) {
                $section->pre_title = $settings['pre_title'] !== '' ? $settings['pre_title'] : null;
                unset($settings['pre_title']);
            }
            if (array_key_exists('subtitle', $settings)) {
                $section->subtitle = $settings['subtitle'] !== '' ? $settings['subtitle'] : null;
                unset($settings['subtitle']);
            }
            if (array_key_exists('limit_data', $settings)) {
                $section->limit_data = max(1, (int) $settings['limit_data']);
                unset($settings['limit_data']);
            }

            if (! empty($settings)) {
                $section->settings = array_replace($section->settings ?? [], $settings);
            }
            $section->save();
        }
    }

    public function shared(string $template, bool $preview = false): array
    {
        $tenant = Tenant::find(sys_tenant_id());
        $settings = LandingPageSetting::forCurrentTenant();

        return [
            'template' => $template,
            'preview' => $preview,
            // Desain tersimpan dari /preview (landing asli memakainya sebagai basis)
            'design' => $this->design(),
            // Metadata semua tema (dari ThemeRegistry) — untuk TemplatePicker frontend.
            'themeOptions' => $this->themes->all(),
            'site' => [
                'name' => $tenant?->name ?: config('app.name'),
                'title' => $tenant?->name ?: config('app.name'),
                'description' => $tenant?->tagline,
                'tagline' => $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.',
                'address' => $settings?->address ?: $tenant?->address,
                'email' => $settings?->contact_email ?: $tenant?->email,
                'phone' => $settings?->contact_phone ?: $tenant?->phone,
                'whatsapp' => $settings?->whatsapp,
                'logo' => $tenant?->logoNavbarUrl() ?: $settings?->logo_url,
                'logoNavbar' => $tenant?->logoNavbarUrl() ?: $settings?->logo_url,
                'logoFooter' => $tenant?->logoFooterUrl(),
                'favicon' => $settings?->favicon_url ?: $tenant?->faviconUrl(),
                'homeUrl' => Route::has('public.index') ? route('public.index') : url('/'),
                'contactUrl' => route('public.contact'),
                'loginUrl' => route('auth.login'),
                'social' => [
                    'facebook' => $settings?->facebook_url,
                    'instagram' => $settings?->instagram_url,
                    'linkedin' => $settings?->linkedin_url,
                    'youtube' => $settings?->youtube_url,
                ],
            ],
            'seo' => [
                'title' => $settings?->meta_title,
                'description' => $settings?->meta_description,
                'keywords' => $settings?->meta_keywords,
            ],
            'menus' => $this->menus(),
            'footerMenus' => $this->footerMenus(),
            // Include sections for navbar/footer visibility on all pages
            'sections' => $this->sectionOrder(),
        ];
    }

    public function home(string $template, bool $preview = false): array
    {
        $data = [
            ...$this->shared($template, $preview),
            'slides' => Slideshow::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (Slideshow $slide) => $this->mapSlide($slide))->values(),
            'announcements' => $this->announcements(6),
            'faqs' => FAQ::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (FAQ $faq) => $this->mapFaq($faq))->values(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (Testimonial $t) => $this->mapTestimonial($t))->values(),
            'partners' => Partner::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (Partner $partner) => $this->mapPartner($partner))->values(),
            'pages' => Page::where('is_published', true)->orderBy('title')->get()
                ->map(fn (Page $page) => $this->pageSummary($page))->values(),
        ];

        // Sections list (for show/hide & reorder) — includes variant for all templates
        $data['sections'] = $this->sectionOrder();

        // Legacy aggregated data used by templates
        $data['landing'] = $this->landingContent();

        return $data;
    }

    public function landingContent(): array
    {
        $cta = Cta::where('is_active', true)->latest('updated_at')->first();

        return [
            'hero' => $this->heroContent(),
            'features' => Feature::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn (Feature $item) => $this->mapFeature($item))->values(),
            'products' => Product::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn (Product $item) => $this->mapProduct($item))->values(),
            'statistics' => Statistic::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn (Statistic $item) => $this->mapStatistic($item))->values(),
            'clients' => Client::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn (Client $item) => $this->mapClient($item))->values(),
            'cta' => $cta ? $this->mapCta($cta) : null,
        ];
    }

    public function page(Page $page, string $template): array
    {
        return [
            ...$this->shared($template),
            'page' => [
                ...$this->pageSummary($page),
                'content' => $this->sanitizeHtml($page->content),
            ],
        ];
    }

    public function news(Pengumuman $item, string $template): array
    {
        return [
            ...$this->shared($template),
            'announcement' => [
                ...$this->announcementSummary($item),
                'content' => $this->sanitizeHtml($item->isi),
            ],
            'related' => $this->announcements(3, $item->getKey()),
        ];
    }

    public function newsIndex(string $template): array
    {
        return [
            ...$this->shared($template),
            'announcements' => $this->announcements(24),
            'header' => [
                'eyebrow' => 'Kabar kampus',
                'title' => 'Berita dan pengumuman',
                'excerpt' => 'Ikuti perkembangan, agenda, dan informasi terbaru dari institusi.',
            ],
        ];
    }

    private function menus(): Collection
    {
        return Menu::with('page')->whereNull('parent_id')
            ->where('position', 'header')->where('is_active', true)->orderBy('sequence')->get()
            ->map(fn (Menu $menu) => $this->mapMenuItem($menu))
            ->push([
                'id' => 'contact',
                'title' => 'Hubungi Kami',
                'url' => route('public.contact'),
                'target' => '_self',
            ])->values();
    }

    private function footerMenus(): Collection
    {
        return Menu::with('page')->whereNull('parent_id')
            ->where('position', 'like', 'footer%')
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('sequence')
            ->get()
            ->map(fn (Menu $menu) => $this->mapMenuItem($menu))
            ->values();
    }

    private function mapMenuItem(Menu $menu): array
    {
        return [
            'id' => $menu->getKey(),
            'title' => $menu->title,
            'url' => match ($menu->type) {
                'page' => $menu->page
                    ? route('public.page.show', ['page' => $menu->page->slug])
                    : '#',
                'route' => $menu->url && Route::has($menu->url)
                    ? route($menu->url)
                    : '#',
                default => $menu->url ?: '#',
            },
            'target' => $menu->target,
        ];
    }

    private function announcements(int $limit, ?int $except = null): Collection
    {
        return Pengumuman::where('is_published', true)
            ->when($except, fn ($query) => $query->whereKeyNot($except))
            ->orderByDesc('published_at')->orderByDesc('created_at')->limit($limit)->get()
            ->map(fn (Pengumuman $item) => $this->announcementSummary($item))->values();
    }

    private function announcementSummary(Pengumuman $item): array
    {
        return [
            'id' => $item->getKey(),
            'title' => $item->judul,
            'excerpt' => Str::limit(strip_tags($item->isi), 150),
            'type' => Str::headline($item->jenis),
            'date' => formatTanggalIndo($item->published_at ?? $item->created_at),
            'image' => $item->cover_medium_url ?: asset('images/no-image.jpg'),
            'url' => route('public.news.show', $item),
        ];
    }

    /**
     * Lightweight section list for preset templates.
     * Now includes variant for visual customization.
     */
    public function sectionOrder(): array
    {
        $tenantId = sys_tenant_id();
        $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        if ($existing->isEmpty()) {
            $this->initializeSections($tenantId);
            $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        }

        return $existing->map(fn (LandingSection $section) => [
            'landing_section_id' => $section->landing_section_id,
            'section_key' => $section->section_key,
            'section_name' => $section->section_name,
            'area' => $section->area,
            'title' => $section->title,
            'pre_title' => $section->pre_title,
            'post_title' => $section->post_title,
            'subtitle' => $section->subtitle,
            'is_active' => $section->is_active,
            'limit_data' => $section->limit_data,
            'variant' => $section->variant,
            'settings' => is_string($section->settings) ? json_decode($section->settings, true) : $section->settings,
        ])->values()->toArray();
    }

    public function sections(): array
    {
        $tenantId = sys_tenant_id();
        $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        if ($existing->isEmpty()) {
            $this->initializeSections($tenantId);
            $existing = LandingSection::where('tenant_id', $tenantId)->ordered()->get();
        }

        return $existing->map(fn (LandingSection $section) => [
            ...$section->toArray(),
            'encrypted_id' => $section->encrypted_landing_section_id,
        ])->values()->toArray();
    }

    public function initializeSections(int $tenantId): void
    {
        foreach (LandingSection::defaultRows($tenantId) as $row) {
            LandingSection::create($row);
        }
    }

    public function updateSection(LandingSection $section, array $data): void
    {
        $section->update($data);
    }

    public function reorderSections(string $area, array $ids): void
    {
        $tenantId = sys_tenant_id();
        $sortOrder = 1;
        foreach ($ids as $id) {
            LandingSection::where('tenant_id', $tenantId)
                ->where('landing_section_id', decryptId($id))
                ->update(['sort_order' => $sortOrder++]);
        }
    }



    /**

     * Urutkan ulang seluruh section sekaligus — termasuk pindah area

     * (top/middle/bottom). Dipakai drag & drop dari Theme Settings offcanvas.

     *

     * @param array<int, array{id: string, area: string}> $order urutan global

     */

    public function reorderSectionsGlobal(array $order): void

    {

        $tenantId = sys_tenant_id();

        $sort = ['top' => 0, 'middle' => 0, 'bottom' => 0];



        foreach ($order as $item) {

            if (! is_array($item) || empty($item['id']) || empty($item['area'])) {

                continue;

            }



            $area = in_array($item['area'], ['top', 'middle', 'bottom'], true) ? $item['area'] : null;

            if ($area === null) {

                continue;

            }



            $id = decryptIdIfEncrypted($item['id']);

            $sort[$area]++;



            LandingSection::where('tenant_id', $tenantId)

                ->where('landing_section_id', $id)

                ->update(['area' => $area, 'sort_order' => $sort[$area]]);

        }

    }



    public function sectionData(LandingSection $section): array
    {
        $data = [
            'section' => $section,
        ];

        switch ($section->section_key) {
            case 'hero':
                $data['hero'] = $this->heroContent($section);
                break;

            case 'stats':
            case 'statistic':
                $data['stats'] = Statistic::where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit($section->limit_data ?? 4)
                    ->get()
                    ->map(fn (Statistic $item) => $this->mapStatistic($item))
                    ->values();
                break;

            case 'products':
            case 'product':
                $data['products'] = Product::where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit($section->limit_data ?? 6)
                    ->get()
                    ->map(fn (Product $item) => $this->mapProduct($item))
                    ->values();
                break;

            case 'features':
            case 'feature':
                $data['features'] = Feature::where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit($section->limit_data ?? 6)
                    ->get()
                    ->map(fn (Feature $item) => $this->mapFeature($item))
                    ->values();
                break;

            case 'testimonials':
            case 'testimonial':
                $data['testimonials'] = Testimonial::where('is_active', true)
                    ->orderBy('seq')
                    ->limit($section->limit_data ?? 3)
                    ->get()
                    ->map(fn (Testimonial $item) => $this->mapTestimonial($item))
                    ->values();
                break;

            case 'clients':
            case 'client':
                $data['clients'] = Client::where('is_active', true)
                    ->orderBy('sort_order')
                    ->limit($section->limit_data ?? 8)
                    ->get()
                    ->map(fn (Client $item) => $this->mapClient($item))
                    ->values();
                break;

            case 'faq':
                $data['faqs'] = FAQ::where('is_active', true)
                    ->orderBy('seq')
                    ->limit($section->limit_data ?? 8)
                    ->get()
                    ->map(fn (FAQ $item) => $this->mapFaq($item))
                    ->values();
                break;

            case 'pengumuman':
            case 'announcement':
                $data['announcements'] = $this->announcements($section->limit_data ?? 6);
                break;

            case 'cta':
                $cta = Cta::where('is_active', true)->latest('updated_at')->first();
                $data['cta'] = $cta ? $this->mapCta($cta) : null;
                break;
        }

        return $data;
    }

    private function pageSummary(Page $page): array
    {
        return [
            'id' => $page->getKey(),
            'title' => $page->title,
            'excerpt' => Str::limit(strip_tags($page->content), 135),
            'url' => route('public.page.show', $page->slug),
        ];
    }

    /**
     * Strip dangerous HTML elements and attributes to prevent XSS.
     * Preserves safe formatting tags (p, br, strong, em, ul, ol, li, h2-h6, a, img, table, blockquote).
     */
    private function sanitizeHtml(string $html): string
    {
        // Remove script/style/iframe/object/embed tags and their contents
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|textarea|button)[^>]*>.*?<\/\1>/is', '', $html);
        // Remove self-closing dangerous tags
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|textarea|button)[^>]*\/?>/is', '', $html);
        // Remove event handlers (onclick, onload, onerror, etc.)
        $html = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/is', '', $html);
        // Remove javascript: protocol in href/src attributes
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:/is', '$1=$2#', $html);
        // Remove data: protocol in src (except data:image)
        $html = preg_replace('/src\s*=\s*(["\'])\s*data:(?!image)/is', 'src=$1#', $html);

        return $html;
    }

    // ─── Shared Model Mappers ───────────────────────────────────────

    private function mapSlide(Slideshow $slide): array
    {
        return [
            'id' => $slide->getKey(),
            'title' => $slide->title,
            'caption' => $slide->caption,
            'image' => $slide->large_url,
            'link' => $slide->link,
        ];
    }

    private function heroContent(?LandingSection $section = null): array
    {
        $section ??= LandingSection::where('tenant_id', sys_tenant_id())
            ->where('section_key', 'hero')
            ->first();

        $tenant = Tenant::find(sys_tenant_id());
        $settings = LandingPageSetting::forCurrentTenant();
        $siteName = $tenant?->name ?: config('app.name');
        $tagline = $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.';

        return [
            'title' => $section?->title ?: $siteName,
            'subtitle' => $section?->subtitle,
            'description' => $section?->post_title ?: $tagline,
            'image' => null,
            'buttonPrimary' => ['text' => 'Masuk', 'link' => route('auth.login')],
            'buttonSecondary' => ['text' => 'Hubungi Kami', 'link' => route('public.contact')],
            // CTA utama di hero: WhatsApp bila nomor tersedia (frontend memakainya
            // sebagai tombol utama), fallback ke buttonPrimary di atas.
            'whatsapp' => $settings?->whatsapp,
            // Microcopy anti-keberatan di bawah tombol hero (bisa di-override
            // per-tenant lewat CMS bila kolomnya ditambahkan nanti).
            'microcopy' => [
                'Respon cepat via WhatsApp',
                'Gratis konsultasi',
                'Tanpa komitmen',
            ],
        ];
    }

    private function mapFeature(Feature $item): array
    {
        return [
            'id' => $item->getKey(),
            'title' => $item->title,
            'description' => $item->description,
            'icon' => $item->icon,
            'image' => $item->image_url,
        ];
    }

    private function mapProduct(Product $item): array
    {
        return [
            'id' => $item->getKey(),
            'name' => $item->name,
            'slug' => $item->slug,
            'shortDescription' => $item->short_description,
            'description' => $item->description,
            'image' => $item->image_url,
            'demoUrl' => $item->demo_url,
        ];
    }

    private function mapStatistic(Statistic $item): array
    {
        return [
            'id' => $item->getKey(),
            'label' => $item->label,
            'value' => $item->value,
            'icon' => $item->icon,
        ];
    }

    private function mapClient(Client $item): array
    {
        return [
            'id' => $item->getKey(),
            'name' => $item->name,
            'logo' => $item->logo_url,
            'website' => $item->website,
        ];
    }

    private function mapCta(Cta $cta): array
    {
        return [
            'title' => $cta->title,
            'description' => $cta->description,
            'buttonText' => $cta->button_text,
            'buttonLink' => $cta->button_link,
            'backgroundImage' => $cta->background_image_url,
        ];
    }

    private function mapFaq(FAQ $item): array
    {
        return [
            'id' => $item->getKey(),
            'question' => $item->question,
            'answer' => $item->answer,
            'category' => $item->category,
        ];
    }

    private function mapTestimonial(Testimonial $item): array
    {
        return [
            'id' => $item->getKey(),
            'name' => $item->name,
            'position' => $item->position,
            'organization' => $item->organization,
            'quote' => $item->quote,
            'rating' => $item->rating,
            'photo' => $item->photo_url,
        ];
    }

    private function mapPartner(Partner $partner): array
    {
        return [
            'id' => $partner->getKey(),
            'name' => $partner->name,
            'category' => $partner->category,
            'url' => $partner->website_url,
            'logo' => $partner->logo_url,
        ];
    }
}
