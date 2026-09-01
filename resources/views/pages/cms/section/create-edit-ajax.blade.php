<div class="section-edit-form">
    <form id="section-edit-form" action="{{ route('cms.landing.update-section', $section) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        @if(!empty($sectionMeta['variants']) && count($sectionMeta['variants']) > 1)
        <div class="mb-3">
            <label class="form-label">Style / Variant</label>
            <select name="variant" class="form-select">
                @foreach($sectionMeta['variants'] as $variantKey => $variantLabel)
                    <option value="{{ $variantKey }}" {{ $section->variant === $variantKey ? 'selected' : '' }}>{{ $variantLabel }}</option>
                @endforeach
            </select>
            <div class="form-hint">Pilih gaya tampilan section ini di landing page.</div>
        </div>
        @else
        <input type="hidden" name="variant" value="{{ $section->variant }}">
        @endif
        
        @if(!in_array($section->section_key, ['navbar', 'footer']))
        <div class="row g-2">
            <div class="col-md-6">
                <x-ui.form-input name="pre_title" label="Pre-Judul" value="{{ old('pre_title', $section->pre_title ?? '') }}" />
            </div>
            <div class="col-md-6">
                <x-ui.form-input name="post_title" label="Post-Judul" value="{{ old('post_title', $section->post_title ?? '') }}" />
            </div>
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <x-ui.form-input name="title" label="Judul Section" value="{{ old('title', $section->title ?? '') }}" />
            </div>
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <x-ui.form-input name="subtitle" label="Subjudul Section" value="{{ old('subtitle', $section->subtitle ?? '') }}" />
            </div>
        </div>

        <div class="mb-3 mt-2">
            @php
                $heroKeys = ['hero'];
                $isHero = in_array($section->section_key, $heroKeys);
                $imgLabel = $isHero ? 'Gambar Hero (sisi visual)' : 'Gambar Section';
                $imgHelp = $isHero
                    ? 'Gambar yang tampil di samping teks (Mode 1: kanan, Mode 5: kiri). Untuk mode center/aurora/gradient, gambar tampil di bawah teks.'
                    : 'Gambar untuk section ini. Maks 4MB.';
            @endphp
            <label class="form-label fw-bold">{{ $imgLabel }}</label>
            @if($section->image_url)
                <div class="mb-2 border rounded p-2 text-center bg-light">
                    <img src="{{ $section->image_url }}" alt="Preview" style="max-height: 120px; max-width: 100%; border-radius: 8px;">
                </div>
            @endif
            <x-ui.form-input type="file" name="section_image" accept="image/png,image/jpeg,image/webp"
                help="{{ $imgHelp }}" />
            <div class="mt-2">
                <label class="form-label" style="font-size: 12px; color: #64748b;">atau tempel URL gambar (Unsplash, dll)</label>
                <input type="url" name="section_image_url" class="form-control" value="{{ old('section_image_url') }}"
                    placeholder="https://images.unsplash.com/photo-..."
                    style="font-size: 13px;">
                <small class="text-muted">Cari gambar gratis: Unsplash, Pexels, Google Images</small>
            </div>
        </div>

        <div class="row g-2 mt-1">
            <div class="col-md-6">
                <label class="form-label">Rata Teks</label>
                <select name="settings[text_align]" class="form-select">
                    @php $textAlign = data_get($section->settings, 'text_align', 'left'); @endphp
                    <option value="left" {{ $textAlign === 'left' ? 'selected' : '' }}>Kiri</option>
                    <option value="center" {{ $textAlign === 'center' ? 'selected' : '' }}>Tengah</option>
                    <option value="right" {{ $textAlign === 'right' ? 'selected' : '' }}>Kanan</option>
                </select>
            </div>
            <div class="col-md-6">
                @include('components.ui.form-input', [
                    'name' => 'limit_data', 'type' => 'number', 'label' => 'Limit Data',
                    'value' => old('limit_data', $section->limit_data ?? ''),
                    'help' => 'Jumlah item yang ditampilkan (1-50).'
                ])
            </div>
        </div>

        @if($section->section_key === 'price')
        <div class="mb-3 mt-2">
            <label class="form-label">Daftar Paket Harga (JSON)</label>
            <textarea name="settings[packages]" class="form-control" rows="10" spellcheck="false"
                style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px;"
                placeholder='[{ "name": "Starter", "description": "…", "price": "99.000", "period": "bulan", "features": ["…"], "highlight": false, "ctaText": "Pilih", "ctaLink": "#kontak" }]'>{{ old('settings.packages', json_encode(data_get($section->settings, 'packages', []), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}</textarea>
            <div class="form-hint">
                Array JSON: <code>name</code>, <code>description</code>, <code>price</code>, <code>period</code>,
                <code>features[]</code>, <code>highlight</code> (paket unggulan), <code>ctaText</code>, <code>ctaLink</code>.
                Kosongkan untuk memakai paket contoh.
            </div>
        </div>
        @endif
        
        @else
            @php
                $logoCollection = $section->section_key === 'navbar' ? 'logo_navbar' : 'logo_footer';
            @endphp
            <input type="hidden" name="pre_title" value="{{ $section->pre_title }}">
            <input type="hidden" name="post_title" value="{{ $section->post_title }}">
            <input type="hidden" name="title" value="{{ $section->title }}">
            <input type="hidden" name="subtitle" value="{{ $section->subtitle }}">
            <input type="hidden" name="limit_data" value="{{ $section->limit_data }}">
            @php $textAlign = data_get($section->settings, 'text_align', 'left'); @endphp
            <input type="hidden" name="settings[text_align]" value="{{ $textAlign }}">
            
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0 fw-bold">Logo {{ $section->section_key === 'navbar' ? 'Navbar' : 'Footer' }}</label>
                    @if($logoUrl)
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-danger logo-delete-btn" data-collection="{{ $logoCollection }}" title="Hapus Logo">
                            <i class="ti ti-trash"></i>
                        </button>
                    @endif
                </div>
                
                @if($logoUrl)
                    <div class="mb-2 border rounded p-2 text-center bg-light">
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 48px; max-width: 100%;">
                    </div>
                @endif
                
                <x-ui.form-input
                    type="file"
                    name="{{ $logoCollection }}"
                    accept="image/jpeg,image/png,image/webp,image/svg+xml"
                    help="Upload logo untuk {{ $section->section_key === 'navbar' ? 'header/navbar' : 'footer' }} landing page. Format: PNG, SVG, WebP. Maks 2MB." />
            </div>

            <div class="alert alert-info d-flex align-items-center gap-2 mt-3" role="alert">
                <i class="ti ti-info-circle fs-3"></i>
                <div>
                    @if($section->section_key === 'navbar')
                        Navbar menampilkan <strong>Logo</strong> dan <strong>Menu Navigasi</strong>. Kelola menu di <a href="{{ route('cms.menu.index') }}" class="alert-link" target="_blank">Menu Publik</a>.
 
            @if($section->section_key === 'navbar')
            <div class="mb-3 mt-3 p-3 border rounded bg-white">
                <div class="fw-bold mb-2">Info Bar Atas (di atas navbar)</div>
                <input type="hidden" name="settings[show_topbar]" value="0">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="showTopbar" name="settings[show_topbar]" value="1" {{ data_get($section->settings, 'show_topbar') ? 'checked' : '' }}>
                    <label class="form-check-label" for="showTopbar">Tampilkan info bar atas (alamat, jam, WhatsApp)</label>
                </div>
                <x-ui.form-input name="settings[topbar_hours]" label="Jam Operasional (info bar atas)"
                    value="{{ old('settings.topbar_hours', data_get($section->settings, 'topbar_hours', '')) }}"
                    placeholder="Senin–Jumat 08.00–17.00"
                    help="Kosongkan bila tidak ingin menampilkan jam. Alamat & WhatsApp diambil dari Media Sosial." />
            </div>
            @endif
                   @else
                        Footer menampilkan info situs, navigasi, dan kontak dari <a href="{{ route('cms.media-social.edit') }}" class="alert-link" target="_blank">Media Sosial</a>.
                    @endif
                </div>
            </div>
        @endif
    </form>
</div>
<script>
window.getSectionForm = function() {
    return document.getElementById('section-edit-form');
};
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.logo-delete-btn');
        if (!btn) return;
        const url = '{{ route("cms.landing.delete-logo", "__COL__") }}'.replace('__COL__', btn.dataset.collection);
        axios.delete(url, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { window.showSuccessMessage('Logo berhasil dihapus.'); setTimeout(() => window.location.reload(), 800); })
            .catch(() => window.showErrorMessage('Gagal menghapus logo.'));
    });
});
</script>
