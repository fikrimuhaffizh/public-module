{{--
    Website Builder — Public Page Layout (render_mode='custom', HTML-first)
    Menerima:
      - $html / $css : hasil compile GrapesJS yang SUDAH disanitasi server-side
      - $theme       : builder_theme (CSS custom properties var(--wbp-*))
      - $themeCss    : stylesheet tema minimal yang selalu ikut dirender
      - $preview     : bar "Mode Preview" untuk admin
    Halaman custom bebas — tidak ada chrome/navbar tenant yang disuntik.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title }}</title>
    @if(!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <meta name="generator" content="Laravel Website Builder">
    <link rel="icon" type="image/x-icon" href="{{ sys_tenant_favicon_url() }}" />

    @php
        // Gaya publik (public-builder = blok WBP, landing = semua section React)
        // sehingga blok hasil drag di editor tampil sama persis di halaman jadi.
        $publicStyles = [];
        foreach (['Modules/Public/resources/assets/css/public-builder.css', 'Modules/Public/resources/assets/css/landing.css'] as $entry) {
            try {
                $publicStyles[] = \Illuminate\Support\Facades\Vite::asset($entry);
            } catch (\Throwable) {
                // abai — CSS opsional bila aset belum dibuild
            }
        }
    @endphp
    @foreach ($publicStyles as $publicStyle)
        <link rel="stylesheet" href="{{ $publicStyle }}">
    @endforeach

    <style>
        {{-- Tema global (inject dari config builder_theme) --}}
        {!! $themeCss !!}

        {{-- CSS hasil compile GrapesJS (terbatas: sanitasi di server) --}}
        {!! $css !!}
    </style>
</head>
<body class="wbp-body" style="margin:0;font-family:var(--wbp-font, ui-sans-serif, system-ui, sans-serif);color:var(--wbp-secondary, #0f172a);">
    {!! $html !!}

    @if($preview)
        <div class="wbp-preview-bar" style="position:fixed;bottom:1rem;right:1rem;display:flex;align-items:center;gap:0.75rem;background:var(--wbp-secondary,#0f172a);color:#fff;padding:0.6rem 0.9rem;border-radius:12px;box-shadow:0 10px 30px -10px rgb(0 0 0 / 50%);font-size:0.9rem;z-index:9999;">
            <span style="background:var(--wbp-primary,#2563eb);padding:0.2rem 0.7rem;border-radius:9999px;font-size:0.75rem;font-weight:700;text-transform:uppercase;">Mode Preview</span>
            <a href="{{ route('cms.builder.pages.editor', $page) }}" style="color:#93c5fd;text-decoration:none;font-weight:600;">
                Kembali ke Editor →
            </a>
        </div>
    @endif
</body>
</html>