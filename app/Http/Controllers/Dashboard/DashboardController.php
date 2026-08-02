<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tools' => Tool::count(),
            'tools_aktif' => Tool::aktif()->count(),
            'tools_nonaktif' => Tool::nonAktif()->count(),
            'total_accounts' => Account::count(),
            'accounts_ready' => Account::ready()->count(),
            'accounts_in_use' => Account::inUse()->count(),
            'accounts_suspended' => Account::suspended()->count(),
            'accounts_expired' => Account::expired()->count(),
        ];
        
        // Recent activities (5 terakhir)
        $recentActivities = ActivityLog::with(['account', 'tool'])
            ->latest('waktu')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact('stats', 'recentActivities'));
    }
}
