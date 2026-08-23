<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Http\Requests\SectionRequest;
use Modules\Public\Models\Section;

class SectionControllerUnified extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.section.view')->only('index');
        $this->middleware('permission:public.cms.section.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.section.update')->only(['edit', 'update', 'reorder', 'toggle']);
        $this->middleware('permission:public.cms.section.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'feature');
        $sections = Section::ofType($type)->ordered()->get();

        return view('public::pages.cms.section.unified.index', [
            'sections' => $sections,
            'type'     => $type,
            'types'    => Section::TYPES,
            'icons'    => Section::TYPE_ICONS,
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'feature');
        return view('public::pages.cms.section.unified.create-edit', [
            'section' => new Section(['type' => $type]),
            'type'    => $type,
            'types'   => Section::TYPES,
        ]);
    }

    public function store(SectionRequest $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $settings = $this->extractSettings($request, $type);
        $data['settings'] = $settings;

        $mediaField = $this->mediaFieldForType($type);
        $mediaFile = $request->file($mediaField);
        Arr::forget($data, $mediaField);

        if (in_array($type, ['product', 'pricing'])) {
            $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['title'], null, $type);
        }

        $data['sort_order'] = (int) Section::ofType($type)->max('sort_order') + 1;
        $section = Section::create($data);

        if ($mediaFile) {
            $filename = $this->resolveMediaFilename($section->title, $mediaFile->getClientOriginalExtension(), $type);
            $section->addMedia($mediaFile)->usingName($filename)->toMediaCollection($mediaField);
        }

        return jsonSuccess(Section::typeLabel($type) . ' berhasil ditambahkan.', route('cms.section.index', ['type' => $type]));
    }

    public function edit(Section $section)
    {
        return view('public::pages.cms.section.unified.create-edit', [
            'section' => $section,
            'type'    => $section->type,
            'types'   => Section::TYPES,
        ]);
    }

    public function update(SectionRequest $request, Section $section)
    {
        $data = $request->validated();
        $type = $section->type;
        $settings = $this->extractSettings($request, $type);
        $data['settings'] = $settings;

        $mediaField = $this->mediaFieldForType($type);
        $mediaFile = $request->file($mediaField);
        Arr::forget($data, $mediaField);

        if (in_array($type, ['product', 'pricing'])) {
            $data['slug'] = $this->resolveSlug($data['slug'] ?? $section->slug, $data['title'], $section->getKey(), $type);
        }

        $section->update($data);

        if ($mediaFile) {
            $section->clearMediaCollection($mediaField);
            $filename = $this->resolveMediaFilename($section->title, $mediaFile->getClientOriginalExtension(), $type);
            $section->addMedia($mediaFile)->usingName($filename)->toMediaCollection($mediaField);
        }

        return jsonSuccess(Section::typeLabel($type) . ' berhasil diperbarui.', route('cms.section.index', ['type' => $type]));
    }

    public function destroy(Section $section)
    {
        $type = $section->type;
        $section->delete();
        return jsonSuccess(Section::typeLabel($type) . ' berhasil dihapus.', route('cms.section.index', ['type' => $type]));
    }

    public function toggle(Section $section)
    {
        $section->update(['is_active' => ! $section->is_active]);
        return jsonSuccess('Status berhasil diubah.');
    }

    public function reorder(ReorderRequest $request)
    {
        $order = $request->validated()['order'] ?? [];
        foreach ($order as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Section::whereKey($id)->update(['sort_order' => $index + 1]);
        }
        return jsonSuccess('Urutan berhasil diperbarui.');
    }

    private function extractSettings(Request $request, string $type): array
    {
        $settings = match ($type) {
            'product' => [
                'short_description' => $request->input('short_description'),
                'demo_url' => $request->input('demo_url'),
            ],
            'client' => ['website' => $request->input('website')],
            'partner' => ['category' => $request->input('category'), 'website_url' => $request->input('website_url')],
            'testimonial' => [
                'position' => $request->input('position'),
                'organization' => $request->input('organization'),
                'rating' => $request->input('rating'),
            ],
            'statistic' => ['value' => $request->input('value')],
            'faq' => ['category' => $request->input('category')],
            'slideshow' => [
                'link' => $request->input('link'),
                'external_image_url' => $request->input('external_image_url'),
            ],
            'pricing' => [
                'price' => $request->input('price'),
                'period' => $request->input('period'),
                'features' => $request->input('features'),
                'highlight' => $request->boolean('highlight'),
            ],
            default => [],
        };
        return array_filter($settings, fn ($v) => $v !== null);
    }

    private function mediaFieldForType(string $type): string
    {
        return Section::MEDIA_COLLECTIONS[$type] ?? 'image';
    }

    private function resolveSlug(?string $slug, string $title, int|string|null $ignoreId = null, string $type = 'product'): string
    {
        $base = Str::slug($slug ?: $title) ?: Str::slug($title);
        $candidate = $base;
        $counter = 1;
        while (Section::where('slug', $candidate)->where('type', $type)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $candidate = $base . '-' . $counter++;
        }
        return $candidate;
    }

    private function resolveMediaFilename(string $title, string $extension, string $type): string
    {
        $prefix = match ($type) {
            'feature' => 'cover', 'product' => 'cover', 'client' => 'logo', 'partner' => 'logo',
            'testimonial' => 'photo', 'slideshow' => 'slide', 'pricing' => 'cover',
            'faq' => 'cover', 'statistic' => 'cover', default => 'image',
        };
        return Str::slug($prefix . '-' . $title) . '.' . $extension;
    }
}
