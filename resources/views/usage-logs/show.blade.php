@extends('layouts.app')

@section('title', 'Detail Log Penggunaan')

@section('content')
<div class="py-4">
    <div class="mb-4">
        <h2>Detail Log Penggunaan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('usage-logs.index') }}">Status Penggunaan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Log Penggunaan</h5>
                        <div>
                            <a href="{{ route('usage-logs.edit', $usageLog) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('usage-logs.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tanggal:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $usageLog->tanggal->format('d M Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Account:</strong>
                        </div>
                        <div class="col-md-8">
                            <a href="{{ route('accounts.show', $usageLog->account) }}">
                                {{ $usageLog->account->email }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tool:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $usageLog->tool->nama }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Limit Digunakan:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $usageLog->limit_used }} / {{ $usageLog->limit_total }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Persentase:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="progress" style="height: 30px; width: 300px;">
                                <div class="progress-bar bg-{{ $usageLog->status_color }}" 
                                     role="progressbar" 
                                     style="width: {{ min($usageLog->usage_percentage, 100) }}%">
                                    {{ number_format($usageLog->usage_percentage, 2) }}%
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Sisa Limit: {{ $usageLog->remaining_limit }}
                            </small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $usageLog->status_color }}">
                                {{ $usageLog->status_emoji }} {{ $usageLog->status }}
                            </span>
                        </div>
                    </div>

                    @if($usageLog->catatan)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Catatan:</strong>
                        </div>
                        <div class="col-md-8">
                            <div class="alert alert-info mb-0">
                                {{ $usageLog->catatan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Dibuat:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $usageLog->created_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Terakhir Diupdate:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $usageLog->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
