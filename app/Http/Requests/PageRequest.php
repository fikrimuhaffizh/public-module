<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PageRequest extends BaseRequest
{
    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = $page ? $page->page_id : null;

        return [
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_page', 'slug')->ignore($pageId, 'page_id')],
            'content' => 'nullable|string',
            'meta_desc' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'main_image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'attachments.*' => 'nullable|file|max:10240',
            'menu_position' => 'nullable|in:header,footer_col_1,footer_col_2,footer_col_3',
            'menu_target' => 'nullable|in:_self,_blank',
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
