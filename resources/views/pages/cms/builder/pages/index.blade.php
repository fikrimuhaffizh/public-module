@extends('layouts.' . active_theme() . '.app')

@section('title', 'Halaman Website Builder')

@section('header')
<x-ui.page-header title="Halaman Website Builder" pretitle="Website Builder">
    <x-slot:actions>
        <x-ui.button type="create" href="{{ route('cms.builder.pages.create') }}" text="Buat Halaman" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <div class="alert alert-info" role="alert">
        <div class="d-flex">
            <i class="ti ti-info-circle me-2 mt-1"></i>
            <div>
                Halaman <strong>Custom</strong> dibuat dengan editor freeform (GrapesJS) dan dirender langsung sebagai HTML.
                Halaman <strong>Template</strong> memakai sistem React existing dan tidak berubah.
            </div>
        </div>
    </div>

    <x-ui.card>
        <x-ui.card-header>
            <x-ui.datatable-toolbar dataTableId="table-builder-pages" :filter="false" />
        </x-ui.card-header>
        <x-ui.card-body class="p-0">
            <x-ui.datatable
                id="table-builder-pages"
                route="{{ route('cms.builder.pages.data') }}"
                :columns="[
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                    ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
                    ['data' => 'render_mode', 'name' => 'render_mode', 'title' => 'Mode'],
                    ['data' => 'slug', 'name' => 'slug', 'title' => 'URL'],
                    ['data' => 'is_published', 'name' => 'is_published', 'title' => 'Status', 'class' => 'text-center'],
                    ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Terakhir Update'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'class' => 'text-center']
                ]"
            />
        </x-ui.card-body>
    </x-ui.card>

@endsection

@push('scripts')
    @vite(['Modules/Public/resources/assets/js/builder-actions.js'])
@endpush