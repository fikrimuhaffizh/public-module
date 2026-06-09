<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class PublicMenuRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:public_menus,menu_id',
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'type' => 'required|in:url,page,route',
            'page_id' => 'nullable|exists:public_pages,page_id',
            'is_active' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => 'Induk Menu',
            'title' => 'Judul Menu',
            'url' => 'URL',
            'type' => 'Tipe',
            'page_id' => 'Halaman Publik',
            'position' => 'Posisi',
            'target' => 'Target',
            'is_active' => 'Status Aktif',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);

        if ($this->has('parent_id') && $this->parent_id) {
            $decoded = \Hashids::decode($this->parent_id);
            if (! empty($decoded)) {
                $this->merge(['parent_id' => $decoded[0]]);
            }
        }

        if ($this->has('page_id') && $this->page_id) {
            $decoded = \Hashids::decode($this->page_id);
            if (! empty($decoded)) {
                $this->merge(['page_id' => $decoded[0]]);
            }
        }
    }
}
