<x-ui.form-modal
    :title="$product->exists ? 'Edit Produk' : 'Tambah Produk'"
    :route="$product->exists ? route('cms.product.update', $product) : route('cms.product.store')"
    :method="$product->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama" :value="$product->name" required />
    <x-ui.form-input name="slug" label="Slug" :value="$product->slug" placeholder="Auto dari nama jika kosong" />
    <x-ui.form-input name="icon" label="Icon Class" :value="$product->icon" placeholder="ti ti-sparkles atau fa fa-star" help="Tabler, FontAwesome, atau Iconify class." />
    @if($product->icon)
        <div class="mb-3"><span class="avatar bg-primary-lt me-2"><i class="{{ $product->icon }}"></i></span> <small class="text-muted">Icon saat ini</small></div>
    @endif
    <x-ui.form-input name="short_description" label="Deskripsi Singkat" :value="$product->short_description" />
    <x-ui.form-textarea name="description" label="Deskripsi Lengkap" :value="$product->description" type="editor" rows="8" />
    <x-ui.form-input name="demo_url" label="Link Demo" :value="$product->demo_url" placeholder="https://..." />
    <x-ui.form-input name="image" type="file" label="Gambar" accept="image/png,image/jpeg,image/webp" />
    @if($product->image_url)
        <div class="mb-3"><img src="{{ $product->image_url }}" alt="Preview" class="rounded border" style="max-height:100px"></div>
    @endif
</x-ui.form-modal>
