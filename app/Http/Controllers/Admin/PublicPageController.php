<?php

namespace Modules\Public\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Public\app\Http\Requests\PageRequest;
use Modules\Public\app\Services\PageService;
use Illuminate\Http\Request;
use Modules\Public\app\Models\Page;
use Yajra\DataTables\Facades\DataTables;

class PublicPageController extends Controller
{
    public function __construct(protected PageService $pageService) {}

    public function index(Request $request)
    {
        return view('public::pages.admin.cms.public-page.index');
    }

    public function data(Request $request)
    {
        $query = $this->pageService->getFilteredQuery($request->all());

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('is_published', function ($row) {
                return $row->is_published
                    ? '<span class="badge bg-success-lt">Published</span>'
                    : '<span class="badge bg-orange-lt">Draft</span>';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="'.route('public.cms.public-page.show', $row->encrypted_id).'" class="btn btn-icon btn-ghost-info" title="Lihat"><i class="ti ti-eye"></i></a>';
                $editBtn = '<a href="'.route('public.cms.public-page.edit', $row->encrypted_id).'" class="btn btn-icon btn-ghost-primary" title="Edit"><i class="ti ti-pencil"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-icon btn-ghost-danger ajax-delete" data-url="'.route('public.cms.public-page.destroy', $row->encrypted_id).'" data-title="Hapus Halaman?" title="Hapus"><i class="ti ti-trash"></i></button>';

                return '<div class="btn-group btn-group-sm" role="group">'.$viewBtn.$editBtn.$deleteBtn.'</div>';
            })
            ->rawColumns(['is_published', 'action'])
            ->make(true);
    }

    public function show(Page $publicPage)
    {
        return view('public::pages.admin.cms.public-page.show', ['page' => $publicPage]);
    }

    public function create()
    {
        return view('public::pages.admin.cms.public-page.create-edit', ['page' => new Page]);
    }

    public function store(PageRequest $request)
    {
        $this->pageService->createPage($request->validated());

        return redirect()->route('public.cms.public-page.index')->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $publicPage)
    {
        return view('public::pages.admin.cms.public-page.create-edit', ['page' => $publicPage]);
    }

    public function update(PageRequest $request, Page $publicPage)
    {
        $this->pageService->updatePage($publicPage, $request->validated());

        return redirect()->route('public.cms.public-page.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $publicPage)
    {
        $this->pageService->deletePage($publicPage);

        return jsonSuccess('Halaman berhasil dihapus.');
    }
}
