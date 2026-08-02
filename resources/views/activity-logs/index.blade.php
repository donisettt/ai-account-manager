@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Log Aktivitas</h2>
        <div class="btn-group">
            <a href="{{ route('activity-logs.timeline') }}" class="btn btn-outline-primary">
                <i class="bi bi-clock-history"></i> Timeline View
            </a>
            <a href="{{ route('activity-logs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Log
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quick Log Action -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Log</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('activity-logs.quick') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="account_id" required>
                        <option value="">Pilih Account</option>
                        @foreach(\App\Models\Account::orderBy('email')->get() as $account)
                            <option value="{{ $account->id }}">{{ $account->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="tool_id" required>
                        <option value="">Pilih Tool</option>
                        @foreach(\App\Models\Tool::aktif()->orderBy('nama')->get() as $tool)
                            <option value="{{ $tool->id }}">{{ $tool->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="aktivitas" required>
                        <option value="Dipakai">▶️ Dipakai</option>
                        <option value="Limit">🔴 Limit</option>
                        <option value="Reset">🔄 Reset</option>
                        <option value="Login">🔓 Login</option>
                        <option value="Logout">🔒 Logout</option>
                        <option value="Error">⚠️ Error</option>
                        <option value="Maintenance">🔧 Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm" name="keterangan" placeholder="Keterangan (optional)">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-plus"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card text-center border-primary">
                <div class="card-body py-2">
                    <small class="text-muted">▶️ Dipakai</small>
                    <h5 class="mb-0 text-primary">{{ $stats['aktivitas_dipakai'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center border-danger">
                <div class="card-body py-2">
                    <small class="text-muted">🔴 Limit</small>
                    <h5 class="mb-0 text-danger">{{ $stats['aktivitas_limit'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center border-success">
                <div class="card-body py-2">
                    <small class="text-muted">🔄 Reset</small>
                    <h5 class="mb-0 text-success">{{ $stats['aktivitas_reset'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center border-warning">
                <div class="card-body py-2">
                    <small class="text-muted">⚠️ Error</small>
                    <h5 class="mb-0 text-warning">{{ $stats['aktivitas_error'] }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th width="8%">Waktu</th>
                            <th width="18%">Account</th>
                            <th width="12%">Tool</th>
                            <th width="12%">Aktivitas</th>
                            <th width="35%">Keterangan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>
                                    <div><strong>{{ $log->formatted_time }}</strong></div>
                                    <small class="text-muted">{{ $log->formatted_date }}</small>
                                </td>
                                <td>{{ $log->account->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $log->tool->nama }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->aktivitas_color }}">
                                        <i class="bi bi-{{ $log->aktivitas_icon }}"></i> {{ $log->aktivitas }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($log->keterangan, 60) ?: '-' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('activity-logs.edit', $log) }}" 
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('activity-logs.destroy', $log) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus log ini?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus">
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
                                    <p class="text-muted mt-2">Belum ada data log aktivitas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $activityLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
