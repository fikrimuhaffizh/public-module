@extends('public::layouts.public-layout')

@section('title', 'Media Sosial')

@section('header')
<x-ui.page-header title="Media Sosial" pretitle="Landing Page">
    <x-slot:actions>
        @can('public.cms.settings.update')
        <button type="submit" form="media-social-form" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i>Simpan
        </button>
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<form id="media-social-form" action="{{ route('cms.media-social.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">

        {{-- Kontak: WhatsApp, telepon, email, alamat --}}
        <div class="col-md-6 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-header py-2">
                    <h4 class="card-title mb-0"><i class="ti ti-phone me-1 text-primary"></i>Kontak</h4>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
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
            </div>
        </div>

        {{-- Media sosial: FB, IG, LinkedIn, YouTube --}}
        <div class="col-md-6 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-header py-2">
                    <h4 class="card-title mb-0"><i class="ti ti-share me-1 text-primary"></i>Media Sosial</h4>
                </div>
                <div class="card-body py-2">
                    <div class="row g-2">
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
            </div>
        </div>

    </div>
</form>
@endsection
