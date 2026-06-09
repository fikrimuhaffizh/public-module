<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class FAQRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:191',
            'seq' => 'nullable|integer',
        ];
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
