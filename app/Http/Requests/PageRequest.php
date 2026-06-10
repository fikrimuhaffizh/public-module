<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PageRequest extends BaseRequest
{
    public function rules(): array
    {
        // Get the page ID from the route if it exists (for update uniqueness)
        // Assuming route parameter is 'public_page' and it's bound via HashidBinding
        $page = $this->route('page');
        $pageId = $page ? $page->page_id : null;

        return [
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_page', 'slug')->ignore($pageId, 'page_id')],
            'content' => 'nullable|string',
            'meta_desc' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'main_image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
            'attachments.*' => 'nullable|file|max:10240',                        // 10MB
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Judul',
            'slug' => 'Slug',
            'content' => 'Konten',
            'meta_desc' => 'Meta Deskripsi',
            'meta_keywords' => 'Meta Keyword',
            'is_published' => 'Status Publikasi',
            'main_image' => 'Gambar Utama',
            'attachments' => 'Lampiran',
            'attachments.*' => 'Lampiran',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_published' => $this->has('is_published'),
        ]);
    }
}
