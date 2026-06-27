<div class="section-edit-form">
    <form id="section-edit-form" action="{{ route('cms.landing.section.update', $section) }}" method="POST">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="variant" value="{{ $section->variant }}">
        
        @if(!in_array($section->section_key, ['navbar', 'footer']))
        <div class="row g-2">
            <div class="col-md-6">
                <x-ui.form-input name="pre_title" label="Pre-Judul" :value="$section->pre_title" />
            </div>
            <div class="col-md-6">
                <x-ui.form-input name="post_title" label="Post-Judul" :value="$section->post_title" />
            </div>
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <x-ui.form-input name="title" label="Judul Section" :value="$section->title" />
            </div>
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <x-ui.form-input name="subtitle" label="Subjudul Section" :value="$section->subtitle" />
            </div>
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-6">
                <label class="form-label">Rata Teks</label>
                <select name="settings[text_align]" class="form-select">
                    @php($textAlign = data_get($section->settings, 'text_align', 'left'))
                    <option value="left" @selected($textAlign === 'left')>Kiri</option>
                    <option value="center" @selected($textAlign === 'center')>Tengah</option>
                    <option value="right" @selected($textAlign === 'right')>Kanan</option>
                </select>
            </div>
            <div class="col-md-6">
                <x-ui.form-input name="limit_data" type="number" label="Limit Data" :value="$section->limit_data" help="Jumlah item yang ditampilkan (1-50)." />
            </div>
        </div>
        
        @else
            <input type="hidden" name="pre_title" value="{{ $section->pre_title }}">
            <input type="hidden" name="post_title" value="{{ $section->post_title }}">
            <input type="hidden" name="title" value="{{ $section->title }}">
            <input type="hidden" name="subtitle" value="{{ $section->subtitle }}">
            <input type="hidden" name="limit_data" value="{{ $section->limit_data }}">
            @php($textAlign = data_get($section->settings, 'text_align', 'left'))
            <input type="hidden" name="settings[text_align]" value="{{ $textAlign }}">
            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="ti ti-info-circle fs-3"></i>
                <div>
                    @if($section->section_key === 'navbar')
                        Header hanya menampilkan <strong>Logo</strong> dan <strong>Menu Navigasi</strong>. Kelola item menu di halaman <a href="{{ route('cms.public-menu.index') }}" class="alert-link" target="_blank">Menu Publik</a>.
                    @else
                        Footer menampilkan informasi situs, navigasi, dan kontak secara otomatis dari pengaturan Configuration.
                    @endif
                </div>
            </div>
        @endif
    </form>
</div>
<script>
// Form is submitted from panel header button (see landing/index.blade.php)
// Expose a helper so the panel header can trigger submission
window.getSectionForm = function() {
    return document.getElementById('section-edit-form');
};
</script>
