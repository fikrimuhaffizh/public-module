@extends('layouts.' . active_theme() . '.app')

@section('title', 'Kontak & SEO')

@section('header')
<x-ui.page-header title="Kontak & SEO" pretitle="Landing Page">
    <x-slot:actions>
        @can('public.cms.settings.update')
        <button type="submit" form="settings-form" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i>Simpan
        </button>
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<form id="settings-form" action="{{ route('cms.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <x-ui.card>
        <x-ui.card-header>
            <ul class="nav nav-tabs card-header-tabs" id="settings-tabs" data-bs-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a href="#tab-kontak" class="nav-link active" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-phone me-2"></i>Kontak
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tab-sosial" class="nav-link" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-share me-2"></i>Media Sosial
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tab-seo" class="nav-link" data-bs-toggle="tab" role="tab">
                        <i class="ti ti-search me-2"></i>SEO
                    </a>
                </li>
            </ul>
        </x-ui.card-header>
        <x-ui.card-body>
            <div class="tab-content">

                {{-- TAB 1: KONTAK --}}
                <div class="tab-pane active show" id="tab-kontak" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input name="whatsapp" label="WhatsApp" value="{{ old('whatsapp', $settings->whatsapp) }}" help="Cth: 6281234567890" />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="contact_phone" label="Telepon" value="{{ old('contact_phone', $settings->contact_phone) }}" />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="contact_email" type="email" label="Email" value="{{ old('contact_email', $settings->contact_email) }}" />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="address" label="Alamat" value="{{ old('address', $settings->address) }}" />
                        </div>
                    </div>
                </div>

                {{-- TAB 2: MEDIA SOSIAL --}}
                <div class="tab-pane" id="tab-sosial" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-ui.form-input name="facebook_url" label="Facebook" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/..." />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="instagram_url" label="Instagram" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/..." />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="linkedin_url" label="LinkedIn" value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/..." />
                        </div>
                        <div class="col-md-6">
                            <x-ui.form-input name="youtube_url" label="YouTube" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/..." />
                        </div>
                    </div>
                </div>

                {{-- TAB 3: SEO --}}
                <div class="tab-pane" id="tab-seo" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <x-ui.form-input name="meta_title" label="Meta Title" value="{{ old('meta_title', $settings->meta_title) }}" help="Maks 191 karakter." />
                        </div>
                        <div class="col-12">
                            <x-ui.form-input name="meta_keywords" label="Meta Keywords" value="{{ old('meta_keywords', $settings->meta_keywords) }}" help="Pisahkan dengan koma." />
                        </div>
                        <div class="col-12">
                            <x-ui.form-input name="meta_description" label="Meta Description" value="{{ old('meta_description', $settings->meta_description) }}" help="Maks 500 karakter." />
                        </div>
                    </div>
                </div>

            </div>
        </x-ui.card-body>
    </x-ui.card>
</form>
@endsection
