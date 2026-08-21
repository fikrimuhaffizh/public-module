<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'white' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container wbp-container-narrow">
        @include('public::components.builder.partials._section-heading', ['content' => $content, 'settings' => $settings])
        {{-- data-cms-source: list items diambil dari database, bukan dari editor --}}
        <div class="wbp-faq" data-cms-source="faq" data-cms-params="limit=10&is_active=true">
            @forelse($content['items'] ?? [] as $item)
                <details>
                    <summary>{{ $item['question'] ?? '' }}</summary>
                    <div class="wbp-faq-body">{{ $item['answer'] ?? '' }}</div>
                </details>
            @empty
            @endforelse
        </div>
    </div>
</section>