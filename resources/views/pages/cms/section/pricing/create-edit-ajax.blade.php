<x-ui.form-modal
    :title="$pricing->exists ? 'Edit Paket Harga' : 'Tambah Paket Harga'"
    :route="$pricing->exists ? route('cms.pricing.update', $pricing) : route('cms.pricing.store')"
    :method="$pricing->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama Paket" :value="$pricing->name" required placeholder="mis. Starter, Pro, Enterprise" />
    <x-ui.form-input name="slug" label="Slug" :value="$pricing->slug" placeholder="Auto dari nama jika kosong" />

    <div class="row g-2">
        <div class="col-md-6">
            <x-ui.form-input name="price" label="Harga" :value="$pricing->price" required placeholder="99.000" help="Tanpa simbol Rp" />
        </div>
        <div class="col-md-6">
            <x-ui.form-input name="period" label="Periode" :value="$pricing->period" placeholder="bulan, tahun, sekali" />
        </div>
    </div>

    <x-ui.form-input name="description" type="textarea" label="Deskripsi Singkat" :value="$pricing->description" rows="2" placeholder="Paket untuk pemula yang baru memulai" />

    <div class="mb-3">
        <label class="form-label">Daftar Fitur</label>
        <div id="features-list">
            @if($pricing->features && count($pricing->features))
                @foreach($pricing->features as $i => $feature)
                    <div class="input-group mb-2">
                        <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="mis. 100 MB storage">
                        <button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>
                    </div>
                @endforeach
            @else
                <div class="input-group mb-2">
                    <input type="text" name="features[]" class="form-control" placeholder="mis. 100 MB storage">
                    <button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>
                </div>
            @endif
        </div>
        <button type="button" class="btn btn-ghost-primary btn-sm" id="add-feature"><i class="ti ti-plus"></i> Tambah Fitur</button>
    </div>

    <div class="row g-2">
        <div class="col-md-6">
            <label class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" name="highlight" value="1" @checked(old('highlight', $pricing->exists ? $pricing->highlight : false))>
                <span class="form-check-label">Paket Unggulan</span>
            </label>
        </div>
        <div class="col-md-6">
            <label class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $pricing->exists ? $pricing->is_active : true))>
                <span class="form-check-label">Aktif</span>
            </label>
        </div>
    </div>

    <x-ui.form-input name="sort_order" type="number" label="Urutan" :value="$pricing->sort_order ?? 0" />
</x-ui.form-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-feature')?.addEventListener('click', function() {
        const list = document.getElementById('features-list');
        const row = document.createElement('div');
        row.className = 'input-group mb-2';
        row.innerHTML = '<input type="text" name="features[]" class="form-control" placeholder="mis. 100 MB storage"><button type="button" class="btn btn-ghost-danger remove-feature" title="Hapus"><i class="ti ti-x"></i></button>';
        list.appendChild(row);
    });
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-feature')) {
            const row = e.target.closest('.input-group');
            const list = document.getElementById('features-list');
            if (list.children.length > 1) row.remove();
            else row.querySelector('input').value = '';
        }
    });
});
</script>
