<?php

namespace Modules\Public\app\Services;

use App\Models\Sys\Tenant;
use App\Services\Sys\TenantConfigService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Public\app\Models\FAQ;
use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Page;
use Modules\Public\app\Models\Pengumuman;
use Modules\Public\app\Models\Slideshow;

class LandingPageService
{
    public const TEMPLATES = ['institutional', 'modern', 'editorial'];

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
            config('public.landing_template', 'institutional')
        );

        return in_array($selected, self::TEMPLATES, true) ? $selected : 'institutional';
    }

    public function saveTemplate(string $template): void
    {
        $this->tenantConfig->set(sys_tenant_id(), 'public', 'landing_template', $template);
    }

    public function shared(string $template, bool $preview = false): array
    {
        $tenant = Tenant::find(sys_tenant_id());

        return [
            'template' => $template,
            'preview' => $preview,
            'site' => [
                'name' => $tenant?->name ?: config('app.name'),
                'tagline' => $tenant?->tagline ?: 'Informasi, layanan, dan inovasi kampus dalam satu ekosistem digital.',
                'address' => $tenant?->address,
                'email' => $tenant?->email,
                'phone' => $tenant?->phone,
                'homeUrl' => route('public.index'),
                'contactUrl' => route('public.contact'),
                'loginUrl' => route('login'),
            ],
            'menus' => $this->menus(),
        ];
    }

    public function home(string $template, bool $preview = false): array
    {
        return [
            ...$this->shared($template, $preview),
            'slides' => Slideshow::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (Slideshow $slide) => [
                    'id' => $slide->getKey(),
                    'title' => $slide->title,
                    'caption' => $slide->caption,
                    'image' => $slide->large_url,
                    'link' => $slide->link,
                ])->values(),
            'announcements' => $this->announcements(6),
            'faqs' => FAQ::where('is_active', true)->orderBy('seq')->get()
                ->map(fn (FAQ $faq) => [
                    'id' => $faq->getKey(),
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                ])->values(),
            'pages' => Page::where('is_published', true)->orderBy('title')->get()
                ->map(fn (Page $page) => $this->pageSummary($page))->values(),
        ];
    }

    public function page(Page $page, string $template): array
    {
        return [
            ...$this->shared($template),
            'page' => [
                ...$this->pageSummary($page),
                'content' => $page->content,
            ],
        ];
    }

    public function news(Pengumuman $item, string $template): array
    {
        return [
            ...$this->shared($template),
            'announcement' => [
                ...$this->announcementSummary($item),
                'content' => $item->isi,
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
            ->map(fn (Menu $menu) => [
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
            ])->push([
                'id' => 'contact',
                'title' => 'Hubungi Kami',
                'url' => route('public.contact'),
                'target' => '_self',
            ])->values();
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

    private function pageSummary(Page $page): array
    {
        return [
            'id' => $page->getKey(),
            'title' => $page->title,
            'excerpt' => Str::limit(strip_tags($page->content), 135),
            'url' => route('public.page.show', $page->slug),
        ];
    }
}
