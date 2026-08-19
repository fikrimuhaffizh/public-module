{{-- Shared section heading: eyebrow, title, subtitle --}}
<div class="wbp-section-heading {{ empty($content['subtitle']) ? '' : 'wbp-has-subtitle' }}">
    @if(!empty($content['eyebrow']))
        <p class="wbp-eyebrow">{{ $content['eyebrow'] }}</p>
    @endif
    @if(!empty($content['title']))
        <h2 class="wbp-title wbp-title-lg">{{ $content['title'] }}</h2>
    @endif
    @if(!empty($content['subtitle']))
        <p class="wbp-subtitle">{{ $content['subtitle'] }}</p>
    @endif
</div>