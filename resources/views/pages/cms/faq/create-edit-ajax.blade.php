<x-ui.form-modal
    :title="$faq->exists ? 'Edit FAQ' : 'Tambah FAQ'"
    :route="$faq->exists ? route('public.cms.faq.update', $faq->encrypted_faq_id) : route('public.cms.faq.store')"
    :method="$faq->exists ? 'PUT' : 'POST'"
>
    <x-ui.form-input 
        name="question" 
        label="Pertanyaan" 
        value="{{ $faq->question }}"
        required="true"
    />

    <div class="mb-3">
        <x-ui.form-textarea 
            name="answer" 
            id="answer"
            label="Jawaban" 
            height="300"
            required="true"
        >{{ $faq->answer }}</x-ui.form-textarea>
    </div>

    <div class="mb-3">
        <x-ui.form-input 
            name="category" 
            label="Kategori (Opsional)" 
            value="{{ $faq->category }}"
        />
    </div>

    <div class="mt-3">
        <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" {{ $faq->exists ? ($faq->is_active ? 'checked' : '') : 'checked' }}>
            <span class="form-check-label">Aktifkan FAQ</span>
        </label>
    </div>
</x-ui.form-modal>

<script>
    if (typeof window.loadHugeRTE === 'function') {
        window.loadHugeRTE('#answer', {
            height: 300,
            setup: function (editor) {
                editor.on('change input blur', function () {
                    editor.save();
                });
            }
        });
    }
</script>
