@extends('layouts.app')

@section('title', 'Tambah Log Aktivitas')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Tambah Log Aktivitas</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Log Aktivitas</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('activity-logs.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Account <span class="text-danger">*</span></label>
                                    <select class="form-select @error('account_id') is-invalid @enderror" 
                                            id="account_id" 
                                            name="account_id"
                                            required>
                                        <option value="">Pilih Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tool_id" class="form-label">Tool <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tool_id') is-invalid @enderror" 
                                            id="tool_id" 
                                            name="tool_id"
                                            required>
                                        <option value="">Pilih Tool</option>
                                        @foreach($tools as $tool)
                                            <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}>
                                                {{ $tool->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tool_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="waktu" class="form-label">Waktu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('waktu') is-invalid @enderror" 
                                           id="waktu" 
                                           name="waktu" 
                                           value="{{ old('waktu', now()->format('Y-m-d\TH:i')) }}"
                                           required>
                                    <small class="text-muted">Timezone: Asia/Jakarta (WIB)</small>
                                    @error('waktu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="aktivitas" class="form-label">Aktivitas <span class="text-danger">*</span></label>
                                    <select class="form-select @error('aktivitas') is-invalid @enderror" 
                                            id="aktivitas" 
                                            name="aktivitas"
                                            required>
                                        @foreach($aktivitasList as $value => $label)
                                            <option value="{{ $value }}" {{ old('aktivitas', 'Dipakai') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('aktivitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="Keterangan aktivitas...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
