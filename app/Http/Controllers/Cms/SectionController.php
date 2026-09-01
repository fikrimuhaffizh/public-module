<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Public\Models\LandingSection;
use Modules\Public\Services\LandingPageService;
use Modules\Tenant\Services\TenantService;
use Modules\Public\Http\Requests\SectionBackgroundRequest;
use Modules\Public\Services\CmsService;

class SectionController extends Controller
{
    public function __construct(private LandingPageService $landing, private TenantService $tenantService)
    {
        $this->middleware('permission:public.cms.view')->only(['index', 'edit', 'sections']);
        $this->middleware('permission:public.cms.update')->only(['update', 'updateSection', 'reorderSections', 'editSection', 'toggleSection', 'uploadBackground', 'uploadLogo', 'deleteLogo']);
    }

    public function index()
    {
        $sections = $this->cmsService->getLandingSectionsByArea();

        return view('public::pages.cms.section.index', [
            'sections' => $sections,
            'registry' => $this->cmsService->getLandingSectionRegistry(),
            'template' => $this->landing->template(),
            'templates' => $this->landing->themeKeys(),
            'settings' => $this->cmsService->getSettings(),
        ]);
    }

    public function edit()
    {
        return view('public::pages.cms.section.settings', [
            'selectedTemplate' => $this->landing->template(),
            'themeGroups' => $this->landing->themeGroups(),
        ]);
    }

    public function sections()
    {
        // Redirect ke halaman utama landing yang sudah include section management
        return redirect()->route('cms.landing.index');
    }

    public function editSection(LandingSection $section)
    {
        $registry = $this->cmsService->getLandingSectionRegistry();
        $sectionMeta = $registry[$section->section_key] ?? [];

        $tenant = sys_tenant();

        return view('public::pages.cms.section.create-edit-ajax', [
            'section' => $section,
            'sectionMeta' => $sectionMeta,
            'logoUrl' => $tenant ? ($section->section_key === 'navbar' ? $tenant->logoNavbarUrl() : $tenant->logoFooterUrl()) : null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'landing_template' => ['required', Rule::in($this->landing->themeKeys())],
        ]);

        $this->landing->saveTemplate($data['landing_template']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template landing page berhasil diperbarui.'
            ]);
        }

        return redirect()->route('cms.landing.index')
            ->with('success', 'Template landing page berhasil diperbarui.');
    }

    public function updateSection(Request $request, LandingSection $section)
    {
        $registry = $this->cmsService->getLandingSectionRegistry();
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

        // Allow variant changes for all templates
        if (!empty($allowedVariants)) {
            $rules['variant'] = ['required', Rule::in($allowedVariants)];
        } elseif (!empty($sectionMeta['variants'])) {
            $rules['variant'] = 'nullable|string|max:50';
        }

        $data = $request->validate($rules);
        // Variant is now saved for all templates (not just custom)

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

        // Handle Section Image Upload (for hero, etc.)
        if ($request->hasFile('section_image')) {
            $request->validate([
                'section_image' => 'file|mimes:png,jpg,jpeg,webp|max:4096'
            ]);
            $section->clearMediaCollection('section_image');
            $section->addMedia($request->file('section_image'))
                ->toMediaCollection('section_image');
        } elseif ($request->filled('section_image_url')) {
            // Handle URL input — download image from URL
            $url = $request->input('section_image_url');
            $request->validate([
                'section_image_url' => 'url|max:2048'
            ]);
            try {
                $section->clearMediaCollection('section_image');
                $section->addMediaFromUrl($url)
                    ->toMediaCollection('section_image');
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh gambar dari URL. Pastikan URL valid dan bisa diakses.'
                ], 422);
            }
        }

        // Handle Logo Uploads (if any)
        $tenant = $this->tenantService->findById(sys_tenant_id());
        if ($tenant) {
            foreach (['logo_navbar', 'logo_footer'] as $logoCollection) {
                if ($request->hasFile($logoCollection)) {
                    $request->validate([
                        $logoCollection => 'file|mimes:png,webp,jpg,jpeg|max:2048'
                    ]);
                    $file = $request->file($logoCollection);
                    $tenant->clearMediaCollection($logoCollection);
                    $tenant->addMedia($file)
                        ->usingFileName($logoCollection . '.' . $file->getClientOriginalExtension())
                        ->toMediaCollection($logoCollection);
                }
            }
        }

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

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|mimes:png,webp,jpg,jpeg|max:2048',
            'collection' => 'required|in:logo_navbar,logo_footer',
        ]);

        $tenant = $this->tenantService->findById(sys_tenant_id());
        if (! $tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        $collection = $request->input('collection');
        $tenant->clearMediaCollection($collection);
        $tenant->addMedia($request->file('logo'))
            ->usingFileName($collection . '.' . $request->file('logo')->getClientOriginalExtension())
            ->toMediaCollection($collection);

        return response()->json(['success' => true, 'message' => 'Logo berhasil diupload.']);
    }

    public function deleteLogo(string $collection)
    {
        if (! in_array($collection, ['logo_navbar', 'logo_footer'], true)) {
            abort(404);
        }

        $tenant = $this->tenantService->findById(sys_tenant_id());
        if ($tenant) {
            $tenant->clearMediaCollection($collection);
        }

        return response()->json(['success' => true, 'message' => 'Logo berhasil dihapus.']);
    }

    /**
     * Upload gambar latar section dari Theme Settings (offcanvas /preview).
     * Disimpan ke media collection tenant; URL dipakai sebagai --sec-image.
     */
    public function uploadBackground(SectionBackgroundRequest $request)
    {
        $tenant = $this->tenantService->findById(sys_tenant_id());
        if (! $tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        $media = $tenant->addMedia($request->file('image'))
            ->toMediaCollection('section_backgrounds');

        return response()->json([
            'success' => true,
            'url' => sys_media_url($media),
            'message' => 'Gambar latar berhasil diupload.',
        ]);
    }
}
