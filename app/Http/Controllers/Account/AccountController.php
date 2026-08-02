<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Tool;
use App\Services\Account\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function index(): View
    {
        $accounts = $this->accountService->getAllAccounts();
        
        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $tools = Tool::aktif()->orderBy('nama')->get();
        $providers = $this->getProviders();
        $statuses = $this->getStatuses();
        
        return view('accounts.create', compact('tools', 'providers', 'statuses'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $this->accountService->createAccount($request->validated());

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account berhasil ditambahkan');
    }

    public function show(Account $account): View
    {
        $account->load('tools');
        
        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account): View
    {
        $account->load('tools');
        $tools = Tool::aktif()->orderBy('nama')->get();
        $providers = $this->getProviders();
        $statuses = $this->getStatuses();
        
        return view('accounts.edit', compact('account', 'tools', 'providers', 'statuses'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->accountService->updateAccount($account, $request->validated());

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account berhasil diupdate');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->accountService->deleteAccount($account);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account berhasil dihapus');
    }

    private function getProviders(): array
    {
        return [
            'Google',
            'Microsoft',
            'Apple',
            'Facebook',
            'GitHub',
            'Custom',
        ];
    }

    private function getStatuses(): array
    {
        return [
            'Ready',
            'In Use',
            'Suspended',
            'Expired',
        ];
    }
}
