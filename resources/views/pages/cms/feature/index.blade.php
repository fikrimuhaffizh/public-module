@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Fitur" pretitle="Content Management">
    <x-slot:actions>
        <a href="{{ route('public.cms.landing.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.feature.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('public.cms.feature.create') }}"
                data-modal-title="Tambah Fitur" text="Tambah Fitur" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($features->isEmpty())
    <x-ui.empty-state title="Belum ada fitur" text="Tambahkan fitur untuk ditampilkan di landing page." icon="ti ti-sparkles" />
@else
    <div class="list-group sortable-list" id="feature-list">
        @foreach($features as $feature)
            <div class="list-group-item d-flex align-items-center gap-3" data-id="{{ $feature->encrypted_feature_id }}">
                <span class="cursor-move text-secondary"><i class="ti ti-grip-vertical"></i></span>
                @if($feature->image_url)
                    <img src="{{ $feature->image_url }}" alt="" class="rounded" style="width:56px;height:40px;object-fit:cover">
                @elseif($feature->icon)
                    <span class="avatar bg-primary-lt"><i class="{{ $feature->icon }}"></i></span>
                @endif
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ $feature->title }}</div>
                    <div class="text-muted small">{{ Str::limit(strip_tags($feature->description), 80) }}</div>
                </div>
                <span class="badge {{ $feature->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $feature->is_active ? 'Aktif' : 'Draft' }}</span>
                <x-ui.dropdown class="btn btn-action text-secondary">
                    @can('public.cms.feature.update')
                        <x-ui.dropdown-item type="edit" href="javascript:void(0)" :url="route('public.cms.feature.edit', $feature)" data-modal-title="Edit Fitur" />
                    @endcan
                    @can('public.cms.feature.delete')
                        <x-ui.dropdown-item type="delete" href="javascript:void(0)" :url="route('public.cms.feature.destroy', $feature)" title="Hapus Fitur?" />
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
    const list = document.getElementById('feature-list');
    if (!list || !window.Sortable) return;
    Sortable.create(list, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('public.cms.feature.reorder') }}', {
            order: [...list.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan fitur diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'))
    });
});
</script>
@endpush
