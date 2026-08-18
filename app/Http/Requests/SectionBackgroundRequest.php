<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class SectionBackgroundRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'Gambar latar',
        ];
    }
}
