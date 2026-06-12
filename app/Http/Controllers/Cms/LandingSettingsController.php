<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Public\app\Models\LandingSection;
use Modules\Public\app\Models\LandingPageSetting;
use Modules\Public\app\Services\LandingPageService;

class LandingSettingsController extends Controller
{
    public function __construct(private LandingPageService $landing)
    {
        $this->middleware('permission:public.cms.view')->only(['index', 'edit', 'sections']);
        $this->middleware('permission:public.cms.update')->only(['update', 'updateSection', 'reorderSections', 'editSection', 'toggleSection']);
    }

    public function index()
    {
        $sections = LandingSection::where('tenant_id', sys_tenant_id())
            ->ordered()
            ->get()
            ->groupBy('area');

        return view('public::pages.cms.landing.index', [
            'sections' => $sections,
            'registry' => LandingSection::registry(),
            'template' => $this->landing->template(),
            'templates' => LandingPageService::TEMPLATES,
            'settings' => LandingPageSetting::forCurrentTenant(),
        ]);
    }

    public function edit()
    {
        return view('public::pages.cms.landing-settings.edit', [
            'selectedTemplate' => $this->landing->template(),
            'templates' => LandingPageService::TEMPLATES,
        ]);
    }

    public function sections()
    {
        $sections = LandingSection::where('tenant_id', sys_tenant_id())
            ->ordered()
            ->get()
            ->groupBy('area');

        return view('public::pages.cms.landing-sections.index', [
            'sections' => $sections,
            'registry' => LandingSection::registry(),
        ]);
    }

    public function editSection(LandingSection $section)
    {
        $registry = LandingSection::registry();
        $sectionMeta = $registry[$section->section_key] ?? [];
        return view('public::pages.cms.landing-sections.create-edit-ajax', [
            'section' => $section,
            'sectionMeta' => $sectionMeta,
            'isCustomTemplate' => $this->landing->template() === 'custom',
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'landing_template' => ['required', Rule::in(LandingPageService::TEMPLATES)],
        ]);

        $this->landing->saveTemplate($data['landing_template']);

        return redirect()->route('public.cms.landing.index')
            ->with('success', 'Template landing page berhasil diperbarui.');
    }

    public function updateSection(Request $request, LandingSection $section)
    {
        $registry = LandingSection::registry();
        $sectionMeta = $registry[$section->section_key] ?? [];
        $allowedVariants = array_keys($sectionMeta['variants'] ?? []);

        $rules = [
            'pre_title'     => 'nullable|string|max:100',
            'title'         => 'nullable|string|max:191',
            'post_title'    => 'nullable|string|max:100',
            'subtitle'      => 'nullable|string|max:500',
            'limit_data'    => 'nullable|integer|min:1|max:50',
            'settings'      => 'nullable|array',
            'settings.text_align' => ['nullable', Rule::in(['left', 'center', 'right'])],
        ];

        // Variants are only used by the custom template. Preset templates keep
        // their own visual style and only consume section content/order/status.
        if ($this->landing->template() === 'custom' && !empty($allowedVariants)) {
            $rules['variant'] = ['required', Rule::in($allowedVariants)];
        } elseif ($this->landing->template() === 'custom') {
            $rules['variant'] = 'nullable|string|max:50';
        }

        $data = $request->validate($rules);
        if ($this->landing->template() !== 'custom') {
            unset($data['variant']);
        }

        // Sanitize text fields — strip any HTML tags
        foreach (['pre_title', 'title', 'post_title', 'subtitle'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = strip_tags($data[$field]);
            }
        }

        $data['settings'] = array_replace(
            $section->settings ?? [],
            $data['settings'] ?? []
        );

        $this->landing->updateSection($section, $data);

        return response()->json([
            'success' => true,
            'message' => 'Section berhasil diperbarui.'
        ]);
    }

    public function toggleSection(Request $request, LandingSection $section)
    {
        $this->landing->updateSection($section, ['is_active' => !$section->is_active]);
        
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Status section berhasil diperbarui.']);
        }
        
        return back()->with('success', 'Status section berhasil diperbarui.');
    }

    public function reorderSections(Request $request)
    {
        $data = $request->validate([
            'area' => 'required|string',
            'ids' => 'required|array',
        ]);

        $this->landing->reorderSections($data['area'], $data['ids']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Section berhasil diurutkan.']);
        }

        return back()->with('success', 'Section berhasil diurutkan.');
    }
}
