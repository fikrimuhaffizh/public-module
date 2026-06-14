@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header :title="ucfirst($type)" pretitle="Content Management">
    <x-slot:actions>
        <x-ui.button type="create" :href="route('public.cms.'.$type . '.create', ['type' => $type])" :text="'Tambah ' . ucfirst($type)" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <x-ui.card class="overflow-hidden">
        <x-ui.card-header>
            <x-ui.datatable-toolbar :dataTableId="$type . '-table'" :filter="false" />
        </x-ui.card-header>
        <x-ui.card-body class="p-0">
            @php
                $columns = [
                    [
                        'title' => '#',
                        'data' => 'DT_RowIndex',
                        'name' => 'DT_RowIndex',
                        'orderable' => false,
                        'searchable' => false,
                        'class' => 'text-center'
                    ],
                    [
                        'title' => 'Cover',
                        'data' => 'cover',
                        'name' => 'cover',
                        'orderable' => false,
                        'searchable' => false
                    ],
                    [
                        'title' => 'Judul',
                        'data' => 'judul',
                        'name' => 'judul',
                        'orderable' => true,
                        'searchable' => true
                    ],
                    [
                        'title' => 'Status',
                        'data' => 'is_published',
                        'name' => 'is_published',
                        'orderable' => true,
                        'searchable' => false
                    ],
                    [
                        'title' => 'Penulis',
                        'data' => 'author',
                        'name' => 'penulis.name',
                    ],
                    [
                        'title' => 'Dibuat Pada',
                        'data' => 'created_at',
                        'name' => 'created_at',
                    ],
                    [
                        'title' => 'Aksi',
                        'data' => 'action',
                        'name' => 'action',
                        'orderable' => false,
                        'searchable' => false,
                        'class' => 'text-center'
                    ]
                ];
            @endphp
            <x-ui.datatable :id="$type . '-table'" :route="route('public.cms.'.$type.'.data')" :columns="$columns" :order="[[4, 'desc']]" />
        </x-ui.card-body>
    </x-ui.card>
@endsection
