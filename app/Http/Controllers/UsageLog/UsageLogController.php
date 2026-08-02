<?php

namespace App\Http\Controllers\UsageLog;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsageLog\StoreUsageLogRequest;
use App\Http\Requests\UsageLog\UpdateUsageLogRequest;
use App\Models\UsageLog;
use App\Models\Account;
use App\Models\Tool;
use App\Services\UsageLog\UsageLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UsageLogController extends Controller
{
    public function __construct(
        private UsageLogService $usageLogService
    ) {}

    public function index(): View
    {
        $usageLogs = $this->usageLogService->getAllUsageLogs();
        $stats = $this->usageLogService->getUsageStatistics();
        
        return view('usage-logs.index', compact('usageLogs', 'stats'));
    }

    public function create(): View
    {
        $accounts = Account::with('tools')->orderBy('email')->get();
        $tools = Tool::aktif()->orderBy('nama')->get();
        $statuses = $this->getStatuses();
        
        return view('usage-logs.create', compact('accounts', 'tools', 'statuses'));
    }

    public function store(StoreUsageLogRequest $request): RedirectResponse
    {
        $this->usageLogService->createUsageLog($request->validated());

        return redirect()
            ->route('usage-logs.index')
            ->with('success', 'Log penggunaan berhasil ditambahkan');
    }

    public function show(UsageLog $usageLog): View
    {
        $usageLog->load(['account', 'tool']);
        
        return view('usage-logs.show', compact('usageLog'));
    }

    public function edit(UsageLog $usageLog): View
    {
        $usageLog->load(['account', 'tool']);
        $accounts = Account::orderBy('email')->get();
        $tools = Tool::aktif()->orderBy('nama')->get();
        $statuses = $this->getStatuses();
        
        return view('usage-logs.edit', compact('usageLog', 'accounts', 'tools', 'statuses'));
    }

    public function update(UpdateUsageLogRequest $request, UsageLog $usageLog): RedirectResponse
    {
        $this->usageLogService->updateUsageLog($usageLog, $request->validated());

        return redirect()
            ->route('usage-logs.index')
            ->with('success', 'Log penggunaan berhasil diupdate');
    }

    public function destroy(UsageLog $usageLog): RedirectResponse
    {
        $this->usageLogService->deleteUsageLog($usageLog);

        return redirect()
            ->route('usage-logs.index')
            ->with('success', 'Log penggunaan berhasil dihapus');
    }

    private function getStatuses(): array
    {
        return [
            'Ready' => '🟢 Ready',
            'Warning' => '🟡 Warning',
            'Limit' => '🔴 Limit',
            'Maintenance' => '⚫ Maintenance',
            'Sedang Dipakai' => '🔵 Sedang Dipakai',
        ];
    }
}
