@extends('layouts.tabler.app')

@section('title', 'Pengaturan Landing Page')

@section('header')
<x-ui.page-header title="Pengaturan Landing Page" pretitle="Content Management">
    <x-slot:actions>
        @can('public.cms.settings.update')
            <x-ui.button type="submit" form="landing-settings-form" text="Simpan Pengaturan" />
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<form id="landing-settings-form" method="POST" action="{{ route('public.cms.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row row-cards">
        <div class="col-lg-8">
            <x-ui.card class="mb-3">
                <x-ui.card-header><h3 class="card-title">SEO</h3></x-ui.card-header>
                <x-ui.card-body>
                    <x-ui.form-input name="meta_title" label="Meta Title" :value="old('meta_title', $settings->meta_title)" />
                    <x-ui.form-input name="meta_description" type="textarea" label="Meta Description" :value="old('meta_description', $settings->meta_description)" rows="2" />
                    <x-ui.form-input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $settings->meta_keywords)" placeholder="kampus, digital, pemutu" />
                </x-ui.card-body>
            </x-ui.card>
            <x-ui.card class="mb-3">
                <x-ui.card-header><h3 class="card-title">Kontak</h3></x-ui.card-header>
                <x-ui.card-body>
                    <div class="row">
                        <div class="col-md-6"><x-ui.form-input name="contact_email" label="Email" :value="old('contact_email', $settings->contact_email)" /></div>
                        <div class="col-md-6"><x-ui.form-input name="contact_phone" label="Telepon" :value="old('contact_phone', $settings->contact_phone)" /></div>
                        <div class="col-md-6"><x-ui.form-input name="whatsapp" label="WhatsApp" :value="old('whatsapp', $settings->whatsapp)" /></div>
                    </div>
                    <x-ui.form-input name="address" type="textarea" label="Alamat" :value="old('address', $settings->address)" rows="2" />
                </x-ui.card-body>
            </x-ui.card>
            <x-ui.card>
                <x-ui.card-header><h3 class="card-title">Media Sosial</h3></x-ui.card-header>
                <x-ui.card-body>
                    <div class="row">
                        <div class="col-md-6"><x-ui.form-input name="facebook_url" label="Facebook" :value="old('facebook_url', $settings->facebook_url)" /></div>
                        <div class="col-md-6"><x-ui.form-input name="instagram_url" label="Instagram" :value="old('instagram_url', $settings->instagram_url)" /></div>
                        <div class="col-md-6"><x-ui.form-input name="linkedin_url" label="LinkedIn" :value="old('linkedin_url', $settings->linkedin_url)" /></div>
                        <div class="col-md-6"><x-ui.form-input name="youtube_url" label="YouTube" :value="old('youtube_url', $settings->youtube_url)" /></div>
                    </div>
                </x-ui.card-body>
            </x-ui.card>
        </div>
        <div class="col-lg-4">
            <x-ui.card>
                <x-ui.card-header><h3 class="card-title">Branding</h3></x-ui.card-header>
                <x-ui.card-body>
                    <x-ui.form-input name="logo" type="file" label="Logo" accept="image/png,image/jpeg,image/webp" />
                    @if($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="Logo" class="rounded border mb-3" style="max-height:80px">
                    @endif
                    <x-ui.form-input name="favicon" type="file" label="Favicon" accept="image/png,image/jpeg,image/webp,image/x-icon" />
                    @if($settings->favicon_url)
                        <img src="{{ $settings->favicon_url }}" alt="Favicon" class="rounded border" style="width:32px;height:32px">
                    @endif
                </x-ui.card-body>
            </x-ui.card>
        </div>
    </div>
</form>
@endsection
