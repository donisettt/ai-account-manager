<?php

namespace App\Http\Controllers\ActivityLog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityLog\StoreActivityLogRequest;
use App\Http\Requests\ActivityLog\UpdateActivityLogRequest;
use App\Models\ActivityLog;
use App\Models\Account;
use App\Models\Tool;
use App\Services\ActivityLog\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function index(): View
    {
        $activityLogs = $this->activityLogService->getAllActivityLogs();
        $stats = $this->activityLogService->getActivityStatistics();
        
        return view('activity-logs.index', compact('activityLogs', 'stats'));
    }

    public function create(): View
    {
        $accounts = Account::with('tools')->orderBy('email')->get();
        $tools = Tool::aktif()->orderBy('nama')->get();
        $aktivitasList = $this->getAktivitasList();
        
        return view('activity-logs.create', compact('accounts', 'tools', 'aktivitasList'));
    }

    public function store(StoreActivityLogRequest $request): RedirectResponse
    {
        $this->activityLogService->createActivityLog($request->validated());

        return redirect()
            ->route('activity-logs.index')
            ->with('success', 'Log aktivitas berhasil ditambahkan');
    }

    public function edit(ActivityLog $activityLog): View
    {
        $activityLog->load(['account', 'tool']);
        $accounts = Account::orderBy('email')->get();
        $tools = Tool::aktif()->orderBy('nama')->get();
        $aktivitasList = $this->getAktivitasList();
        
        return view('activity-logs.edit', compact('activityLog', 'accounts', 'tools', 'aktivitasList'));
    }

    public function update(UpdateActivityLogRequest $request, ActivityLog $activityLog): RedirectResponse
    {
        $this->activityLogService->updateActivityLog($activityLog, $request->validated());

        return redirect()
            ->route('activity-logs.index')
            ->with('success', 'Log aktivitas berhasil diupdate');
    }

    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        $this->activityLogService->deleteActivityLog($activityLog);

        return redirect()
            ->route('activity-logs.index')
            ->with('success', 'Log aktivitas berhasil dihapus');
    }

    // Quick action untuk log cepat
    public function quickLog(Request $request): RedirectResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'tool_id' => 'required|exists:tools,id',
            'aktivitas' => 'required|in:Dipakai,Limit,Reset,Login,Logout,Error,Maintenance',
            'keterangan' => 'nullable|string',
        ]);

        $this->activityLogService->createActivityLog([
            'account_id' => $request->account_id,
            'tool_id' => $request->tool_id,
            'waktu' => now(),
            'aktivitas' => $request->aktivitas,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Log aktivitas berhasil ditambahkan');
    }

    // Timeline view
    public function timeline(): View
    {
        $timeline = $this->activityLogService->getTimelineGroupedByDate();
        
        return view('activity-logs.timeline', compact('timeline'));
    }

    private function getAktivitasList(): array
    {
        return [
            'Dipakai' => '▶️ Dipakai',
            'Limit' => '🔴 Limit',
            'Reset' => '🔄 Reset',
            'Login' => '🔓 Login',
            'Logout' => '🔒 Logout',
            'Error' => '⚠️ Error',
            'Maintenance' => '🔧 Maintenance',
        ];
    }
}
