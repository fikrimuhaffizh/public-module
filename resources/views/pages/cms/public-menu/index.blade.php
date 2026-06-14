@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Manajemen Halaman & Navigasi" pretitle="Content Management">
    <x-slot:actions>
        <div class="d-flex gap-2">
            <x-ui.button href="{{ route('public.cms.page.create') }}" type="create" id="btn-add-page" text="Buat Halaman Baru" />
            <x-ui.button
                type="create"
                href="#"
                class="ajax-modal-btn d-none"
                data-url="{{ route('public.cms.menu.create') }}"
                id="btn-add-menu"
                text="Tambah Menu"
            />
        </div>
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <x-ui.card>
        <x-ui.card-header>
            <ul class="nav nav-tabs card-header-tabs" id="top-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a href="#tabs-page" class="nav-link active" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-file-text me-2"></i> Daftar Halaman
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-menu" class="nav-link" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-menu-2 me-2"></i> Struktur Menu
                    </a>
                </li>
            </ul>
        </x-ui.card-header>
        <x-ui.card-body>
            <div class="tab-content">
                {{-- TAB 1: MANAJEMEN HALAMAN (DEFAULT) --}}
                <div class="tab-pane active show" id="tabs-page" role="tabpanel">
                    <div>
                        <x-ui.datatable-toolbar dataTableId="pages-table" :filter="false" />
                        <x-ui.datatable
                            id="pages-table"
                            route="{{ route('public.cms.page.data') }}"
                            :columns="[
                                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'No', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                                ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
                                ['data' => 'slug', 'name' => 'slug', 'title' => 'Slug'],
                                ['data' => 'is_published', 'name' => 'is_published', 'title' => 'Status'],
                                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Terakhir Update'],
                                ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false, 'class' => 'text-center'],
                            ]"
                        />
                    </div>
                </div>

                {{-- TAB 2: MANAJEMEN MENU --}}
                <div class="tab-pane" id="tabs-menu" role="tabpanel">
                    @if($orgUnits->isEmpty())
                        <x-ui.empty-state
                            title="Belum ada Menu"
                            text="Silakan tambahkan menu baru."
                            icon="ti ti-menu-2"
                        />
                    @else
                        <ul class="list-group list-group-flush sortable-list" id="menu-tree">
                            @foreach($orgUnits as $menu)
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
        border: 1px solid rgba(101, 109, 119, 0.16);
        margin-bottom: 5px;
        border-radius: 4px;
        background: #fff;
    }
    .sortable-list .sortable-list {
        margin-left: 20px;
        margin-top: 5px;
        border: none;
        background: transparent;
    }
    .sortable-list .sortable-list .list-group-item {
        background: #fcfdfe;
    }
    .cursor-move {
        cursor: move;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- BUTTON TOGGLE LOGIC ---
    const btnAddMenu = document.getElementById('btn-add-menu');
    const btnAddPage = document.getElementById('btn-add-page');

    function updateButtons(href) {
        if (href === '#tabs-menu') {
            if(btnAddMenu) btnAddMenu.classList.remove('d-none');
            if(btnAddPage) btnAddPage.classList.add('d-none');
        } else {
            if(btnAddMenu) btnAddMenu.classList.add('d-none');
            if(btnAddPage) btnAddPage.classList.remove('d-none');
        }
    }

    // Listen for tab changes to update buttons
    document.addEventListener('shown.bs.tab', function (e) {
        if (e.target.closest('#top-tabs')) {
            updateButtons(e.target.getAttribute('href'));
        }
    });

    // Initial button state based on active tab (restored by global helper)
    setTimeout(() => {
        const activeTab = document.querySelector('#top-tabs .nav-link.active');
        if (activeTab) updateButtons(activeTab.getAttribute('href'));
    }, 200);

    // --- SORTABLE LOGIC (Menu) ---
    const nestedSortables = [].slice.call(document.querySelectorAll('.sortable-list'));

    function getHierarchyFromUl(ul) {
        const items = [];
        Array.from(ul.children).forEach(li => {
            const id = li.dataset.id;
            if (!id) return;

            const item = { id: id };
            const nestedUl = li.querySelector(':scope > .sortable-list');
            if (nestedUl && nestedUl.children.length > 0) {
                item.children = getHierarchyFromUl(nestedUl);
            }
            items.push(item);
        });
        return items;
    }

    function saveHierarchy() {
        const rootUl = document.getElementById('menu-tree');
        if(!rootUl) return;

        const hierarchy = getHierarchyFromUl(rootUl);

        axios.post('{{ route("public.cms.menu.reorder") }}', { hierarchy: hierarchy })
            .then(response => {
                showSuccessMessage('Urutan menu berhasil diperbarui');
            })
            .catch(error => {
                console.error('Failed to save hierarchy', error);
                showErrorMessage('Gagal menyimpan urutan');
            });
    }

    nestedSortables.forEach(function (el) {
        new Sortable(el, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.drag-handle',
            onEnd: function (evt) {
                saveHierarchy();
            }
        });
    });
});
</script>
@endpush
