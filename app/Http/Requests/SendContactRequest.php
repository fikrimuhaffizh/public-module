<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;

class SendContactRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'name'    => 'Nama',
            'email'   => 'Email',
            'subject' => 'Subjek',
            'message' => 'Pesan',
        ];
    }
}
