@php
    $count = max(1, min(4, (int) ($settings['count'] ?? 2)));
    $align = $settings['text_align'] ?? 'left';
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        <div class="wbp-grid wbp-grid-{{ $count }} wbp-text-{{ $align }}">
            @forelse($content['columns'] ?? [] as $col)
                <div class="wbp-col">
                    @if(!empty($col['image']))
                        <div class="wbp-media-figure">
                            <img src="{{ $col['image'] }}" alt="{{ $col['heading'] ?? '' }}" loading="lazy">
                        </div>
                    @endif
                    @if(!empty($col['heading']))
                        <h3 class="wbp-card-title">{{ $col['heading'] }}</h3>
                    @endif
                    @if(!empty($col['text']))
                        <p class="wbp-card-text">{{ $col['text'] }}</p>
                    @endif
                    @if(!empty($col['button_text']))
                        <a href="{{ $col['button_url'] ?? '#' }}" class="wbp-btn wbp-btn-primary">{{ $col['button_text'] }}</a>
                    @endif
                </div>
            @empty
            @endforelse
        </div>
    </div>
</section>