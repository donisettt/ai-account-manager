<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\Account;
use App\Models\Tool;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
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

        // Log hari ini
        if ($account3 && $antigravity) {
            ActivityLog::create([
                'account_id' => $account3->id,
                'tool_id' => $antigravity->id,
                'waktu' => Carbon::today()->setTime(9, 20),
                'aktivitas' => 'Dipakai',
                'keterangan' => 'Mulai menggunakan untuk development project',
            ]);

            ActivityLog::create([
                'account_id' => $account3->id,
                'tool_id' => $antigravity->id,
                'waktu' => Carbon::today()->setTime(10, 15),
                'aktivitas' => 'Limit',
                'keterangan' => 'Hampir mencapai limit harian',
            ]);

            ActivityLog::create([
                'account_id' => $account3->id,
                'tool_id' => $antigravity->id,
                'waktu' => Carbon::today()->setTime(18, 40),
                'aktivitas' => 'Reset',
                'keterangan' => 'Limit direset untuk hari berikutnya',
            ]);
        }

        if ($account1 && $kiro) {
            ActivityLog::create([
                'account_id' => $account1->id,
                'tool_id' => $kiro->id,
                'waktu' => Carbon::today()->setTime(8, 30),
                'aktivitas' => 'Login',
                'keterangan' => 'Login pagi untuk start working',
            ]);

            ActivityLog::create([
                'account_id' => $account1->id,
                'tool_id' => $kiro->id,
                'waktu' => Carbon::today()->setTime(8, 35),
                'aktivitas' => 'Dipakai',
                'keterangan' => 'Development AI Account Manager',
            ]);
        }

        if ($account2 && $gemini) {
            ActivityLog::create([
                'account_id' => $account2->id,
                'tool_id' => $gemini->id,
                'waktu' => Carbon::today()->setTime(14, 20),
                'aktivitas' => 'Dipakai',
                'keterangan' => 'Testing fitur multimodal',
            ]);
        }

        // Log kemarin
        if ($account1 && $claude) {
            ActivityLog::create([
                'account_id' => $account1->id,
                'tool_id' => $claude->id,
                'waktu' => Carbon::yesterday()->setTime(9, 0),
                'aktivitas' => 'Login',
                'keterangan' => 'Login pagi',
            ]);

            ActivityLog::create([
                'account_id' => $account1->id,
                'tool_id' => $claude->id,
                'waktu' => Carbon::yesterday()->setTime(16, 30),
                'aktivitas' => 'Limit',
                'keterangan' => 'Mencapai limit harian',
            ]);

            ActivityLog::create([
                'account_id' => $account1->id,
                'tool_id' => $claude->id,
                'waktu' => Carbon::yesterday()->setTime(17, 0),
                'aktivitas' => 'Logout',
                'keterangan' => 'Selesai menggunakan',
            ]);
        }

        // Log error
        if ($account2 && $claude) {
            ActivityLog::create([
                'account_id' => $account2->id,
                'tool_id' => $claude->id,
                'waktu' => Carbon::today()->setTime(11, 45),
                'aktivitas' => 'Error',
                'keterangan' => 'Connection timeout, mencoba reconnect',
            ]);
        }

        // Log maintenance
        if ($account3 && $kiro) {
            ActivityLog::create([
                'account_id' => $account3->id,
                'tool_id' => $kiro->id,
                'waktu' => Carbon::yesterday()->setTime(2, 0),
                'aktivitas' => 'Maintenance',
                'keterangan' => 'Scheduled maintenance',
            ]);
        }
    }
}
