<x-ui.form-modal
    :title="$menu->exists ? 'Edit Menu' : 'Tambah Menu'"
    :route="$menu->exists ? route('public.cms.public-menu.update', $menu->encrypted_menu_id) : route('public.cms.public-menu.store')"
    :method="$menu->exists ? 'PUT' : 'POST'"
>
    
    <x-ui.form-select name="parent_id" label="Parent Menu">
            <option value="">-- Menu Utama (Root) --</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->encrypted_menu_id }}" {{ (old('parent_id') ?? ($menu->parent_id ? $menu->parent->encrypted_menu_id : '')) == $parent->encrypted_menu_id ? 'selected' : '' }}>
                    {{ $parent->title }}
                </option>
            @endforeach
    </x-ui.form-select>

    <x-ui.form-input 
        name="title" 
        label="Judul Menu" 
        :value="$menu->title"
        required
    />

    <x-ui.form-select name="type" id="menu-type-select" label="Tipe Menu" required="true">
            <option value="url" {{ $menu->type == 'url' ? 'selected' : '' }}>URL Eksternal / Link Biasa</option>
            <option value="page" {{ $menu->type == 'page' ? 'selected' : '' }}>Halaman CMS (Public Page)</option>
            <option value="route" {{ $menu->type == 'route' ? 'selected' : '' }}>Route Internal App</option>
    </x-ui.form-select>

    <div id="type-url-group" class="mb-3" style="display: none;">
        <x-ui.form-input 
            name="url" 
            label="URL / Link" 
            :value="$menu->url"
            placeholder="https://example.com atau /path/to/link"
        />
    </div>

    <div id="type-page-group" class="mb-3" style="display: none;">
        <x-ui.form-select name="page_id" label="Pilih Halaman" required="true">
            <option value="">-- Pilih Halaman --</option>
            @foreach($pages as $page)
                <option value="{{ $page->encrypted_page_id }}" {{ $menu->page_id == $page->page_id ? 'selected' : '' }}>
                    {{ $page->title }}
                </option>
            @endforeach
        </x-ui.form-select>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <x-ui.form-select name="position" label="Posisi" required="true">
                    <option value="header" {{ $menu->position == 'header' ? 'selected' : '' }}>Header (Navbar)</option>
                    <option value="footer_col_1" {{ $menu->position == 'footer_col_1' ? 'selected' : '' }}>Footer Kolom 1</option>
                    <option value="footer_col_2" {{ $menu->position == 'footer_col_2' ? 'selected' : '' }}>Footer Kolom 2</option>
                    <option value="footer_col_3" {{ $menu->position == 'footer_col_3' ? 'selected' : '' }}>Footer Kolom 3</option>
            </x-ui.form-select>
        </div>
        <div class="col-md-6">
             <x-ui.form-select name="target" label="Target" required="true">
                    <option value="_self" {{ $menu->target == '_self' ? 'selected' : '' }}>Tab Sama (_self)</option>
                    <option value="_blank" {{ $menu->target == '_blank' ? 'selected' : '' }}>Tab Baru (_blank)</option>
             </x-ui.form-select>
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" {{ $menu->exists ? ($menu->is_active ? 'checked' : '') : 'checked' }}>
            <span class="form-check-label">Menu Aktif</span>
        </label>
    </div>

    <script>
        // Inline script to handle type change (re-run via Xhr or use global delegated listener if preferred)
        // Since this is loaded via AJAX modal, we execute immediately
        (function() {
            const typeSelect = document.getElementById('menu-type-select');
            const urlGroup = document.getElementById('type-url-group');
            const pageGroup = document.getElementById('type-page-group');
            
            function toggleFields() {
                const val = typeSelect.value;
                if (val === 'page') {
                    urlGroup.style.display = 'none';
                    pageGroup.style.display = 'block';
                } else {
                    urlGroup.style.display = 'block';
                    pageGroup.style.display = 'none';
                    // Change label based on route or url
                    const urlLabel = urlGroup.querySelector('label');
                    if(val === 'route') {
                        urlLabel.innerText = "Nama Route (contoh: login)";
                    } else {
                        urlLabel.innerText = "URL / Link";
                    }
                }
            }

            if(typeSelect) {
                typeSelect.addEventListener('change', toggleFields);
                toggleFields(); // Init
            }
        })();
    </script>

</x-ui.form-modal>
