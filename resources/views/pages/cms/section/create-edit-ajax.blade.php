<div class="section-edit-form">
    <form id="section-edit-form" action="{{ route('cms.section.update', $section) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="variant" value="{{ $section->variant }}">
        
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
        
        @else
            @php
                $tenant = \App\Models\Account\Tenant::find(sys_tenant_id());
                $logoCollection = $section->section_key === 'navbar' ? 'logo_navbar' : 'logo_footer';
                $logoUrl = $section->section_key === 'navbar' ? $tenant->logoNavbarUrl() : $tenant->logoFooterUrl();
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
                    @else
                        Footer menampilkan info situs, navigasi, dan kontak dari <a href="{{ route('cms.settings.edit') }}" class="alert-link" target="_blank">Pengaturan</a>.
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
        const url = '{{ route("cms.section.delete-logo", "__COL__") }}'.replace('__COL__', btn.dataset.collection);
        axios.delete(url, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => { window.showSuccessMessage('Logo berhasil dihapus.'); setTimeout(() => window.location.reload(), 800); })
            .catch(() => window.showErrorMessage('Gagal menghapus logo.'));
    });
});
</script>
