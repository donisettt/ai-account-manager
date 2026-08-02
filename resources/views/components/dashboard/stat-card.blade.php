@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="col-md-6 col-lg-3">
    <div class="card stats-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">{{ $title }}</h6>
                    <h3 class="mb-0 {{ $color !== 'primary' ? 'text-' . $color : '' }}">{{ $value }}</h3>
                </div>
                <div class="stats-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                    <i class="bi bi-{{ $icon }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .stats-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
</style>
@endpush
@endonce
