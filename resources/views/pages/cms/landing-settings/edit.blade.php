@extends('public::layouts.public-layout')

@section('title', 'Template Landing Page')

@section('header')
<x-ui.page-header title="Template Landing Page" pretitle="Content Management" />
@endsection

@section('content')
<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
    <i class="ti ti-info-circle fs-2 me-2"></i>
    <div>Pilih salah satu template untuk langsung <strong>diterapkan</strong>. Pratinjau tidak mengubah template yang sedang digunakan.</div>
</div>
<form id="landing-template-form" method="POST" action="{{ route('cms.landing.update') }}">
    @csrf
    @method('PUT')
    <div class="row row-cards">
        @foreach($templates as $template)
            @php
                $details = match($template) {
                    'modern' => ['Modern', 'Visual progresif dengan pengalaman digital yang dinamis.', 'sparkles'],
                    'editorial' => ['Editorial', 'Berorientasi konten dengan tipografi dan berita yang kuat.', 'news'],
                    'corporate' => ['Corporate', 'Tampilan mewah dan elegan untuk institusi serta mitra korporat.', 'building-skyscraper'],
                    'launch' => ['Launch UI', 'Desain segar dengan hero, fitur, produk, statistik, dan CTA yang dapat dikelola penuh.', 'rocket'],
                    'aurora' => ['Aurora', 'Dark-mode bento grid dengan efek aurora dan glassmorphism untuk nuansa SaaS modern.', 'brand-aurora'],
                    'enterprise' => ['Enterprise', 'Tampilan profesional monokrom dengan aksen biru, fokus pada kepercayaan dan data.', 'shield-check'],
                    'registration' => ['Registration', 'Berorientasi pendaftaran dengan form ringkasan, langkah-langkah, dan testimoni.', 'clipboard-check'],
                    'profile' => ['Profile', 'Elegant company profile dengan tipografi serif, kutipan, dan visual bersih.', 'building-arch'],
                    'campus' => ['Campus', 'Beranda kampus akademik dengan foto, highlight bericon, program, dan statistik.', 'school'],
                    'admissions' => ['Admissions', 'Halaman pendaftaran dengan alur langkah, jalur masuk, dan informasi biaya.', 'clipboard-list'],
                    'tracer' => ['Tracer Study', 'Jejak alumni dengan dashboard data, metrik lulusan, dan kuesioner tracer.', 'chart-bar'],
                };
            @endphp
            @php($isSelected = old('landing_template', $selectedTemplate) === $template)
            <div class="col-md-6 col-xl-3">
                <label class="card card-link card-link-pop h-100 cursor-pointer {{ $isSelected ? 'border-primary border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <span class="avatar avatar-lg bg-primary-lt text-primary"><i class="ti ti-{{ $details[2] }} fs-1"></i></span>
                            <input class="form-check-input" type="radio" name="landing_template" value="{{ $template }}" @checked($isSelected) @cannot('public.cms.update') disabled @endcannot>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h3 class="card-title mb-0">{{ $details[0] }}</h3>
                            @if($isSelected)
                                <span class="badge bg-primary-lt">Sedang digunakan</span>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ $details[1] }}</p>
                        <a href="{{ route('public.preview', ['template' => $template]) }}" target="_blank" class="text-primary" onclick="event.stopPropagation()">Lihat pratinjau <i class="ti ti-arrow-up-right ms-1"></i></a>
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const templateForm = document.getElementById('landing-template-form');
    if (templateForm) {
        const templateInputs = templateForm.querySelectorAll('input[name="landing_template"]');
        templateInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedTemplate = this.value;
                
                // Update UI immediately for better UX
                templateInputs.forEach(t => {
                    const card = t.closest('.card');
                    if (t.value === selectedTemplate) {
                        card.classList.add('border-primary', 'border-2');
                        const badge = card.querySelector('.badge');
                        if (!badge) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge bg-primary-lt';
                            newBadge.textContent = 'Sedang digunakan';
                            card.querySelector('.d-flex.align-items-center.gap-2').appendChild(newBadge);
                        }
                    } else {
                        card.classList.remove('border-primary', 'border-2');
                        const badge = card.querySelector('.badge');
                        if (badge) badge.remove();
                    }
                });

                // Send auto-save request
                axios.put('{{ route('cms.landing.update') }}', {
                    _token: '{{ csrf_token() }}',
                    landing_template: selectedTemplate
                })
                .then(response => {
                    window.showSuccessMessage('Template berhasil diubah ke ' + selectedTemplate + '!');
                })
                .catch(error => {
                    console.error('Template save error:', error);
                    let msg = 'Gagal mengubah template!';
                    if (error.response?.data?.message) {
                        msg = error.response.data.message;
                    } else if (error.response?.data?.errors) {
                        msg = Object.values(error.response.data.errors).flat().join(', ');
                    }
                    window.showErrorMessage(msg);
                });
            });
        });
    }
});
</script>
@endpush
