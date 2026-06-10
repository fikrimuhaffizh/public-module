@extends('layouts.tabler.app')

@section('header')
<x-ui.page-header title="Manajemen FAQ" pretitle="Info Publik">
    <x-slot:actions>
        <x-ui.button 
            type="create" 
            class="ajax-modal-btn d-none d-sm-inline-block" 
            data-url="{{ route('public.cms.faq.create') }}" 
            data-modal-title="Tambah FAQ" 
            text="Tambah FAQ" 
        />
    </x-slot:actions>
</x-ui.page-header>
@endsection

@section('content')
        <div class="row row-cards">
            @if($faqs->isEmpty())
                <div class="col-12">
                    <x-ui.card>
                        <x-ui.card-body>
                            <x-ui.empty-state
                                title="Belum ada FAQ"
                                text="Silakan tambahkan FAQ baru."
                                icon="ti ti-help-circle"
                            />
                        </x-ui.card-body>
                    </x-ui.card>
                </div>
            @else
                @foreach($faqs as $category => $items)
                    <div class="col-md-6">
                        <x-ui.card class="h-100">
                            <x-ui.card-header>
                                <h3 class="card-title">
                                    {{ $category ?: 'Umum (Tanpa Kategori)' }}
                                    <span class="badge bg-primary-lt ms-2">{{ $items->count() }}</span>
                                </h3>
                            </x-ui.card-header>
                            <x-ui.card-body class="p-0">
                                <div class="list-group list-group-flush sortable-list" data-category="{{ $category }}">
                                    @foreach($items as $faq)
                                        <div class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $faq->encrypted_faq_id }}">
                                            <div class="drag-handle cursor-move me-2">
                                                <i class="ti ti-grip-vertical text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold mb-1">{{ $faq->question }}</div>
                                                <div class="text-muted small text-truncate" style="max-width: 300px;">
                                                    {!! Str::limit(strip_tags($faq->answer), 100) !!}
                                                </div>
                                                <div class="mt-1">
                                                    @if($faq->is_active)
                                                        <span class="badge bg-success-lt">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary-lt">Draft</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="btn-list flex-nowrap ms-2">
                                                <x-ui.button 
                                                    type="button" 
                                                    class="btn-icon btn-ghost-primary ajax-modal-btn" 
                                                    icon="ti ti-pencil" 
                                                    data-url="{{ route('public.cms.faq.edit', $faq->encrypted_faq_id) }}"
                                                    data-modal-title="Edit FAQ"
                                                    title="Edit"
                                                />
                                                <x-ui.button 
                                                    type="button" 
                                                    class="btn-icon btn-ghost-danger ajax-delete" 
                                                    icon="ti ti-trash" 
                                                    data-url="{{ route('public.cms.faq.destroy', $faq->encrypted_faq_id) }}"
                                                    data-title="Hapus FAQ?"
                                                    title="Delete"
                                                />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui.card-body>
                        </x-ui.card>
                    </div>
                @endforeach
            @endif
        </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sortables = document.querySelectorAll('.sortable-list');
    
    sortables.forEach(el => {
        new Sortable(el, {
            group: 'faq-list', // Allow dragging between lists
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-indigo-lt',
            onEnd: function (evt) {
                saveOrder();
            }
        });
    });

    function saveOrder() {
        let orderData = {};
        
        document.querySelectorAll('.sortable-list').forEach(list => {
            let category = list.dataset.category || 'null'; // Use 'null' string for empty category
            let items = [];
            
            list.querySelectorAll('.list-group-item').forEach(item => {
                items.push(item.dataset.id);
            });
            
            if (items.length > 0) {
                orderData[category] = items;
            }
        });

        // Send to server
        axios.post('{{ route("public.cms.faq.reorder") }}', { order: orderData })
            .then(response => {
                showSuccessMessage('Urutan & Kategori berhasil diperbarui');
            })
            .catch(error => {
                showErrorMessage('Gagal menyimpan urutan');
                console.error(error);
            });
    }
});
</script>
@endpush
