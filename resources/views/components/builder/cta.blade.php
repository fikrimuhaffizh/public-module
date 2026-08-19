@php
    $bgKey = $settings['background'] ?? 'brand';
    $darkBg = in_array($bgKey, ['brand', 'dark', 'image'], true);
@endphp
<section class="wbp-section wbp-bg-{{ $bgKey }} wbp-py-{{ $settings['padding_y'] ?? 'xl' }}">
    <div class="wbp-container wbp-text-center">
        <div class="wbp-cta-box">
            @if(!empty($content['title']))
                <h2 class="wbp-cta-title">{{ $content['title'] }}</h2>
            @endif
            @if(!empty($content['description']))
                <p class="wbp-cta-desc">{{ $content['description'] }}</p>
            @endif
            @if(!empty($content['button_text']))
                <div class="wbp-cta-actions">
                    <a href="{{ $content['button_url'] ?? '#' }}" class="wbp-btn wbp-btn-lg {{ $darkBg ? 'wbp-btn-white' : 'wbp-btn-primary' }}">{{ $content['button_text'] }}</a>
                </div>
            @endif
        </div>
    </div>
</section>