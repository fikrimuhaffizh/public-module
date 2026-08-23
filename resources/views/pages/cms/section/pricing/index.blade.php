@extends('layouts.admin-app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="page-pretitle">CMS Landing</div>
        <h2 class="page-title">Paket Harga</h2>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Paket Harga</h3>
                <div class="card-actions">
                    <a href="{{ route('cms.pricing.create') }}" class="btn btn-primary" data-ajax-modal>
                        <i class="ti ti-plus"></i> Tambah Paket
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="width:40px"></th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Periode</th>
                            <th>Fitur</th>
                            <th>Unggulan</th>
                            <th>Aktif</th>
                            <th style="width:100px"></th>
                        </tr>
                    </thead>
                    <tbody class="sortable-list" data-url="{{ route('cms.pricing.reorder') }}">
                        @forelse($packages as $pkg)
                            <tr data-id="{{ $pkg->hashid }}" class="sortable-item">
                                <td><span class="sortable-handle cursor-grab text-muted"><i class="ti ti-grip-vertical"></i></span></td>
                                <td>
                                    <strong>{{ $pkg->name }}</strong>
                                    @if($pkg->description)
                                        <div class="text-muted small">{{ Str::limit($pkg->description, 60) }}</div>
                                    @endif
                                </td>
                                <td><span class="fw-bold text-primary">Rp {{ number_format((float) str_replace('.', '', $pkg->price), 0, ',', '.') }}</span></td>
                                <td>{{ $pkg->period ?? '-' }}</td>
                                <td>
                                    @if($pkg->features)
                                        <span class="badge bg-blue-lt">{{ count($pkg->features) }} fitur</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pkg->highlight)
                                        <span class="badge bg-yellow-lt"><i class="ti ti-star"></i> Unggulan</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" {{ $pkg->is_active ? 'checked' : '' }}
                                               onchange="axios.patch('{{ route('cms.landing.toggle-section', $pkg) }}', {}, { headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} })">
                                    </label>
                                </td>
                                <td>
                                    <a href="{{ route('cms.pricing.edit', $pkg) }}" class="btn btn-ghost-primary btn-sm" data-ajax-modal title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form action="{{ route('cms.pricing.destroy', $pkg) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus paket ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-ghost-danger btn-sm" title="Hapus"><i class="ti ti-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada paket harga.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
