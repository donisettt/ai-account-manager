@extends('layouts.app')

@section('title', 'Tambah Account')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Account</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Master Account</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="akun01@gmail.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="••••••••"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Password akan di-encrypt (dapat dibaca kembali)</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                                    <select class="form-select @error('provider') is-invalid @enderror" 
                                            id="provider" 
                                            name="provider"
                                            required>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider }}" {{ old('provider', 'Google') == $provider ? 'selected' : '' }}>
                                                {{ $provider }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="provider_login" class="form-label">Provider Login</label>
                                    <input type="text" 
                                           class="form-control @error('provider_login') is-invalid @enderror" 
                                           id="provider_login" 
                                           name="provider_login" 
                                           value="{{ old('provider_login') }}" 
                                           placeholder="URL atau method login">
                                    @error('provider_login')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recovery_email" class="form-label">Recovery Email</label>
                                    <input type="email" 
                                           class="form-control @error('recovery_email') is-invalid @enderror" 
                                           id="recovery_email" 
                                           name="recovery_email" 
                                           value="{{ old('recovery_email') }}" 
                                           placeholder="recovery@email.com">
                                    @error('recovery_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status"
                                            required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ old('status', 'Ready') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tools yang Digunakan</label>
                            <div class="border rounded p-3">
                                <div class="row">
                                    @foreach($tools as $tool)
                                        <div class="col-md-3 col-sm-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="tools[]" 
                                                       value="{{ $tool->id }}" 
                                                       id="tool{{ $tool->id }}"
                                                       {{ in_array($tool->id, old('tools', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tool{{ $tool->id }}">
                                                    {{ $tool->nama }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('tools')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" 
                                      name="catatan" 
                                      rows="3"
                                      placeholder="Premium sementara, atau catatan lainnya...">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
@endsection
