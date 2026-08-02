@extends('layouts.app')

@section('title', 'Detail Account')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Detail Account</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Master Account</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Account</h5>
                        <div>
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $account->email }}
                            <button type="button" 
                                    class="btn btn-sm btn-link p-0 ms-1" 
                                    onclick="copyToClipboard('{{ $account->email }}')"
                                    title="Copy email">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Password:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-2">
                                <span id="passwordText">••••••••</span>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        onclick="togglePasswordVisibility()">
                                    <i class="bi bi-eye" id="eyeIcon"></i> <span id="btnText">Tampilkan</span>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary" 
                                        onclick="copyPassword()"
                                        id="copyBtn"
                                        style="display: none;">
                                    <i class="bi bi-clipboard"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Provider:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ $account->provider }}</span>
                        </div>
                    </div>

                    @if($account->provider_login)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Provider Login:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $account->provider_login }}
                        </div>
                    </div>
                    @endif

                    @if($account->recovery_email)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Recovery Email:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $account->recovery_email }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tools yang Digunakan:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($account->tools->count() > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($account->tools as $tool)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-check-circle"></i> {{ $tool->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Belum ada tool yang terhubung</span>
                            @endif
                        </div>
                    </div>

                    @if($account->catatan)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Catatan:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="alert alert-info mb-0">
                                {{ $account->catatan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Dibuat:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $account->created_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Terakhir Diupdate:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $account->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isPasswordVisible = false;
const actualPassword = '{{ $account->decrypted_password }}';

function togglePasswordVisibility() {
    const passwordText = document.getElementById('passwordText');
    const eyeIcon = document.getElementById('eyeIcon');
    const btnText = document.getElementById('btnText');
    const copyBtn = document.getElementById('copyBtn');
    
    if (isPasswordVisible) {
        passwordText.textContent = '••••••••';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
        btnText.textContent = 'Tampilkan';
        copyBtn.style.display = 'none';
        isPasswordVisible = false;
    } else {
        passwordText.textContent = actualPassword;
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
        btnText.textContent = 'Sembunyikan';
        copyBtn.style.display = 'inline-block';
        isPasswordVisible = true;
    }
}

function copyPassword() {
    navigator.clipboard.writeText(actualPassword).then(() => {
        alert('Password berhasil dicopy!');
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Email berhasil dicopy!');
    });
}
</script>
@endpush
@endsection
