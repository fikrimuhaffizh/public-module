<?php

namespace Modules\Public\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Public\Services\ThemeRegistry;

class SaveDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return sys_tenant_id(null) !== null && can('public.cms.settings.update');
    }

    public function rules(): array
    {
        $rules = [
            'template' => ['required', 'string', Rule::in(app(ThemeRegistry::class)->keys())],
            'paletteKey' => ['nullable', 'string', 'max:60'],
            'customPalette' => ['nullable', 'array:primary,primaryDark,accent,background,card,foreground,muted,border,tint', 'required_array_keys:primary,primaryDark,accent,background,card,foreground,muted,border,tint'],
            'customPalette.*' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'designFamily' => ['nullable', Rule::in(['studio', 'editorial', 'institutional', 'gallery', 'atelier', 'minimal', 'bento', 'contrast'])],
            'font' => ['nullable', 'string', 'max:60'],
            'card' => ['nullable', 'string', 'max:30'],
            'nav' => ['nullable', 'string', 'max:30'],
            'button' => ['nullable', 'string', 'max:30'],
            'radius' => ['nullable', 'string', 'max:30'],
            'density' => ['nullable', 'string', 'max:30'],
            'elevation' => ['nullable', 'string', 'max:30'],
            'dark' => ['nullable', 'boolean'],
            'heroFill' => ['nullable', 'boolean'],
            'customCss' => ['nullable', 'string', 'max:20000'],
            'sectionVariants' => ['nullable', 'array'],
            'sectionColors' => ['nullable', 'array'],
            'sectionSettings' => ['nullable', 'array'],
        ];
        $sections = config('landing_sections.sections', []);
        foreach (['sectionVariants', 'sectionColors', 'sectionSettings'] as $key) {
            $rules[$key] = ['nullable', 'array:'.implode(',', array_keys($sections))];
        }
        foreach ($sections as $key => $section) {
            $rules["sectionVariants.$key"] = ['sometimes', 'string', Rule::in(array_keys($section['variants'] ?? []))];
        }
        $rules['radius'] = ['nullable', Rule::in(['square', 'default', 'rounded', 'pill'])];
        $rules['density'] = ['nullable', Rule::in(['compact', 'standard', 'spacious'])];
        $rules['elevation'] = ['nullable', Rule::in(['flat', 'soft', 'medium', 'strong'])];
        $rules['sectionColors.*'] = ['array:bg,text,heading,accent,pretext_color,text_color,posttext_color,pattern,image'];
        foreach (['bg', 'text', 'heading', 'accent', 'pretext_color', 'text_color', 'posttext_color'] as $key) {
            $rules["sectionColors.*.$key"] = ['nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'];
        }
        $rules['sectionColors.*.pattern'] = ['nullable', Rule::in(['dots', 'grid', 'diagonal', 'waves', 'beams', 'noise'])];
        $rules['sectionColors.*.image'] = ['nullable', 'string', 'max:2048', 'regex:~^(?:https?://[^\\s]+|/(?!/)[^\\s]*)$~'];
        $rules['sectionSettings.*'] = ['array:active,title,pre_title,subtitle,text_align,limit_data,topbar_hours,show_login,show_topbar,showTopbar'];
        foreach (['active', 'show_login', 'show_topbar', 'showTopbar'] as $key) {
            $rules["sectionSettings.*.$key"] = ['sometimes', 'boolean'];
        }
        foreach (['title', 'pre_title', 'subtitle', 'topbar_hours'] as $key) {
            $rules["sectionSettings.*.$key"] = ['nullable', 'string', 'max:1000'];
        }
        $rules['sectionSettings.*.text_align'] = ['nullable', Rule::in(['left', 'center', 'right'])];
        $rules['sectionSettings.*.limit_data'] = ['nullable', 'integer', 'min:1', 'max:100'];

        return $rules;
    }
}
