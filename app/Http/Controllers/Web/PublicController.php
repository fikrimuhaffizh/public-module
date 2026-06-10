<?php

namespace Modules\Public\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Public\app\Models\FAQ;
use Modules\Public\app\Models\Pengumuman;
use Modules\Public\app\Models\Slideshow;
use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Page;

class PublicController extends Controller
{
    public function home()
    {
        $recentNews = Pengumuman::where('is_published', true)
            ->where('jenis', 'berita')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $slideshows = Slideshow::where('is_active', true)
            ->orderBy('seq', 'asc')
            ->get();

        $faqs = FAQ::where('is_active', true)
            ->orderBy('seq', 'asc')
            ->get()
            ->groupBy('category');

        return view('public::pages.web.index', compact('recentNews', 'slideshows', 'faqs'));
    }

    public function preview()
    {
        $slideshows = Slideshow::where('is_active', true)->orderBy('seq')->get();
        $announcements = Pengumuman::where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
        $faqs = FAQ::where('is_active', true)->orderBy('seq')->get()->groupBy('category');
        $menus = Menu::with(['page', 'children.page'])
            ->whereNull('parent_id')
            ->where('position', 'header')
            ->where('is_active', true)
            ->orderBy('sequence')
            ->get();
        $pages = Page::where('is_published', true)->orderBy('title')->get();

        return view('public::pages.web.preview', compact(
            'slideshows',
            'announcements',
            'faqs',
            'menus',
            'pages'
        ));
    }

    public function showPage(Page $page)
    {
        abort_unless($page->is_published, 404);

        return view('public::pages.web.page', compact('page'));
    }

    public function showNews(Pengumuman $pengumuman)
    {
        if (!$pengumuman->is_published) {
            abort(404);
        }

        return view('public::pages.web.news.show', compact('pengumuman'));
    }

    public function showAllNews()
    {
        $allNews = Pengumuman::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('public::pages.web.news.index', compact('allNews'));
    }
}
