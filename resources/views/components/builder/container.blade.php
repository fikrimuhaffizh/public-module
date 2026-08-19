{{-- Container — blok kosong dengan tinggi/background (spacer) --}}
@php
    $minHeight = match ($settings['min_height'] ?? 'sm') {
        'md' => '160px',
        'lg' => '260px',
        default => '80px',
    };
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container" style="min-height:{{ $minHeight }}"></div>
</section>