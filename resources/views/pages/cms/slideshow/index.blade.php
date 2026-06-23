@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Slideshow" pretitle="Content Management">
    <x-slot:actions>
        <x-ui.button 
            type="create" 
            class="ajax-modal-btn d-none d-sm-inline-block" 
            data-url="{{ route('public.cms.slideshow.create') }}" 
            data-modal-title="Tambah Slideshow"
            text="Tambah Slideshow" 
        />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    @if($slideshows->isEmpty())
        <x-ui.empty-state
            title="Belum ada Slideshow"
            text="Silakan tambahkan slideshow baru."
            icon="ti ti-photo"
        >
            <x-slot:action>
                <x-ui.button 
                    type="create" 
                    class="ajax-modal-btn" 
                    data-url="{{ route('public.cms.slideshow.create') }}" 
                    data-modal-title="Tambah Slideshow"
                    text="Tambah Slideshow" 
                />
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="row row-cards" id="slideshow-grid">
            @foreach($slideshows as $slide)
                <div class="col-md-6 col-lg-4" data-id="{{ $slide->encrypted_slideshow_id }}">
                    <x-ui.card class="card-sm">
                        <div class="d-block position-relative">
                            @if($slide->has_image)
                                <img src="{{ $slide->thumb_url }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $slide->title }}">
                                @if($slide->is_external_image)
                                    <a href="{{ $slide->image_url }}" target="_blank" class="position-absolute top-0 end-0 m-2 btn btn-sm btn-primary btn-icon rounded-circle shadow" title="Buka Link Gambar">
                                        <i class="ti ti-link"></i>
                                    </a>
                                @endif
                            @else
                                <div class="card-img-top bg-muted-lt d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="ti ti-photo-off fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <x-ui.card-body>
                            <div class="d-flex justify-content-end mb-2">
                                <div>
                                    @if($slide->is_active)
                                        <span class="badge bg-success-lt">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-lt">Draft</span>
                                    @endif
                                </div>
                            </div>
                            <h3 class="card-title mb-1">
                                {{ $slide->title ?: 'Tanpa Judul' }}
                            </h3>
                            @if($slide->caption)
                                <div class="text-secondary small text-truncate">{{ Str::limit($slide->caption, 50) }}</div>
                            @endif
                        </x-ui.card-body>
                        <x-ui.card-footer class="d-flex py-3">
                            <span class="cursor-move text-secondary me-auto" title="Drag to reorder">
                                <i class="ti ti-grid-dots fs-2"></i>
                            </span>
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                <x-ui.dropdown-item
                                    type="edit"
                                    href="javascript:void(0)"
                                    :url="route('public.cms.slideshow.edit', $slide->encrypted_slideshow_id)"
                                    data-modal-title="Edit Slideshow"
                                />
                                <x-ui.dropdown-item
                                    type="delete"
                                    href="javascript:void(0)"
                                    :url="route('public.cms.slideshow.destroy', $slide->encrypted_slideshow_id)"
                                    title="Hapus Slideshow?"
                                />
                            </x-ui.dropdown>
                        </x-ui.card-footer>
                    </x-ui.card>
                </div>
            @endforeach
        </div>
    @endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('slideshow-grid');
        if(el && window.Sortable){
            Sortable.create(el, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-indigo-lt',
                onEnd: function (evt) {
                    saveOrder();
                }
            });
        }
    });

    function saveOrder() {
        var order = [];
        document.querySelectorAll('#slideshow-grid > div').forEach(function (el) {
            order.push(el.getAttribute('data-id'));
        });

        axios.post('{{ route('public.cms.slideshow.reorder') }}', {
            order: order,
            _token: '{{ csrf_token() }}'
        })
        .then(function (response) {
            window.showSuccessMessage(response.data.message || 'Urutan berhasil disimpan.');
        })
        .catch(function (error) {
            window.showErrorMessage('Gagal', 'Urutan belum tersimpan. Silakan coba geser kembali.');
            console.error(error);
        });
    }
</script>
@endpush
@endsection
