@extends('layouts.' . active_theme() . '.app')

@section('title', 'Landing Page')

@section('header')
<x-ui.page-header title="Section" pretitle="Landing Page" />
@endsection

@section('content')
    @php
        // Helper: build compact content preview string from section titles

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
            <x-ui.card>
                <x-ui.card-body>
                    <!-- Top Area -->
                    @if(isset($sections['top']) && count($sections['top']) > 0)
                        <div class="mb-4">
                            <h5 class="text-muted mb-2">Top Area</h5>
                            <div class="section-list">
                                @foreach($sections['top'] as $section)
                                    @php
                                        $icon = $sectionIcons[$section->section_key] ?? 'layout';
                                    @endphp
                                    <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-section='@json($section)' data-edit-url="{{ route('cms.landing.edit-section', $section) }}" data-manage-url="{{ isset($registry[$section->section_key]['manage_data_route']) ? ($registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '') : '' }}" data-toggle-url="{{ route('cms.landing.toggle-section', $section) }}">
                                        <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                        <div class="flex-fill overflow-hidden">
                                            <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                        </div>
                                        <a href="javascript:void(0)" class="section-visibility-toggle" data-url="{{ route('cms.landing.toggle-section', $section) }}" onclick="event.stopPropagation()" title="{{ $section->is_active ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <i class="ti ti-{{ $section->is_active ? 'eye' : 'eye-off' }} section-visibility-icon {{ $section->is_active ? '' : 'text-muted' }}"></i>
                                        </a>
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
                                    <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-id="{{ $section->encrypted_landing_section_id }}" data-section='@json($section)' data-edit-url="{{ route('cms.landing.edit-section', $section) }}" data-manage-url="{{ $registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '' }}" data-toggle-url="{{ route('cms.landing.toggle-section', $section) }}">
                                        <div class="drag-handle"><i class="ti ti-grip-vertical"></i></div>
                                        <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                        <div class="flex-fill overflow-hidden">
                                            <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                        </div>
                                        <a href="javascript:void(0)" class="section-visibility-toggle" data-url="{{ route('cms.landing.toggle-section', $section) }}" onclick="event.stopPropagation()" title="{{ $section->is_active ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <i class="ti ti-{{ $section->is_active ? 'eye' : 'eye-off' }} section-visibility-icon {{ $section->is_active ? '' : 'text-muted' }}"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <!-- Bottom Area -->
                    @if(isset($sections['bottom']) && count($sections['bottom']) > 0)
                        <div class="mb-0">
                            <h5 class="text-muted mb-2">Bottom Area</h5>
                            <div class="section-list">
                                @foreach($sections['bottom'] as $section)
                                    @php
                                        $icon = $sectionIcons[$section->section_key] ?? 'layout';
                                    @endphp
                                    <div class="section-item {{ $section->is_active ? 'active' : '' }} cursor-pointer" data-section='@json($section)' data-edit-url="{{ route('cms.landing.edit-section', $section) }}" data-manage-url="{{ isset($registry[$section->section_key]['manage_data_route']) ? ($registry[$section->section_key]['manage_data_route'] ? route($registry[$section->section_key]['manage_data_route']) : '') : '' }}" data-toggle-url="{{ route('cms.landing.toggle-section', $section) }}">
                                        <i class="ti ti-{{ $icon }} text-muted" style="font-size: 1.1rem;"></i>
                                        <div class="flex-fill overflow-hidden">
                                            <div class="font-weight-medium text-truncate" style="font-size: 0.85rem; line-height: 1.2;">{{ $section->section_name }}</div>
                                        </div>
                                        <a href="javascript:void(0)" class="section-visibility-toggle" data-url="{{ route('cms.landing.toggle-section', $section) }}" onclick="event.stopPropagation()" title="{{ $section->is_active ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <i class="ti ti-{{ $section->is_active ? 'eye' : 'eye-off' }} section-visibility-icon {{ $section->is_active ? '' : 'text-muted' }}"></i>
                                        </a>
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
            <x-ui.card id="section-editor-panel" >
                <x-ui.card-header title="Pilih Section">
                    <x-slot:actions>
                        <div class="d-flex align-items-center gap-2" id="panel-actions">
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
@endsection

@push('styles')
    @vite(['Modules/Public/resources/assets/css/cms-section.css'])
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedSectionEl = null;
    const saveBtn = document.getElementById('save-section-btn');
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

        // Pastikan panel editor terlihat (layout menumpuk di layar sempit)
        const editorPanel = document.getElementById('section-editor-panel');
        if (editorPanel) {
            const panelRect = editorPanel.getBoundingClientRect();
            if (panelRect.top < 0 || panelRect.bottom > window.innerHeight) {
                editorPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        const editUrl = sectionEl.dataset.editUrl;
        const manageUrl = sectionEl.dataset.manageUrl;
        const section = JSON.parse(sectionEl.dataset.section);
        const editorTitle = document.querySelector('#section-editor-panel .card-title');
        const editorBody = document.getElementById('section-editor-body');

        editorTitle.textContent = section.section_name;
        closeBtn.style.display = 'flex';
        saveBtn.style.display = 'inline-flex';
        saveBtn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Simpan';
        saveBtn.disabled = false;

        editorBody.innerHTML = '<div class="text-center py-8"><span class="spinner-border text-primary" role="status"></span></div>';

        // Fetch edit form
        axios.get(editUrl)
            .then(response => {
                editorBody.innerHTML = '';

                // Add the edit form
                const formContainer = document.createElement('div');
                formContainer.innerHTML = response.data;
                editorBody.appendChild(formContainer);
            })
            .catch(error => {
                editorBody.innerHTML = '<div class="alert alert-danger">Gagal memuat form edit</div>';
            });
    }

    // Section visibility toggle (eye icon)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.section-visibility-toggle');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const url = btn.dataset.url;
        const sectionItem = btn.closest('.section-item');
        const icon = btn.querySelector('.section-visibility-icon');

        btn.style.pointerEvents = 'none';
        icon.style.opacity = '0.4';

        axios.post(url, { _token: '{{ csrf_token() }}' })
            .then(() => {
                const isNowHidden = sectionItem.classList.toggle('section-item--hidden');
                sectionItem.classList.toggle('active', !isNowHidden);

                icon.className = isNowHidden
                    ? 'ti ti-eye-off section-visibility-icon text-muted'
                    : 'ti ti-eye section-visibility-icon';

                btn.title = isNowHidden ? 'Tampilkan' : 'Sembunyikan';
                btn.style.pointerEvents = '';
                icon.style.opacity = '';
            })
            .catch(() => {
                btn.style.pointerEvents = '';
                icon.style.opacity = '';
                window.showErrorMessage('Gagal mengubah status section.');
            });
    });

    // Close editor
    document.getElementById('close-editor-btn').addEventListener('click', function () {
        if (selectedSectionEl) {
            selectedSectionEl.classList.remove('selected');
            selectedSectionEl = null;
        }
        document.querySelector('#section-editor-panel .card-title').textContent = 'Pilih Section';
        document.getElementById('section-editor-body').innerHTML = '<div class="text-center text-muted py-8"><i class="ti ti-layout-2 fs-2 mb-2 d-block"></i><p class="mb-0">Klik salah satu section di daftar sebelah kiri untuk mengeditnya</p></div>';
        this.style.display = 'none';
        saveBtn.style.display = 'none';
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
                axios.post('{{ route('cms.landing.reorder-sections') }}', {
                    area: 'middle',
                    ids: ids,
                    _token: '{{ csrf_token() }}',
                }).then(() => window.showSuccessMessage('Urutan section diperbarui.'))
                  .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'));
            }
        });
    }

    // Auto-save template selection
    const templateForm = document.getElementById('landing-template-form');
    if (templateForm) {
        const templateInputs = templateForm.querySelectorAll('input[name="landing_template"]');
        templateInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedTemplate = this.value;
                
                // Update UI immediately for better UX
                templateInputs.forEach(t => {
                    const card = t.closest('.card');
                    if (t.value === selectedTemplate) {
                        card.classList.add('border-primary', 'border-2');
                        const badge = card.querySelector('.badge');
                        if (!badge) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge bg-primary-lt';
                            newBadge.textContent = 'Sedang digunakan';
                            card.querySelector('.d-flex.align-items-center.gap-2').appendChild(newBadge);
                        }
                    } else {
                        card.classList.remove('border-primary', 'border-2');
                        const badge = card.querySelector('.badge');
                        if (badge) badge.remove();
                    }
                });

                // Send auto-save request
                axios.put('{{ route('cms.landing.template.update') }}', {
                    _token: '{{ csrf_token() }}',
                    landing_template: selectedTemplate
                })
                .then(response => {
                    window.showSuccessMessage('Template berhasil diubah ke ' + selectedTemplate + '!');
                })
                .catch(error => {
                    console.error('Template save error:', error);
                    let msg = 'Gagal mengubah template!';
                    if (error.response?.data?.message) {
                        msg = error.response.data.message;
                    } else if (error.response?.data?.errors) {
                        msg = Object.values(error.response.data.errors).flat().join(', ');
                    }
                    window.showErrorMessage(msg);
                });
            });
        });
    }
});
</script>
@endpush
