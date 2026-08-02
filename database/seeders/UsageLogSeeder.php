<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsageLog;
use App\Models\Account;
use App\Models\Tool;
use Carbon\Carbon;

class UsageLogSeeder extends Seeder
{
    public function run(): void
    {
        $account1 = Account::where('email', 'akun01@gmail.com')->first();
        $account2 = Account::where('email', 'akun02@outlook.com')->first();
        $account3 = Account::where('email', 'akun03@yahoo.com')->first();

        $kiro = Tool::where('nama', 'Kiro')->first();
        $claude = Tool::where('nama', 'Claude')->first();
        $antigravity = Tool::where('nama', 'Antigravity')->first();
        $gemini = Tool::where('nama', 'Gemini')->first();

        if ($account1 && $kiro) {
            UsageLog::create([
                'account_id' => $account1->id,
                'tool_id' => $kiro->id,
                'tanggal' => Carbon::today(),
                'limit_used' => 3.6,
                'limit_total' => 50,
                'status' => 'Sedang Dipakai',
                'catatan' => 'Digunakan untuk development project AI Account Manager',
            ]);
        }

        if ($account1 && $claude) {
            UsageLog::create([
                'account_id' => $account1->id,
                'tool_id' => $claude->id,
                'tanggal' => Carbon::today(),
                'limit_used' => 45.8,
                'limit_total' => 50,
                'status' => 'Limit',
                'catatan' => 'Hampir mencapai limit harian',
            ]);
        }

        if ($account2 && $gemini) {
            UsageLog::create([
                'account_id' => $account2->id,
                'tool_id' => $gemini->id,
                'tanggal' => Carbon::today(),
                'limit_used' => 38.2,
                'limit_total' => 50,
                'status' => 'Warning',
                'catatan' => 'Usage tinggi, perlu monitoring',
            ]);
        }

        if ($account1 && $antigravity) {
            UsageLog::create([
                'account_id' => $account1->id,
                'tool_id' => $antigravity->id,
                'tanggal' => Carbon::yesterday(),
                'limit_used' => 50,
                'limit_total' => 50,
                'status' => 'Limit',
                'catatan' => 'Limit penuh kemarin',
            ]);
        }

        if ($account3 && $kiro) {
            UsageLog::create([
                'account_id' => $account3->id,
                'tool_id' => $kiro->id,
                'tanggal' => Carbon::today(),
                'limit_used' => 0,
                'limit_total' => 50,
                'status' => 'Ready',
                'catatan' => 'Belum digunakan hari ini',
            ]);
        }

        if ($account2 && $claude) {
            UsageLog::create([
                'account_id' => $account2->id,
                'tool_id' => $claude->id,
                'tanggal' => Carbon::today()->subDays(2),
                'limit_used' => 25.5,
                'limit_total' => 50,
                'status' => 'Sedang Dipakai',
                'catatan' => 'Testing fitur baru',
            ]);
        }
    }
}
