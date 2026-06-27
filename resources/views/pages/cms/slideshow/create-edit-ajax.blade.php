<x-ui.form-modal
    :title="$slideshow->exists ? 'Edit Slideshow' : 'Tambah Slideshow'"
    :route="$slideshow->exists ? route('cms.slideshow.update', $slideshow->encrypted_slideshow_id) : route('cms.slideshow.store')"
    :method="$slideshow->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <div class="mb-3">
        <x-ui.form-input 
            name="slideshow_image" 
            label="Upload Gambar" 
            type="file" 
            class="filepond-input"
            :required="!$slideshow->exists && empty($slideshow->image_url)"
            accept="image/*"
            help="Ukuran yang disarankan 1200x600 px."
        />
    </div>

    <div class="mb-3">
        <div class="hr-text my-2">ATAU</div>
        <x-ui.form-input 
            name="image_url" 
            label="Image URL (Opsional)" 
            value="{{ $slideshow->image_url && filter_var($slideshow->image_url, FILTER_VALIDATE_URL) ? $slideshow->image_url : '' }}"
            placeholder="https://example.com/image.jpg"
            help="Gunakan URL jika gambar tidak ingin diunggah secara lokal."
        />
    </div>

    <x-ui.form-input 
        name="title" 
        label="Judul (Opsional)" 
        value="{{ $slideshow->title }}"
        placeholder="Judul Slideshow"
    />

    <x-ui.form-textarea 
        name="caption" 
        label="Keterangan (Opsional)"
        placeholder="Deskripsi singkat slideshow"
    >{{ $slideshow->caption }}</x-ui.form-textarea>

    <div class="mb-3">
        <x-ui.form-input 
            name="link" 
            label="Link Redirect (Opsional)" 
            value="{{ $slideshow->link }}"
            placeholder="https://example.com"
        />
    </div>

    <div class="mt-3">
        <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" {{ $slideshow->exists ? ($slideshow->is_active ? 'checked' : '') : 'checked' }}>
            <span class="form-check-label">Aktifkan Slideshow</span>
        </label>
    </div>
</x-ui.form-modal>

<script>
    if (window.initFilePond) {
        window.initFilePond();
    }
</script>
