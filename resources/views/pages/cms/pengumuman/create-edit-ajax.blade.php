<x-ui.form-modal
    id_form="{{ $pengumuman->exists ? 'editPengumumanForm' : 'createPengumumanForm' }}"
    title="{{ ($pengumuman->exists ? 'Edit ' : 'Buat ') . ucfirst($type) }}"
    route="{{ $pengumuman->exists ? route('public.cms.'.$type.'.update', $pengumuman->encrypted_pengumuman_id) : route('public.cms.'.$type.'.store') }}"
    method="{{ $pengumuman->exists ? 'PUT' : 'POST' }}"
    enctype="multipart/form-data"
>
    <input type="hidden" name="jenis" value="{{ $type }}">

    <div class="mb-3">
        <x-ui.form-input
            name="judul"
            label="Judul"
            value="{{ old('judul', $pengumuman->judul) }}"
            placeholder="Masukkan judul..."
            required
        />
    </div>

    <div class="mb-3">
        <x-ui.form-textarea
            name="isi"
            label="Konten"
            type="editor"
            id="{{ $pengumuman->exists ? 'isi_edit_modal' : 'isi_modal' }}"
            rows="10"
            :value="old('isi', $pengumuman->isi)"
        />
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <div class="form-selectgroup">
            <label class="form-selectgroup-item">
                <input type="radio" name="is_published" value="1" class="form-selectgroup-input" {{ old('is_published', $pengumuman->is_published) ? 'checked' : '' }}>
                <span class="form-selectgroup-label text-success">
                    <i class="ti ti-check me-1"></i> Published
                </span>
            </label>
            <label class="form-selectgroup-item">
                <input type="radio" name="is_published" value="0" class="form-selectgroup-input" {{ !old('is_published', $pengumuman->is_published) ? 'checked' : '' }}>
                <span class="form-selectgroup-label text-warning">
                    <i class="ti ti-file me-1"></i> Draft
                </span>
            </label>
        </div>
    </div>

    <div class="mb-3">
        <x-ui.form-input
            name="cover"
            type="file"
            label="Gambar Utama (Cover)"
            accept="image/*"
            class="filepond-input"
            help="Maksimal 5MB. Format: JPG, PNG, WEBP."
        />
        @if($pengumuman->cover_url)
            <div class="mt-2 text-center">
                <img src="{{ $pengumuman->cover_url }}" class="rounded shadow-sm" style="max-height: 80px;">
                <p class="text-muted small">Current Cover</p>
            </div>
        @endif
    </div>

    <div class="mb-3">
        <x-ui.form-input
            name="attachments[]"
            type="file"
            label="File Pendukung (Lampiran)"
            multiple
            class="filepond-input"
            help="Maksimal 10MB per file. Bisa upload banyak file sekaligus."
        />
    </div>
</x-ui.form-modal>

@push('js')
<script>
    if (window.loadHugeRTE) {
        window.loadHugeRTE('#{{ $pengumuman->exists ? "isi_edit_modal" : "isi_modal" }}', { 
            height: 400,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }
</script>
@endpush
