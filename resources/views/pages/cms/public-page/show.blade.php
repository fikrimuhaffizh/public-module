@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header :title="$page->title" pretitle="Content Management">
    <x-slot:actions>
        <x-ui.button type="edit" :href="route('public.cms.page.edit', $page->encrypted_page_id)" />
        <x-ui.button type="back" />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
        <div class="row row-cards">
            <div class="col-lg-8">
                <x-ui.card>
                    <x-ui.card-body>
                        @if($page->hasMedia('main_image'))
                            <div class="mb-3">
                                <img src="{{ sys_media_url($page->getFirstMedia('main_image')) }}" alt="{{ $page->title }}" class="img-fluid rounded w-100 object-cover" style="max-height: 400px;">
                            </div>
                        @endif

                        <div class="typography">
                            {!! $page->content !!}
                        </div>
                    </x-ui.card-body>
                </x-ui.card>
            </div>

            <div class="col-lg-4">
                <x-ui.card class="mb-3">
                    <x-ui.card-header>
                        <h3 class="card-title">Informasi Halaman</h3>
                    </x-ui.card-header>
                    <x-ui.card-body>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            @if($page->is_published)
                                <span class="badge bg-success me-1"></span> Published
                            @else
                                <span class="badge bg-orange me-1"></span> Draft
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <div class="form-control-plaintext">{{ $page->slug }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Terakhir Diupdate</label>
                            <div class="form-control-plaintext">{{ formatTanggalIndo($page->updated_at) }}</div>
                            <small class="text-muted">Oleh: {{ $page->updated_by ?? '-' }}</small>
                        </div>
                    </x-ui.card-body>
                </x-ui.card>

                @if($page->hasMedia('attachments'))
                <x-ui.card>
                    <x-ui.card-header>
                        <h3 class="card-title">File Pendukung</h3>
                    </x-ui.card-header>
                    <div class="list-group list-group-flush">
                        @foreach($page->getMedia('attachments') as $media)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="text-truncate me-3">
                                <i class="ti ti-file me-2"></i>
                                <a href="{{ sys_media_url($media) }}" target="_blank" class="text-reset text-truncate">
                                    {{ $media->file_name }}
                                </a>
                            </div>
                            <span class="badge bg-secondary-lt">{{ $media->human_readable_size }}</span>
                        </div>
                        @endforeach
                    </div>
                </x-ui.card>
                @endif
            </div>
        </div>
@endsection
