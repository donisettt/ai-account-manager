@extends('layouts.app')

@section('title', 'Master Tool')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Master Tool</h2>
        <a href="{{ route('tools.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Tool
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Logo</th>
                            <th width="20%">Nama</th>
                            <th width="15%">Status</th>
                            <th width="30%">Keterangan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tools as $tool)
                            <tr>
                                <td>{{ $tools->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($tool->logo)
                                        <img src="{{ Storage::url($tool->logo) }}" 
                                             alt="{{ $tool->nama }}" 
                                             class="img-thumbnail"
                                             style="max-width: 60px; max-height: 60px; object-fit: contain;">
                                    @else
                                        <span class="badge bg-secondary">No Logo</span>
                                    @endif
                                </td>
                                <td><strong>{{ $tool->nama }}</strong></td>
                                <td>
                                    <form action="{{ route('tools.toggle-status', $tool) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $tool->status_aktif ? 'success' : 'secondary' }}">
                                            <i class="bi bi-{{ $tool->status_aktif ? 'check-circle' : 'x-circle' }}"></i>
                                            {{ $tool->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ Str::limit($tool->keterangan, 50) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tools.edit', $tool) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('tools.destroy', $tool) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus tool ini?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada data tool</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tools->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
