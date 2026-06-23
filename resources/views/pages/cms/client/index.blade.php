@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Klien" pretitle="Content Management">
    <x-slot:actions>
        <a href="{{ route('public.cms.landing.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.client.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('public.cms.client.create') }}"
                data-modal-title="Tambah Klien" text="Tambah Klien" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($clients->isEmpty())
    <x-ui.empty-state title="Belum ada klien" text="Tambahkan logo klien untuk ditampilkan di landing page." icon="ti ti-users-group" />
@else
    <div class="row row-cards" id="client-grid">
        @foreach($clients as $client)
            <div class="col-sm-6 col-lg-3" data-id="{{ $client->encrypted_client_id }}">
                <x-ui.card class="h-100">
                    <x-ui.card-body class="text-center position-relative">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                @can('public.cms.client.update')
                                    <x-ui.dropdown-item type="edit" href="javascript:void(0)" :url="route('public.cms.client.edit', $client)" data-modal-title="Edit Klien" />
                                @endcan
                                @can('public.cms.client.delete')
                                    <x-ui.dropdown-item type="delete" href="javascript:void(0)" :url="route('public.cms.client.destroy', $client)" title="Hapus Klien?" />
                                @endcan
                            </x-ui.dropdown>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mb-3" style="height:80px">
                            @if($client->logo_url)
                                <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" style="max-width:140px;max-height:70px;object-fit:contain">
                            @else
                                <span class="avatar avatar-xl bg-primary-lt"><i class="ti ti-building fs-1"></i></span>
                            @endif
                        </div>
                        <h3 class="card-title mb-0">{{ $client->name }}</h3>
                    </x-ui.card-body>
                    <x-ui.card-footer class="d-flex align-items-center">
                        <span class="cursor-move text-secondary me-auto"><i class="ti ti-grip-vertical"></i></span>
                        <span class="badge {{ $client->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $client->is_active ? 'Aktif' : 'Draft' }}</span>
                    </x-ui.card-footer>
                </x-ui.card>
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('client-grid');
    if (!grid || !window.Sortable) return;
    Sortable.create(grid, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('public.cms.client.reorder') }}', {
            order: [...grid.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan klien diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'))
    });
});
</script>
@endpush
