@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Hero Section" pretitle="CMS">
    <x-slot:actions>
        @can('public.cms.hero.create')
            <x-ui.button type="create" class="ajax-modal-btn"
                data-url="{{ route('public.cms.hero.create') }}"
                data-modal-title="Tambah Hero Section" text="Tambah Hero" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<div class="alert alert-info mb-3">
    <i class="ti ti-info-circle me-1"></i> Hanya satu hero yang dapat aktif sekaligus. Hero aktif akan ditampilkan pada template Launch.
</div>
@if($heroes->isEmpty())
    <x-ui.empty-state title="Belum ada hero section" text="Tambahkan hero untuk bagian utama landing page." icon="ti ti-layout-navbar" />
@else
    <div class="row row-cards">
        @foreach($heroes as $hero)
            <div class="col-md-6 col-xl-4">
                <x-ui.card class="h-100 {{ $hero->is_active ? 'border-primary border-2' : '' }}">
                    <x-ui.card-body class="position-relative">
                        <div class="position-absolute top-0 end-0 mt-2 me-2">
                            <x-ui.dropdown class="btn btn-action text-secondary">
                                @can('public.cms.hero.update')
                                    <x-ui.dropdown-item type="edit" href="javascript:void(0)"
                                        :url="route('public.cms.hero.edit', $hero)"
                                        data-modal-title="Edit Hero Section" />
                                @endcan
                                @can('public.cms.hero.delete')
                                    <x-ui.dropdown-item type="delete" href="javascript:void(0)"
                                        :url="route('public.cms.hero.destroy', $hero)"
                                        title="Hapus Hero?" />
                                @endcan
                            </x-ui.dropdown>
                        </div>
                        @if($hero->image_url)
                            <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="rounded mb-3 w-100" style="height:160px;object-fit:cover">
                        @else
                            <div class="bg-secondary-lt rounded mb-3 d-flex align-items-center justify-content-center" style="height:160px">
                                <i class="ti ti-photo fs-1 text-secondary"></i>
                            </div>
                        @endif
                        <h3 class="card-title">{{ $hero->title }}</h3>
                        @if($hero->subtitle)<p class="text-secondary mb-1">{{ $hero->subtitle }}</p>@endif
                        <span class="badge {{ $hero->is_active ? 'bg-success-lt' : 'bg-secondary-lt' }}">{{ $hero->is_active ? 'Aktif' : 'Draft' }}</span>
                    </x-ui.card-body>
                </x-ui.card>
            </div>
        @endforeach
    </div>
@endif
@endsection
