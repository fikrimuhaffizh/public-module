<x-ui.form-modal
    :title="$client->exists ? 'Edit Klien' : 'Tambah Klien'"
    :route="$client->exists ? route('cms.client.update', $client) : route('cms.client.store')"
    :method="$client->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama Klien" :value="$client->name" required />
    <x-ui.form-input name="website" label="Website" :value="$client->website" placeholder="https://..." />
    <x-ui.form-input name="logo" type="file" label="Logo" accept="image/png,image/jpeg,image/webp" />
    @if($client->logo_url)
        <div class="mb-3"><img src="{{ $client->logo_url }}" alt="Preview" class="rounded border" style="max-height:80px"></div>
    @endif
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $client->exists ? $client->is_active : true))>
        <span class="form-check-label">Tampilkan di landing page</span>
    </label>
</x-ui.form-modal>
