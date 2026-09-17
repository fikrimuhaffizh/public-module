<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class UploadLogoRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'logo'       => 'required|file|mimes:png,webp,jpg,jpeg|max:2048',
            'collection' => 'required|in:logo_navbar,logo_footer',
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'logo'       => 'Logo',
            'collection' => 'Koleksi',
        ];
    }
}
