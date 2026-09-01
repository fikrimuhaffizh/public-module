<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Http\Requests\StatisticRequest;
use Modules\Public\Models\Statistic;
use Modules\Public\Services\CmsService;

class StatisticController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.statistic.view')->only('index');
        $this->middleware('permission:public.cms.statistic.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.statistic.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.statistic.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.statistic.index', [
            'statistics' => $this->cmsService->getOrdered(Statistic::class),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.statistic.create-edit-ajax', ['statistic' => new Statistic]);
    }

    public function store(StatisticRequest $request)
    {
        $data = $request->validated();
        $data['sort_order'] = $this->cmsService->nextSortOrder(Statistic::class);
        $this->cmsService->create(Statistic::class, $data);

        return jsonSuccess('Statistik berhasil ditambahkan.', route('cms.statistic.index'));
    }

    public function edit(Statistic $statistic)
    {
        return view('public::pages.cms.section.statistic.create-edit-ajax', compact('statistic'));
    }

    public function update(StatisticRequest $request, Statistic $statistic)
    {
        $statistic->update($request->validated());

        return jsonSuccess('Statistik berhasil diperbarui.', route('cms.statistic.index'));
    }

    public function destroy(Statistic $statistic)
    {
        $statistic->delete();

        return jsonSuccess('Statistik berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            $this->cmsService->updateSortOrder(Statistic::class, $id, $index + 1);
        }

        return jsonSuccess('Urutan statistik berhasil diperbarui.');
    }
}
