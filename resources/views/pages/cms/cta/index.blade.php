@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Call To Action" pretitle="CMS">
    <x-slot:actions>
        <a href="{{ route('public.cms.landing.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
        @can('public.cms.cta.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('public.cms.cta.create') }}"
                data-modal-title="Tambah CTA" text="Tambah CTA" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<div class="alert alert-info mb-3">
    <i class="ti ti-info-circle me-1"></i> Hanya satu CTA yang dapat aktif sekaligus.
</div>
@if($ctas->isEmpty())
    <x-ui.empty-state title="Belum ada CTA" text="Tambahkan call to action untuk bagian penutup landing page." icon="ti ti-click" />
@else
    <div class="row row-cards">
        @foreach($ctas as $cta)
            <div class="col-md-6">
                <x-ui.card class="h-100 {{ $cta->is_active ? 'border-primary border-2' : '' }}">
                    <x-ui.card-body class="position-relative">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                @can('public.cms.cta.update')
                                    <x-ui.dropdown-item type="edit" href="javascript:void(0)" :url="route('public.cms.cta.edit', $cta)" data-modal-title="Edit CTA" />
                                @endcan
                                @can('public.cms.cta.delete')
                                    <x-ui.dropdown-item type="delete" href="javascript:void(0)" :url="route('public.cms.cta.destroy', $cta)" title="Hapus CTA?" />
                                @endcan
                            </x-ui.dropdown>
                        </div>
                        @if($cta->background_image_url)
                            <img src="{{ $cta->background_image_url }}" alt="" class="rounded mb-3 w-100" style="height:120px;object-fit:cover">
                        @endif
                        <h3 class="card-title">{{ $cta->title }}</h3>
                        <p class="text-muted">{{ Str::limit($cta->description, 120) }}</p>
                        @if($cta->button_text)
                            <span class="badge bg-primary-lt">{{ $cta->button_text }}</span>
                        @endif
                        <div class="mt-2">
                            <span class="badge {{ $cta->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $cta->is_active ? 'Aktif' : 'Draft' }}</span>
                        </div>
                    </x-ui.card-body>
                </x-ui.card>
            </div>
        @endforeach
    </div>
@endif
@endsection
