@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header title="Manajemen Halaman & Navigasi" pretitle="Content Management">
    <x-slot:actions>
        <div class="btn-group" role="group">
            <a href="{{ route('cms.page.create') }}" class="btn btn-primary">
                <i class="ti ti-file-text me-1"></i> Tambah Halaman
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <div class="dropdown-menu">
                <a href="{{ route('cms.page.create') }}" class="dropdown-item">
                    <i class="ti ti-file-text me-2"></i> Halaman (dengan konten)
                </a>
                <a href="javascript:void(0)" class="dropdown-item ajax-modal-btn" data-url="{{ route('cms.menu.create') }}">
                    <i class="ti ti-link me-2"></i> Link / URL Eksternal
                </a>
            </div>
        </div>
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <x-ui.card>
        <x-ui.card-header>
            <ul class="nav nav-tabs card-header-tabs" id="top-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a href="#tabs-list" class="nav-link active" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-list me-2"></i> Daftar Halaman & Link
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-header" class="nav-link" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-layout-navbar me-2"></i> Struktur Header
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-footer" class="nav-link" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-layout-bottombar me-2"></i> Struktur Footer
                    </a>
                </li>
            </ul>
        </x-ui.card-header>
        <x-ui.card-body>
            <div class="tab-content">
                {{-- TAB 1: DAFTAR HALAMAN & LINK --}}
                <div class="tab-pane active show" id="tabs-list" role="tabpanel">
                    <x-ui.datatable-toolbar dataTableId="items-table" :filter="false" />
                    <x-ui.datatable
                        id="items-table"
                        route="{{ route('cms.menu.data') }}"
                        :columns="[
                            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                            ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
                            ['data' => 'type', 'name' => 'type', 'title' => 'Tipe'],
                            ['data' => 'page_slug', 'name' => 'page_slug', 'title' => 'Slug / URL', 'orderable' => false],
                            ['data' => 'position', 'name' => 'position', 'title' => 'Posisi'],
                            ['data' => 'is_active', 'name' => 'is_active', 'title' => 'Status'],
                            ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                        ]"
                    />
                </div>

                {{-- TAB 2: STRUKTUR HEADER --}}
                <div class="tab-pane" id="tabs-header" role="tabpanel">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <span>Seret item untuk mengubah urutan di header navigasi.</span>
                    </div>
                    @if($headerMenus->isEmpty())
                        <x-ui.empty-state
                            title="Belum ada item di Header"
                            text="Tambahkan halaman atau link lalu atur posisinya ke Header."
                            icon="ti ti-layout-navbar"
                        />
                    @else
                        <ul class="list-group list-group-flush sortable-list" id="header-tree">
                            @foreach($headerMenus as $menu)
                                @include('public::pages.cms.public-menu.item', ['menu' => $menu])
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- TAB 3: STRUKTUR FOOTER --}}
                <div class="tab-pane" id="tabs-footer" role="tabpanel">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <span>Seret item untuk mengubah urutan di footer navigasi.</span>
                    </div>
                    @if($footerMenus->isEmpty())
                        <x-ui.empty-state
                            title="Belum ada item di Footer"
                            text="Tambahkan halaman atau link lalu atur posisinya ke Footer."
                            icon="ti ti-layout-bottombar"
                        />
                    @else
                        <ul class="list-group list-group-flush sortable-list" id="footer-tree">
                            @foreach($footerMenus as $menu)
                                @include('public::pages.cms.public-menu.item', ['menu' => $menu])
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </x-ui.card-body>
    </x-ui.card>
@endsection

@push('styles')
<style>
    .sortable-list {
        min-height: 10px;
    }
    .sortable-list .list-group-item {
        border: 1px solid var(--tblr-border-color, rgba(101, 109, 119, 0.16));
        margin-bottom: 5px;
        border-radius: 4px;
        background: var(--tblr-bg-surface, #fff);
        color: var(--tblr-body-color, inherit);
    }
    .sortable-list .sortable-list {
        margin-left: 20px;
        margin-top: 5px;
        border: none;
        background: transparent;
    }
    .sortable-list .sortable-list .list-group-item {
        background: var(--tblr-bg-surface-secondary, #fcfdfe);
    }
    .cursor-move {
        cursor: move;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- SORTABLE LOGIC FOR HEADER & FOOTER ---
    function initSortable(listId, positionValue) {
        const el = document.getElementById(listId);
        if (!el) return;

        new Sortable(el, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.drag-handle',
            onEnd: function () {
                savePositionOrder(listId, positionValue);
            }
        });

        // Also init nested children
        el.querySelectorAll('.sortable-list').forEach(function (childUl) {
            new Sortable(childUl, {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                handle: '.drag-handle',
            });
        });
    }

    function savePositionOrder(listId, positionValue) {
        const ul = document.getElementById(listId);
        if (!ul) return;

        const ids = [];
        ul.querySelectorAll(':scope > li[data-id]').forEach(function (li) {
            ids.push(li.dataset.id);
        });

        axios.post('{{ route("cms.menu.reorder-position") }}', {
            ids: ids,
            position: positionValue
        })
        .then(function () {
            showSuccessMessage('Urutan berhasil diperbarui');
        })
        .catch(function () {
            showErrorMessage('Gagal menyimpan urutan');
        });
    }

    initSortable('header-tree', 'header');
    initSortable('footer-tree', 'footer_col_1');
});
</script>
@endpush
