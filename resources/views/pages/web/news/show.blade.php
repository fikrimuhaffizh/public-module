@extends('public::layouts.web.app')

@section('content')
<div class="breadcrumbs">
  <div class="container">
    <ol class="breadcrumb breadcrumb-arranged flex-lg-row flex-column justify-content-lg-start justify-content-center align-items-lg-center align-items-center">
      <li class="breadcrumb-item"><a href="{{ route('public.index') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('public.announcements.index') }}">Announcements</a></li>
      <li class="breadcrumb-item active">{{ $pengumuman->judul }}</li>
    </ol>
  </div>
</div>

<section class="blog-details section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <article>
          <div class="post">
            <div class="post-img">
              <img src="{{ $pengumuman->cover_url }}" alt="{{ $pengumuman->judul }}" class="img-fluid">
            </div>

            <div class="meta-top d-flex">
              <ul class="list-unstyled d-flex flex-wrap align-items-center">
                <li class="d-flex align-items-center me-4"><i class="bi bi-person"></i> <span>{{ $pengumuman->penulis ? $pengumuman->penulis->name : 'System' }}</span></li>
                <li class="d-flex align-items-center me-4"><i class="bi bi-clock"></i> <time datetime="{{ $pengumuman->created_at->format('Y-m-d') }}">{{ $pengumuman->created_at->format('M d, Y') }}</time></li>
                <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i> <span>{{ ucfirst($pengumuman->jenis) }}</span></li>
              </ul>
            </div>

            <h1 class="mb-4">{{ $pengumuman->judul }}</h1>

            <div class="content">
              <div>
                {!! $pengumuman->isi !!}
              </div>
            </div>

            <div class="post-tags d-flex justify-content-between align-items-center">
              <div class="tags">
                <span class="badge bg-primary text-white">{{ ucfirst($pengumuman->jenis) }}</span>
              </div>
              <a href="{{ route('public.announcements.index') }}" class="btn btn-primary">Back to Announcements</a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>
@endsection
