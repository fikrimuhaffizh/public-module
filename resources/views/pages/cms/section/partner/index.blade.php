@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Partner" pretitle="Landing Page">
    <x-slot:actions>
        @can('public.cms.partner.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('cms.partner.create') }}"
                data-modal-title="Tambah Partner" text="Tambah Partner" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($partners->isEmpty())
    <x-ui.empty-state title="Belum ada partner" text="Tambahkan partner untuk membangun social proof pada landing page." icon="ti ti-building-community" />
@else
    <div class="row row-cards" id="partner-grid">
        @foreach($partners as $partner)
            <div class="col-sm-6 col-lg-3" data-id="{{ $partner->encrypted_partner_id }}">
                <x-ui.card class="h-100">
                    <x-ui.card-body class="text-center position-relative">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                @can('public.cms.partner.update')
                                    <x-ui.dropdown-item type="edit" href="javascript:void(0)"
                                        :url="route('cms.partner.edit', $partner)"
                                        data-modal-title="Edit Partner" />
                                @endcan
                                @can('public.cms.partner.delete')
                                    <x-ui.dropdown-item type="delete" href="javascript:void(0)"
                                        :url="route('cms.partner.destroy', $partner)"
                                        title="Hapus Partner?" />
                                @endcan
                            </x-ui.dropdown>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mb-3" style="height:90px">
                            @if($partner->logo_url)
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" style="max-width:160px;max-height:75px;object-fit:contain">
                            @else
                                <span class="avatar avatar-xl bg-primary-lt"><i class="ti ti-building fs-1"></i></span>
                            @endif
                        </div>
                        <h3 class="card-title mb-1">{{ $partner->name }}</h3>
                        <div class="text-secondary small">{{ $partner->category ?: 'Partner Institusi' }}</div>
                    </x-ui.card-body>
                    <x-ui.card-footer class="d-flex align-items-center">
                        <span class="cursor-move text-secondary me-auto"><i class="ti ti-grip-vertical"></i></span>
                        <span class="badge {{ $partner->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $partner->is_active ? 'Aktif' : 'Draft' }}</span>
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
    const grid = document.getElementById('partner-grid');
    if (!grid || !window.Sortable) return;
    Sortable.create(grid, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('cms.partner.reorder') }}', {
            order: [...grid.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan partner diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan partner.'))
    });
});
</script>
@endpush
