<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends BaseRequest
{
    public function rules(): array
    {
        $section = $this->route('section');
        $sectionId = $section?->getKey();
        $type = $this->input('type', $section?->type ?? 'feature');

        $allowedTypes = implode(',', array_keys(\Modules\Public\Models\Section::TYPES));
        // CTA is also allowed as a type (for backward compatibility) but not shown in UI
        $allowedTypes .= ',cta';

        $rules = [
            'type'        => ['required', 'string', "in:$allowedTypes"],
            'title'       => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:10000'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        if ($type === 'product' || $type === 'pricing') {
            $rules['slug'] = [
                'nullable', 'string', 'max:191',
                Rule::unique('cms_sections', 'slug')
                    ->where('tenant_id', sys_tenant_id())
                    ->where('type', $type)
                    ->ignore($sectionId, 'section_id'),
            ];
        }

        if ($type === 'product') {
            $rules['short_description'] = ['nullable', 'string', 'max:255'];
            $rules['demo_url'] = ['nullable', 'url', 'max:255'];
        }

        if ($type === 'client') {
            $rules['website'] = ['nullable', 'url', 'max:255'];
        }

        if ($type === 'partner') {
            $rules['category'] = ['nullable', 'string', 'max:100'];
            $rules['website_url'] = ['nullable', 'url', 'max:255'];
        }

        if ($type === 'testimonial') {
            $rules['position'] = ['nullable', 'string', 'max:100'];
            $rules['organization'] = ['nullable', 'string', 'max:191'];
            $rules['rating'] = ['nullable', 'integer', 'min:1', 'max:5'];
        }

        if ($type === 'statistic') {
            $rules['value'] = ['nullable', 'string', 'max:100'];
        }

        if ($type === 'slideshow') {
            $rules['link'] = ['nullable', 'url', 'max:255'];
            $rules['external_image_url'] = ['nullable', 'url', 'max:500'];
        }

        if ($type === 'pricing') {
            $rules['price'] = ['nullable', 'string', 'max:50'];
            $rules['period'] = ['nullable', 'string', 'max:50'];
            $rules['features'] = ['nullable', 'array'];
            $rules['features.*'] = ['string', 'max:255'];
            $rules['highlight'] = ['nullable', 'boolean'];
        }

        // Image validation for all types that have media
        $mediaField = \Modules\Public\Models\Section::MEDIA_COLLECTIONS[$type] ?? null;
        if ($mediaField) {
            $rules[$mediaField] = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
    }
}
