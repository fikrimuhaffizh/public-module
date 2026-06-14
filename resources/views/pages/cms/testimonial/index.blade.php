@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Testimoni" pretitle="Content Management">
    <x-slot:actions>
        <a href="{{ route('public.cms.landing.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.testimonial.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('public.cms.testimonial.create') }}"
                data-modal-title="Tambah Testimoni" text="Tambah Testimoni" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
@if($testimonials->isEmpty())
    <x-ui.empty-state title="Belum ada testimoni" text="Tambahkan testimoni untuk ditampilkan di landing page." icon="ti ti-message-star" />
@else
    <div class="row row-cards" id="testimonial-grid">
        @foreach($testimonials as $testimonial)
            <div class="col-md-6 col-xl-4" data-id="{{ $testimonial->encrypted_testimonial_id }}">
                <x-ui.card class="h-100">
                    <x-ui.card-body>
                        <div class="d-flex align-items-start gap-3">
                            @if($testimonial->photo_url)
                                <span class="avatar avatar-lg rounded-circle" style="background-image:url('{{ $testimonial->photo_url }}')"></span>
                            @else
                                <span class="avatar avatar-lg rounded-circle bg-primary-lt">{{ Str::upper(Str::substr($testimonial->name, 0, 2)) }}</span>
                            @endif
                            <div class="flex-fill min-w-0">
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <h3 class="card-title mb-1">{{ $testimonial->name }}</h3>
                                        <div class="text-secondary small">{{ collect([$testimonial->position, $testimonial->organization])->filter()->join(' · ') ?: '-' }}</div>
                                    </div>
                                    <x-ui.dropdown class="btn btn-action text-secondary">
                                        @can('public.cms.testimonial.update')
                                            <x-ui.dropdown-item type="edit" href="javascript:void(0)"
                                                :url="route('public.cms.testimonial.edit', $testimonial)"
                                                data-modal-title="Edit Testimoni" />
                                        @endcan
                                        @can('public.cms.testimonial.delete')
                                            <x-ui.dropdown-item type="delete" href="javascript:void(0)"
                                                :url="route('public.cms.testimonial.destroy', $testimonial)"
                                                title="Hapus Testimoni?" />
                                        @endcan
                                    </x-ui.dropdown>
                                </div>
                            </div>
                        </div>
                        <div class="text-warning mt-3">
                            @for($i = 1; $i <= 5; $i++)<i class="ti ti-star{{ $i <= $testimonial->rating ? '-filled' : '' }}"></i>@endfor
                        </div>
                        <blockquote class="mt-3 mb-0 text-secondary">“{{ $testimonial->quote }}”</blockquote>
                    </x-ui.card-body>
                    <x-ui.card-footer class="d-flex align-items-center">
                        <span class="cursor-move text-secondary me-auto" title="Geser untuk mengurutkan"><i class="ti ti-grip-vertical"></i></span>
                        <span class="badge {{ $testimonial->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $testimonial->is_active ? 'Aktif' : 'Draft' }}</span>
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
    const grid = document.getElementById('testimonial-grid');
    if (!grid || !window.Sortable) return;
    Sortable.create(grid, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: () => axios.post('{{ route('public.cms.testimonial.reorder') }}', {
            order: [...grid.children].map(item => item.dataset.id),
            _token: '{{ csrf_token() }}'
        }).then(() => window.showSuccessMessage('Urutan testimoni diperbarui.'))
          .catch(() => window.showErrorMessage('Gagal menyimpan urutan testimoni.'))
    });
});
</script>
@endpush
