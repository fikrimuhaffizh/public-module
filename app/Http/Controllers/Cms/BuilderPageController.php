<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Public\Http\Requests\BuilderPageRequest;
use Modules\Public\Models\Page;
use Modules\Public\Services\BuilderPageService;
use Yajra\DataTables\Facades\DataTables;

class BuilderPageController extends Controller
{
    public function __construct(protected BuilderPageService $builder)
    {
        $this->middleware('permission:public.builder.view')->only(['index', 'data', 'show', 'editor']);
        $this->middleware('permission:public.builder.create')->only(['create', 'store']);
        $this->middleware('permission:public.builder.update')->only(['edit', 'update', 'saveProject', 'upload']);
        $this->middleware('permission:public.builder.delete')->only(['destroy']);
        $this->middleware('permission:public.builder.publish')->only(['publish', 'unpublish']);
    }

    public function index()
    {
        return view('public::pages.cms.builder.pages.index');
    }

    public function data(Request $request)
    {
        $query = Page::query()
            ->with('builderData')
            ->select('cms_page.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('render_mode', function (Page $row) {
                $label = $row->isCustom() ? 'Custom' : 'Template';
                $class = $row->isCustom() ? 'bg-purple-lt' : 'bg-blue-lt';

                return '<span class="badge '.$class.'">'.$label.'</span>';
            })
            ->editColumn('is_published', function (Page $row) {
                return $row->is_published
                    ? '<span class="badge bg-success-lt">Published</span>'
                    : '<span class="badge bg-orange-lt">Draft</span>';
            })
            ->editColumn('slug', function (Page $row) {
                return '<code>/'.$row->slug.'</code>';
            })
            ->editColumn('updated_at', function (Page $row) {
                return formatTanggalIndo($row->updated_at);
            })
            ->addColumn('action', function (Page $row) {
                return $this->renderActions($row);
            })
            ->rawColumns(['render_mode', 'is_published', 'slug', 'action'])
            ->make(true);
    }

    /** Editor GrapesJS (Phase 2 dipakai sebagai halaman target). */
    public function editor(Page $page)
    {
        $editorConfig = [
            'saveUrl' => route('cms.builder.pages.save-project', $page),
            'publishUrl' => route('cms.builder.pages.publish', $page),
            'unpublishUrl' => route('cms.builder.pages.unpublish', $page),
            'uploadUrl' => route('cms.builder.pages.upload', $page),
            'previewUrl' => route('cms.builder.pages.preview', $page),
            'csrf' => csrf_token(),
            'isPublished' => (bool) $page->is_published,
            'title' => $page->title,
            'slug' => $page->slug,
        ];

        $payload = $this->builder->editorPayload($page);
        $editorConfig['gjsProject'] = $payload['gjs_project'];
        $editorConfig['html'] = $payload['html'];
        $editorConfig['css'] = $payload['css'];

        $editorConfig['blocks'] = $this->builder->sectionBlocks();
        $editorConfig['themeCss'] = $this->builder->themeCss();
        $editorConfig['canvasStyles'] = $this->builderCanvasStyles();
        $editorConfig['sections'] = config('builder_sections.sections', []);

        return response()->view('public::pages.cms.builder.pages.editor', [
            'page' => $page,
            'editorConfig' => $editorConfig,
        ]);
    }

    /**
     * URL stylesheet yang ikut dirender di dalam kanvas editor (seperti publik).
     * public-builder.css = gaya blok WBP builder; landing.css = semua CSS section
     * React (Tailwind + sections/*.css) agar blok hasil SSR tampil utuh.
     */
    protected function builderCanvasStyles(): array
    {
        $entries = [
            'Modules/Public/resources/assets/css/public-builder.css',
            'Modules/Public/resources/assets/css/landing.css',
        ];

        $urls = [];
        foreach ($entries as $entry) {
            try {
                $urls[] = \Illuminate\Support\Facades\Vite::asset($entry);
            } catch (\Throwable) {
                // Entry belum tersedia (belum build / hot mati) — dilewati.
            }
        }

        return $urls;
    }

    /** Upload asset (gambar) untuk halaman builder — koleksi media `builder_assets`. */
    public function upload(Request $request, Page $page): JsonResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif,avif,svg', 'max:5120'],
        ]);

        $assets = [];

        foreach ((array) $request->file('files') as $file) {
            $media = $page->addMedia($file)->toMediaCollection('builder_assets');
            $assets[] = [
                'src' => sys_media_url($media),
                'name' => $media->file_name,
                'type' => 'image',
                'width' => $media->custom_properties['width'] ?? null,
                'height' => $media->custom_properties['height'] ?? null,
            ];
        }

        logActivity('builder_page', "Unggah ".count($assets)." asset untuk halaman: {$page->title}", $page);

        return response()->json(['data' => $assets]);
    }

    /** Payload JSON editor (project + hasil compile). */
    public function project(Page $page): JsonResponse
    {
        return response()->json($this->builder->editorPayload($page));
    }

    /** Simpan project GrapesJS — semua difilter/sanitasi server-side. */
    public function saveProject(Request $request, Page $page): JsonResponse
    {
        $data = $request->validate([
            'gjs_project' => ['nullable', 'array'],
            'html' => ['nullable', 'string', 'max:8388608'],
            'css' => ['nullable', 'string', 'max:8388608'],
        ]);

        $this->builder->saveProject(
            $page,
            $data['gjs_project'] ?? [],
            $data['html'] ?? '',
            $data['css'] ?? ''
        );

        return jsonSuccess('Project halaman berhasil disimpan.', null, [
            'compiled_at' => now()->toIso8601String(),
        ]);
    }

    public function preview(Page $page)
    {
        if (! $page->isCustom()) {
            return redirect()->route('public.page.show', ['slug' => $page->slug]);
        }

        return $this->renderCustomPage($page, preview: true);
    }

    public function publish(Page $page): JsonResponse
    {
        $this->builder->publish($page);

        return jsonSuccess('Halaman berhasil dipublikasikan.');
    }

    public function unpublish(Page $page): JsonResponse
    {
        $this->builder->unpublish($page);

        return jsonSuccess('Halaman berhasil dihentikan (unpublish).');
    }

    public function create()
    {
        return view('public::pages.cms.builder.pages.create-edit', [
            'page' => new Page,
            'templates' => $this->builder->templateCatalog(),
            'isCustom' => true,
        ]);
    }

    public function store(BuilderPageRequest $request)
    {
        $data = $request->validated();
        $templateKey = $request->input('template_key');
        $page = $this->builder->createCustomPage($data, $templateKey);

        return jsonSuccess('Halaman custom dibuat.', route('cms.builder.pages.editor', $page));
    }

    public function edit(Page $page)
    {
        return view('public::pages.cms.builder.pages.create-edit', [
            'page' => $page,
            'templates' => $this->builder->templateCatalog(),
            'isCustom' => $page->isCustom(),
        ]);
    }

    public function update(BuilderPageRequest $request, Page $page)
    {
        $this->builder->updateSettings($page, $request->validated());

        return jsonSuccess('Pengaturan halaman diperbarui.', route('cms.builder.pages.edit', $page));
    }

    public function destroy(Page $page)
    {
        $this->builder->deletePage($page);

        return jsonSuccess('Halaman berhasil dihapus.');
    }

    /** Render halaman custom (HTML-first) — dipakai preview admin & rute publik. */
    public function renderCustomPage(Page $page, bool $preview = false)
    {
        $data = $this->builder->dataFor($page->load('builderData'));

        $html = $data?->html_compiled ?? '';
        $css = $data?->css_compiled ?? '';
        $theme = config('builder_theme', []);

        $response = response()->view('public::layouts.public-custom-page', [
            'page' => $page,
            'siteName' => sys_tenant_name(),
            'title' => $page->seo_title ?: ($page->title.' | '.sys_tenant_name()),
            'metaDescription' => $page->meta_desc,
            'theme' => $theme,
            'themeCss' => $this->builder->themeCss(),
            'html' => $html,
            'css' => $css,
            'compiledAt' => $data?->compiled_at?->toIso8601String(),
            'preview' => $preview,
        ]);

        return $response
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Security-Policy', self::cspPolicy());
    }

    /** CSP untuk rute publik halaman custom (pertahanan lapis kedua pasca-sanitasi). */
    public static function cspPolicy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "media-src 'self' data: blob:",
            "font-src 'self' data:",
            "frame-src https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }

    private function renderActions(Page $row): string
    {
        $actions = [];

        if ($row->isCustom()) {
            $actions[] = sprintf(
                '<a href="%s" class="btn btn-icon btn-sm btn-outline-primary" title="Buka Editor"><i class="ti ti-palette"></i></a>',
                route('cms.builder.pages.editor', $row)
            );
            $actions[] = sprintf(
                '<a href="%s" class="btn btn-icon btn-sm btn-outline-info" title="Preview"><i class="ti ti-eye"></i></a>',
                route('cms.builder.pages.preview', $row)
            );
        } else {
            $actions[] = sprintf(
                '<a href="%s" class="btn btn-icon btn-sm btn-outline-info" title="Lihat di situs"><i class="ti ti-external-link"></i></a>',
                $row->is_published ? route('public.page.show', ['page' => $row->slug]) : '#'
            );
        }

        $actions[] = sprintf(
            '<a href="%s" class="btn btn-icon btn-sm btn-outline-dark" title="Pengaturan"><i class="ti ti-settings"></i></a>',
            route('cms.builder.pages.edit', $row)
        );

        $actions[] = $row->is_published
            ? sprintf(
                '<button type="button" class="btn btn-icon btn-sm btn-outline-warning builder-post" data-method="post" data-url="%s" data-confirm="Hentikan publikasi halaman ini?"><i class="ti ti-player-pause"></i></button>',
                route('cms.builder.pages.unpublish', $row)
            )
            : sprintf(
                '<button type="button" class="btn btn-icon btn-sm btn-outline-success builder-post" data-method="post" data-url="%s" data-confirm="Publikasikan halaman ini?"><i class="ti ti-player-play"></i></button>',
                route('cms.builder.pages.publish', $row)
            );

        $actions[] = sprintf(
            '<button type="button" class="btn btn-icon btn-sm btn-danger builder-post" data-method="delete" data-url="%s" data-confirm="Hapus halaman ini?"><i class="ti ti-trash"></i></button>',
            route('cms.builder.pages.destroy', $row)
        );

        return '<div class="btn-list">'.implode('', $actions).'</div>';
    }
}