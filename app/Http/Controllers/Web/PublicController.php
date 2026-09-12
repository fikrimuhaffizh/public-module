<?php

namespace Modules\Public\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Public\Http\Requests\SaveDesignRequest;
use Modules\Public\Models\Page;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Services\LandingPageService;

class PublicController extends Controller
{
    public function __construct(private LandingPageService $landing)
    {
        // Dipakai juga sebagai root controller (LANDING_CONTROLLER) yang tidak
        // membawa middleware Inertia di route-nya — lihat routes/web.php.
        $this->middleware(HandleInertiaRequests::class);
    }

    /** Entry point untuk route '/' saat modul ini dipakai sebagai LANDING_CONTROLLER. */
    public function index(): Response
    {
        return $this->home();
    }

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

        return Inertia::render('Contact', [
            ...$this->landing->shared($template),
            'header' => [
                'eyebrow' => 'Hubungi kami',
                'title' => 'Mari memulai percakapan',
                'excerpt' => 'Sampaikan pertanyaan, kebutuhan layanan, atau peluang kolaborasi kepada tim kami.',
            ],
        ]);
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

    /**
     * Simpan desain dari tombol "Terapkan ke landing" di /preview.
     * Menerima state customizer (palet, font, radius, dll.) + variant/warna
     * section, lalu menyimpan tema aktif + desain penuh per-tenant.
     */
    public function saveDesign(SaveDesignRequest $request): JsonResponse
    {
        $data = $request->validated();

        $design = [
            'paletteKey' => $data['paletteKey'] ?? null,
            'customPalette' => $data['customPalette'] ?? null,
            'designFamily' => $data['designFamily'] ?? null,
            'font' => $data['font'] ?? null,
            'card' => $data['card'] ?? null,
            'nav' => $data['nav'] ?? null,
            'button' => $data['button'] ?? null,
            'radius' => $data['radius'] ?? null,
            'density' => $data['density'] ?? null,
            'elevation' => $data['elevation'] ?? null,
            'dark' => (bool) ($data['dark'] ?? false),
            'heroFill' => (bool) ($data['heroFill'] ?? true),
            'customCss' => $data['customCss'] ?? '',
            'sectionVariants' => $data['sectionVariants'] ?? [],
            'sectionColors' => $data['sectionColors'] ?? [],
        ];

        $this->landing->saveDesign($data['template'], $design, $data['sectionSettings'] ?? []);

        // Pengaturan per-section (mis. navbar → show_topbar) ditulis ke settings section DB.

        return response()->json(['ok' => true, 'template' => $data['template']]);
    }
}
