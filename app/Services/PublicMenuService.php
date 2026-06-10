<?php

namespace Modules\Public\app\Services;

use Modules\Public\app\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PublicMenuService
{
    public function getBaseQuery(): Builder
    {
        return Menu::query();
    }

    public function getFilteredQuery(array $filters = []): Builder
    {
        return $this->getBaseQuery();
    }

    public function findById(string|int $id): Menu
    {
        return Menu::findOrFail(decryptIdIfEncrypted($id));
    }

    public function createMenu(array $data): Menu
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['page_id']) && ! is_numeric($data['page_id'])) {
                $data['page_id'] = decryptIdIfEncrypted($data['page_id'], false);
            }
            if (isset($data['parent_id']) && ! is_numeric($data['parent_id'])) {
                $data['parent_id'] = decryptIdIfEncrypted($data['parent_id'], false);
            }

            if (! isset($data['sequence'])) {
                $maxSeq = Menu::where('parent_id', $data['parent_id'] ?? null)->max('sequence');
                $data['sequence'] = $maxSeq ? $maxSeq + 1 : 1;
            }

            $menu = Menu::create($data);
            logActivity('public_menu', "Membuat menu: {$menu->title}", $menu);

            return $menu;
        });
    }

    public function updateMenu(string|int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $menu = $this->findById($id);

            if (isset($data['page_id']) && ! is_numeric($data['page_id'])) {
                $data['page_id'] = decryptIdIfEncrypted($data['page_id'], false);
            }
            if (isset($data['parent_id']) && ! is_numeric($data['parent_id'])) {
                $data['parent_id'] = decryptIdIfEncrypted($data['parent_id'], false);
            }

            $menu->update($data);
            logActivity('public_menu', "Update menu: {$menu->title}", $menu);

            return true;
        });
    }

    public function deleteMenu(string|int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $menu = $this->findById($id);
            $title = $menu->title;
            $menu->delete();
            logActivity('public_menu', "Hapus menu: {$title}");

            return true;
        });
    }

    public function reorderMenus(array $hierarchy, $parentId = null)
    {
        return DB::transaction(function () use ($hierarchy, $parentId) {
            foreach ($hierarchy as $index => $item) {
                $id = isset($item['id']) ? decryptIdIfEncrypted($item['id'], false) : null;

                if ($id) {
                    $menu = Menu::find($id);
                    if ($menu) {
                        $menu->update([
                            'parent_id' => $parentId,
                            'sequence' => $index + 1,
                        ]);

                        if (isset($item['children']) && is_array($item['children'])) {
                            $this->reorderMenus($item['children'], $id);
                        }
                    }
                }
            }

            return true;
        });
    }
}
