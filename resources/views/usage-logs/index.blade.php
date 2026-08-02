@extends('layouts.app')

@section('title', 'Status Penggunaan')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Status Penggunaan</h2>
        <a href="{{ route('usage-logs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Log
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">Total Logs</h6>
                    <h4 class="mb-0">{{ $stats['total_logs'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-success">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">🟢 Ready</h6>
                    <h4 class="mb-0 text-success">{{ $stats['status_ready'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">🟡 Warning</h6>
                    <h4 class="mb-0 text-warning">{{ $stats['status_warning'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-danger">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">🔴 Limit</h6>
                    <h4 class="mb-0 text-danger">{{ $stats['status_limit'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-dark">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">⚫ Maintenance</h6>
                    <h4 class="mb-0 text-dark">{{ $stats['status_maintenance'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-primary">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1 small">🔵 Dipakai</h6>
                    <h4 class="mb-0 text-primary">{{ $stats['status_in_use'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th width="15%">Account</th>
                            <th width="12%">Tool</th>
                            <th width="15%">Limit</th>
                            <th width="8%">Status</th>
                            <th width="23%">Catatan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usageLogs as $log)
                            <tr>
                                <td>{{ $usageLogs->firstItem() + $loop->index }}</td>
                                <td>
                                    <small>{{ $log->tanggal->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $log->account->email }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->tool->nama }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $log->status_color }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($log->usage_percentage, 100) }}%"
                                                     aria-valuenow="{{ $log->usage_percentage }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    {{ number_format($log->usage_percentage, 1) }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $log->limit_used }}/{{ $log->limit_total }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->status_color }}">
                                        {{ $log->status_emoji }} {{ $log->status }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($log->catatan, 40) }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('usage-logs.show', $log) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('usage-logs.edit', $log) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('usage-logs.destroy', $log) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus log ini?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Belum ada data log penggunaan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $usageLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
