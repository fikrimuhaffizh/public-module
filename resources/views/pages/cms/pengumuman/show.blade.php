@if(request()->ajax() || request()->has('ajax'))
    <x-ui.form-modal title="{{ $pengumuman->judul }}" method="none">
        <div class="datagrid">
            <div class="datagrid-item">
                <div class="datagrid-title">Title</div>
                <div class="datagrid-content">{{ $pengumuman->judul }}</div>
            </div>
            <div class="datagrid-item">
                <div class="datagrid-title">Type</div>
                <div class="datagrid-content">
                    <span class="badge bg-{{ $pengumuman->jenis == 'cms_pengumuman' ? 'primary' : 'info' }}">
                        {{ ucfirst($pengumuman->jenis) }}
                    </span>
                </div>
            </div>
            <div class="datagrid-item">
                <div class="datagrid-title">Author</div>
                <div class="datagrid-content">{{ $pengumuman->penulis ? $pengumuman->penulis->name : 'System' }}</div>
            </div>
            <div class="datagrid-item">
                <div class="datagrid-title">Status</div>
                <div class="datagrid-content">
                    <span class="badge bg-{{ $pengumuman->is_published ? 'success' : 'warning' }}">
                        {{ $pengumuman->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>
            <div class="datagrid-item">
                <div class="datagrid-title">Created At</div>
                <div class="datagrid-content">{{ formatTanggalIndo($pengumuman->created_at) }}</div>
            </div>
        </div>

        @php
            $coverMedia = $pengumuman->getFirstMedia('cover');
            $attachments = $pengumuman->getMedia('attachments');
        @endphp

        @if($coverMedia)
            <div class="mt-4">
                <h4 class="card-title mb-3">Cover Image</h4>
                <div class="text-center">
                    <img src="{{ sys_media_url($coverMedia) }}" alt="Cover Image" class="rounded border shadow-sm img-fluid" style="max-height: 250px;">
                </div>
            </div>
        @endif

        <div class="mt-4">
            <h4 class="card-title mb-3">Content</h4>
            <div class="p-3 border rounded bg-light">
                {!! $pengumuman->isi !!}
            </div>
        </div>

        @if($attachments->count() > 0)
            <div class="mt-4">
                <h4 class="card-title mb-3">Attachments</h4>
                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                    @foreach($attachments as $attachment)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <div>
                                <div class="fw-bold">{{ $attachment->file_name }}</div>
                                <div class="text-muted small">{{ number_format($attachment->size / 1024, 2) }} KB • {{ strtoupper($attachment->extension) }}</div>
                            </div>
                            <x-ui.button type="link" :href="sys_media_url($attachment)" text="Download" icon="ti ti-download" class="btn-sm btn-outline-primary" target="_blank" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <x-slot:footer>
            <x-ui.button type="cancel" data-bs-dismiss="modal" text="Tutup" />
            <x-ui.button type="edit" :href="route('cms.'.$pengumuman->jenis.'.edit', $pengumuman)" class="ms-auto" />
        </x-slot:footer>
    </x-ui.form-modal>
@else
    @extends('layouts.' . active_theme() . '.app')

    @section('title', $pengumuman->judul)

    @section('header')
        <x-ui.page-header :title="$pengumuman->judul" pretitle="Content Management">
            <x-slot:actions>
                <x-ui.button type="edit" :href="route('cms.'.$pengumuman->jenis.'.edit', $pengumuman)" />
                <x-ui.button type="back" :href="route('cms.'.$pengumuman->jenis.'.index')" />
            </x-slot:actions>
        </x-ui.page-header>
    @endsection

    @section('content')
        <div class="row">
            <div class="col-12">
                <x-ui.card>
                    <x-ui.card-body>
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Title</div>
                                <div class="datagrid-content">{{ $pengumuman->judul }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Type</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-{{ $pengumuman->jenis == 'cms_pengumuman' ? 'primary' : 'info' }}">
                                        {{ ucfirst($pengumuman->jenis) }}
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Author</div>
                                <div class="datagrid-content">{{ $pengumuman->penulis ? $pengumuman->penulis->name : 'System' }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Status</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-{{ $pengumuman->is_published ? 'success' : 'warning' }}">
                                        {{ $pengumuman->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Created At</div>
                                <div class="datagrid-content">{{ formatTanggalIndo($pengumuman->created_at) }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Last Updated</div>
                                <div class="datagrid-content">{{ formatTanggalIndo($pengumuman->updated_at) }}</div>
                            </div>
                        </div>

                        @php
                            $coverMedia = $pengumuman->getFirstMedia('cover');
                            $attachments = $pengumuman->getMedia('attachments');
                        @endphp

                        @if($coverMedia)
                            <div class="mt-4">
                                <h4 class="card-title mb-3">Cover Image</h4>
                                <img src="{{ sys_media_url($coverMedia) }}" alt="Cover Image" class="rounded border shadow-sm" style="max-height: 300px;">
                            </div>
                        @endif

                        <div class="mt-4">
                            <h4 class="card-title mb-3">Content</h4>
                            <div class="p-3 border rounded bg-light">
                                {!! $pengumuman->isi !!}
                            </div>
                        </div>

                        @if($attachments->count() > 0)
                            <div class="mt-4">
                                <h4 class="card-title mb-3">Attachments</h4>
                                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                    @foreach($attachments as $attachment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                            <div>
                                                <div class="fw-bold">{{ $attachment->file_name }}</div>
                                                <div class="text-muted small">{{ number_format($attachment->size / 1024, 2) }} KB • {{ strtoupper($attachment->extension) }}</div>
                                            </div>
                                            <x-ui.button type="link" :href="sys_media_url($attachment)" text="Download" icon="ti ti-download" class="btn-sm btn-outline-primary" target="_blank" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <x-ui.button type="delete" 
                                        class="ajax-delete"
                                        :data-url="route('cms.'.$pengumuman->jenis.'.destroy', $pengumuman)"
                                        data-title="Hapus {{ ucfirst($pengumuman->jenis) }}"
                                        data-text="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan."
                                        data-redirect="{{ route('cms.'.$pengumuman->jenis.'.index') }}"
                                        icon="ti ti-trash" />
                        </div>
                    </x-ui.card-body>
                </x-ui.card>
            </div>
        </div>
    @endsection
@endif
