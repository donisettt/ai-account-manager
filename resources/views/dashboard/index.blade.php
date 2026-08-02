@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="text-muted">
            <i class="bi bi-calendar3"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Master Tool Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="bi bi-tools text-primary"></i> Master Tool
            </h5>
        </div>
        
        <!-- Info Box: Total Tools -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="info-box bg-gradient-info">
                <div class="info-box-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Total Tools</span>
                    <span class="info-box-number">{{ $stats['total_tools'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Semua tools terdaftar
                    </span>
                </div>
            </div>
        </div>

        <!-- Info Box: Aktif -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="info-box bg-gradient-success">
                <div class="info-box-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Aktif</span>
                    <span class="info-box-number">{{ $stats['tools_aktif'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $stats['total_tools'] > 0 ? ($stats['tools_aktif'] / $stats['total_tools'] * 100) : 0 }}%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $stats['total_tools'] > 0 ? number_format(($stats['tools_aktif'] / $stats['total_tools'] * 100), 1) : 0 }}% dari total
                    </span>
                </div>
            </div>
        </div>

        <!-- Info Box: Non-Aktif -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="info-box bg-gradient-secondary">
                <div class="info-box-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="info-box-content">
                    <span class="info-box-text">Non-Aktif</span>
                    <span class="info-box-number">{{ $stats['tools_nonaktif'] }}</span>
                    <div class="progress">
                        <div class="progress-bar bg-secondary" style="width: {{ $stats['total_tools'] > 0 ? ($stats['tools_nonaktif'] / $stats['total_tools'] * 100) : 0 }}%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $stats['total_tools'] > 0 ? number_format(($stats['tools_nonaktif'] / $stats['total_tools'] * 100), 1) : 0 }}% dari total
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Account Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="bi bi-person-badge text-primary"></i> Master Account
            </h5>
        </div>

        <!-- Small Box: Total Accounts -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_accounts'] }}</h3>
                    <p>Total Accounts</p>
                </div>
                <div class="icon">
                    <i class="bi bi-person-badge"></i>
                </div>
                <a href="{{ route('accounts.index') }}" class="small-box-footer">
                    Lihat Detail <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Small Box: Ready -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['accounts_ready'] }}</h3>
                    <p>Ready</p>
                </div>
                <div class="icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <a href="{{ route('accounts.index') }}" class="small-box-footer">
                    Lihat Detail <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Small Box: In Use -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['accounts_in_use'] }}</h3>
                    <p>In Use</p>
                </div>
                <div class="icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <a href="{{ route('accounts.index') }}" class="small-box-footer">
                    Lihat Detail <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Small Box: Suspended -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['accounts_suspended'] }}</h3>
                    <p>Suspended</p>
                </div>
                <div class="icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <a href="{{ route('accounts.index') }}" class="small-box-footer">
                    Lihat Detail <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Small Box: Expired -->
        <div class="col-12 col-lg-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['accounts_expired'] }}</h3>
                    <p>Expired</p>
                </div>
                <div class="icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <a href="{{ route('accounts.index') }}" class="small-box-footer">
                    Lihat Detail <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="row g-3">
        <!-- Welcome Card -->
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door"></i> Selamat Datang, {{ auth()->user()->name }}! 👋
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        Selamat datang di <strong>Sistem Monitoring Akun AI Manager</strong>. 
                        Kelola dan monitor semua akun AI Anda dalam satu platform terpusat.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="description-block border-end">
                                <h5 class="description-header text-primary">{{ $stats['total_tools'] }}</h5>
                                <span class="description-text">TOTAL TOOLS</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="description-block">
                                <h5 class="description-header text-success">{{ $stats['total_accounts'] }}</h5>
                                <span class="description-text">TOTAL ACCOUNTS</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Quick Actions:</strong>
                        <a href="{{ route('accounts.create') }}" class="alert-link">Tambah Account</a> · 
                        <a href="{{ route('usage-logs.index') }}" class="alert-link">Cek Status</a> · 
                        <a href="{{ route('telegram.settings') }}" class="alert-link">Setup Telegram</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history"></i> Aktivitas Terbaru
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-tool btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentActivities->count() > 0)
                        <ul class="products-list product-list-in-card">
                            @foreach($recentActivities as $activity)
                                <li class="item">
                                    <div class="product-img">
                                        <span class="badge rounded-circle bg-{{ 
                                            match($activity->aktivitas) {
                                                'Dipakai' => 'primary',
                                                'Selesai' => 'success',
                                                'Limit' => 'danger',
                                                'Reset' => 'info',
                                                'Error' => 'warning',
                                                default => 'secondary'
                                            }
                                        }}" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                            <i class="bi bi-{{ 
                                                match($activity->aktivitas) {
                                                    'Dipakai' => 'play-circle',
                                                    'Selesai' => 'check-circle',
                                                    'Limit' => 'x-circle',
                                                    'Reset' => 'arrow-clockwise',
                                                    'Error' => 'exclamation-triangle',
                                                    default => 'circle'
                                                }
                                            }}"></i>
                                        </span>
                                    </div>
                                    <div class="product-info">
                                        <span class="product-title">
                                            {{ $activity->account->email }}
                                            <span class="badge badge-sm bg-secondary float-end">{{ $activity->tool->nama }}</span>
                                        </span>
                                        <span class="product-description">
                                            <strong>{{ $activity->aktivitas }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> {{ $activity->waktu->diffForHumans() }}
                                            </small>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #dee2e6;"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
                @if($recentActivities->count() > 0)
                    <div class="card-footer text-center">
                        <a href="{{ route('activity-logs.index') }}" class="text-decoration-none">
                            Lihat Semua Aktivitas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* AdminLTE Style Info Boxes */
.info-box {
    display: flex;
    border-radius: 0.5rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 1rem;
    min-height: 100px;
    padding: 0.75rem;
    position: relative;
    overflow: hidden;
    color: white;
}

.info-box-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 90px;
    font-size: 3rem;
    opacity: 0.8;
}

.info-box-content {
    flex: 1;
    padding-left: 1rem;
}

.info-box-text {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 0.9;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 2rem;
}

.info-box .progress {
    background-color: rgba(255,255,255,.2);
    height: 3px;
    margin: 5px 0;
}

.info-box .progress .progress-bar {
    background-color: rgba(255,255,255,.7);
}

.progress-description {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 3px;
}

/* Gradient backgrounds */
.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
}

/* AdminLTE Style Small Boxes */
.small-box {
    border-radius: 0.5rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    color: white;
}

.small-box > .inner {
    padding: 1rem;
}

.small-box h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 10px;
    padding: 0;
    white-space: nowrap;
}

.small-box p {
    font-size: 1rem;
    margin: 0;
    opacity: 0.9;
}

.small-box .icon {
    position: absolute;
    top: -10px;
    right: 15px;
    font-size: 90px;
    opacity: 0.2;
    transition: transform 0.3s ease-in-out;
}

.small-box:hover .icon {
    transform: scale(1.1);
}

.small-box .small-box-footer {
    background-color: rgba(0,0,0,.1);
    color: rgba(255,255,255,.8);
    display: block;
    padding: 0.5rem;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
    transition: background-color 0.3s;
}

.small-box .small-box-footer:hover {
    background-color: rgba(0,0,0,.2);
    color: white;
}

/* Card Outlines */
.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.card-success.card-outline {
    border-top: 3px solid #28a745;
}

/* Description Blocks */
.description-block {
    padding: 0 10px;
    text-align: center;
}

.description-header {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.description-text {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 600;
}

/* Products List (Activities) */
.products-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.products-list > .item {
    border-radius: 0.25rem;
    padding: 10px;
    display: flex;
}

.products-list > .item:hover {
    background-color: #f8f9fa;
}

.product-img {
    margin-right: 15px;
}

.product-info {
    flex: 1;
}

.product-title {
    display: block;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-description {
    display: block;
    color: #6c757d;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
}

/* Card Tools */
.card-tools {
    float: right;
    margin-right: -0.625rem;
}

.btn-tool {
    background-color: transparent;
    border: none;
    color: #6c757d;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.btn-tool:hover {
    color: #007bff;
}

/* Breadcrumb */
.breadcrumb {
    background: none;
    padding: 0;
    margin-bottom: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    font-size: 1.2rem;
}
</style>
@endpush
@endsection
