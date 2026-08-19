@extends('public::layouts.public-layout')

@section('header')
<x-ui.page-header :title="$page->exists ? 'Edit Halaman' : 'Buat Halaman Custom'" pretitle="Website Builder">
    <x-slot:actions>
        <x-ui.button type="back" class="d-none d-sm-inline-block" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
    <form action="{{ $page->exists ? route('cms.builder.pages.update', $page->encrypted_page_id) : route('cms.builder.pages.store') }}" method="POST" class="ajax-form">
        @csrf
        @if($page->exists)
            @method('PUT')
        @endif

        <div class="row row-cards">
            <div class="col-lg-8">
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="card-title">Informasi Halaman</h3>
                    </x-ui.card-header>
                    <x-ui.card-body>
                        <x-ui.form-input
                            name="title"
                            label="Nama Halaman"
                            :value="old('title', $page->title)"
                            placeholder="Contoh: Landing Produk"
                            required
                        />

                        <div class="mb-3">
                            <x-ui.form-input
                                name="slug"
                                label="Slug / URL"
                                :value="old('slug', $page->slug)"
                                placeholder="contoh: landing-produk"
                            />
                            <small class="form-hint">
                                URL public: <code>{{ url('/') }}/<span id="slug-preview">{{ $page->slug ?? 'slug-anda' }}</span></code>
                            </small>
                        </div>
                    </x-ui.card-body>
                </x-ui.card>

                @if(!$page->exists)
                    <x-ui.card>
                        <x-ui.card-header>
                            <h3 class="card-title">Pilih Template Awal</h3>
                        </x-ui.card-header>
                        <x-ui.card-body>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-selectgroup form-selectgroup-pills mb-0 w-100">
                                        <input type="radio" name="template_key" value="" class="form-selectgroup-input" checked>
                                        <span class="form-selectgroup-label d-flex flex-column align-items-center py-3">
                                            <i class="ti ti-blank-square fs-2 mb-2"></i>
                                            <span class="fw-bold">Blank</span>
                                            <small class="text-secondary text-center">Mulai dari halaman kosong</small>
                                        </span>
                                    </label>
                                </div>

                                @foreach($templates as $template)
                                    <div class="col-md-6 col-lg-4">
                                        <label class="form-selectgroup form-selectgroup-pills mb-0 w-100">
                                            <input type="radio" name="template_key" value="{{ $template->key }}" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center py-3">
                                                <i class="ti ti-template fs-2 mb-2"></i>
                                                <span class="fw-bold">{{ $template->name }}</span>
                                                <small class="text-secondary text-center">{{ $template->description }}</small>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-hint mt-2 d-block">
                                Template hanya titik awal — setelah dibuat, seluruh markup bisa diedit bebas di editor.
                            </small>
                        </x-ui.card-body>
                    </x-ui.card>
                @endif

                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="card-title">SEO</h3>
                    </x-ui.card-header>
                    <x-ui.card-body>
                        <x-ui.form-input
                            name="seo_title"
                            label="SEO Title"
                            :value="old('seo_title', $page->seo_title)"
                            placeholder="Judul | {{ sys_tenant_name() }}"
                        />

                        <x-ui.form-textarea
                            name="meta_desc"
                            label="Meta Description"
                            :value="old('meta_desc', $page->meta_desc)"
                            rows="3"
                            placeholder="Deskripsi singkat halaman untuk mesin pencari..."
                        />

                        <x-ui.form-input
                            name="meta_keywords"
                            label="Meta Keywords"
                            :value="old('meta_keywords', $page->meta_keywords)"
                            placeholder="kata, kunci, dipisah, koma"
                        />
                    </x-ui.card-body>
                </x-ui.card>
            </div>

            <div class="col-lg-4">
                <x-ui.card>
                    <x-ui.card-body>
                        <div class="mb-3">
                            <label class="form-label">Mode Render</label>
                            <div>
                                <span class="badge {{ $isCustom ? 'bg-purple-lt' : 'bg-blue-lt' }}">{{ $isCustom ? 'Custom (GrapesJS)' : 'Template (React)' }}</span>
                            </div>
                            @if($page->exists)
                                <small class="form-hint">Mode tidak bisa diubah — buat halaman baru untuk mode lain.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            @if($page->exists)
                                @if($page->is_published)
                                    <span class="badge bg-success-lt">Published</span>
                                    <small class="d-block mt-1 text-secondary">Halaman tampil untuk publik di <code>/{{ $page->slug }}</code>.</small>
                                @else
                                    <span class="badge bg-orange-lt">Draft</span>
                                    <small class="d-block mt-1 text-secondary">Halaman belum tampil untuk publik.</small>
                                @endif
                            @else
                                <span class="badge bg-secondary-lt">Draft (by default)</span>
                            @endif
                        </div>

                        @if($page->exists && $page->isCustom())
                            <div class="mb-3">
                                <label class="form-label d-block">Aksi Cepat</label>
                                <div class="btn-list">
                                    <a href="{{ route('cms.builder.pages.editor', $page) }}" class="btn btn-primary w-100">
                                        <i class="ti ti-palette me-1"></i> Buka Editor
                                    </a>
                                    <a href="{{ route('cms.builder.pages.preview', $page) }}" target="_blank" class="btn btn-outline-info w-100">
                                        <i class="ti ti-eye me-1"></i> Preview Halaman
                                    </a>
                                    @if($page->is_published)
                                        <button type="button" class="btn btn-outline-warning w-100 builder-post"
                                                data-method="post"
                                                data-url="{{ route('cms.builder.pages.unpublish', $page) }}"
                                                data-confirm="Hentikan publikasi halaman ini?">
                                            <i class="ti ti-player-pause me-1"></i> Unpublish
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-success w-100 builder-post"
                                                data-method="post"
                                                data-url="{{ route('cms.builder.pages.publish', $page) }}"
                                                data-confirm="Publikasikan halaman ini?">
                                            <i class="ti ti-player-play me-1"></i> Publish
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </x-ui.card-body>
                </x-ui.card>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-device-floppy me-1"></i> {{ $page->exists ? 'Simpan Perubahan' : 'Buat & Buka Editor' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    @vite(['Modules/Public/resources/assets/js/builder-actions.js'])
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.querySelector('input[name="slug"]');
        const slugPreview = document.getElementById('slug-preview');

        if (titleInput && slugInput) {
            let editingSlug = slugInput.value.trim() !== '';

            titleInput.addEventListener('input', function () {
                if (!editingSlug) {
                    slugInput.value = this.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .trim()
                        .replace(/[\s_]+/g, '-')
                        .replace(/-+/g, '-');
                    updateSlugPreview();
                }
            });

            slugInput.addEventListener('input', function () {
                editingSlug = true;
                updateSlugPreview();
            });

            function updateSlugPreview() {
                if (!slugPreview) return;
                slugPreview.textContent = slugInput.value || 'slug-anda';
            }
        }
    });
    </script>
@endpush