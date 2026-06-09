@extends('public::layouts.web.app')

@section('content')

<section id="hero" class="hero section p-0 overflow-hidden">
    @if($slideshows->count() > 0)
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($slideshows as $index => $slide)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($slideshows as $index => $slide)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" style="height: 85vh; min-height: 500px;">
                        <img src="{{ $slide->large_url }}" class="d-block w-100 h-100" style="object-fit: cover; filter: brightness(0.6);" alt="{{ $slide->title }}">
                        <div class="carousel-caption d-none d-md-block text-start mb-5 pb-5">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-8" data-aos="fade-up">
                                        <h1 class="display-3 fw-bold text-white mb-3">{{ $slide->title }}</h1>
                                        <p class="lead text-white-50 mb-4 fs-4">{{ $slide->caption }}</p>
                                        @if($slide->link)
                                            <a href="{{ $slide->link }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                                                Explore More <i class="bi bi-arrow-right ms-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    @else
        <div class="container pt-5 mt-5" data-aos="fade-up" data-aos-delay="100">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 hero-text" data-aos="fade-right" data-aos-delay="200">
                        <h1>{{ config('app.name') }}</h1>
                        <p>Platform terintegrasi untuk layanan dan informasi.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

<style>
    .carousel-caption { bottom: 20%; }
    .carousel-indicators [data-bs-target] {
        width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;
    }
</style>

<section id="faqs" class="faqs section pb-0">
  <div class="container" data-aos="fade-up">
    <div class="section-title">
      <h2>Frequently Asked Questions</h2>
      <p>Pertanyaan yang sering diajukan</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-10">
        @php $faqCount = 0; @endphp
        @forelse($faqs as $category => $categoryFaqs)
          <div class="mb-5" data-aos="fade-up">
            <h3 class="mb-4 border-bottom pb-2 text-primary">
              <i class="bi bi-folder2-open me-2"></i> {{ $category ?: 'Umum' }}
            </h3>
            <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden" id="faqAccordion-{{ Str::slug($category ?: 'umum') }}">
              @foreach($categoryFaqs as $index => $faq)
              <div class="accordion-item" data-aos="fade-up" data-aos-delay="{{ 100 * (++$faqCount) }}">
                <h3 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-content-{{ $faq->id }}">
                    <i class="bi bi-question-circle me-2 text-primary"></i>
                    {{ $faq->question }}
                  </button>
                </h3>
                <div id="faq-content-{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion-{{ Str::slug($category ?: 'umum') }}">
                  <div class="accordion-body">
                    {!! $faq->answer !!}
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        @empty
          <div class="text-center py-5">
            <p class="text-muted">Belum ada FAQ.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</section>

<section id="recent-blog-posts" class="recent-blog-posts section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Recent News & Announcements</h2>
    <p>Stay updated with the latest news and announcements</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4">
      @forelse($recentNews as $news)
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
  </div>
</section>

<section class="call-to-action section" id="call-to-action">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row align-items-center justify-content-center text-center">
      <div class="col-lg-8">
        <div class="cta-content p-5 bg-primary text-white rounded-4 shadow-lg">
          <h2 class="mb-3 text-white">Butuh Bantuan atau Informasi Lebih Lanjut?</h2>
          <p class="mb-4 text-white-50">Tim kami siap membantu Anda.</p>
          <div class="cta-actions">
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill shadow-lg">Hubungi Kami</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
