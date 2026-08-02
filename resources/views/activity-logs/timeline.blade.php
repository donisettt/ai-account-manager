@extends('layouts.app')

@section('title', 'Timeline Aktivitas')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Timeline Aktivitas</h2>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
            <i class="bi bi-list"></i> List View
        </a>
    </div>

    @if($timeline->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <p class="text-muted mt-3">Belum ada log aktivitas</p>
        </div>
    @else
        @foreach($timeline as $date => $logs)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar3"></i> 
                        {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                        <span class="badge bg-primary">{{ $logs->count() }} aktivitas</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($logs as $log)
                            <div class="timeline-item mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="timeline-time">
                                            <strong>{{ $log->formatted_time }}</strong>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mx-3">
                                        <div class="timeline-icon bg-{{ $log->aktivitas_color }}">
                                            <i class="bi bi-{{ $log->aktivitas_icon }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="badge bg-{{ $log->aktivitas_color }}">{{ $log->aktivitas }}</span>
                                                        <span class="text-muted">-</span>
                                                        {{ $log->account->email }}
                                                        <span class="text-muted">menggunakan</span>
                                                        <span class="badge bg-secondary">{{ $log->tool->nama }}</span>
                                                    </h6>
                                                    @if($log->keterangan)
                                                        <p class="text-muted mb-0 small">
                                                            <i class="bi bi-chat-left-text"></i> {{ $log->keterangan }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('activity-logs.edit', $log) }}" class="btn btn-outline-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('activity-logs.destroy', $log) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Yakin ingin menghapus?')"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@push('styles')
<style>
.timeline {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-time {
    min-width: 60px;
    text-align: right;
    padding-top: 8px;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush
@endsection
