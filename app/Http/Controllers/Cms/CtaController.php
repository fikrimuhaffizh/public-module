<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\CtaRequest;
use Modules\Public\app\Models\Cta;

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
        return view('public::pages.cms.cta.index', [
            'ctas' => Cta::orderByDesc('is_active')->orderByDesc('updated_at')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.cta.create-edit-ajax', ['cta' => new Cta]);
    }

    public function store(CtaRequest $request)
    {
        $data = Arr::except($request->validated(), ['background_image']);
        $cta = Cta::create($data);

        if ($request->hasFile('background_image')) {
            $cta->addMediaFromRequest('background_image')->toMediaCollection('background');
        }

        if ($cta->is_active) {
            $this->deactivateOthers($cta);
        }

        return jsonSuccess('CTA berhasil ditambahkan.', route('public.cms.cta.index'));
    }

    public function edit(Cta $cta)
    {
        return view('public::pages.cms.cta.create-edit-ajax', compact('cta'));
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

        return jsonSuccess('CTA berhasil diperbarui.', route('public.cms.cta.index'));
    }

    public function destroy(Cta $cta)
    {
        $cta->delete();

        return jsonSuccess('CTA berhasil dihapus.');
    }

    private function deactivateOthers(Cta $active): void
    {
        Cta::whereKeyNot($active->getKey())->update(['is_active' => false]);
    }
}
