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
        <div class="btn-list">
            <x-ui.button 
                type="button" 
                class="btn-icon btn-ghost-primary ajax-modal-btn" 
                data-url="{{ route('public.cms.public-menu.edit', $menu->encrypted_menu_id) }}"
                data-modal-title="Edit Menu"
                icon="ti ti-pencil" 
            />
            <x-ui.button 
                type="button" 
                class="btn-icon btn-ghost-danger ajax-delete" 
                data-url="{{ route('public.cms.public-menu.destroy', $menu->encrypted_menu_id) }}"
                data-title="Hapus Menu?"
                icon="ti ti-trash" 
            />
        </div>
    </div>

    <ul class="list-group list-group-flush sortable-list mt-2">
        @foreach($menu->children as $child)
            @include('pages.admin.cms.public-menu.item', ['menu' => $child])
        @endforeach
    </ul>
</li>
