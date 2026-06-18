@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header :title="$page->exists ? 'Edit Halaman' : 'Buat Halaman Baru'" pretitle="Content Management">
    <x-slot:actions>
        <x-ui.button type="back" class="d-none d-sm-inline-block" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
        <form action="{{ $page->exists ? route('public.cms.page.update', $page->encrypted_page_id) : route('public.cms.page.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($page->exists)
                @method('PUT')
            @endif

            <div class="row row-cards">
                <div class="col-lg-8">
                    <x-ui.card>
                        <x-ui.card-body>
                            <x-ui.form-input
                                name="title"
                                label="Judul Halaman"
                                :value="$page->title"
                                placeholder="Masukkan judul halaman..."
                                required
                            />

                            <div class="mb-3">
                                <x-ui.form-textarea
                                    name="content"
                                    id="content"
                                    label="Konten"
                                    rows="20"
                                    :value="$page->content"
                                />
                            </div>
                        </x-ui.card-body>
                    </x-ui.card>
                </div>
                
                <div class="col-lg-4">
                    <x-ui.card>
                        <x-ui.card-body>
                            <div class="mb-3">
                                <label class="form-label">Posisi Menu</label>
                                <select name="menu_position" class="form-select">
                                    <option value="header" {{ ($linkedMenu->position ?? 'header') === 'header' ? 'selected' : '' }}>Header (Navbar)</option>
                                    <option value="footer_col_1" {{ ($linkedMenu->position ?? '') === 'footer_col_1' ? 'selected' : '' }}>Footer Kolom 1</option>
                                    <option value="footer_col_2" {{ ($linkedMenu->position ?? '') === 'footer_col_2' ? 'selected' : '' }}>Footer Kolom 2</option>
                                    <option value="footer_col_3" {{ ($linkedMenu->position ?? '') === 'footer_col_3' ? 'selected' : '' }}>Footer Kolom 3</option>
                                </select>
                                <small class="form-hint">Tentukan di mana item ini tampil di navigasi publik.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target Link</label>
                                <select name="menu_target" class="form-select">
                                    <option value="_self" {{ ($linkedMenu->target ?? '_self') === '_self' ? 'selected' : '' }}>Tab Sama (_self)</option>
                                    <option value="_blank" {{ ($linkedMenu->target ?? '') === '_blank' ? 'selected' : '' }}>Tab Baru (_blank)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="is_published" value="1" class="form-selectgroup-input" {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label text-success">
                                            <i class="ti ti-check me-1"></i> Published
                                        </span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="is_published" value="0" class="form-selectgroup-input" {{ !old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
                                        <span class="form-selectgroup-label text-warning">
                                            <i class="ti ti-file me-1"></i> Draft
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <x-ui.form-input
                                    name="main_image"
                                    type="file"
                                    label="Gambar Utama"
                                    accept="image/*"
                                    class="filepond-input"
                                    help="Maksimal 5MB. Format: JPG, PNG, WEBP."
                                />
                            </div>

                            <div class="mb-3">
                                <x-ui.form-input
                                    name="attachments[]"
                                    type="file"
                                    label="File Pendukung"
                                    multiple
                                    class="filepond-input"
                                    help="Maksimal 10MB per file. Bisa upload banyak file sekaligus."
                                />
                            </div>

                             <x-ui.form-textarea
                                name="meta_desc"
                                label="Meta Description (SEO)"
                                :value="$page->meta_desc"
                                rows="3"
                            />

                            <x-ui.form-input
                                name="meta_keywords"
                                label="Meta Keywords (SEO)"
                                :value="$page->meta_keywords"
                                placeholder="Keyword 1, Keyword 2..."
                            />

                            <div class="mt-4">
                                <x-ui.button
                                    type="submit"
                                    class="w-100"
                                    text="Simpan Halaman"
                                />
                            </div>
                        </x-ui.card-body>
                    </x-ui.card>
                </div>
            </div>
        </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.loadHugeRTE === 'function') {
        window.loadHugeRTE('#content', {
            height: 600,
            plugins: 'lists link image code codesample table fullscreen searchreplace',
            toolbar: 'undo redo | styles | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image table | codesample code | fullscreen',
            codesample_languages: [
                { text: 'HTML/XML', value: 'markup' },
                { text: 'JavaScript', value: 'javascript' },
                { text: 'CSS', value: 'css' },
                { text: 'PHP', value: 'php' },
                { text: 'Python', value: 'python' },
                { text: 'Java', value: 'java' },
                { text: 'C', value: 'c' },
                { text: 'C++', value: 'cpp' },
                { text: 'SQL', value: 'sql' },
                { text: 'Bash', value: 'bash' },
                { text: 'JSON', value: 'json' },
                { text: 'Markdown', value: 'markdown' },
            ],
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }
});
</script>
@endpush
