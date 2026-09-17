<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Modules\Public\Services\CmsService;

class UpdateSectionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pre_title'        => 'nullable|string|max:100',
            'title'            => 'nullable|string|max:191',
            'post_title'       => 'nullable|string|max:100',
            'subtitle'         => 'nullable|string|max:500',
            'limit_data'       => 'nullable|integer|min:1|max:50',
            'settings'         => 'nullable|array',
            'settings.text_align' => ['nullable', Rule::in(['left', 'center', 'right'])],
            'variant'          => 'nullable|string|max:50',
            'section_image'    => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
            'section_image_url'=> 'nullable|url|max:2048',
            'logo_navbar'      => 'nullable|file|mimes:png,webp,jpg,jpeg|max:2048',
            'logo_footer'      => 'nullable|file|mimes:png,webp,jpg,jpeg|max:2048',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $section = $this->route('section');
            if (! $section) {
                return;
            }

            $cmsService = app(CmsService::class);
            $registry = $cmsService->getLandingSectionRegistry();
            $sectionMeta = $registry[$section->section_key] ?? [];
            $allowedVariants = array_keys($sectionMeta['variants'] ?? []);

            if (! empty($allowedVariants)) {
                $variant = $this->input('variant');
                if (empty($variant)) {
                    $validator->errors()->add('variant', 'Variant wajib diisi.');
                } elseif (! in_array($variant, $allowedVariants)) {
                    $validator->errors()->add('variant', 'Variant yang dipilih tidak valid.');
                }
            }
        });
    }

    protected function customAttributes(): array
    {
        return [
            'pre_title'         => 'Judul Sebelum',
            'title'             => 'Judul',
            'post_title'        => 'Judul Sesudah',
            'subtitle'          => 'Sub Judul',
            'limit_data'        => 'Batas Data',
            'settings'          => 'Pengaturan',
            'settings.text_align' => 'Rata Teks',
            'variant'           => 'Varian',
            'section_image'     => 'Gambar Section',
            'section_image_url' => 'URL Gambar Section',
            'logo_navbar'       => 'Logo Navbar',
            'logo_footer'       => 'Logo Footer',
        ];
    }
}
