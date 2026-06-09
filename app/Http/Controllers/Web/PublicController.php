<?php

namespace Modules\Public\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Public\app\Models\FAQ;
use Modules\Public\app\Models\Pengumuman;
use Modules\Public\app\Models\Slideshow;

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
