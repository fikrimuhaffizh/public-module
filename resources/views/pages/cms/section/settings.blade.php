@extends('public::layouts.public-layout')

@section('title', 'Template Landing Page')

@section('header')
<x-ui.page-header title="Template" pretitle="Landing Page" />
@endsection

@section('content')

<form id="landing-template-form" method="POST" action="{{ route('cms.section.template.update') }}">
    @csrf
    @method('PUT')

    @foreach($themeGroups as $category => $themes)
        @php
            $categoryLabel = $category === 'umkm' ? 'UMKM & Bisnis' : 'Institusi & Platform';
        @endphp
        <h2 class="mb-1 text-uppercase text-muted small fw-bold">{{ $categoryLabel }}</h2>
        <div class="row row-cards">
            @foreach($themes as $template => $meta)
                @php($isSelected = old('landing_template', $selectedTemplate) === $template)
                <div class="col-md-6 col-xl-3">
                    <label class="card card-link card-link-pop h-100 cursor-pointer {{ $isSelected ? 'border-primary border-2' : '' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <span class="avatar avatar-lg bg-primary-lt text-primary"><i class="ti ti-{{ $meta['icon'] ?? 'palette' }} fs-1"></i></span>
                                <input class="form-check-input" type="radio" name="landing_template" value="{{ $template }}" @checked($isSelected) @cannot('public.cms.update') disabled @endcannot>
                            </div>
                            @php($thumbnail = public_path('theme-thumbnails/'.$template.'.svg'))
                            @if (file_exists($thumbnail))
                                <img src="{{ asset('theme-thumbnails/'.$template.'.svg') }}" alt="Pratinjau {{ $meta['name'] }}" class="w-100 rounded-3 border mb-3" style="aspect-ratio: 16 / 10; object-fit: cover;">
                            @endif
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h3 class="card-title mb-0">{{ $meta['name'] }}</h3>
                                @if($isSelected)
                                    <span class="badge bg-primary-lt">Sedang digunakan</span>
                                @endif
                            </div>
                            <p class="text-muted mb-3">{{ $meta['description'] }}</p>
                            <a href="{{ route('public.preview', ['template' => $template]) }}" target="_blank" class="text-primary" onclick="event.stopPropagation()">Lihat pratinjau <i class="ti ti-arrow-up-right ms-1"></i></a>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach
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
                axios.put('{{ route('cms.section.template.update') }}', {
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
