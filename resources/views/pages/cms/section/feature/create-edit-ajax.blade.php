<x-ui.form-modal
    :title="$feature->exists ? 'Edit Fitur' : 'Tambah Fitur'"
    :route="$feature->exists ? route('cms.feature.update', $feature) : route('cms.feature.store')"
    :method="$feature->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="title" label="Judul" :value="$feature->title" required />
    <x-ui.form-input name="description" type="textarea" label="Deskripsi" :value="$feature->description" rows="3" />
    <x-ui.form-input name="icon" label="Icon Class" :value="$feature->icon" placeholder="ti ti-sparkles atau fa fa-star" help="Tabler, FontAwesome, atau Iconify class." />
    @if($feature->icon)
        <div class="mb-3"><span class="avatar bg-primary-lt me-2"><i class="{{ $feature->icon }}"></i></span> <small class="text-muted">Icon saat ini</small></div>
    @endif
    <x-ui.form-input name="image" type="file" label="Gambar" accept="image/png,image/jpeg,image/webp" />
    @if($feature->image_url)
        <div class="mb-3"><img src="{{ $feature->image_url }}" alt="Preview" class="rounded border" style="max-height:100px"></div>
    @endif
</x-ui.form-modal>
