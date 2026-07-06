<?php

namespace Modules\Public\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Public\Models\Page;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Services\LandingPageService;

class PublicController extends Controller
{
    public function __construct(private LandingPageService $landing) {}

    public function home(): Response
    {
        $template = $this->landing->template();

        return Inertia::render('Home', $this->landing->home($template));
    }

    public function preview(Request $request): Response
    {
        $template = $this->landing->template($request->string('template')->lower()->value());

        return Inertia::render('Home', $this->landing->home($template, true));
    }

    public function contact(): Response
    {
        $template = $this->landing->template();

        return Inertia::render('Contact', $this->landing->shared($template));
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        logActivity('public_contact', 'Pesan kontak diterima dari '.$request->string('email'));

        return back()->with('success', 'Pesan Anda berhasil diterima. Tim kami akan segera menghubungi Anda.');
    }

    public function showPage(Page $page): Response
    {
        abort_unless($page->is_published, 404);
        $template = $this->landing->template();

        return Inertia::render('ContentPage', $this->landing->page($page, $template));
    }

    public function showNews(Pengumuman $pengumuman): Response
    {
        abort_unless($pengumuman->is_published, 404);
        $template = $this->landing->template();

        return Inertia::render('NewsDetail', $this->landing->news($pengumuman, $template));
    }

    public function showAllNews(): Response
    {
        $template = $this->landing->template();

        return Inertia::render('NewsIndex', $this->landing->newsIndex($template));
    }
}
