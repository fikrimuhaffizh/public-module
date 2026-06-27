<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Http\Requests\TestimonialRequest;
use Modules\Public\app\Models\Testimonial;

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
        return view('public::pages.cms.testimonial.index', [
            'testimonials' => Testimonial::orderBy('seq')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.testimonial.create-edit-ajax', [
            'testimonial' => new Testimonial,
        ]);
    }

    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        $data = Arr::except($data, ['photo']);
        $data['seq'] = (int) Testimonial::max('seq') + 1;
        $testimonial = Testimonial::create($data);

        if ($request->hasFile('photo')) {
            $testimonial->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return jsonSuccess('Testimoni berhasil ditambahkan.', route('cms.testimonial.index'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('public::pages.cms.testimonial.create-edit-ajax', compact('testimonial'));
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
            Testimonial::whereKey($id)->update(['seq' => $index + 1]);
        }

        return jsonSuccess('Urutan testimoni berhasil diperbarui.');
    }
}
