<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\Http\Requests\PageRequest;
use Modules\Public\Services\PageService;
use Illuminate\Http\Request;
use Modules\Public\Models\Page;
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
                return view('components.ui.datatables-actions', [
                    'editUrl' => route('cms.page.edit', $row->encrypted_page_id),
                    'editModal' => false,
                    'viewUrl' => route('cms.page.show', $row->encrypted_page_id),
                    'deleteUrl' => route('cms.page.destroy', $row->encrypted_page_id),
                    'deleteTitle' => 'Hapus Halaman?',
                ])->render();
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
        return view('public::pages.cms.public-page.create-edit', [
            'page' => new Page,
            'linkedMenu' => null,
        ]);
    }

    public function store(PageRequest $request)
    {
        $this->pageService->createPage($request->validated());

        return redirect()->route('cms.menu.index')->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page)
    {
        $linkedMenu = \Modules\Public\Models\Menu::where('page_id', $page->page_id)->first();

        return view('public::pages.cms.public-page.create-edit', [
            'page' => $page,
            'linkedMenu' => $linkedMenu,
        ]);
    }

    public function update(PageRequest $request, Page $page)
    {
        $this->pageService->updatePage($page->getKey(), $request->validated());

        return redirect()->route('cms.menu.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        $this->pageService->deletePage($page->getKey());

        return jsonSuccess('Halaman berhasil dihapus.');
    }
}
