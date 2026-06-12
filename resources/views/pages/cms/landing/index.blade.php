@extends('layouts.tabler.app')

@section('title', 'Landing Page')

@section('header')
<x-ui.page-header title="Landing Page" pretitle="CMS">
    <x-slot:actions>
        <a href="{{ route('public.preview', ['template' => $template]) }}" target="_blank" class="btn btn-outline-primary">
            <i class="ti ti-eye me-1"></i>Pratinjau
        </a>
        @can('public.cms.update')
            <x-ui.button type="submit" form="landing-template-form" text="Simpan Template" class="d-none d-sm-inline-block" />
        @endcan
        @can('public.cms.settings.update')
            <x-ui.button type="submit" form="landing-settings-form" text="Simpan Pengaturan" class="d-none d-sm-inline-block" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<x-ui.card>
    <x-ui.card-body>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a href="#tab-sections" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                    <i class="ti ti-layout-2 me-2"></i>Sections
                </a>
            </li>
            <li class="nav-item">
                <a href="#tab-configuration" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                    <i class="ti ti-settings me-2"></i>Configuration
                </a>
            </li>
            <li class="nav-item">
                <a href="#tab-template" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab">
                    <i class="ti ti-palette me-2"></i>Template
                </a>
            </li>
        </ul>
        <div class="tab-content mt-4">
            <!-- Sections Tab -->
            <div id="tab-sections" class="tab-pane active show" role="tabpanel">
                @php
    // Helper: build compact content preview string from section titles
    $contentPreview = function($section) {
        $parts = array_filter([
            $section->pre_title ? '[' . $section->pre_title . ']' : null,
            $section->title ? '« ' . $section->title . ' »' : null,
            $section->post_title ? '[' . $section->post_title . ']' : null,
        ]);
        $line1 = implode(' ', $parts);
        $line2 = $section->subtitle ?: '';
        return ['titles' => $line1, 'subtitle' => $line2];
    };

                    $areaOrder = ['top', 'middle', 'bottom'];
                    $sectionIcons = [
                        'navbar' => 'navbar',
                        'hero' => 'rocket',
                        'products' => 'apps',
                        'product' => 'apps',
                        'stats' => 'chart-bar',
                        'statistic' => 'chart-bar',
                        'features' => 'star',
                        'feature' => 'star',
                        'testimonials' => 'message-circle-heart',
                        'testimonial' => 'message-circle-heart',
                        'clients' => 'brand-tailwind',
                        'client' => 'brand-tailwind',
                        'faq' => 'help-circle',
                        'announcement' => 'announcement',
                        'pengumuman' => 'announcement',
                        'cta' => 'alert-circle',
                        'footer' => 'layout-bottombar'
                    ];
                    $allSections = [];
                    foreach($areaOrder as $area) {
                        if(isset($sections[$area])) {
                            foreach($sections[$area] as $section) {
                                $section->area = $area;
                                $allSections[] = $section;
                            }
                        }
                    }
                @endphp
                <div class="row">
                    <!-- Left: Section List -->
                    <div class="col-12 col-lg-5 mb-4 mb-lg-0">
                        <x-ui.card class="h-100" style="overflow: visible">
                            <x-ui.card-header title="Daftar Section" />
                            <x-ui.card-body>
                                <!-- Top Area -->
                                @if(isset($sections['top']) && count($sections['top']) > 0)
                                    <div class="mb-4">
                                        <h5 class="text-muted mb-2">Top Area (Fixed)</h5>
                                        <div class="section-list">
                                            @foreach($sections['top'] as $section)
                                                @php
                                                    $icon = $sectionIcons[$section->section_key] ?? 'layout';
                                                @endphp
                                                <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-section='@json($section)' data-edit-url="{{ route('public.cms.landing.section.edit', $section) }}" data-manage-url="{{ isset($registry[$section->section_key]['manage_data_route']) ? ($registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '') : '' }}" data-toggle-url="{{ route('public.cms.landing.section.toggle', $section) }}">
                                                    <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                                    <div class="flex-fill overflow-hidden">
                                                        <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                                        @php $preview = $contentPreview($section); @endphp
                                                        @if($preview['titles'])<div class="text-truncate" style="font-size: 0.72rem; color: #495057; line-height: 1.3;">{{ $preview['titles'] }}</div>@endif
                                                        @if($preview['subtitle'])<small class="text-muted text-truncate d-block" style="font-size: 0.68rem; line-height: 1.2;">{{ $preview['subtitle'] }}</small>@endif
                                                    </div>
                                                    <span class="badge {{ $section->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                        {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <!-- Middle Area (Drag & Drop) -->
                                @if(isset($sections['middle']) && count($sections['middle']) > 0)
                                    <div class="mb-4">
                                        <h5 class="text-muted mb-2">Middle Area (Drag to Reorder)</h5>
                                        <div id="middle-sections" class="section-list">
                                            @foreach($sections['middle'] as $section)
                                                @php
                                                    $icon = $sectionIcons[$section->section_key] ?? 'layout';
                                                @endphp
                                                <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-id="{{ $section->encrypted_landing_section_id }}" data-section='@json($section)' data-edit-url="{{ route('public.cms.landing.section.edit', $section) }}" data-manage-url="{{ $registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '' }}" data-toggle-url="{{ route('public.cms.landing.section.toggle', $section) }}">
                                                    <div class="drag-handle"><i class="ti ti-grip-vertical"></i></div>
                                                    <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                                    <div class="flex-fill overflow-hidden">
                                                        <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                                        @php $preview = $contentPreview($section); @endphp
                                                        @if($preview['titles'])<div class="text-truncate" style="font-size: 0.72rem; color: #495057; line-height: 1.3;">{{ $preview['titles'] }}</div>@endif
                                                        @if($preview['subtitle'])<small class="text-muted text-truncate d-block" style="font-size: 0.68rem; line-height: 1.2;">{{ $preview['subtitle'] }}</small>@endif
                                                    </div>
                                                    <span class="badge {{ $section->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                        {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <!-- Bottom Area -->
                                @if(isset($sections['bottom']) && count($sections['bottom']) > 0)
                                    <div class="mb-0">
                                        <h5 class="text-muted mb-2">Bottom Area (Fixed)</h5>
                                        <div class="section-list">
                                            @foreach($sections['bottom'] as $section)
                                                @php
                                                    $icon = $sectionIcons[$section->section_key] ?? 'layout';
                                                @endphp
                                                <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-section='@json($section)' data-edit-url="{{ route('public.cms.landing.section.edit', $section) }}" data-manage-url="{{ isset($registry[$section->section_key]['manage_data_route']) ? ($registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '') : '' }}" data-toggle-url="{{ route('public.cms.landing.section.toggle', $section) }}">
                                                    <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                                    <div class="flex-fill overflow-hidden">
                                                        <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                                        @php $preview = $contentPreview($section); @endphp
                                                        @if($preview['titles'])<div class="text-truncate" style="font-size: 0.72rem; color: #495057; line-height: 1.3;">{{ $preview['titles'] }}</div>@endif
                                                        @if($preview['subtitle'])<small class="text-muted text-truncate d-block" style="font-size: 0.68rem; line-height: 1.2;">{{ $preview['subtitle'] }}</small>@endif
                                                    </div>
                                                    <span class="badge {{ $section->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                        {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </x-ui.card-body>
                        </x-ui.card>
                    </div>
                    <!-- Right: Section Editor Panel -->
                    <div class="col-12 col-lg-7">
                        <x-ui.card id="section-editor-panel" class="h-100">
                            <x-ui.card-header title="{!! '<span id=\'section-editor-title\'>Pilih Section</span>' !!}">
                                <x-slot:actions>
                                    <div class="d-flex align-items-center gap-2" id="panel-actions">
                                        <a id="manage-data-btn" href="#" class="btn btn-sm btn-outline-primary" style="display: none;">
                                            <i class="ti ti-database-edit me-1"></i>Kelola Data
                                        </a>
                                        <button id="save-section-btn" class="btn btn-sm btn-primary" style="display: none;">
                                            <i class="ti ti-device-floppy me-1"></i>Simpan
                                        </button>
                                        <button id="close-editor-btn" class="btn btn-sm btn-icon btn-ghost-secondary" style="display: none;">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </x-slot:actions>
                            </x-ui.card-header>
                            <x-ui.card-body id="section-editor-body">
                                <div class="text-center text-muted py-8">
                                    <i class="ti ti-layout-2 fs-2 mb-2 d-block"></i>
                                    <p class="mb-0">Klik salah satu section di daftar sebelah kiri untuk mengeditnya</p>
                                </div>
                            </x-ui.card-body>
                        </x-ui.card>
                    </div>
                </div>
            </div>
            <!-- Configuration Tab -->
            <div id="tab-configuration" class="tab-pane" role="tabpanel">
                <form id="landing-settings-form" method="POST" action="{{ route('public.cms.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row row-cards">
                        <div class="col-lg-8">
                            <x-ui.card class="mb-3">
                                <x-ui.card-header><h3 class="card-title">Informasi Situs</h3></x-ui.card-header>
                                <x-ui.card-body>
                                    <x-ui.form-input name="site_title" label="Judul Situs" :value="old('site_title', $settings->site_title)" />
                                    <x-ui.form-input name="site_description" type="textarea" label="Deskripsi Situs" :value="old('site_description', $settings->site_description)" rows="3" />
                                </x-ui.card-body>
                            </x-ui.card>
                            <x-ui.card class="mb-3">
                                <x-ui.card-header><h3 class="card-title">SEO</h3></x-ui.card-header>
                                <x-ui.card-body>
                                    <x-ui.form-input name="meta_title" label="Meta Title" :value="old('meta_title', $settings->meta_title)" />
                                    <x-ui.form-input name="meta_description" type="textarea" label="Meta Description" :value="old('meta_description', $settings->meta_description)" rows="2" />
                                    <x-ui.form-input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $settings->meta_keywords)" placeholder="kampus, digital, pemutu" />
                                </x-ui.card-body>
                            </x-ui.card>
                            <x-ui.card class="mb-3">
                                <x-ui.card-header><h3 class="card-title">Kontak</h3></x-ui.card-header>
                                <x-ui.card-body>
                                    <div class="row">
                                        <div class="col-md-6"><x-ui.form-input name="contact_email" label="Email" :value="old('contact_email', $settings->contact_email)" /></div>
                                        <div class="col-md-6"><x-ui.form-input name="contact_phone" label="Telepon" :value="old('contact_phone', $settings->contact_phone)" /></div>
                                        <div class="col-md-6"><x-ui.form-input name="whatsapp" label="WhatsApp" :value="old('whatsapp', $settings->whatsapp)" /></div>
                                    </div>
                                    <x-ui.form-input name="address" type="textarea" label="Alamat" :value="old('address', $settings->address)" rows="2" />
                                </x-ui.card-body>
                            </x-ui.card>
                            <x-ui.card>
                                <x-ui.card-header><h3 class="card-title">Media Sosial</h3></x-ui.card-header>
                                <x-ui.card-body>
                                    <div class="row">
                                        <div class="col-md-6"><x-ui.form-input name="facebook_url" label="Facebook" :value="old('facebook_url', $settings->facebook_url)" /></div>
                                        <div class="col-md-6"><x-ui.form-input name="instagram_url" label="Instagram" :value="old('instagram_url', $settings->instagram_url)" /></div>
                                        <div class="col-md-6"><x-ui.form-input name="linkedin_url" label="LinkedIn" :value="old('linkedin_url', $settings->linkedin_url)" /></div>
                                        <div class="col-md-6"><x-ui.form-input name="youtube_url" label="YouTube" :value="old('youtube_url', $settings->youtube_url)" /></div>
                                    </div>
                                </x-ui.card-body>
                            </x-ui.card>
                        </div>
                        <div class="col-lg-4">
                            <x-ui.card>
                                <x-ui.card-header><h3 class="card-title">Branding</h3></x-ui.card-header>
                                <x-ui.card-body>
                                    <x-ui.form-input name="logo" type="file" label="Logo" accept="image/png,image/jpeg,image/webp" />
                                    @if($settings->logo_url)
                                        <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded border mb-3" style="max-height:80px">
                                    @endif
                                    <x-ui.form-input name="favicon" type="file" label="Favicon" accept="image/png,image/jpeg,image/webp,image/x-icon" />
                                    @if($settings->favicon_url)
                                        <img src="{{ $settings->favicon_url }}" alt="Favicon" class="rounded border" style="width:32px;height:32px">
                                    @endif
                                </x-ui.card-body>
                            </x-ui.card>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Template Tab -->
            <div id="tab-template" class="tab-pane" role="tabpanel">
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="ti ti-info-circle fs-2 me-2"></i>
                    <div>Pilih salah satu template, lalu klik <strong>Simpan Template</strong>. Pratinjau tidak mengubah template yang sedang digunakan.</div>
                </div>
                <form id="landing-template-form" method="POST" action="{{ route('public.cms.landing.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row row-cards">
                        @foreach($templates as $templateOption)
                            @php
                                $details = match($templateOption) {
                                    'institutional' => ['Institusional', 'Tampilan terpercaya dan formal untuk profil institusi.', 'building-bank'],
                                    'modern' => ['Modern', 'Visual progresif dengan pengalaman digital yang dinamis.', 'sparkles'],
                                    'editorial' => ['Editorial', 'Berorientasi konten dengan tipografi dan berita yang kuat.', 'news'],
                                    'corporate' => ['Corporate', 'Tampilan mewah dan elegan untuk institusi serta mitra korporat.', 'building-skyscraper'],
                                    'launch' => ['Launch UI', 'Desain segar dengan hero, fitur, produk, statistik, dan CTA yang dapat dikelola penuh.', 'rocket'],
                                    'custom' => ['Custom', 'Template sepenuhnya dapat dikustomisasi dengan drag & drop sections.', 'settings'],
                                };
                            @endphp
                            @php($isSelected = old('landing_template', $template) === $templateOption)
                            <div class="col-md-6 col-xl-3">
                                <label class="card card-link card-link-pop h-100 cursor-pointer {{ $isSelected ? 'border-primary border-2' : '' }}">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-4">
                                            <span class="avatar avatar-lg bg-primary-lt text-primary"><i class="ti ti-{{ $details[2] }} fs-1"></i></span>
                                            <input class="form-check-input" type="radio" name="landing_template" value="{{ $templateOption }}" @checked($isSelected) @cannot('public.cms.update') disabled @endcannot>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <h3 class="card-title mb-0">{{ $details[0] }}</h3>
                                            @if($isSelected)
                                                <span class="badge bg-primary-lt">Sedang digunakan</span>
                                            @endif
                                        </div>
                                        <p class="text-muted mb-3">{{ $details[1] }}</p>
                                        <a href="{{ route('public.preview', ['template' => $templateOption]) }}" target="_blank" class="text-primary" onclick="event.stopPropagation()">Lihat pratinjau <i class="ti ti-arrow-up-right ms-1"></i></a>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </x-ui.card-body>
</x-ui.card>
@endsection

@push('styles')
<style>
.section-list {
    min-height: 100px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 4px;
    padding: 10px;
}

.section-item {
    cursor: pointer;
    user-select: none;
    margin-bottom: 6px;
    background: white;
    border: 1px dashed #dee2e6;
    padding: 6px 10px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    width: 100%;
    transition: all 0.2s;
}

.section-item.selected {
    border-style: solid;
    border-color: #206bc4;
    background: #f1f7ff;
}

.section-item .drag-handle {
    color: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    cursor: grab;
}

.section-item i.ti {
    flex-shrink: 0;
}

.section-item .flex-fill {
    min-width: 0;
}

.section-item:hover {
    border-style: solid;
    border-color: #206bc4;
    background: #f1f7ff;
}

.section-item.active {
    border-color: #2fb344;
    border-style: solid;
}

.ghost {
    opacity: 0.4;
    background: #c8ebfb !important;
}

.sortable-drag {
    background: white !important;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedSectionEl = null;
    const saveBtn = document.getElementById('save-section-btn');
    const manageBtn = document.getElementById('manage-data-btn');
    const closeBtn = document.getElementById('close-editor-btn');

    // Save button click handler
    saveBtn.addEventListener('click', function () {
        const form = window.getSectionForm ? window.getSectionForm() : document.getElementById('section-edit-form');
        if (!form) return;
        const formData = new FormData(form);
        const originalHTML = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        axios.post(form.action, formData)
            .then(() => {
                window.showSuccessMessage('Section berhasil diperbarui!');
                setTimeout(() => window.location.reload(), 800);
            })
            .catch(error => {
                const msg = error.response?.data?.message || 'Gagal menyimpan section!';
                window.showErrorMessage(msg);
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHTML;
            });
    });

    // Helper to load section form in right panel
    function loadSectionEditor(sectionEl) {
        // Remove previous selection
        document.querySelectorAll('.section-item.selected').forEach(el => {
            el.classList.remove('selected');
        });

        // Select new
        sectionEl.classList.add('selected');
        selectedSectionEl = sectionEl;

        const editUrl = sectionEl.dataset.editUrl;
        const manageUrl = sectionEl.dataset.manageUrl;
        const section = JSON.parse(sectionEl.dataset.section);
        const editorTitle = document.getElementById('section-editor-title');
        const editorBody = document.getElementById('section-editor-body');

        editorTitle.textContent = section.section_name;
        closeBtn.style.display = 'flex';
        saveBtn.style.display = 'inline-flex';
        saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Simpan';
        saveBtn.disabled = false;

        // Manage data button
        if (manageUrl) {
            manageBtn.href = manageUrl;
            manageBtn.style.display = 'inline-flex';
        } else {
            manageBtn.style.display = 'none';
        }

        editorBody.innerHTML = '<div class="text-center py-8"><span class="spinner-border text-primary" role="status"></span></div>';

        // Fetch edit form
        axios.get(editUrl)
            .then(response => {
                editorBody.innerHTML = '';
                
                // Add toggle active button
                const toggleBtn = document.createElement('button');
                toggleBtn.className = `btn w-100 mb-4 ${section.is_active ? 'btn-outline-danger' : 'btn-outline-success'}`;
                toggleBtn.innerHTML = `<i class="ti ti-${section.is_active ? 'power-off' : 'power'} me-2"></i>${section.is_active ? 'Nonaktifkan Section' : 'Aktifkan Section'}`;
                toggleBtn.addEventListener('click', function () {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                    const toggleUrl = sectionEl.dataset.toggleUrl;
                    axios.post(toggleUrl, { _token: '{{ csrf_token() }}' })
                        .then(() => window.location.reload())
                        .catch(() => {
                            this.disabled = false;
                            this.innerHTML = `<i class="ti ti-${section.is_active ? 'power-off' : 'power'} me-2"></i>${section.is_active ? 'Nonaktifkan Section' : 'Aktifkan Section'}`;
                        });
                });
                editorBody.appendChild(toggleBtn);
                
                // Add the edit form
                const formContainer = document.createElement('div');
                formContainer.innerHTML = response.data;
                editorBody.appendChild(formContainer);
            })
            .catch(error => {
                editorBody.innerHTML = '<div class="alert alert-danger">Gagal memuat form edit</div>';
            });
    }

    // Close editor
    document.getElementById('close-editor-btn').addEventListener('click', function () {
        if (selectedSectionEl) {
            selectedSectionEl.classList.remove('selected');
            selectedSectionEl = null;
        }
        document.getElementById('section-editor-title').textContent = 'Pilih Section';
        document.getElementById('section-editor-body').innerHTML = '<div class="text-center text-muted py-8"><i class="ti ti-layout-2 fs-2 mb-2 d-block"></i><p class="mb-0">Klik salah satu section di daftar sebelah kiri untuk mengeditnya</p></div>';
        this.style.display = 'none';
        saveBtn.style.display = 'none';
        manageBtn.style.display = 'none';
    });

    // Click on section items
    document.querySelectorAll('.section-item').forEach(sectionEl => {
        sectionEl.addEventListener('click', function (e) {
            // Don't toggle if clicking on drag handle
            if (e.target.closest('.drag-handle')) return;
            loadSectionEditor(sectionEl);
        });
    });

    // Sortable
    const middleSections = document.getElementById('middle-sections');
    if (middleSections && window.Sortable) {
        new Sortable(middleSections, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'ghost',
            sortClass: 'sortable-drag',
            onEnd: () => {
                const ids = [...middleSections.children].map(item => item.dataset.id);
                axios.post('{{ route('public.cms.landing.sections.reorder') }}', {
                    area: 'middle',
                    ids: ids,
                    _token: '{{ csrf_token() }}',
                }).then(() => window.showSuccessMessage('Urutan section diperbarui.'))
                  .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'));
            }
        });
    }
});
</script>
@endpush
