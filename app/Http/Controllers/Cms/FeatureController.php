<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\FeatureRequest;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Models\Feature;

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
        return view('public::pages.cms.feature.index', [
            'features' => Feature::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.feature.create-edit-ajax', ['feature' => new Feature]);
    }

    public function store(FeatureRequest $request)
    {
        $data = Arr::except($request->validated(), ['image']);
        $data['sort_order'] = (int) Feature::max('sort_order') + 1;
        $feature = Feature::create($data);

        if ($request->hasFile('image')) {
            $feature->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return jsonSuccess('Fitur berhasil ditambahkan.', route('cms.feature.index'));
    }

    public function edit(Feature $feature)
    {
        return view('public::pages.cms.feature.create-edit-ajax', compact('feature'));
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

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Feature::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return jsonSuccess('Urutan fitur berhasil diperbarui.');
    }
}
