<x-ui.form-modal
    :title="$hero->exists ? 'Edit Hero Section' : 'Tambah Hero Section'"
    :route="$hero->exists ? route('cms.hero.update', $hero) : route('cms.hero.store')"
    :method="$hero->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="title" label="Judul" :value="$hero->title" required />
    <x-ui.form-input name="subtitle" label="Subjudul" :value="$hero->subtitle" />
    <x-ui.form-input name="description" type="textarea" label="Deskripsi" :value="$hero->description" rows="4" />
    <x-ui.form-input name="image" type="file" label="Gambar Hero" accept="image/png,image/jpeg,image/webp" help="Rekomendasi 1600x900 px, maks 4 MB." />
    @if($hero->image_url)
        <div class="mb-3"><img src="{{ $hero->image_url }}" alt="Preview" class="rounded border" style="max-height:120px"></div>
    @endif
    <div class="row">
        <div class="col-md-6"><x-ui.form-input name="button_primary_text" label="Tombol Utama - Teks" :value="$hero->button_primary_text" /></div>
        <div class="col-md-6"><x-ui.form-input name="button_primary_link" label="Tombol Utama - Link" :value="$hero->button_primary_link" placeholder="https://..." /></div>
        <div class="col-md-6"><x-ui.form-input name="button_secondary_text" label="Tombol Sekunder - Teks" :value="$hero->button_secondary_text" /></div>
        <div class="col-md-6"><x-ui.form-input name="button_secondary_link" label="Tombol Sekunder - Link" :value="$hero->button_secondary_link" placeholder="https://..." /></div>
    </div>
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $hero->exists ? $hero->is_active : false))>
        <span class="form-check-label">Jadikan hero aktif (nonaktifkan hero lain)</span>
    </label>
</x-ui.form-modal>
