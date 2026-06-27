<x-ui.form-modal
    :title="$product->exists ? 'Edit Produk' : 'Tambah Produk'"
    :route="$product->exists ? route('cms.product.update', $product) : route('cms.product.store')"
    :method="$product->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama" :value="$product->name" required />
    <x-ui.form-input name="slug" label="Slug" :value="$product->slug" placeholder="Auto dari nama jika kosong" />
    <x-ui.form-input name="short_description" label="Deskripsi Singkat" :value="$product->short_description" />
    <x-ui.form-input name="description" type="textarea" label="Deskripsi Lengkap" :value="$product->description" rows="4" />
    <x-ui.form-input name="demo_url" label="Link Demo" :value="$product->demo_url" placeholder="https://..." />
    <x-ui.form-input name="image" type="file" label="Gambar" accept="image/png,image/jpeg,image/webp" />
    @if($product->image_url)
        <div class="mb-3"><img src="{{ $product->image_url }}" alt="Preview" class="rounded border" style="max-height:100px"></div>
    @endif
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))>
        <span class="form-check-label">Tampilkan di landing page</span>
    </label>
</x-ui.form-modal>
