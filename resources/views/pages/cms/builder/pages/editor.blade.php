@extends('public::layouts.public-layout')

@section('title')
    {{ $page->title }} — Website Builder
@endsection

@push('styles')
    @vite(['Modules/Public/resources/assets/css/builder-editor.css'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('builder-editor');
        });
    </script>
@endpush

@section('content')
    <div id="builder-editor-root">
        <div class="be-toolbar">
            <div class="be-tb-left">
                <a href="{{ route('cms.builder.pages.index') }}" class="be-btn be-btn-icon" title="Kembali ke daftar halaman">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <div class="be-tb-title">
                    <span class="be-title">{{ $page->title }}</span>
                    <span class="be-subtitle">/{{ $page->slug }}</span>
                </div>
                <span class="be-divider"></span>
                <span id="be-status" class="be-status be-status-idle">Siap</span>
                <button type="button" id="be-pick-section" class="be-btn be-btn-outline" title="Pilih band blok (elemen teratas) agar ganti background-nya menjangkau seluruh band">
                    <i class="ti ti-frame"></i><span>Pilih Band</span>
                </button>
            </div>

            <div class="be-tb-right">
                <button type="button" id="be-undo" class="be-btn be-btn-icon" title="Undo (Ctrl+Z)">
                    <i class="ti ti-arrow-back-up"></i>
                </button>
                <button type="button" id="be-redo" class="be-btn be-btn-icon" title="Redo (Ctrl+Y)">
                    <i class="ti ti-arrow-forward-up"></i>
                </button>
                <button type="button" id="be-clear" class="be-btn be-btn-icon" title="Kosongkan kanvas">
                    <i class="ti ti-trash"></i>
                </button>

                <span class="be-divider"></span>

                <div class="be-device-toggle" role="group" aria-label="Device preview">
                    <button type="button" id="be-device-desktop" class="be-device-btn active" title="Desktop (Full width)" data-device="Desktop">
                        <i class="ti ti-device-desktop"></i>
                    </button>
                    <button type="button" id="be-device-tablet" class="be-device-btn" title="Tablet (768px)" data-device="Tablet">
                        <i class="ti ti-device-tablet"></i>
                    </button>
                    <button type="button" id="be-device-mobile" class="be-device-btn" title="Mobile (375px)" data-device="Mobile portrait">
                        <i class="ti ti-device-mobile"></i>
                    </button>
                </div>

                <span class="be-divider"></span>

                <a href="{{ route('cms.builder.pages.preview', $page) }}" target="_blank" class="be-btn" title="Buka preview di tab baru">
                    <i class="ti ti-eye"></i><span>Preview</span>
                </a>

                <button type="button" id="be-save" class="be-btn be-btn-primary" title="Simpan (Ctrl+S)">
                    <i class="ti ti-device-floppy"></i><span>Simpan</span>
                </button>

                @if($page->is_published)
                    <button type="button" id="be-publish" data-action="unpublish" class="be-btn be-btn-warning" title="Hentikan publikasi halaman">
                        <i class="ti ti-player-pause"></i><span>Unpublish</span>
                    </button>
                @else
                    <button type="button" id="be-publish" data-action="publish" class="be-btn be-btn-success" title="Publikasikan halaman">
                        <i class="ti ti-player-play"></i><span>Publish</span>
                    </button>
                @endif
            </div>
        </div>

        <div id="gjs"></div>
    </div>
@endsection

@push('scripts')
    <script>
        window.__BUILDER_CONFIG = @json($editorConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    </script>
    @vite(['Modules/Public/resources/assets/js/builder-editor.js'])
@endpush
