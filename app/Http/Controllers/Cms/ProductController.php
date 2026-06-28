<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Public\app\Http\Requests\ProductRequest;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.product.view')->only('index');
        $this->middleware('permission:public.cms.product.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.product.update')->only(['edit', 'update', 'reorder']);
        $this->middleware('permission:public.cms.product.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.product.index', [
            'products' => Product::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.product.create-edit-ajax', ['product' => new Product]);
    }

    public function store(ProductRequest $request)
    {
        $data = Arr::except($request->validated(), ['image']);
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name']);
        $data['sort_order'] = (int) Product::max('sort_order') + 1;
        $product = Product::create($data);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return jsonSuccess('Produk berhasil ditambahkan.', route('cms.product.index'));
    }

    public function edit(Product $product)
    {
        return view('public::pages.cms.section.product.create-edit-ajax', compact('product'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = Arr::except($request->validated(), ['image']);
        $data['slug'] = $this->resolveSlug($data['slug'] ?? $product->slug, $data['name'], $product->getKey());
        $product->update($data);

        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return jsonSuccess('Produk berhasil diperbarui.', route('cms.product.index'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return jsonSuccess('Produk berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            Product::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return jsonSuccess('Urutan produk berhasil diperbarui.');
    }

    private function resolveSlug(?string $slug, string $name, int|string|null $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name) ?: Str::slug($name);
        $candidate = $base;
        $counter = 1;

        while (
            Product::where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$counter++;
        }

        return $candidate;
    }
}
