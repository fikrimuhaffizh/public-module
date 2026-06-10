<?php

namespace Modules\Public\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Public\app\Http\Requests\PublicMenuRequest;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Page;
use Modules\Public\app\Services\PublicMenuService;

class PublicMenuController extends Controller
{
    public function __construct(protected PublicMenuService $menuService) {}

    public function index()
    {
        $orgUnits = Menu::whereNull('parent_id')
            ->orderBy('sequence')
            ->with(['children', 'page'])
            ->get();

        $pages = Page::where('is_published', true)->orderBy('title')->get();

        return view('public::pages.admin.cms.public-menu.index', compact('orgUnits', 'pages'));
    }

    public function create()
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::orderBy('title')->get();

        return view('public::pages.admin.cms.public-menu.create-edit-ajax', [
            'menu' => new Menu,
            'pages' => $pages,
            'parents' => $parents,
        ]);
    }

    public function store(PublicMenuRequest $request)
    {
        $this->menuService->createMenu($request->validated());

        return jsonSuccess('Menu berhasil ditambahkan.', route('public.cms.menu.index'));
    }

    public function edit(Menu $publicMenu)
    {
        $pages = Page::where('is_published', true)->orderBy('title')->get();
        $parents = Menu::where('menu_id', '!=', $publicMenu->menu_id)->orderBy('title')->get();

        return view('public::pages.admin.cms.public-menu.create-edit-ajax', [
            'menu' => $publicMenu,
            'pages' => $pages,
            'parents' => $parents,
        ]);
    }

    public function update(PublicMenuRequest $request, Menu $publicMenu)
    {
        $this->menuService->updateMenu($publicMenu->getKey(), $request->validated());

        return jsonSuccess('Menu berhasil diperbarui.', route('public.cms.menu.index'));
    }

    public function destroy(Menu $publicMenu)
    {
        $this->menuService->deleteMenu($publicMenu->getKey());

        return jsonSuccess('Menu berhasil dihapus.', route('public.cms.menu.index'));
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
}
