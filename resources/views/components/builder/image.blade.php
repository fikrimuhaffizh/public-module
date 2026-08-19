@php
    $maxWidth = match ($settings['max_width'] ?? 'md') {
        'sm' => '320px',
        'lg' => '800px',
        default => '560px',
    };
    $rounded = !empty($settings['rounded']) ? 'var(--wbp-radius)' : '0';
@endphp
<section class="wbp-section wbp-bg-white wbp-py-{{ $settings['padding_y'] ?? 'md' }}">
    <div class="wbp-container wbp-text-{{ $settings['align'] ?? 'center' }}">
        @if(!empty($content['image']))
            <figure style="max-width:{{ $maxWidth }};margin:0 auto;">
                <img src="{{ $content['image'] }}" alt="{{ $content['alt'] ?? '' }}" loading="lazy"
                     style="width:100%;height:auto;border-radius:{{ $rounded }};display:block;">
                @if(!empty($content['caption']))
                    <figcaption class="wbp-gray-text" style="margin-top:0.5rem;font-size:0.875rem">{{ $content['caption'] }}</figcaption>
                @endif
            </figure>
        @endif
    </div>
</section>