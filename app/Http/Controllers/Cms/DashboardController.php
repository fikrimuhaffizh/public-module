<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Page;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Slideshow;

class DashboardController extends Controller
{
    public function index()
    {
        // Slideshow
        $slideshows = Slideshow::where('is_active', true)
            ->orderBy('seq', 'asc')
            ->get();

        // 4 Latest News
        $recentNews = Pengumuman::where('is_published', true)
            ->where('jenis', 'artikel_berita')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // 5 Latest Announcements for Timeline
        $recentAnnouncements = Pengumuman::where('is_published', true)
            ->where('jenis', 'cms_pengumuman')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Module stats
        $stats = [
            'total_slideshows' => Slideshow::count(),
            'total_announcements' => Pengumuman::where('jenis', 'cms_pengumuman')->count(),
            'total_news' => Pengumuman::where('jenis', 'artikel_berita')->count(),
            'total_pages' => Page::count(),
            'total_menus' => Menu::count(),
        ];

        return view('public::pages.cms.dashboard', compact('slideshows', 'recentNews', 'recentAnnouncements', 'stats'));
    }
}
