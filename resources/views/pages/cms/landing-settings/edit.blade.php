@extends('layouts.tabler.app')

@section('title', 'Template Landing Page')

@section('header')
<x-ui.page-header title="Template Landing Page" pretitle="CMS">
    <x-slot:actions>
        <a href="{{ route('public.preview', ['template' => $selectedTemplate]) }}" target="_blank" class="btn btn-outline-primary">
            <i class="ti ti-eye me-1"></i>Pratinjau
        </a>
        @can('public.cms.update')
            <x-ui.button type="submit" form="landing-template-form" text="Simpan Template" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
    <i class="ti ti-info-circle fs-2 me-2"></i>
    <div>Pilih salah satu template, lalu klik <strong>Simpan Template</strong>. Pratinjau tidak mengubah template yang sedang digunakan.</div>
</div>
<form id="landing-template-form" method="POST" action="{{ route('public.cms.landing.update') }}">
    @csrf
    @method('PUT')
    <div class="row row-cards">
        @foreach($templates as $template)
            @php
                $details = match($template) {
                    'institutional' => ['Institusional', 'Tampilan terpercaya dan formal untuk profil institusi.', 'building-bank'],
                    'modern' => ['Modern', 'Visual progresif dengan pengalaman digital yang dinamis.', 'sparkles'],
                    'editorial' => ['Editorial', 'Berorientasi konten dengan tipografi dan berita yang kuat.', 'news'],
                    'corporate' => ['Corporate', 'Tampilan mewah dan elegan untuk institusi serta mitra korporat.', 'building-skyscraper'],
                    'launch' => ['Launch UI', 'Desain segar dengan hero, fitur, produk, statistik, dan CTA yang dapat dikelola penuh.', 'rocket'],
                    'custom' => ['Custom', 'Template sepenuhnya dapat dikustomisasi dengan drag & drop sections.', 'settings'],
                };
            @endphp
            @php($isSelected = old('landing_template', $selectedTemplate) === $template)
            <div class="col-md-6 col-xl-3">
                <label class="card card-link card-link-pop h-100 cursor-pointer {{ $isSelected ? 'border-primary border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <span class="avatar avatar-lg bg-primary-lt text-primary"><i class="ti ti-{{ $details[2] }} fs-1"></i></span>
                            <input class="form-check-input" type="radio" name="landing_template" value="{{ $template }}" @checked($isSelected) @cannot('public.cms.update') disabled @endcannot>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h3 class="card-title mb-0">{{ $details[0] }}</h3>
                            @if($isSelected)
                                <span class="badge bg-primary-lt">Sedang digunakan</span>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ $details[1] }}</p>
                        <a href="{{ route('public.preview', ['template' => $template]) }}" target="_blank" class="text-primary" onclick="event.stopPropagation()">Lihat pratinjau <i class="ti ti-arrow-up-right ms-1"></i></a>
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</form>
@endsection
