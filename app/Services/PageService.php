<?php

namespace Modules\Public\app\Services;

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

            logActivity('public_page', "Membuat halaman: {$page->title}", $page);

            return $page;
        });
    }

    public function updatePage(string|int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $page = $this->findById($id);

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

            logActivity('public_page', "Update halaman: {$page->title}", $page);

            return true;
        });
    }

    public function deletePage(string|int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $page = $this->findById($id);
            $title = $page->title;

            $page->delete();
            logActivity('public_page', "Hapus halaman: {$title}");

            return true;
        });
    }
}
