{{--
    Website Builder — Public Page Layout (standalone, server-rendered)
    Tema global (builder_theme) dikirim sebagai CSS custom properties
    var(--wbp-*), sehingga mengganti primary/font/radius sekali akan
    mengubah seluruh halaman builder.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="
    --wbp-primary: {{ $theme['primary'] ?? '#2563EB' }};
    --wbp-secondary: {{ $theme['secondary'] ?? '#0F172A' }};
    --wbp-font: {{ $theme['font'] ?? 'Inter' }}, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
    --wbp-radius: {{ ($theme['radius'] ?? '12') }}{{ is_numeric($theme['radius'] ?? null) ? 'px' : '' }};
    --wbp-container: {{ ($theme['container_width'] ?? 1140) }}{{ is_numeric($theme['container_width'] ?? null) ? 'px' : '' }};
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title }}</title>
    @if(!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <meta name="generator" content="Laravel Website Builder">
    <link rel="icon" type="image/x-icon" href="{{ sys_tenant_favicon_url() }}" />

    @vite(['Modules/Public/resources/assets/css/public-builder.css'])
    @stack('styles')
</head>
<body class="wbp-body">
    <a class="wbp-skip-link" href="#wbp-content">Langsung ke konten</a>

    <main id="wbp-content" class="wbp-page">
        @foreach($sections as $section)
            @include('public::components.builder.'.$section['view'], [
                'content' => $section['content'],
                'settings' => $section['settings'],
                'theme' => $theme,
                'section' => $section,
                'index' => $loop->index,
            ])
        @endforeach
    </main>

    <footer class="wbp-footer">
        <div class="wbp-container wbp-footer-inner">
            <span>&copy; {{ date('Y') }} {{ $siteName }}</span>
            <span class="wbp-footer-sep"></span>
            <span>Dibuat dengan Website Builder</span>
        </div>
    </footer>

    @if($preview)
        <div class="wbp-preview-bar">
            <span class="wbp-preview-badge">Mode Preview</span>
            <a href="{{ route('cms.builder.pages.editor', $page) }}" class="wbp-preview-link">
                Kembali ke Editor →
            </a>
        </div>
    @endif

    @stack('scripts')
</body>
</html>