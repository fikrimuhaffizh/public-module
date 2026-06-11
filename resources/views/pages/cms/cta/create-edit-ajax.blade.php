<x-ui.form-modal
    :title="$cta->exists ? 'Edit CTA' : 'Tambah CTA'"
    :route="$cta->exists ? route('public.cms.cta.update', $cta) : route('public.cms.cta.store')"
    :method="$cta->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="title" label="Judul" :value="$cta->title" required />
    <x-ui.form-input name="description" type="textarea" label="Deskripsi" :value="$cta->description" rows="3" />
    <x-ui.form-input name="button_text" label="Teks Tombol" :value="$cta->button_text" />
    <x-ui.form-input name="button_link" label="Link Tombol" :value="$cta->button_link" placeholder="https://..." />
    <x-ui.form-input name="background_image" type="file" label="Background Image" accept="image/png,image/jpeg,image/webp" />
    @if($cta->background_image_url)
        <div class="mb-3"><img src="{{ $cta->background_image_url }}" alt="Preview" class="rounded border w-100" style="max-height:120px;object-fit:cover"></div>
    @endif
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $cta->exists ? $cta->is_active : false))>
        <span class="form-check-label">Jadikan CTA aktif (nonaktifkan CTA lain)</span>
    </label>
</x-ui.form-modal>
