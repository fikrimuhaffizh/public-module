<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class BuilderPageRequest extends BaseRequest
{
    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = $page ? $page->page_id : null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('cms_page', 'slug')->ignore($pageId, 'page_id')],
            'meta_desc' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function customAttributes(): array
    {
        return [
            'title' => 'Judul',
            'slug' => 'Slug',
            'meta_desc' => 'Meta Deskripsi',
            'meta_keywords' => 'Meta Keyword',
            'seo_title' => 'SEO Title',
            'is_published' => 'Status Publikasi',
        ];
    }
}