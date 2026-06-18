@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Manajemen Halaman Publik" pretitle="Content Management">
    <x-slot:actions>
        <x-ui.button type="create" href="{{ route('public.cms.page.create') }}" text="Tambah Halaman" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <x-ui.card>
        <x-ui.card-header>
            <x-ui.datatable-toolbar dataTableId="table-public-pages" :filter="false" />
        </x-ui.card-header>
            <x-ui.datatable
                id="table-public-pages"
                route="{{ route('public.cms.page.data') }}"
                :columns="[
                    ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                    ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
                    ['data' => 'slug', 'name' => 'slug', 'title' => 'Slug'],
                    ['data' => 'is_published', 'name' => 'is_published', 'title' => 'Status'],
                    ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Terakhir Update'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'class' => 'text-center']
                ]"
            />
    </x-ui.card>

@endsection
