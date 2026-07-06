<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Public\Http\Requests\PublicMenuRequest;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Page;
use Modules\Public\Services\PublicMenuService;
use Yajra\DataTables\Facades\DataTables;

class PublicMenuController extends Controller
{
    public function __construct(protected PublicMenuService $menuService) {}

    public function index()
    {
        $headerMenus = Menu::whereNull('parent_id')
            ->where('position', 'header')
            ->orderBy('sequence')
            ->with(['children', 'page'])
            ->get();

        $footerMenus = Menu::whereNull('parent_id')
            ->where('position', 'like', 'footer%')
            ->orderBy('position')
            ->orderBy('sequence')
            ->with(['children', 'page'])
            ->get();

        return view('public::pages.cms.public-menu.index', compact('headerMenus', 'footerMenus'));
    }

    public function data(Request $request)
    {
        $query = Menu::whereNull('parent_id')
            ->with('page')
            ->orderBy('position')
            ->orderBy('sequence');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('type', function ($row) {
                return match ($row->type) {
                    'page' => '<span class="badge bg-blue-lt"><i class="ti ti-file-text me-1"></i>Halaman</span>',
                    'url' => '<span class="badge bg-green-lt"><i class="ti ti-link me-1"></i>Link</span>',
                    'route' => '<span class="badge bg-purple-lt"><i class="ti ti-sign-right me-1"></i>Route</span>',
                    default => '<span class="badge bg-secondary-lt">' . ucfirst($row->type) . '</span>',
                };
            })
            ->editColumn('position', function ($row) {
                $label = match ($row->position) {
                    'header' => 'Header',
                    'footer', 'footer_col_1' => 'Footer 1',
                    'footer_col_2' => 'Footer 2',
                    'footer_col_3' => 'Footer 3',
                    default => ucfirst($row->position),
                };
                return '<span class="badge bg-azure-lt">' . $label . '</span>';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success-lt">Aktif</span>'
                    : '<span class="badge bg-orange-lt">Nonaktif</span>';
            })
            ->addColumn('page_slug', function ($row) {
                return $row->page ? $row->page->slug : ($row->url ?: '-');
            })
            ->addColumn('action', function ($row) {
                $editUrl = $row->type === 'page' && $row->page
                    ? route('cms.page.edit', $row->page->encrypted_page_id)
                    : route('cms.menu.edit', $row->encrypted_menu_id);

                $isModal = $row->type !== 'page';

                return view('components.ui.datatables-actions', [
                    'editUrl' => $editUrl,
                    'editModal' => $isModal,
                    'viewUrl' => null,
                    'deleteUrl' => route('cms.menu.destroy', $row->encrypted_menu_id),
                    'deleteTitle' => 'Hapus item ini?',
                ])->render();
            })
            ->rawColumns(['type', 'position', 'is_active', 'action'])
            ->make(true);
    }

    public function create()
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::orderBy('title')->get();

        return view('public::pages.cms.public-menu.create-edit-ajax', [
            'menu' => new Menu,
            'pages' => $pages,
            'parents' => $parents,
        ]);
    }

    public function store(PublicMenuRequest $request)
    {
        $this->menuService->createMenu($request->validated());

        return jsonSuccess('Menu berhasil ditambahkan.', route('cms.menu.index'));
    }

    public function edit(Menu $menu)
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::where('menu_id', '!=', $menu->menu_id)->orderBy('title')->get();

        return view('public::pages.cms.public-menu.create-edit-ajax', [
            'menu' => $menu,
            'pages' => $pages,
            'parents' => $parents,
        ]);
    }

    public function update(PublicMenuRequest $request, Menu $menu)
    {
        $this->menuService->updateMenu($menu->getKey(), $request->validated());

        return jsonSuccess('Menu berhasil diperbarui.', route('cms.menu.index'));
    }

    public function destroy(Menu $menu)
    {
        $this->menuService->deleteMenu($menu->getKey());

        return jsonSuccess('Menu berhasil dihapus.', route('cms.menu.index'));
    }

    public function reorder(ReorderRequest $request)
    {
        $hierarchy = $request->validated()['hierarchy'] ?? [];
        if ($hierarchy) {
            $this->menuService->reorderMenus($hierarchy);

            return jsonSuccess('Struktur menu berhasil diperbarui.');
        }

        return jsonError('Data struktur tidak valid.');
    }

    public function reorderPosition(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string',
            'position' => 'required|string',
        ]);

        $this->menuService->reorderForPosition($request->input('ids'), $request->input('position'));

        return jsonSuccess('Urutan berhasil diperbarui.');
    }
}
