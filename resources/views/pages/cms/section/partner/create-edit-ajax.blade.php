<x-ui.form-modal
    :title="$partner->exists ? 'Edit Partner' : 'Tambah Partner'"
    :route="$partner->exists ? route('cms.partner.update', $partner) : route('cms.partner.store')"
    :method="$partner->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama Partner" :value="$partner->name" required />
    <x-ui.form-input name="category" label="Kategori" :value="$partner->category" placeholder="Industri, Pemerintah, Pendidikan..." />
    <x-ui.form-input name="website_url" label="Website" :value="$partner->website_url" placeholder="https://..." />
    <x-ui.form-input name="logo" type="file" label="Logo" accept="image/png,image/jpeg,image/webp" help="Maksimal 2 MB. PNG transparan disarankan." />
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $partner->exists ? $partner->is_active : true))>
        <span class="form-check-label">Tampilkan di landing page</span>
    </label>
</x-ui.form-modal>
