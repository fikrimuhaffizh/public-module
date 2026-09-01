<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\Http\Requests\FeatureRequest;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Models\Feature;
use Modules\Public\Services\CmsService;

class FeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.feature.view')->only('index');
        $this->middleware('permission:public.cms.feature.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.feature.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.feature.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.feature.index', [
            'features' => $this->cmsService->getOrdered(Feature::class),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.feature.create-edit-ajax', ['feature' => new Feature]);
    }

    public function store(FeatureRequest $request)
    {
        $data = Arr::except($request->validated(), ['image']);
        $data['sort_order'] = $this->cmsService->nextSortOrder(Feature::class);
        $feature = $this->cmsService->create(Feature::class, $data);

        if ($request->hasFile('image')) {
            $feature->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return jsonSuccess('Fitur berhasil ditambahkan.', route('cms.feature.index'));
    }

    public function edit(Feature $feature)
    {
        return view('public::pages.cms.section.feature.create-edit-ajax', compact('feature'));
    }

    public function update(FeatureRequest $request, Feature $feature)
    {
        $feature->update(Arr::except($request->validated(), ['image']));

        if ($request->hasFile('image')) {
            $feature->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return jsonSuccess('Fitur berhasil diperbarui.', route('cms.feature.index'));
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return jsonSuccess('Fitur berhasil dihapus.');
    }

    public function toggle(Feature $feature)
    {
        $feature->update(["is_active" => !$feature->is_active]);
        return jsonSuccess("Status fitur berhasil diubah.");
    }


    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            $this->cmsService->updateSortOrder(Feature::class, $id, $index + 1);
        }

        return jsonSuccess('Urutan fitur berhasil diperbarui.');
    }
}
