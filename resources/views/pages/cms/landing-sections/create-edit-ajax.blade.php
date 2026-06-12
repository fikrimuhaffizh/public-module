<div class="section-edit-form">
    <form id="section-edit-form" action="{{ route('public.cms.landing.section.update', $section) }}" method="POST">
        @csrf
        @method('PUT')
        
        @if(($isCustomTemplate ?? false) && count($sectionMeta['variants'] ?? []) > 0)
        <div class="mb-3">
            <label class="form-label fw-bold mb-2">Desain Variant</label>
            <div class="row g-2">
                @foreach($sectionMeta['variants'] as $variantKey => $variantLabel)
                    <div class="col-6">
                        <label class="form-selectgroup-item cursor-pointer d-block">
                            <input type="radio" name="variant" value="{{ $variantKey }}" class="form-selectgroup-input" {{ $section->variant === $variantKey ? 'checked' : '' }}>
                            <span class="form-selectgroup-label d-flex align-items-center gap-2 py-2">
                                <span class="form-selectgroup-check"></span>
                                <span>
                                    <span class="fw-medium">{{ $variantLabel }}</span>
                                    <span class="text-muted small ms-1">({{ $variantKey }})</span>
                                </span>
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        @else
            <input type="hidden" name="variant" value="{{ $section->variant }}">
        @endif
        
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
        </div>
        
        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <x-ui.form-input name="limit_data" type="number" label="Limit Data" :value="$section->limit_data" help="Jumlah item yang ditampilkan (1-50)." />
            </div>
        </div>
    </form>
</div>
<script>
// Form is submitted from panel header button (see landing/index.blade.php)
// Expose a helper so the panel header can trigger submission
window.getSectionForm = function() {
    return document.getElementById('section-edit-form');
};
</script>
