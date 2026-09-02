@extends('layouts.' . active_theme() . '.app')

@section('title', 'SEO')

@section('header')
<x-ui.page-header title="SEO" pretitle="Landing Page">
    <x-slot:actions>
        @can('public.cms.settings.update')
        <button type="submit" form="seo-form" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i>Simpan
        </button>
        @endcan
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
<form id="seo-form" action="{{ route('cms.seo.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
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
