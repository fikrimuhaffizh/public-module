<x-ui.form-modal
    :title="$statistic->exists ? 'Edit Statistik' : 'Tambah Statistik'"
    :route="$statistic->exists ? route('cms.statistic.update', $statistic) : route('cms.statistic.store')"
    :method="$statistic->exists ? 'PUT' : 'POST'"
>
    <x-ui.form-input name="label" label="Label" :value="$statistic->label" placeholder="Kampus Mitra" required />
    <x-ui.form-input name="value" label="Nilai" :value="$statistic->value" placeholder="25 atau 15.000+" required />
    <x-ui.form-input name="icon" label="Icon Class" :value="$statistic->icon" placeholder="ti ti-building-community" />
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $statistic->exists ? $statistic->is_active : true))>
        <span class="form-check-label">Tampilkan di landing page</span>
    </label>
</x-ui.form-modal>
