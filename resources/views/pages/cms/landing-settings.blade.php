@extends('public::layouts.public-layout')

@section('title', 'Pengaturan Landing Page')

@section('header')
<x-ui.page-header title="Pengaturan" pretitle="Landing Page">
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
<form id="settings-form" action="{{ route('cms.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-3">

        {{-- Kiri: Informasi Situs + Media Sosial --}}
        <div class="col-md-6 d-flex flex-column gap-3">

            <div class="card">
                <div class="card-header py-2">
                    <h4 class="card-title mb-0"><i class="ti ti-info-circle me-1 text-primary"></i>Informasi Situs</h4>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
                        <div class="col-12">
                            <x-ui.form-input name="site_title" label="Judul Situs" value="{{ old('site_title', $settings->site_title) }}" />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="contact_email" type="email" label="Email" value="{{ old('contact_email', $settings->contact_email) }}" />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="contact_phone" label="Telepon" value="{{ old('contact_phone', $settings->contact_phone) }}" />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="whatsapp" label="WhatsApp" value="{{ old('whatsapp', $settings->whatsapp) }}" help="Cth: 6281234567890" />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="address" label="Alamat" value="{{ old('address', $settings->address) }}" />
                        </div>
                        <div class="col-12">
                            <x-ui.form-input name="site_description" label="Deskripsi" value="{{ old('site_description', $settings->site_description) }}" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-2">
                    <h4 class="card-title mb-0"><i class="ti ti-share me-1 text-primary"></i>Media Sosial</h4>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
                        <div class="col-6">
                            <x-ui.form-input name="facebook_url" label="Facebook" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/..." />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="instagram_url" label="Instagram" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/..." />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="linkedin_url" label="LinkedIn" value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/..." />
                        </div>
                        <div class="col-6">
                            <x-ui.form-input name="youtube_url" label="YouTube" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/..." />
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kanan: SEO --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header py-2">
                    <h4 class="card-title mb-0"><i class="ti ti-search me-1 text-primary"></i>SEO</h4>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
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
        </div>

    </div>
</form>
@endsection

