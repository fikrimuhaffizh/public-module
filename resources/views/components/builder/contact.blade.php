@php
    $headingContent = $content;
    if (!empty($content['description']) && empty($content['subtitle'])) {
        $headingContent['subtitle'] = $content['description'];
    }
@endphp
<section class="wbp-section wbp-bg-{{ $settings['background'] ?? 'gray' }} wbp-py-{{ $settings['padding_y'] ?? 'lg' }}">
    <div class="wbp-container">
        @include('public::components.builder.partials._section-heading', ['content' => $headingContent, 'settings' => $settings])
        <div class="wbp-contact-grid">
            @if(!empty($content['email']))
                <div class="wbp-contact-item">
                    <span class="wbp-contact-icon" aria-hidden="true">✉</span>
                    <div>
                        <div class="wbp-contact-label">Email</div>
                        <div class="wbp-contact-value"><a href="mailto:{{ $content['email'] }}">{{ $content['email'] }}</a></div>
                    </div>
                </div>
            @endif
            @if(!empty($content['phone']))
                <div class="wbp-contact-item">
                    <span class="wbp-contact-icon" aria-hidden="true">☎</span>
                    <div>
                        <div class="wbp-contact-label">Telepon</div>
                        <div class="wbp-contact-value"><a href="tel:{{ $content['phone'] }}">{{ $content['phone'] }}</a></div>
                    </div>
                </div>
            @endif
            @if(!empty($content['address']))
                <div class="wbp-contact-item">
                    <span class="wbp-contact-icon" aria-hidden="true">⌖</span>
                    <div>
                        <div class="wbp-contact-label">Alamat</div>
                        <div class="wbp-contact-value">{{ $content['address'] }}</div>
                    </div>
                </div>
            @endif
            @if(!empty($content['hours']))
                <div class="wbp-contact-item">
                    <span class="wbp-contact-icon" aria-hidden="true">◷</span>
                    <div>
                        <div class="wbp-contact-label">Jam Operasional</div>
                        <div class="wbp-contact-value">{{ $content['hours'] }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>