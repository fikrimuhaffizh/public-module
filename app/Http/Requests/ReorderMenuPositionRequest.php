<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class ReorderMenuPositionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'ids'      => 'required|array',
            'ids.*'    => 'required|string',
            'position' => 'required|string',
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'ids'      => 'ID Menu',
            'ids.*'    => 'ID Menu',
            'position' => 'Posisi',
        ];
    }
}
