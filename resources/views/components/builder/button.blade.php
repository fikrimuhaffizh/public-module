<section class="wbp-section wbp-bg-white wbp-py-{{ $settings['padding_y'] ?? 'md' }}">
    <div class="wbp-container wbp-text-{{ $settings['align'] ?? 'left' }}">
        @if(!empty($content['text']))
            <a href="{{ $content['url'] ?? '#' }}" class="wbp-btn wbp-btn-{{ $content['variant'] ?? 'primary' }} {{ !empty($settings['full_width']) ? 'wbp-btn-block' : '' }}">{{ $content['text'] }}</a>
        @endif
    </div>
</section>