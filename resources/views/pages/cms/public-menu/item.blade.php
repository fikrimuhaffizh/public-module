<li class="list-group-item" data-id="{{ $menu->encrypted_menu_id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="drag-handle cursor-move me-2 text-muted">
                <i class="ti ti-grip-vertical"></i>
            </span>
            <div>
                <span class="fw-bold">{{ $menu->title }}</span>
                <div class="text-muted small">
                    @if($menu->type === 'url')
                        <i class="ti ti-link me-1"></i> {{ $menu->url }}
                    @elseif($menu->type === 'page')
                        <i class="ti ti-file-text me-1"></i> Page: {{ $menu->page->title ?? 'N/A' }}
                    @elseif($menu->type === 'route')
                        <i class="ti ti-sign-right me-1"></i> Route: {{ $menu->url }}
                    @endif
                    
                    @if(!$menu->is_active)
                        <span class="badge bg-secondary-lt ms-2">Draft</span>
                    @endif
                    
                    <span class="badge bg-azure-lt ms-1">{{ ucfirst($menu->position) }}</span>
                </div>
            </div>
        </div>
        <x-ui.dropdown class="btn btn-action text-secondary">
            <x-ui.dropdown-item
                type="edit"
                href="javascript:void(0)"
                :url="route('public.cms.menu.edit', $menu->encrypted_menu_id)"
                data-modal-title="Edit Menu"
            />
            <x-ui.dropdown-item
                type="delete"
                href="javascript:void(0)"
                :url="route('public.cms.menu.destroy', $menu->encrypted_menu_id)"
                title="Hapus Menu?"
            />
        </x-ui.dropdown>
    </div>

    <ul class="list-group list-group-flush sortable-list mt-2">
        @foreach($menu->children as $child)
            @include('public::pages.cms.public-menu.item', ['menu' => $child])
        @endforeach
    </ul>
</li>
