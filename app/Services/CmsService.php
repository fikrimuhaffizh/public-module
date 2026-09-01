<?php

namespace Modules\Public\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Public\Models\Cta;
use Modules\Public\Models\LandingSection;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Page;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Pricing;
use Modules\Public\Models\Product;
use Modules\Public\Models\Section;
use Modules\Public\Models\Slideshow;

/**
 * Generic CMS Service — handles CRUD for all CMS entities.
 * Replaces direct Model calls in CMS Controllers (Slim Controller pattern).
 */
class CmsService
{
    // ─── Generic CRUD ────────────────────────────────────────────

    /**
     * Get all records ordered by a column.
     */
    public function getOrdered(string $modelClass, string $orderBy = 'sort_order', string $direction = 'asc'): \Illuminate\Support\Collection
    {
        return $modelClass::orderBy($orderBy, $direction)->get();
    }

    /**
     * Get the next sort order value for a model.
     */
    public function nextSortOrder(string $modelClass, string $sortColumn = 'sort_order'): int
    {
        return (int) $modelClass::max($sortColumn) + 1;
    }

    /**
     * Create a new record.
     */
    public function create(string $modelClass, array $data): Model
    {
        return $modelClass::create($data);
    }

    /**
     * Update sort order for a record.
     */
    public function updateSortOrder(string $modelClass, int $id, int $sortOrder, string $sortColumn = 'sort_order'): void
    {
        $modelClass::whereKey($id)->update([$sortColumn => $sortOrder]);
    }

    // ─── Menu ────────────────────────────────────────────────────

    public function getMenusByPosition(string $position): \Illuminate\Support\Collection
    {
        return Menu::whereNull('parent_id')
            ->where('position', $position)
            ->orderBy('title')
            ->get();
    }

    public function getMenusForEdit(): array
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::orderBy('title')->get();
        return compact('pages', 'parents');
    }

    public function getMenusForEditWithExclude(int $excludeMenuId): array
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::where('menu_id', '!=', $excludeMenuId)->orderBy('title')->get();
        return compact('pages', 'parents');
    }

    public function getLinkedMenu(int $pageId): ?Menu
    {
        return Menu::where('page_id', $pageId)->first();
    }

    // ─── Page ────────────────────────────────────────────────────

    public function queryPages(): Builder
    {
        return Page::query();
    }

    // ─── Section ─────────────────────────────────────────────────

    public function getSectionsByType(string $type): \Illuminate\Support\Collection
    {
        return Section::ofType($type)->ordered()->get();
    }

    public function getSectionTypes(): array
    {
        return Section::TYPES;
    }

    public function getSectionTypeIcons(): array
    {
        return Section::TYPE_ICONS;
    }

    public function getMediaType(string $type): string
    {
        return Section::MEDIA_COLLECTIONS[$type] ?? 'image';
    }

    public function sectionTypeLabel(string $type): string
    {
        return Section::typeLabel($type);
    }

    public function nextSectionSortOrder(string $type): int
    {
        return (int) Section::ofType($type)->max('sort_order') + 1;
    }

    public function uniqueSlug(string $type, string $base, ?int $ignoreId = null): string
    {
        $candidate = Str::slug($base);
        $i = 2;
        while (Section::where('slug', $candidate)->where('type', $type)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $candidate = Str::slug($base) . '-' . $i++;
        }
        return $candidate;
    }

    // ─── Landing Section ─────────────────────────────────────────

    public function getLandingSections(): \Illuminate\Support\Collection
    {
        return LandingSection::where('tenant_id', sys_tenant_id())
            ->orderBy('sort_order')
            ->get();
    }

    public function getLandingSectionRegistry(): array
    {
        return LandingSection::registry();
    }

    // ─── Dashboard Stats ─────────────────────────────────────────

    public function getDashboardStats(): array
    {
        return [
            'slideshows' => Slideshow::where('is_active', true)
                ->orderBy('seq')->limit(5)->get(),
            'recent_news' => Pengumuman::where('is_published', true)
                ->where('jenis', 'artikel_berita')->latest('published_at')->limit(5)->get(),
            'recent_announcements' => Pengumuman::where('is_published', true)
                ->where('jenis', 'cms_pengumuman')->latest('published_at')->limit(5)->get(),
            'total_slideshows' => Slideshow::count(),
            'total_announcements' => Pengumuman::where('jenis', 'cms_pengumuman')->count(),
            'total_news' => Pengumuman::where('jenis', 'artikel_berita')->count(),
            'total_pages' => Page::count(),
            'total_menus' => Menu::count(),
        ];
    }

    // ─── Pricing unique slug ─────────────────────────────────────

    public function uniquePricingSlug(string $base, ?int $ignoreId = null): string
    {
        $candidate = Str::slug($base);
        $i = 2;
        while (Pricing::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $candidate = Str::slug($base) . '-' . $i++;
        }
        return $candidate;
    }

    public function uniqueProductSlug(string $base, ?int $ignoreId = null): string
    {
        $candidate = Str::slug($base);
        $i = 2;
        while (Product::where('slug', $candidate)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $candidate = Str::slug($base) . '-' . $i++;
        }
        return $candidate;
    }

    // ─── Cta activate ────────────────────────────────────────────

    public function deactivateOtherCtas(int $activeId): void
    {
        Cta::whereKeyNot($activeId)->update(['is_active' => false]);
    }
    // ─── CTA ─────────────────────────────────────────────────────

    public function getCtaOrdered(): \Illuminate\Support\Collection
    {
        return Cta::orderByDesc('is_active')->orderByDesc('updated_at')->get();
    }

    // ─── Settings ────────────────────────────────────────────────

    public function getSettings(): \Illuminate\Support\Collection
    {
        return \Modules\Public\Models\LandingPageSetting::forCurrentTenant();
    }

    public function updateSettings(array $data): bool
    {
        return \Modules\Public\Models\LandingPageSetting::forCurrentTenant()->update($data);
    }

    // ─── Menu Queries ────────────────────────────────────────────

    public function getMenuHeaders(): \Illuminate\Support\Collection
    {
        return Menu::whereNull('parent_id')
            ->where('position', 'header')
            ->orderBy('sequence')
            ->with(['children', 'page'])
            ->get();
    }

    public function getMenuFooters(): \Illuminate\Support\Collection
    {
        return Menu::whereNull('parent_id')
            ->where('position', 'like', 'footer%')
            ->orderBy('position')
            ->orderBy('sequence')
            ->with(['children', 'page'])
            ->get();
    }

    public function getMenuDataQuery()
    {
        return Menu::whereNull('parent_id')
            ->with('page')
            ->orderBy('position')
            ->orderBy('sequence');
    }

    public function getMenusOrdered(): \Illuminate\Support\Collection
    {
        return Menu::orderBy('title')->get();
    }

    public function getLandingSectionsByArea(): \Illuminate\Support\Collection
    {
        return LandingSection::where('tenant_id', sys_tenant_id())
            ->ordered()
            ->get()
            ->groupBy('area');
    }

}