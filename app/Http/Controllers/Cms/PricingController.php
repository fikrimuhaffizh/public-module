<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Public\Http\Requests\PricingRequest;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Models\Pricing;

class PricingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.pricing.view')->only('index');
        $this->middleware('permission:public.cms.pricing.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.pricing.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.pricing.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.pricing.index', [
            'packages' => Pricing::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.pricing.create-edit-ajax', ['pricing' => new Pricing]);
    }

    public function store(PricingRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name']);
        $data['sort_order'] = (int) Pricing::max('sort_order') + 1;
        $data['features'] = $data['features'] ?? [];

        Pricing::create($data);

        return jsonSuccess('Paket harga berhasil ditambahkan.', route('cms.pricing.index'));
    }

    public function edit(Pricing $pricing)
    {
        return view('public::pages.cms.section.pricing.create-edit-ajax', compact('pricing'));
    }

    public function update(PricingRequest $request, Pricing $pricing)
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? $pricing->slug, $data['name'], $pricing->getKey());
        $data['features'] = $data['features'] ?? [];

        $pricing->update($data);

        return jsonSuccess('Paket harga berhasil diperbarui.', route('cms.pricing.index'));
    }

    public function destroy(Pricing $pricing)
    {
        $pricing->delete();

        return jsonSuccess('Paket harga berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Pricing::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return jsonSuccess('Urutan paket harga berhasil diperbarui.');
    }

    private function resolveSlug(?string $slug, string $name, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: Str::slug($name);
        $candidate = $base;
        $counter = 1;

        while (
            Pricing::where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$counter++;
        }

        return $candidate;
    }
}
