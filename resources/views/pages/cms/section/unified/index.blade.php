@extends('layouts.' . active_theme() . '.app')

@section('title', 'CMS Sections')

@section('header')
<x-ui.page-header title="CMS Sections" pretitle="Landing Page">
    <x-slot:actions>
        @can('public.cms.section.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('cms.section.create', ['type' => $type]) }}"
                data-modal-title="Tambah {{ Models\Section::typeLabel($type) }}"
                text="Tambah" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<x-ui.card>
    <x-ui.card-header>
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            @foreach($types as $key => $label)
                <li class="nav-item">
                    <a href="{{ route('cms.section.index', ['type' => $key]) }}"
                       class="nav-link {{ $type === $key ? 'active' : '' }}">
                        <i class="{{ $icons[$key] ?? 'ti ti-layout-list' }} me-1"></i>{{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-ui.card-header>
    <x-ui.card-body>
        @if($sections->isEmpty())
            <x-ui.empty-state
                title="Belum ada {{ strtolower(Models\Section::typeLabel($type)) }}"
                text="Tambahkan item untuk ditampilkan di landing page."
                icon="ti ti-layout-list"
            />
        @else
            <div class="list-group sortable-list" id="section-list">
                @foreach($sections as $section)
                    <div class="list-group-item d-flex align-items-center gap-3"
                         data-id="{{ $section->encrypted_section_id }}">
                        <span class="cursor-move text-secondary"><i class="ti ti-grip-vertical"></i></span>

                        {{-- Image/Logo --}}
                        @if($section->image_url)
                            <img src="{{ $section->image_url }}" alt=""
                                 class="rounded" style="width:56px;height:40px;object-fit:cover">
                        @endif

                        {{-- Icon --}}
                        @if($section->icon)
                            <span class="avatar bg-primary-lt">
                                <i class="{{ $section->icon }}"></i>
                            </span>
                        @endif

                        {{-- Content --}}
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $section->title }}</div>
                            @if($section->description)
                                <div class="text-muted small">
                                    {{ Str::limit(strip_tags($section->description), 80) }}
                                </div>
                            @endif
                            @if($type === 'product' && $section->slug)
                                <div class="text-secondary small">/{{ $section->slug }}</div>
                            @endif
                            @if($type === 'testimonial' && $section->getSetting('organization'))
                                <div class="text-muted small">{{ $section->getSetting('organization') }}</div>
                            @endif
                            @if($type === 'statistic' && $section->getSetting('value'))
                                <div class="text-muted small">{{ $section->getSetting('value') }}</div>
                            @endif
                            @if($type === 'pricing' && $section->getSetting('price'))
                                <div class="text-muted small">Rp {{ $section->getSetting('price') }} / {{ $section->getSetting('period', '-') }}</div>
                            @endif
                            @if($type === 'faq' && $section->getSetting('category'))
                                <div class="text-muted small">{{ $section->getSetting('category') }}</div>
                            @endif
                        </div>

                        {{-- Toggle --}}
                        <label class="form-check form-switch mb-0" title="Aktif / Nonaktif">
                            <input class="form-check-input toggle-section" type="checkbox"
                                   data-id="{{ $section->encrypted_section_id }}"
                                   {{ $section->is_active ? 'checked' : '' }}>
                        </label>

                        {{-- Actions --}}
                        <x-ui.dropdown class="btn btn-action text-secondary">
                            @can('public.cms.section.update')
                                <x-ui.dropdown-item type="edit" href="javascript:void(0)"
                                    :url="route('cms.section.edit', $section)"
                                    data-modal-title="Edit {{ Models\Section::typeLabel($type) }}" />
                            @endcan
                            @can('public.cms.section.delete')
                                <x-ui.dropdown-item type="delete" href="javascript:void(0)"
                                    :url="route('cms.section.destroy', $section)"
                                    title="Hapus?" />
                            @endcan
                        </x-ui.dropdown>
                    </div>
                @endforeach
            </div>
        @endif
    </x-ui.card-body>
</x-ui.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('section-list');
    if (!list || !window.Sortable) return;

    Sortable.create(list, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('cms.section.reorder') }}', {
            order: [...list.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'))
    });

    document.addEventListener('change', function (e) {
        const cb = e.target;
        if (!cb.classList.contains('toggle-section')) return;
        const original = cb.checked;
        axios.post('{{ url("cms/section/unified") }}/' + cb.dataset.id + '/toggle')
            .then(resp => {
                if (resp.data && resp.data.success) {
                    showSuccessMessage(resp.data.message);
                }
            })
            .catch(() => { cb.checked = !original; showErrorMessage('Gagal mengubah status.'); });
    });
});
</script>
@endpush
