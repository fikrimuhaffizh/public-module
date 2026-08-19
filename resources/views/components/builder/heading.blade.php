@php
    $level = (int) ($settings['level'] ?? 2);
    $level = in_array($level, [1, 2, 3, 4], true) ? $level : 2;
    $colorClass = match ($settings['color'] ?? 'default') {
        'brand' => 'wbp-color-brand',
        'muted' => 'wbp-gray-text',
        default => '',
    };
    $sizeClass = 'wbp-title-' . ($settings['size'] ?? 'lg');
@endphp
<section class="wbp-section wbp-bg-white wbp-py-{{ $settings['padding_y'] ?? 'md' }}">
    <div class="wbp-container wbp-text-{{ $settings['align'] ?? 'left' }}">
        <h{{ $level }} class="wbp-title {{ $sizeClass }} {{ $colorClass }}" style="margin:0">{{ $content['title'] ?? '' }}</h{{ $level }}>
    </div>
</section>