<?php

namespace Modules\Public\app\Http\Controllers\Cms;

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
        return view('public::pages.cms.public-page.index');
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
                return formatTanggalIndo($row->updated_at);
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '<a href="'.route('public.cms.page.show', $row->encrypted_page_id).'" class="btn btn-icon btn-ghost-info" title="Lihat"><i class="ti ti-eye"></i></a>';
                $editBtn = '<a href="'.route('public.cms.page.edit', $row->encrypted_page_id).'" class="btn btn-icon btn-ghost-primary" title="Edit"><i class="ti ti-pencil"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-icon btn-ghost-danger ajax-delete" data-url="'.route('public.cms.page.destroy', $row->encrypted_page_id).'" data-title="Hapus Halaman?" title="Hapus"><i class="ti ti-trash"></i></button>';

                return '<div class="btn-group btn-group-sm" role="group">'.$viewBtn.$editBtn.$deleteBtn.'</div>';
            })
            ->rawColumns(['is_published', 'action'])
            ->make(true);
    }

    public function show(Page $page)
    {
        return view('public::pages.cms.public-page.show', compact('page'));
    }

    public function create()
    {
        return view('public::pages.cms.public-page.create-edit', ['page' => new Page]);
    }

    public function store(PageRequest $request)
    {
        $this->pageService->createPage($request->validated());

        return redirect()->route('public.cms.page.index')->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page)
    {
        return view('public::pages.cms.public-page.create-edit', compact('page'));
    }

    public function update(PageRequest $request, Page $page)
    {
        $this->pageService->updatePage($page->getKey(), $request->validated());

        return redirect()->route('public.cms.page.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        $this->pageService->deletePage($page->getKey());

        return jsonSuccess('Halaman berhasil dihapus.');
    }
}
