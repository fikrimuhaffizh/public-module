<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Http\Requests\StatisticRequest;
use Modules\Public\app\Models\Statistic;

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
        return view('public::pages.cms.statistic.index', [
            'statistics' => Statistic::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.statistic.create-edit-ajax', ['statistic' => new Statistic]);
    }

    public function store(StatisticRequest $request)
    {
        $data = $request->validated();
        $data['sort_order'] = (int) Statistic::max('sort_order') + 1;
        Statistic::create($data);

        return jsonSuccess('Statistik berhasil ditambahkan.', route('public.cms.statistic.index'));
    }

    public function edit(Statistic $statistic)
    {
        return view('public::pages.cms.statistic.create-edit-ajax', compact('statistic'));
    }

    public function update(StatisticRequest $request, Statistic $statistic)
    {
        $statistic->update($request->validated());

        return jsonSuccess('Statistik berhasil diperbarui.', route('public.cms.statistic.index'));
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
            Statistic::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return jsonSuccess('Urutan statistik berhasil diperbarui.');
    }
}
