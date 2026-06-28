@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Statistik" pretitle="Landing Page">
    <x-slot:actions>
        <a href="{{ route('cms.section.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.statistic.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('cms.statistic.create') }}"
                data-modal-title="Tambah Statistik" text="Tambah Statistik" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($statistics->isEmpty())
    <x-ui.empty-state title="Belum ada statistik" text="Tambahkan angka pencapaian untuk social proof." icon="ti ti-chart-bar" />
@else
    <div class="list-group sortable-list" id="statistic-list">
        @foreach($statistics as $statistic)
            <div class="list-group-item d-flex align-items-center gap-3" data-id="{{ $statistic->encrypted_statistic_id }}">
                <span class="cursor-move text-secondary"><i class="ti ti-grip-vertical"></i></span>
                @if($statistic->icon)<span class="avatar bg-primary-lt"><i class="{{ $statistic->icon }}"></i></span>@endif
                <div class="flex-grow-1">
                    <div class="fw-bold fs-3">{{ $statistic->value }}</div>
                    <div class="text-muted">{{ $statistic->label }}</div>
                </div>
                <span class="badge {{ $statistic->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $statistic->is_active ? 'Aktif' : 'Draft' }}</span>
                <x-ui.dropdown class="btn btn-action text-secondary">
                    @can('public.cms.statistic.update')
                        <x-ui.dropdown-item type="edit" href="javascript:void(0)" :url="route('cms.statistic.edit', $statistic)" data-modal-title="Edit Statistik" />
                    @endcan
                    @can('public.cms.statistic.delete')
                        <x-ui.dropdown-item type="delete" href="javascript:void(0)" :url="route('cms.statistic.destroy', $statistic)" title="Hapus Statistik?" />
                    @endcan
                </x-ui.dropdown>
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('statistic-list');
    if (!list || !window.Sortable) return;
    Sortable.create(list, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('cms.statistic.reorder') }}', {
            order: [...list.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan statistik diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'))
    });
});
</script>
@endpush
