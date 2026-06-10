<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $page->meta_desc }}">
    <title>{{ $page->title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-5 py-4">
            <a href="{{ route('public.preview') }}" class="font-bold">{{ config('app.name') }}</a>
            <a href="{{ route('public.preview') }}" class="text-sm font-semibold text-blue-700">Kembali ke preview</a>
        </div>
    </header>
    <main class="mx-auto max-w-5xl px-5 py-12">
        <article class="bg-white p-7 shadow-sm sm:p-12">
            <h1 class="text-3xl font-bold sm:text-4xl">{{ $page->title }}</h1>
            <div class="prose prose-slate mt-8 max-w-none leading-7">{!! $page->content !!}</div>
        </article>
    </main>
</body>
</html>
