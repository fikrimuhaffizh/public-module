<x-ui.form-modal
    :title="$section->exists ? 'Edit ' . Models\Section::typeLabel($type) : 'Tambah ' . Models\Section::typeLabel($type)"
    :route="$section->exists ? route('cms.section.update', $section) : route('cms.section.store')"
    :method="$section->exists ? 'PUT' : 'POST'"
    size="modal-xl"
    enctype="multipart/form-data"
>
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="row g-4">
        {{-- LEFT: General Info --}}
        <div class="col-md-6">
            <h6 class="text-uppercase text-muted mb-3"><i class="ti ti-info-circle me-1"></i>Informasi Umum</h6>

            <x-ui.form-input name="title" label="Judul" :value="$section->title" required />

            @if(in_array($type, ['product', 'pricing']))
                <x-ui.form-input name="slug" label="Slug" :value="$section->slug" placeholder="Auto dari judul jika kosong" />
            @endif

            <x-ui.form-input name="icon" label="Icon Class" :value="$section->icon"
                placeholder="ti ti-sparkles atau fa fa-star"
                help="Tabler, FontAwesome, atau Iconify class." />

            @if($section->icon)
                <div class="mb-3">
                    <span class="avatar bg-primary-lt me-2"><i class="{{ $section->icon }}"></i></span>
                    <small class="text-muted">Icon saat ini</small>
                </div>
            @endif

            {{-- Type-specific fields --}}
            @if($type === 'product')
                <x-ui.form-input name="short_description" label="Deskripsi Singkat" :value="$section->getSetting('short_description')" />
                <x-ui.form-input name="demo_url" label="Link Demo" :value="$section->getSetting('demo_url')" placeholder="https://..." />
            @endif

            @if($type === 'client')
                <x-ui.form-input name="website" label="Website" :value="$section->getSetting('website')" placeholder="https://..." />
            @endif

            @if($type === 'partner')
                <x-ui.form-input name="category" label="Kategori" :value="$section->getSetting('category')" placeholder="Teknologi, Pendidikan, dll." />
                <x-ui.form-input name="website_url" label="Website" :value="$section->getSetting('website_url')" placeholder="https://..." />
            @endif

            @if($type === 'testimonial')
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.form-input name="position" label="Jabatan" :value="$section->getSetting('position')" />
                    </div>
                    <div class="col-md-6">
                        <x-ui.form-input name="organization" label="Organisasi" :value="$section->getSetting('organization')" />
                    </div>
                </div>
                <x-ui.form-input name="rating" label="Rating (1-5)" :value="$section->getSetting('rating')" type="number" />
            @endif

            @if($type === 'statistic')
                <x-ui.form-input name="value" label="Nilai" :value="$section->getSetting('value')" placeholder="1000+, 95%, dll." required />
            @endif

            @if($type === 'slideshow')
                <x-ui.form-input name="link" label="Link" :value="$section->getSetting('link')" placeholder="https://..." />
                <x-ui.form-input name="external_image_url" label="URL Gambar Eksternal" :value="$section->getSetting('external_image_url')" placeholder="https://... (opsional, upload juga bisa)" />
            @endif

            @if($type === 'faq')
                <x-ui.form-input name="category" label="Kategori" :value="$section->getSetting('category')" placeholder="Pendaftaran, Akademik, dll." />
            @endif

            @if($type === 'pricing')
                <div class="row">
                    <div class="col-md-6">
                        <x-ui.form-input name="price" label="Harga" :value="$section->getSetting('price')" placeholder="99.000" />
                    </div>
                    <div class="col-md-6">
                        <x-ui.form-input name="period" label="Periode" :value="$section->getSetting('period')" placeholder="bulan, tahun, sekali" />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Daftar Fitur</label>
                    <div id="features-list">
                        @php $features = $section->getSetting('features', []); @endphp
                        @if(is_array($features) && count($features))
                            @foreach($features as $feature)
                                <div class="input-group mb-2">
                                    <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="mis. 100 MB storage">
                                    <button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" name="features[]" class="form-control" placeholder="mis. 100 MB storage">
                                <button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-ghost-primary btn-sm" id="add-feature"><i class="ti ti-plus"></i> Tambah Fitur</button>
                </div>
                <label class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="highlight" value="1" {{ $section->getSetting('highlight') ? 'checked' : '' }}>
                    <span class="form-check-label">Paket Unggulan</span>
                </label>
            @endif
        </div>

        {{-- RIGHT: Description + Upload --}}
        <div class="col-md-6">
            <h6 class="text-uppercase text-muted mb-3"><i class="ti ti-pencil me-1"></i>Deskripsi & Media</h6>

            @if(in_array($type, ['feature', 'product']))
                <x-ui.form-textarea name="description" label="Deskripsi Lengkap" :value="$section->description" type="editor" rows="8" />
            @elseif($type === 'testimonial')
                <x-ui.form-textarea name="description" label="Kutipan (Quote)" :value="$section->description" rows="5" />
            @elseif(in_array($type, ['faq', 'slideshow']))
                <x-ui.form-textarea name="description" label="Deskripsi" :value="$section->description" type="editor" rows="6" />
            @elseif($type === 'pricing')
                <x-ui.form-textarea name="description" label="Deskripsi Singkat" :value="$section->description" rows="3" />
            @endif

            {{-- Media upload --}}
            @php
                $mediaField = Models\Section::MEDIA_COLLECTIONS[$type] ?? null;
                $mediaLabel = match($type) {
                    'feature', 'product', 'pricing', 'faq', 'statistic' => 'Gambar/Cover',
                    'client', 'partner'  => 'Logo',
                    'testimonial'        => 'Foto',
                    'slideshow'          => 'Gambar Slide',
                    default              => 'Gambar',
                };
            @endphp

            @if($mediaField)
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ $mediaLabel }}</label>
                    <input type="file" name="{{ $mediaField }}" class="form-control"
                           accept="image/png,image/jpeg,image/webp">
                    @if($section->image_url)
                        <div class="mt-2">
                            <img src="{{ $section->image_url }}" alt="Preview" class="rounded border" style="max-height:150px">
                     
                           <small class="text-muted d-block mt-1">Gambar saat ini</small>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-ui.form-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-feature')?.addEventListener('click', function() {
        const list = document.getElementById('features-list');
        const row = document.createElement('div');
        row.className = 'input-group mb-2';
        row.innerHTML = '<input type="text" name="features[]" class="form-control" placeholder="mis. 100 MB storage"><button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>';
        list.appendChild(row);
    });
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const row = e.target.closest('.input-group');
            const list = document.getElementById('features-list');
            if (list.children.length > 1) row.remove();
            else row.querySelector('input').value = '';
        }
    });
});
</script>
