<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\PartnerRequest;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Models\Partner;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.partner.view')->only('index');
        $this->middleware('permission:public.cms.partner.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.partner.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.partner.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.partner.index', [
            'partners' => Partner::orderBy('seq')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.partner.create-edit-ajax', ['partner' => new Partner]);
    }

    public function store(PartnerRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['logo']);
        $data['seq'] = (int) Partner::max('seq') + 1;
        $partner = Partner::create($data);

        if ($request->hasFile('logo')) {
            $partner->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return jsonSuccess('Partner berhasil ditambahkan.', route('cms.partner.index'));
    }

    public function edit(Partner $partner)
    {
        return view('public::pages.cms.section.partner.create-edit-ajax', compact('partner'));
    }

    public function update(PartnerRequest $request, Partner $partner)
    {
        $partner->update(Arr::except($request->validated(), ['logo']));

        if ($request->hasFile('logo')) {
            $partner->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        return jsonSuccess('Partner berhasil diperbarui.', route('cms.partner.index'));
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return jsonSuccess('Partner berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Partner::whereKey($id)->update(['seq' => $index + 1]);
        }

        return jsonSuccess('Urutan partner berhasil diperbarui.');
    }
}
