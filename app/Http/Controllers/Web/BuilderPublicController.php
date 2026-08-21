<?php

namespace Modules\Public\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Modules\Public\Models\Page;
use Modules\Public\Services\BuilderPageService;
use Modules\Public\Services\DynamicBlockService;

/**
 * Resolver halaman builder di /{slug}.
 *
 * Prioritas sesuai desain:
 *   - render_mode = 'custom'   -> response HTML-first (GrapesJS hasil sanitasi)
 *   - render_mode = 'template' -> redirect ke /page/{slug} (React/Inertia, tidak berubah)
 *   - selain itu / tidak ada   -> 404
 */
class BuilderPublicController extends Controller
{
    public function __construct(
        protected BuilderPageService $builder,
        protected DynamicBlockService $dynamicBlocks,
    ) {}

    public function show(string $slug)
    {
        $page = Page::query()
            ->with('builderData')
            ->where('slug', strtolower($slug))
            ->first();

        abort_unless($page, 404);

        if (! $page->isCustom()) {
            return redirect()->route('public.page.show', ['slug' => $page->slug]);
        }

        abort_unless($page->is_published, 404);

        $data = $page->builderData;

        // Resolve dynamic placeholders ({{faq:...}}, {{pengumuman:...}}, etc.)
        $html = $data?->html_compiled ?? '';
        $html = $this->dynamicBlocks->resolve($html);

        $response = response()->view('public::layouts.public-custom-page', [
            'page' => $page,
            'siteName' => sys_tenant_name(),
            'title' => $page->seo_title ?: ($page->title.' | '.sys_tenant_name()),
            'metaDescription' => $page->meta_desc,
            'theme' => config('builder_theme', []),
            'themeCss' => $this->builder->themeCss(),
            'html' => $html,
            'css' => $data?->css_compiled ?? '',
            'compiledAt' => $data?->compiled_at?->toIso8601String(),
            'preview' => false,
        ]);

        return $response
            ->header('X-Content-Type-Options', 'nosniff');
            // Hold sementara — CSP dimatikan utk debugging connect-src jsdelivr (tabler-icons .css.map)
            // ->header('Content-Security-Policy', \Modules\Public\Http\Controllers\Cms\BuilderPageController::cspPolicy());
    }
}