@extends('public::layouts.public-layout')

@section('content')
        <div class="row row-cards">
            @if(isset($slideshows) && $slideshows->count() > 0)
            <div class="col-12">
                <x-ui.card id="carousel-slideshow" class="carousel slide shadow-sm border-0" data-bs-ride="carousel" style="border-radius: 12px; overflow: hidden;">
                    <div class="carousel-indicators">
                        @foreach($slideshows as $index => $slide)
                        <button type="button" data-bs-target="#carousel-slideshow" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($slideshows as $index => $slide)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            @if($slide->link)
                                <a href="{{ $slide->link }}" target="_blank">
                                    <img src="{{ $slide->large_url }}" class="d-block w-100" style="height: 400px; object-fit: cover; cursor: pointer;" alt="{{ $slide->title }}">
                                </a>
                            @else
                                <img src="{{ $slide->large_url }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="{{ $slide->title }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-slideshow" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-slideshow" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </x-ui.card>
            </div>
            @endif

            <div class="col-12 d-none">
                <x-ui.card class="card-md border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #1e3a5f 0%, #2c3e50 50%, #34495e 100%); color: white; border-radius: 12px;">
                    <x-ui.card-body class="d-flex align-items-center py-5 position-relative">
                        <div class="me-4 d-none d-md-block">
                            <span class="avatar avatar-xl rounded-circle border border-2 border-white-50 shadow-sm" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0054a6&color=fff&size=128)"></span>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="display-6 fw-bold mb-2">Selamat Datang, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
                            <p class="fs-4 opacity-75 mb-4">Sistem Informasi Manajemen Terpadu Politeknik Caltex Riau</p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <x-ui.button href="{{ route('sys.notifications.index') }}" style="light" class="px-4 py-2 shadow-sm" text="Semua Notifikasi" icon="ti ti-bell" />
                                <x-ui.button href="{{ route('cms.pengumuman.index') }}" style="outline-light" class="px-4 py-2" text="Pusat Pengumuman" icon="ti ti-news" />
                            </div>
                        </div>
                        <div class="ms-auto d-none d-lg-block opacity-10 position-absolute end-0 me-4">
                            <i class="ti ti-school" style="font-size: 10rem;"></i>
                        </div>
                    </x-ui.card-body>
                </x-ui.card>
            </div>

            <div class="col-lg-5 mt-4">
                <div class="d-flex align-items-center mb-3">
                    <h3 class="m-0 fw-bold"><i class="ti ti-speakerphone me-2 text-primary"></i> Pengumuman</h3>
                </div>
                <x-ui.card class="shadow-sm border-0" style="border-radius: 12px;">
                    <x-ui.card-body>
                        <ul class="timeline">
                            @forelse($recentAnnouncements as $announcement)
                            <li class="timeline-event">
                                <div class="timeline-event-icon bg-transparent border-0">
                                    <i class="ti ti-speakerphone text-danger fs-2"></i>
                                </div>
                                <div class="timeline-event-card shadow-none border-0 p-0">
                                    <div class="fw-bold fs-3 text-dark mb-1">
                                        <a href="{{ route('cms.pengumuman.show', $announcement->pengumuman_id) }}" class="text-reset text-decoration-none">{{ $announcement->judul }}</a>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ti ti-clock me-1"></i> {{ formatTanggalIndo($announcement->created_at) }}
                                    </div>
                                    <div class="text-secondary small lh-base" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ strip_tags($announcement->isi) }}
                                    </div>
                                </div>
                            </li>
                            @empty
                            <p class="text-secondary small text-center py-3">Tidak ada pengumuman terbaru.</p>
                            @endforelse
                        </ul>
                        <div class="mt-3 border-top pt-3 text-center">
                            <a href="{{ route('cms.pengumuman.index') }}" class="text-danger fw-bold text-decoration-none small">
                               <i class="ti ti-chevron-right me-1"></i> Lihat Semua
                            </a>
                        </div>
                    </x-ui.card-body>
                </x-ui.card>
            </div>

            <div class="col-lg-7 mt-4">
                <div class="d-flex align-items-center mb-3">
                    <h3 class="m-0 fw-bold"><i class="ti ti-news me-2 text-primary"></i> Berita Terbaru</h3>
                </div>
                <div class="vstack gap-3">
                    @forelse($recentNews as $news)
                    <x-ui.card class="border-0 shadow-sm hover-shadow-lg transition-shadow" style="border-radius: 12px; overflow: hidden;">
                        <div class="row g-0">
                            @if($news->cover_medium_url)
                            <div class="col-md-3 col-lg-2">
                                <img src="{{ $news->cover_medium_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $news->judul }}" style="min-height: 120px; max-height: 180px;">
                            </div>
                            @else
                            <div class="col-md-3 col-lg-2 bg-light d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <i class="ti ti-news text-muted fs-1"></i>
                            </div>
                            @endif
                            <div class="col-md-9 col-lg-10">
                                <x-ui.card-body class="p-3 p-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ti ti-calendar-event me-1 text-muted"></i>
                                        <span class="text-muted small">{{ $news->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="card-title mb-2 fw-semibold">
                                        <a href="{{ route('cms.pengumuman.show', $news->pengumuman_id) }}" class="text-dark text-decoration-none stretched-link">{{ $news->judul }}</a>
                                    </h4>
                                    <p class="text-secondary mb-3 small">{{ Str::limit(strip_tags($news->isi), 120) }}</p>
                                    <div class="d-flex align-items-center">
                                        <span class="small fw-bold text-primary">Baca Lengkap <i class="ti ti-arrow-right ms-1"></i></span>
                                    </div>
                                </x-ui.card-body>
                            </div>
                        </div>
                    </x-ui.card>
                    @empty
                    <x-ui.card class="border-0 shadow-sm">
                        <x-ui.card-body class="text-center py-5">
                            <i class="ti ti-news text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">Tidak ada berita terbaru.</p>
                        </x-ui.card-body>
                    </x-ui.card>
                    @endforelse
                </div>
            </div>
        </div>
@endsection
