<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'md' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        <div class="wbp-logo-strip">
            @forelse($content['items'] ?? [] as $item)
                @if(!empty($item['image']))
                    <a href="{{ $item['url'] ?? '#' }}" class="wbp-logo-item" title="{{ $item['name'] ?? '' }}">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] ?? '' }}" loading="lazy">
                    </a>
                @endif
            @empty
            @endforelse
        </div>
    </div>
</section>