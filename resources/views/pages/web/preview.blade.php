<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-4">
            <a href="{{ route('public.preview') }}" class="text-lg font-bold">{{ config('app.name') }}</a>
            <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                @foreach($menus as $menu)
                    @php
                        $menuUrl = $menu->type === 'page' && $menu->page
                            ? route('public.page.show', $menu->page->slug)
                            : ($menu->url ?: '#');
                    @endphp
                    <a href="{{ $menuUrl }}" target="{{ $menu->target }}" class="text-slate-600 hover:text-blue-700">
                        {{ $menu->title }}
                    </a>
                @endforeach
            </nav>
            <a href="{{ route('login') }}" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Masuk</a>
        </div>
    </header>

    <main>
        <section class="relative min-h-[520px] overflow-hidden bg-slate-900">
            @if($slideshows->isNotEmpty())
                @php $hero = $slideshows->first(); @endphp
                <img src="{{ $hero->large_url }}" alt="{{ $hero->title }}" class="absolute inset-0 h-full w-full object-cover opacity-50">
                <div class="relative mx-auto flex min-h-[520px] max-w-7xl items-end px-5 pb-16 pt-28">
                    <div class="max-w-3xl text-white">
                        <h1 class="text-4xl font-bold sm:text-6xl">{{ $hero->title ?: config('app.name') }}</h1>
                        <p class="mt-5 max-w-2xl text-lg text-slate-200">{{ $hero->caption }}</p>
                        @if($hero->link)
                            <a href="{{ $hero->link }}" class="mt-8 inline-block rounded-md bg-white px-5 py-3 font-semibold text-slate-900">Selengkapnya</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="mx-auto flex min-h-[520px] max-w-7xl items-center px-5">
                    <div class="max-w-3xl text-white">
                        <h1 class="text-4xl font-bold sm:text-6xl">{{ config('app.name') }}</h1>
                        <p class="mt-5 text-lg text-slate-300">Informasi dan layanan institusi dalam satu laman.</p>
                    </div>
                </div>
            @endif
        </section>

        @if($pages->isNotEmpty())
            <section class="mx-auto max-w-7xl px-5 py-14">
                <h2 class="text-2xl font-bold">Informasi Utama</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    @foreach($pages->take(6) as $page)
                        <a href="{{ route('public.page.show', $page->slug) }}" class="border border-slate-200 bg-white p-6 hover:border-blue-500">
                            <h3 class="font-semibold text-blue-800">{{ $page->title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ Str::limit(strip_tags($page->content), 130) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="border-y border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-14">
                <h2 class="text-2xl font-bold">Berita dan Pengumuman</h2>
                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    @forelse($announcements as $item)
                        <article>
                            <img src="{{ $item->cover_medium_url }}" alt="{{ $item->judul }}" class="aspect-[16/9] w-full object-cover">
                            <p class="mt-4 text-xs font-semibold uppercase text-blue-700">{{ $item->jenis }}</p>
                            <h3 class="mt-2 text-lg font-semibold">{{ $item->judul }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ Str::limit(strip_tags($item->isi), 120) }}</p>
                        </article>
                    @empty
                        <p class="text-slate-500">Belum ada berita atau pengumuman yang diterbitkan.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-4xl px-5 py-14">
            <h2 class="text-2xl font-bold">Pertanyaan Umum</h2>
            <div class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                @forelse($faqs->flatten(1) as $faq)
                    <details class="py-5">
                        <summary class="cursor-pointer font-semibold">{{ $faq->question }}</summary>
                        <div class="prose mt-3 max-w-none text-sm leading-6 text-slate-600">{!! $faq->answer !!}</div>
                    </details>
                @empty
                    <p class="py-5 text-slate-500">Belum ada FAQ aktif.</p>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="bg-slate-900 text-slate-300">
        <div class="mx-auto max-w-7xl px-5 py-8 text-sm">
            &copy; {{ now()->year }} {{ config('app.name') }}
        </div>
    </footer>
</body>
</html>
