@php
    $columns = max(1, min(3, (int) ($settings['columns'] ?? 2)));
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'gray' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        {{-- data-cms-source: testimonials diambil dari database --}}
        <div class="wbp-grid wbp-grid-{{ $columns }}" data-cms-source="testimonial" data-cms-params="limit=6&is_active=true">
            @forelse($content['items'] ?? [] as $item)
                <div class="wbp-card">
                    <p class="wbp-quote">"{{ $item['quote'] ?? '' }}"</p>
                    <div class="wbp-person">
                        @if(!empty($item['photo']))
                            <img src="{{ $item['photo'] }}" alt="{{ $item['name'] ?? '' }}" class="wbp-avatar" loading="lazy">
                        @else
                            <div class="wbp-avatar wbp-avatar-placeholder">{{ mb_substr(trim($item['name'] ?? '?'), 0, 1) }}</div>
                        @endif
                        <div class="wbp-person-meta">
                            <div class="wbp-person-name">{{ $item['name'] ?? '' }}</div>
                            <div class="wbp-person-role">{{ $item['position'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>