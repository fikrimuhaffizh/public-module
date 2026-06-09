@extends('public::layouts.web.app')

@section('content')
<div class="breadcrumbs">
  <div class="container">
    <h2>Announcements & News</h2>
    <p>Stay updated with the latest news and announcements</p>
  </div>
</div>

<section id="recent-news" class="recent-news section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4">
      @forelse($allNews as $news)
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="post-box">
            <div class="meta">
              <ul>
                <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <time datetime="{{ $news->created_at->format('Y-m-d') }}">{{ $news->created_at->format('M d, Y') }}</time></li>
                <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i> <span>{{ ucfirst($news->jenis) }}</span></li>
              </ul>
            </div>
            <div class="d-flex align-items-start">
              <div class="flex-shrink-0 me-3">
                <img src="{{ $news->cover_small_url }}"
                     class="img-fluid rounded"
                     alt="{{ $news->judul }}"
                     style="width: 80px; height: 80px; object-fit: cover;">
              </div>
              <div class="flex-grow-1">
                <h3 class="post-title">{{ $news->judul }}</h3>
                <div class="post-content">
                  <p>{!! Str::limit(strip_tags($news->isi), 120, '...') !!}</p>
                  <a href="{{ route('public.news.show', $news) }}" class="readmore stretched-link">
                    <span>Read More</span>
                    <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="text-center py-5">
            <i class="bi bi-bell icon-lg mb-3"></i>
            <h4>No announcements or news available</h4>
            <p>Please check back later for updates.</p>
          </div>
        </div>
      @endforelse
    </div>

    @if($allNews && $allNews->hasPages())
    <div class="row mt-5">
      <div class="col-12">
        <div class="d-flex justify-content-center">
          {{ $allNews->links() }}
        </div>
      </div>
    </div>
    @endif
  </div>
</section>
@endsection
