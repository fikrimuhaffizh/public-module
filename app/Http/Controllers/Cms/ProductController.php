<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\Public\Http\Requests\ProductRequest;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Models\Product;
use Modules\Public\Services\CmsService;

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
            'products' => $this->cmsService->getOrdered(Product::class),
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
        $data['sort_order'] = $this->cmsService->nextSortOrder(Product::class);
        $product = $this->cmsService->create(Product::class, $data);

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

    public function toggle(Product $product)
    {
        $product->update(["is_active" => !$product->is_active]);
        return jsonSuccess("Status produk berhasil diubah.");
    }


    public function reorder(ReorderRequest $request)
    {
        foreach ($request->validated()['order'] ?? [] as $index => $encryptedId) {
            $id = decryptIdIfEncrypted($encryptedId, false);
            $this->cmsService->updateSortOrder(Product::class, $id, $index + 1);
        }

        return jsonSuccess('Urutan produk berhasil diperbarui.');
    }

    private function resolveSlug(?string $slug, string $name, int|string|null $ignoreId = null): string
    {
        return $this->cmsService->uniqueProductSlug($slug ?: $name, $ignoreId);
    }
}
