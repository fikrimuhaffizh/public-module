@php
    $columns = max(1, min(4, (int) ($settings['columns'] ?? 3)));
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'gray' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        {{-- data-cms-source: statistics diambil dari database --}}
        <div class="wbp-grid wbp-grid-{{ $columns }} wbp-text-center" data-cms-source="statistik" data-cms-params="limit=6&is_active=true">
            @forelse($content['items'] ?? [] as $item)
                <div class="wbp-stat">
                    <div class="wbp-stat-value">{{ $item['value'] ?? '' }}</div>
                    <div class="wbp-stat-label">{{ $item['label'] ?? '' }}</div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>