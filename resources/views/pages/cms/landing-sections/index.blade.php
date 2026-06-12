@extends('layouts.tabler.app')

@section('title', 'Landing Sections')

@section('header')
<x-ui.page-header title="Landing Sections" pretitle="Landing Page">
    <x-slot:actions>
        <a href="{{ route('public.preview', ['template' => 'custom']) }}" target="_blank" class="btn btn-outline-primary">
            <i class="ti ti-eye me-1"></i>Pratinjau
        </a>
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@php
    $areaOrder = ['top', 'middle', 'bottom'];
@endphp
<div class="row row-cards">
    @foreach($areaOrder as $area)
        @if(isset($sections[$area]))
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ ucfirst($area) }} Area
                            @if($area === 'middle')
                                <span class="text-muted ml-2">(Drag to Reorder)</span>
                            @else
                                <span class="text-muted ml-2">(Fixed Order)</span>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div @if($area === 'middle') id="middle-sections" @endif class="row row-cards">
                            @foreach($sections[$area] as $section)
                                <div class="col-md-6" @if($area === 'middle') data-id="{{ $section->encrypted_landing_section_id }}" @endif>
                                    <x-ui.card class="h-100 {{ $section->is_active ? 'border-primary border-2' : '' }}">
                                        <x-ui.card-body class="position-relative">
                                            <div class="position-absolute top-0 end-0 mt-2 me-2">
                                                <x-ui.dropdown class="btn btn-action text-secondary">
                                                    @can('public.cms.update')
                                                        <x-ui.dropdown-item
                                                            href="javascript:void(0)"
                                                            icon="ti ti-power"
                                                            label="{{ $section->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                            class="ajax-toggle"
                                                            data-url="{{ route('public.cms.landing.section.toggle', $section) }}"
                                                            data-token="{{ csrf_token() }}"
                                                        />
                                                        <x-ui.dropdown-item type="edit" href="javascript:void(0)"
                                                            :url="route('public.cms.landing.section.edit', $section)"
                                                            data-modal-title="Edit {{ $section->section_name }}" />
                                                    @endcan
                                                    @if($registry[$section->section_key]['manage_data_route'])
                                                        <x-ui.dropdown-item
                                                            :href="route($registry[$section->section_key]['manage_data_route'])"
                                                            icon="ti ti-folder"
                                                            label="Kelola Data" />
                                                    @endif
                                                </x-ui.dropdown>
                                            </div>
                                            <div class="d-flex align-items-start mb-3">
                                                @if($area === 'middle')
                                                    <span class="cursor-move text-secondary me-2 mt-1">
                                                        <i class="ti ti-grip-vertical fs-4"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <h4 class="card-title mb-0">{{ $section->section_name }}</h4>
                                                    @php
                                                        $parts = array_filter([
                                                            $section->pre_title ? '[' . $section->pre_title . ']' : null,
                                                            $section->title ? '« ' . $section->title . ' »' : null,
                                                            $section->post_title ? '[' . $section->post_title . ']' : null,
                                                        ]);
                                                        $titlesLine = implode(' ', $parts);
                                                    @endphp
                                                    @if($titlesLine)<p class="text-secondary mb-0" style="font-size: 0.8rem;">{{ $titlesLine }}</p>@endif
                                                    @if($section->subtitle)<p class="text-muted mb-1" style="font-size: 0.75rem;">{{ $section->subtitle }}</p>@endif
                                                </div>
                                            </div>
                                            <span class="badge {{ $section->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                                {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </x-ui.card-body>
                                    </x-ui.card>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle active via ajax
    document.querySelectorAll('.ajax-toggle').forEach(el => {
        el.addEventListener('click', function () {
            const url = this.dataset.url;
            const token = this.dataset.token;

            axios.post(url, { _token: token })
                .then(() => {
                    window.location.reload();
                })
                .catch(() => {
                    window.showErrorMessage('Gagal mengubah status.');
                });
        });
    });

    // Sortable
    const middleSections = document.getElementById('middle-sections');
    if (middleSections && window.Sortable) {
        Sortable.create(middleSections, {
            animation: 150,
            handle: '.cursor-move',
            ghostClass: 'bg-indigo-lt',
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
