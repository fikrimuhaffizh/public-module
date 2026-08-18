@extends('public::layouts.public-layout')

@section('content')
        <div class="row row-cards">
            <!-- Live Preview Landing -->
            <div class="col-12">
                <x-ui.card class="border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <x-ui.card-header class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
                        <div>
                            <h3 class="card-title mb-0"><i class="ti ti-device-desktop me-2 text-primary"></i> Preview Halaman Depan</h3>
                            <div class="text-muted small">Pratinjau versi customize (/preview) — ubah tema, section, dan warna langsung dari sini.</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="const pf=document.getElementById('landing-preview-frame');pf.src=pf.src.split('?')[0]+'?ts='+Date.now()" title="Muat ulang pratinjau">
                                <i class="ti ti-refresh me-1"></i>Muat Ulang
                            </button>
                            <a href="{{ route('public.preview') }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Buka preview di tab baru">
                                <i class="ti ti-external-link me-1"></i>Tab Baru
                            </a>
                            <a href="{{ route('public.preview') }}" class="btn btn-sm btn-primary" title="Customize tampilan (tema, section, warna)">
                                <i class="ti ti-adjustments me-1"></i>Customize
                            </a>
                        </div>
                    </x-ui.card-header>
                    <x-ui.card-body class="p-2">
                        <iframe
                            id="landing-preview-frame"
                            src="{{ route('public.preview') }}"
                            class="w-100 border rounded"
                            style="height: calc(100vh - 260px); min-height: 480px; background: #fff;"
                            loading="lazy"
                            title="Preview halaman depan landing page"
                        ></iframe>
                    </x-ui.card-body>
                </x-ui.card>
            </div>
        </div>
@endsection
