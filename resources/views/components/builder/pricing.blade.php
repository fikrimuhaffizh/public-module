@php
    $columns = max(1, min(4, (int) ($settings['columns'] ?? 3)));
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'gray' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        <div class="wbp-grid wbp-grid-{{ $columns }}">
            @forelse($content['items'] ?? [] as $item)
                <div class="wbp-card wbp-price-card {{ !empty($item['featured']) ? 'wbp-featured' : '' }}">
                    <div class="wbp-price-name">{{ $item['name'] ?? '' }}</div>
                    <div class="wbp-price-amount">{{ $item['price'] ?? '' }}<span class="wbp-price-period">{{ $item['period'] ?? '' }}</span></div>
                    <p class="wbp-price-desc">{{ $item['description'] ?? '' }}</p>
                    <ul class="wbp-price-features">
                        @forelse($item['features'] ?? [] as $feature)
                            <li>{{ $feature }}</li>
                        @empty
                        @endforelse
                    </ul>
                    @if(!empty($item['button_text']))
                        <a href="{{ $item['button_url'] ?? '#' }}" class="wbp-btn wbp-btn-block {{ !empty($item['featured']) ? 'wbp-btn-primary' : 'wbp-btn-outline' }}">{{ $item['button_text'] }}</a>
                    @endif
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>