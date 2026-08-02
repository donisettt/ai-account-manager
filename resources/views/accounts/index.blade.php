@extends('layouts.app')

@section('title', 'Master Account')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Master Account</h2>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Account
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
                            <th width="15%">Email</th>
                            <th width="10%">Provider</th>
                            <th width="10%">Recovery Email</th>
                            <th width="10%">Status</th>
                            <th width="25%">Tools</th>
                            <th width="15%">Catatan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{ $accounts->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $account->email }}</strong>
                                    <button type="button" 
                                            class="btn btn-sm btn-link p-0 ms-1" 
                                            onclick="copyToClipboard('{{ $account->email }}')"
                                            title="Copy email">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $account->provider }}</span>
                                </td>
                                <td>
                                    @if($account->recovery_email)
                                        <small>{{ $account->recovery_email }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Ready' => 'success',
                                            'In Use' => 'primary',
                                            'Suspended' => 'warning',
                                            'Expired' => 'danger',
                                        ];
                                        $color = $statusColors[$account->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $account->status }}</span>
                                </td>
                                <td>
                                    @if($account->tools->count() > 0)
                                        @foreach($account->tools as $tool)
                                            <span class="badge bg-secondary mb-1">
                                                <i class="bi bi-check-circle"></i> {{ $tool->nama }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Belum ada tool</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ Str::limit($account->catatan, 30) }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('accounts.show', $account) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('accounts.edit', $account) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('accounts.destroy', $account) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus account ini?')"
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
                                    <p class="text-muted mt-2">Belum ada data account</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Email berhasil dicopy!');
    });
}
</script>
@endpush
@endsection
