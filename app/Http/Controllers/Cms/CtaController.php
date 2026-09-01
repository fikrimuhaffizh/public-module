<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\Http\Requests\CtaRequest;
use Modules\Public\Models\Cta;
use Modules\Public\Services\CmsService;

class CtaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.cta.view')->only('index');
        $this->middleware('permission:public.cms.cta.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.cta.update')->only(['edit', 'update']);
        $this->middleware('permission:public.cms.cta.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.cta.index', [
            'ctas' => $this->cmsService->getCtaOrdered(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.cta.create-edit-ajax', ['cta' => new Cta]);
    }

    public function store(CtaRequest $request)
    {
        $data = Arr::except($request->validated(), ['background_image']);
        $cta = $this->cmsService->create(Cta::class, $data);

        if ($request->hasFile('background_image')) {
            $cta->addMediaFromRequest('background_image')->toMediaCollection('background');
        }

        if ($cta->is_active) {
            $this->deactivateOthers($cta);
        }

        return jsonSuccess('CTA berhasil ditambahkan.', route('cms.cta.index'));
    }

    public function edit(Cta $cta)
    {
        return view('public::pages.cms.section.cta.create-edit-ajax', compact('cta'));
    }

    public function update(CtaRequest $request, Cta $cta)
    {
        $cta->update(Arr::except($request->validated(), ['background_image']));

        if ($request->hasFile('background_image')) {
            $cta->addMediaFromRequest('background_image')->toMediaCollection('background');
        }

        if ($cta->is_active) {
            $this->deactivateOthers($cta);
        }

        return jsonSuccess('CTA berhasil diperbarui.', route('cms.cta.index'));
    }

    public function destroy(Cta $cta)
    {
        $cta->delete();

        return jsonSuccess('CTA berhasil dihapus.');
    }

    private function deactivateOthers(Cta $active): void
    {
        $this->cmsService->deactivateOtherCtas($active->getKey());
    }
}
