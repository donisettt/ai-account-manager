@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h2 class="mb-4">Dashboard</h2>
    
    <!-- Master Tool Stats -->
    <h5 class="mb-3"><i class="bi bi-tools"></i> Master Tool</h5>
    <div class="row g-4 mb-4">
        <x-dashboard.stat-card 
            title="Total Tools"
            :value="$stats['total_tools']"
            icon="tools"
            color="primary"
        />
        
        <x-dashboard.stat-card 
            title="Aktif"
            :value="$stats['tools_aktif']"
            icon="check-circle"
            color="success"
        />
        
        <x-dashboard.stat-card 
            title="Non-Aktif"
            :value="$stats['tools_nonaktif']"
            icon="x-circle"
            color="secondary"
        />
    </div>

    <!-- Master Account Stats -->
    <h5 class="mb-3 mt-4"><i class="bi bi-person-badge"></i> Master Account</h5>
    <div class="row g-4 mb-4">
        <x-dashboard.stat-card 
            title="Total Accounts"
            :value="$stats['total_accounts']"
            icon="person-badge"
            color="primary"
        />
        
        <x-dashboard.stat-card 
            title="Ready"
            :value="$stats['accounts_ready']"
            icon="check-circle"
            color="success"
        />
        
        <x-dashboard.stat-card 
            title="In Use"
            :value="$stats['accounts_in_use']"
            icon="hourglass-split"
            color="info"
        />
        
        <x-dashboard.stat-card 
            title="Suspended"
            :value="$stats['accounts_suspended']"
            icon="exclamation-circle"
            color="warning"
        />
        
        <x-dashboard.stat-card 
            title="Expired"
            :value="$stats['accounts_expired']"
            icon="x-circle"
            color="danger"
        />
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-dashboard.welcome-card />
        </div>
        
        <!-- Recent Activities Widget -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h6>
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentActivities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentActivities as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
                                            <span class="badge bg-{{ $activity->aktivitas_color }} rounded-pill">
                                                <i class="bi bi-{{ $activity->aktivitas_icon }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small">
                                                <strong>{{ $activity->account->email }}</strong>
                                                <span class="text-muted">· {{ $activity->tool->nama }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                {{ $activity->aktivitas }} · {{ $activity->waktu->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0 small">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
