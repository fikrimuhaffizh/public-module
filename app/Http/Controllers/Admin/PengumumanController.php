<?php

namespace Modules\Public\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Public\app\Http\Requests\PengumumanRequest;
use Modules\Public\app\Models\Pengumuman;
use App\Models\Sys\User;
use Modules\Public\app\Services\PengumumanService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PengumumanController extends Controller
{
    public function __construct(protected PengumumanService $pengumumanService) {}

    public function index(Request $request)
    {
        return view('public::pages.admin.cms.pengumuman.index', ['type' => 'pengumuman']);
    }

    public function beritaIndex(Request $request)
    {
        return view('public::pages.admin.cms.pengumuman.index', ['type' => 'berita']);
    }

    public function create($type = 'pengumuman')
    {
        $penulisOptions = User::all();
        $pengumuman = new Pengumuman;

        return view('public::pages.admin.cms.pengumuman.create-edit', compact('type', 'penulisOptions', 'pengumuman'));
    }

    public function store(PengumumanRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover');
        }
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments');
        }

        $pengumuman = $this->pengumumanService->createPengumuman($data);
        $redirectRoute = $pengumuman->jenis === 'pengumuman' ? 'public.cms.pengumuman.index' : 'public.cms.berita.index';

        return redirect()->route($redirectRoute)->with('success', ucfirst($pengumuman->jenis).' berhasil ditambahkan.');
    }

    public function show(Pengumuman $pengumuman)
    {
        return view('public::pages.admin.cms.pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        $penulisOptions = User::all();
        $type = $pengumuman->jenis;

        return view('public::pages.admin.cms.pengumuman.create-edit', compact('pengumuman', 'type', 'penulisOptions'));
    }

    public function update(PengumumanRequest $request, Pengumuman $pengumuman)
    {
        $data = $request->validated();
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover');
        }
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments');
        }

        $this->pengumumanService->updatePengumuman($pengumuman->getKey(), $data);
        $redirectRoute = $pengumuman->jenis === 'pengumuman' ? 'public.cms.pengumuman.index' : 'public.cms.berita.index';

        return redirect()->route($redirectRoute)->with('success', ucfirst($pengumuman->jenis).' berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $jenis = $pengumuman->jenis;
        $this->pengumumanService->deletePengumuman($pengumuman->getKey());
        $redirectRoute = $jenis === 'pengumuman' ? 'public.cms.pengumuman.index' : 'public.cms.berita.index';

        return jsonSuccess(ucfirst($jenis).' deleted successfully.', route($redirectRoute));
    }

    public function data(Request $request)
    {
        $routeName = $request->route()->getName();
        $type = (str_contains($routeName, 'berita') || $request->type === 'berita') ? 'berita' : 'pengumuman';

        $query = $this->pengumumanService->getFilteredQuery($type);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('judul', function ($item) {
                $encryptedId = encryptId($item->pengumuman_id);
                $routePrefix = $item->jenis === 'berita' ? 'public.cms.berita' : 'public.cms.pengumuman';

                return $item->judul;
            })
            ->addColumn('cover', function ($item) {
                $url = $item->cover_small_url ?? '';
                if (! $url) {
                    return '';
                }

                return '<img src="'.$url.'" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">';
            })
            ->addColumn('author', function ($item) {
                return $item->penulis ? $item->penulis->name : '-';
            })
            ->editColumn('is_published', function ($item) {
                $status = $item->is_published ? 'Published' : 'Draft';
                $class = $item->is_published ? 'bg-label-success' : 'bg-label-warning';

                return '<span class="badge '.$class.'">'.$status.'</span>';
            })
            ->editColumn('created_at', function ($item) {
                return formatTanggalIndo($item->created_at);
            })
            ->addColumn('action', function ($item) {
                $encryptedId = encryptId($item->pengumuman_id);
                $routePrefix = $item->jenis === 'berita' ? 'public.cms.berita' : 'public.cms.pengumuman';

                return view('components.ui.datatables-actions', [
                    'editUrl' => route($routePrefix.'.edit', $encryptedId),
                    'editModal' => false,
                    'viewUrl' => route($routePrefix.'.show', $encryptedId),
                    'deleteUrl' => route($routePrefix.'.destroy', $encryptedId),
                ])->render();
            })
            ->rawColumns(['judul', 'is_published', 'cover', 'action'])
            ->make(true);
    }
}
