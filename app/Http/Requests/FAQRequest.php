<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class FAQRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:191',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:191',
            'seq' => 'nullable|integer',
            'is_active' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'question' => 'Pertanyaan',
            'answer' => 'Jawaban',
            'category' => 'Kategori',
            'seq' => 'Urutan',
        ];
    }
}
