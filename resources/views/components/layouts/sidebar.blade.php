<div class="col-md-3 col-lg-2 px-0 sidebar">
    <div class="py-4">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" 
               href="{{ route('dashboard.index') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            <div class="px-3 py-2 text-muted small fw-bold">MASTER DATA</div>
            
            <a class="nav-link {{ request()->routeIs('tools.*') ? 'active' : '' }}" 
               href="{{ route('tools.index') }}">
                <i class="bi bi-tools"></i> Master Tool
            </a>
            <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" 
               href="{{ route('accounts.index') }}">
                <i class="bi bi-person-badge"></i> Master Account
            </a>
            
            <div class="px-3 py-2 text-muted small fw-bold mt-3">MONITORING</div>
            
            <a class="nav-link {{ request()->routeIs('usage-logs.*') ? 'active' : '' }}" 
               href="{{ route('usage-logs.index') }}">
                <i class="bi bi-activity"></i> Status Penggunaan
            </a>
            <a class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" 
               href="{{ route('activity-logs.index') }}">
                <i class="bi bi-clock-history"></i> Log Aktivitas
            </a>
            
            <div class="px-3 py-2 text-muted small fw-bold mt-3">INTEGRASI</div>
            
            <a class="nav-link {{ request()->routeIs('telegram.*') ? 'active' : '' }}" 
               href="{{ route('telegram.settings') }}">
                <i class="bi bi-telegram"></i> Telegram Bot
            </a>
        </nav>
    </div>
</div>
