<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class StatisticRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:191'],
            'value' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
