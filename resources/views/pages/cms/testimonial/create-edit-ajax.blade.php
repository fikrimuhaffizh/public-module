<x-ui.form-modal
    :title="$testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni'"
    :route="$testimonial->exists ? route('public.cms.testimonial.update', $testimonial) : route('public.cms.testimonial.store')"
    :method="$testimonial->exists ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
>
    <x-ui.form-input name="name" label="Nama" :value="$testimonial->name" required />
    <div class="row">
        <div class="col-md-6"><x-ui.form-input name="position" label="Jabatan / Peran" :value="$testimonial->position" /></div>
        <div class="col-md-6"><x-ui.form-input name="organization" label="Instansi" :value="$testimonial->organization" /></div>
    </div>
    <x-ui.form-textarea name="quote" label="Isi Testimoni" required>{{ $testimonial->quote }}</x-ui.form-textarea>
    <x-ui.form-select name="rating" label="Rating">
        @foreach(range(5, 1) as $rating)
            <option value="{{ $rating }}" @selected((int) old('rating', $testimonial->rating ?: 5) === $rating)>{{ $rating }} Bintang</option>
        @endforeach
    </x-ui.form-select>
    <x-ui.form-input name="photo" type="file" label="Foto" accept="image/*" help="Opsional. Maksimal 2 MB, disarankan foto persegi." />
    <label class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $testimonial->exists ? $testimonial->is_active : true))>
        <span class="form-check-label">Tampilkan di landing page</span>
    </label>
</x-ui.form-modal>
