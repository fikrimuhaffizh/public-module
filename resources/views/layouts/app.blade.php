@php
    use App\Contracts\ThemeEngineInterface;

    $themeEngine = app(ThemeEngineInterface::class);
    $themeData = $themeEngine->getThemeData('tabler');
    $layoutData = $themeEngine->getLayoutData('tabler');

    $dark = ($themeData['theme'] ?? 'light') === 'dark';
    $tenantName = sys_tenant_name();
@endphp

<!DOCTYPE html>
<html lang="en" {!! $themeEngine->getHtmlAttributes('tabler') !!}>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="description" content="@yield('meta_description', $tenantName)" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $tenantName)</title>
    <link rel="icon" type="image/x-icon" href="{{ sys_tenant_favicon_url() }}" />

    @yield('css')
    
    @vite(['resources/themes/tabler/assets/css/tabler.css'])
    {!! $themeEngine->getStyleBlock('tabler') !!}

    @stack('styles')

    <style>
        body {
            background-color: var(--tblr-bg-surface-secondary);
        }
        .blank-layout-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>

<body class="d-flex flex-column {{ $layoutData['bodyClass'] ?? '' }}">
    <div class="page page-center blank-layout-container">
        <div class="container container-tight py-4">
            @yield('content')
        </div>
    </div>

    {{-- Global Generic Modal --}}
    <div class="modal modal-blur fade" id="modalAction" tabindex="-1" aria-hidden="true" style="z-index: 99999;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" id="modalContent">
                <div class="modal-header">
                    <h5 class="modal-title">Loading...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite([
        'resources/themes/tabler/assets/js/tabler.js'
    ])

    @stack('scripts')

</body>
</html>
