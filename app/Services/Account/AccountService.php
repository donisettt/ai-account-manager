<?php

namespace App\Services\Account;

use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountService
{
    public function getAllAccounts(): LengthAwarePaginator
    {
        return Account::with('tools')->latest()->paginate(10);
    }

    public function getAccountById(int $id): ?Account
    {
        return Account::with('tools')->find($id);
    }

    public function createAccount(array $data): Account
    {
        // Pisahkan data tools
        $tools = $data['tools'] ?? [];
        unset($data['tools']);

        // Create account (password akan otomatis di-encrypt oleh mutator)
        $account = Account::create($data);

        // Sync relasi dengan tools
        if (!empty($tools)) {
            $account->tools()->sync($tools);
        }

        return $account->load('tools');
    }

    public function updateAccount(Account $account, array $data): bool
    {
        // Pisahkan data tools
        $tools = $data['tools'] ?? [];
        unset($data['tools']);

        // Jika password kosong (tidak diubah), hapus dari data
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Update account
        $updated = $account->update($data);

        // Sync relasi dengan tools
        $account->tools()->sync($tools);

        return $updated;
    }

    public function deleteAccount(Account $account): bool
    {
        // Relasi akan otomatis terhapus karena onDelete cascade
        return $account->delete();
    }

    public function changeStatus(Account $account, string $status): bool
    {
        return $account->update(['status' => $status]);
    }

    public function getAccountsByProvider(string $provider): LengthAwarePaginator
    {
        return Account::with('tools')
            ->byProvider($provider)
            ->latest()
            ->paginate(10);
    }

    public function getAccountsByStatus(string $status): LengthAwarePaginator
    {
        return Account::with('tools')
            ->where('status', $status)
            ->latest()
            ->paginate(10);
    }
}
