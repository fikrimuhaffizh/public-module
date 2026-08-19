@php
    $columns = max(1, min(4, (int) ($settings['columns'] ?? 3)));
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        <div class="wbp-grid wbp-grid-{{ $columns }} wbp-gallery">
            @forelse($content['items'] ?? [] as $item)
                @if(!empty($item['image']))
                    <figure class="wbp-gallery-item">
                        <img src="{{ $item['image'] }}" alt="{{ $item['caption'] ?? '' }}" loading="lazy">
                        @if(!empty($item['caption']))
                            <figcaption>{{ $item['caption'] }}</figcaption>
                        @endif
                    </figure>
                @endif
            @empty
            @endforelse
        </div>
    </div>
</section>