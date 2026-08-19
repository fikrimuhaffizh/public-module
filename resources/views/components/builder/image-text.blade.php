@php
    $flip = !empty($content['image_side']) && $content['image_side'] === 'right';
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        <div class="wbp-media-row {{ $flip ? 'wbp-media-flip' : '' }}">
            <div class="wbp-media-figure">
                @if(!empty($content['image']))
                    <img src="{{ $content['image'] }}" alt="{{ $content['heading'] ?? '' }}" loading="lazy">
                @endif
            </div>
            <div class="wbp-media-content">
                @if(!empty($content['heading']))
                    <h2 class="wbp-title wbp-title-lg">{{ $content['heading'] }}</h2>
                @endif
                @if(!empty($content['text']))
                    <p class="wbp-lead">{{ $content['text'] }}</p>
                @endif
                @if(!empty($content['button_text']))
                    <a href="{{ $content['button_url'] ?? '#' }}" class="wbp-btn wbp-btn-primary">{{ $content['button_text'] }}</a>
                @endif
            </div>
        </div>
    </div>
</section>