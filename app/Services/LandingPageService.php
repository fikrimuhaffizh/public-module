<?php

namespace Modules\Public\app\Services;

use App\Models\Account\Tenant;
use App\Services\Account\TenantConfigService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Public\app\Models\FAQ;
use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Client;
use Modules\Public\app\Models\Cta;
use Modules\Public\app\Models\Feature;
use Modules\Public\app\Models\LandingPageSetting;
use Modules\Public\app\Models\LandingSection;
use Modules\Public\app\Models\Page;
use Modules\Public\app\Models\Partner;
use Modules\Public\app\Models\Pengumuman;
use Modules\Public\app\Models\Product;
use Modules\Public\app\Models\Slideshow;
use Modules\Public\app\Models\Statistic;
use Modules\Public\app\Models\Testimonial;

class LandingPageService
{
    public const TEMPLATES = ['modern', 'editorial', 'corporate', 'launch', 'aurora'];

    public function __construct(private TenantConfigService $tenantConfig) {}

    public function template(?string $previewTemplate = null): string
    {
        if (in_array($previewTemplate, self::TEMPLATES, true)) {
            return $previewTemplate;
        }

        $selected = $this->tenantConfig->get(
            sys_tenant_id(),
            'public',
            'landing_template',
            config('public.landing_template', 'modern')
        );

        return in_array($selected, self::TEMPLATES, true) ? $selected : 'modern';
    }

    public function saveTemplate(string $template): void
    {
        $this->tenantConfig->set(sys_tenant_id(), 'public', 'landing_template', $template);
    }

    public function shared(string $template, bool $preview = false): array
    {
        $tenant = Tenant::find(sys_tenant_id());
        $settings = LandingPageSetting::forCurrentTenant();

        return [
            'template' => $template,
            'preview' => $preview,
            'site' => [
                'name' => $tenant?->name ?: config('app.name'),
                'tagline' => $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.',
                'address' => $settings?->address ?: $tenant?->address,
                'email' => $settings?->contact_email ?: $tenant?->email,
                'phone' => $settings?->contact_phone ?: $tenant?->phone,
                'whatsapp' => $settings?->whatsapp,
                'logo' => $settings?->logo_url,
                'favicon' => $settings?->favicon_url,
                'homeUrl' => route('public.index'),
                'contactUrl' => route('public.contact'),
                'loginUrl' => route('login'),
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
            'settings' => $section->settings,
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
        $siteName = $tenant?->name ?: config('app.name');
        $tagline = $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.';

        return [
            'title' => $section?->title ?: $siteName,
            'subtitle' => $section?->subtitle,
            'description' => $section?->post_title ?: $tagline,
            'image' => null,
            'buttonPrimary' => ['text' => 'Masuk', 'link' => route('login')],
            'buttonSecondary' => ['text' => 'Hubungi Kami', 'link' => route('public.contact')],
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
