<?php

namespace Modules\Public\app\Services;

use Modules\Public\app\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    public function getFilteredQuery(array $filters = [])
    {
        return Page::query();
    }

    public function createPage(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            // Auto generate slug if empty
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $page = Page::create($data);

            // Handle Main Image
            if (isset($data['main_image'])) {
                $page->addMediaFromRequest('main_image')->toMediaCollection('main_image');
            }

            // Handle Attachments
            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $page->addMedia($file)->toMediaCollection('attachments');
                }
            }

            logActivity('public_page', "Membuat halaman: {$page->title}", $page);

            return $page;
        });
    }

    public function updatePage(Page $page, array $data): bool
    {
        return DB::transaction(function () use ($page, $data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $page->update($data);

            // Handle Main Image
            if (isset($data['main_image'])) {
                $page->addMediaFromRequest('main_image')->toMediaCollection('main_image');
            }

            // Handle Attachments
            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $page->addMedia($file)->toMediaCollection('attachments');
                }
            }

            logActivity('public_page', "Update halaman: {$page->title}", $page);

            return true;
        });
    }

    public function deletePage(Page $page): bool
    {
        return DB::transaction(function () use ($page) {
            $title = $page->title;
            // Detach media first if hard delete, but soft delete handles it usually.
            // Spatie handles media deletion on model deletion logic if configured,
            // but for SoftDeletes it keeps them.

            $page->delete();
            logActivity('public_page', "Hapus halaman: {$title}");

            return true;
        });
    }
}
