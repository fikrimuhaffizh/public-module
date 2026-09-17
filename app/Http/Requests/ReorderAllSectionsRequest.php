<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class ReorderAllSectionsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'order'          => 'required|array|min:1',
            'order.*.id'     => 'required',
            'order.*.area'   => 'required|string|in:top,middle,bottom',
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'order'        => 'Urutan',
            'order.*.id'   => 'ID Section',
            'order.*.area' => 'Area',
        ];
    }
}
