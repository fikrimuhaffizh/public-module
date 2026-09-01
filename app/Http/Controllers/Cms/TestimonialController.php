<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Http\Requests\TestimonialRequest;
use Modules\Public\Models\Testimonial;
use Modules\Public\Services\CmsService;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.testimonial.view')->only('index');
        $this->middleware('permission:public.cms.testimonial.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.testimonial.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.testimonial.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.testimonial.index', [
            'testimonials' => $this->cmsService->getOrdered(Testimonial::class, 'seq'),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.testimonial.create-edit-ajax', [
            'testimonial' => new Testimonial,
        ]);
    }

    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['photo']);
        $data['seq'] = $this->cmsService->nextSortOrder(Testimonial::class, 'seq');
        $testimonial = $this->cmsService->create(Testimonial::class, $data);

        if ($request->hasFile('photo')) {
            $testimonial->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return jsonSuccess('Testimoni berhasil ditambahkan.', route('cms.testimonial.index'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('public::pages.cms.section.testimonial.create-edit-ajax', compact('testimonial'));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $testimonial->update(Arr::except($request->validated(), ['photo']));

        if ($request->hasFile('photo')) {
            $testimonial->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return jsonSuccess('Testimoni berhasil diperbarui.', route('cms.testimonial.index'));
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return jsonSuccess('Testimoni berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            $this->cmsService->updateSortOrder(Testimonial::class, $id, $index + 1, 'seq');
        }

        return jsonSuccess('Urutan testimoni berhasil diperbarui.');
    }
}
