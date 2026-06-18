<?php

namespace Modules\Public\app\Services;

use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    public function getBaseQuery(): Builder
    {
        return Page::query();
    }

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->getBaseQuery();
    }

    public function findById(string|int $id): Page
    {
        return Page::findOrFail(decryptIdIfEncrypted($id));
    }

    public function createPage(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            // Extract menu fields before creating page
            $menuPosition = $data['menu_position'] ?? 'header';
            $menuTarget = $data['menu_target'] ?? '_self';
            unset($data['menu_position'], $data['menu_target']);

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $page = Page::create($data);

            if (isset($data['main_image'])) {
                $page->addMediaFromRequest('main_image')->toMediaCollection('main_image');
            }

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $page->addMedia($file)->toMediaCollection('attachments');
                }
            }

            // Auto-create linked menu item
            $maxSeq = Menu::where('position', $menuPosition)
                ->whereNull('parent_id')
                ->max('sequence');

            Menu::create([
                'title' => $page->title,
                'type' => 'page',
                'page_id' => $page->page_id,
                'position' => $menuPosition,
                'target' => $menuTarget,
                'sequence' => ($maxSeq ?? 0) + 1,
                'is_active' => $page->is_published,
            ]);

            logActivity('public_page', "Membuat halaman: {$page->title}", $page);

            return $page;
        });
    }

    public function updatePage(string|int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $page = $this->findById($id);

            // Extract menu fields before updating page
            $menuPosition = $data['menu_position'] ?? null;
            $menuTarget = $data['menu_target'] ?? null;
            unset($data['menu_position'], $data['menu_target']);

            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $page->update($data);

            if (isset($data['main_image'])) {
                $page->addMediaFromRequest('main_image')->toMediaCollection('main_image');
            }

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $page->addMedia($file)->toMediaCollection('attachments');
                }
            }

            // Auto-update linked menu item
            $menu = Menu::where('page_id', $page->page_id)->first();
            if ($menu) {
                $menuData = [
                    'title' => $page->title,
                    'is_active' => $page->is_published,
                ];
                if ($menuPosition) {
                    $menuData['position'] = $menuPosition;
                }
                if ($menuTarget) {
                    $menuData['target'] = $menuTarget;
                }
                $menu->update($menuData);
            }

            logActivity('public_page', "Update halaman: {$page->title}", $page);

            return true;
        });
    }

    public function deletePage(string|int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $page = $this->findById($id);
            $title = $page->title;

            // Auto-delete linked menu item
            Menu::where('page_id', $page->page_id)->delete();

            $page->delete();
            logActivity('public_page', "Hapus halaman: {$title}");

            return true;
        });
    }
}
