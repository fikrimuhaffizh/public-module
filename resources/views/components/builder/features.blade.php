@php
    $columns = max(1, min(4, (int) ($settings['columns'] ?? 3)));
    $align = $settings['text_align'] ?? 'center';
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        <div class="wbp-grid wbp-grid-{{ $columns }} wbp-text-{{ $align }}">
            @forelse($content['items'] ?? [] as $item)
                <div class="wbp-card">
                    @if(!empty($item['icon']))
                        <div class="wbp-feature-icon" aria-hidden="true">•</div>
                    @endif
                    <h3 class="wbp-card-title">{{ $item['title'] ?? '' }}</h3>
                    <p class="wbp-card-text">{{ $item['description'] ?? '' }}</p>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>