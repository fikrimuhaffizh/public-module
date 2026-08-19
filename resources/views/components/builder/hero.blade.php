@php
    $bgKey = $settings['background'] ?? 'brand';
    $useAsBackground = $bgKey === 'image' && !empty($content['image']);
    $showMedia = !$useAsBackground && !empty($content['image']);
    $bgClass = $useAsBackground ? 'wbp-bg-image' : 'wbp-bg-' . $bgKey;
    $bgStyle = $useAsBackground ? "background-image:url('" . e($content['image']) . "');background-color:var(--wbp-secondary)" : '';
    $align = $settings['text_align'] ?? 'center';
    $darkBg = in_array($bgKey, ['brand', 'dark', 'image'], true) || $useAsBackground;
    $sizeClass = 'wbp-title-' . ($settings['title_size'] ?? 'xl');
@endphp
<section class="wbp-section wbp-py-{{ $settings['padding_y'] ?? 'xl' }} {{ $bgClass }}" @if($bgStyle) style="{{ $bgStyle }}" @endif>
    <div class="wbp-container">
        <div class="wbp-hero {{ $showMedia ? 'has-image' : '' }} {{ $align === 'center' ? 'wbp-text-center' : '' }}">
            <div class="wbp-hero-content">
                @if(!empty($content['eyebrow']))
                    <p class="wbp-eyebrow wbp-hero-eyebrow">{{ $content['eyebrow'] }}</p>
                @endif
                @if(!empty($content['title']))
                    <h1 class="wbp-title {{ $sizeClass }} wbp-hero-title">{{ $content['title'] }}</h1>
                @endif
                @if(!empty($content['description']))
                    <p class="wbp-hero-description wbp-lead">{{ $content['description'] }}</p>
                @endif
                @if(!empty($content['button_text']) || !empty($content['button_text_2']))
                    <div class="wbp-hero-actions">
                        @if(!empty($content['button_text']))
                            <a href="{{ $content['button_url'] ?? '#' }}" class="wbp-btn wbp-btn-lg {{ $darkBg ? 'wbp-btn-white' : 'wbp-btn-primary' }}">{{ $content['button_text'] }}</a>
                        @endif
                        @if(!empty($content['button_text_2']))
                            <a href="{{ $content['button_url_2'] ?? '#' }}" class="wbp-btn wbp-btn-lg wbp-btn-outline {{ $darkBg ? 'wbp-btn-light' : 'wbp-btn-outline-dark' }}">{{ $content['button_text_2'] }}</a>
                        @endif
                    </div>
                @endif
            </div>
            @if($showMedia)
                <div class="wbp-hero-media">
                    <img src="{{ $content['image'] }}" alt="{{ $content['title'] ?? '' }}" loading="lazy">
                </div>
            @endif
        </div>
    </div>
</section>