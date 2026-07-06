<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class ReorderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order' => 'nullable|array',
            'hierarchy' => 'nullable|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'order' => 'Urutan',
            'hierarchy' => 'Hierarki',
        ];
    }
}
