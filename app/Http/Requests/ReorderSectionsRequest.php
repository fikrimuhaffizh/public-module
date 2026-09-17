<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class ReorderSectionsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'area' => 'required|string',
            'ids'  => 'required|array',
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'area' => 'Area',
            'ids'  => 'ID Section',
        ];
    }
}
