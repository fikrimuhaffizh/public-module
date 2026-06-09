<?php

namespace Modules\Public\app\Http\Requests;

use App\Http\Requests\BaseRequest;

class SlideshowRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [
            'title' => 'nullable|string|max:191',
            'caption' => 'nullable|string',
            'link' => 'nullable|url',
            'seq' => 'nullable|integer',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable',
        ];

        if ($this->isMethod('POST')) {
            $rules['slideshow_image'] = 'required_without:image_url|image|mimes:jpeg,png,jpg,gif';
        } else {
            $rules['slideshow_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'slideshow_image' => 'Gambar Slideshow',
            'title' => 'Judul',
            'caption' => 'Caption',
            'link' => 'URL Link',
            'seq' => 'Urutan',
        ];
    }
}
