@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Produk" pretitle="Landing Page">
    <x-slot:actions>
        <a href="{{ route('cms.section.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.product.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('cms.product.create') }}"
                data-modal-title="Tambah Produk" text="Tambah Produk" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($products->isEmpty())
    <x-ui.empty-state title="Belum ada produk" text="Tambahkan modul seperti E-Office, SPMI, HR, dll." icon="ti ti-apps" />
@else
    <div class="row row-cards" id="product-grid">
        @foreach($products as $product)
            <div class="col-sm-6 col-lg-4" data-id="{{ $product->encrypted_product_id }}">
                <x-ui.card class="h-100">
                    <x-ui.card-body class="position-relative">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                @can('public.cms.product.update')
                                    <x-ui.dropdown-item type="edit" href="javascript:void(0)" :url="route('cms.product.edit', $product)" data-modal-title="Edit Produk" />
                                @endcan
                                @can('public.cms.product.delete')
                                    <x-ui.dropdown-item type="delete" href="javascript:void(0)" :url="route('cms.product.destroy', $product)" title="Hapus Produk?" />
                                @endcan
                            </x-ui.dropdown>
                        </div>
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded mb-3 w-100" style="height:140px;object-fit:cover">
                        @endif
                        <h3 class="card-title mb-1">{{ $product->name }}</h3>
                        <div class="text-secondary small mb-2">{{ $product->slug }}</div>
                        <p class="text-muted small mb-0">{{ Str::limit($product->short_description, 90) }}</p>
                    </x-ui.card-body>
                    <x-ui.card-footer class="d-flex align-items-center">
                        <span class="cursor-move text-secondary me-auto"><i class="ti ti-grip-vertical"></i></span>
                        <span class="badge {{ $product->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $product->is_active ? 'Aktif' : 'Draft' }}</span>
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
    const grid = document.getElementById('product-grid');
    if (!grid || !window.Sortable) return;
    Sortable.create(grid, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('cms.product.reorder') }}', {
            order: [...grid.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan produk diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan.'))
    });
});
</script>
@endpush
