<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Tool;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa tools
        $antigravity = Tool::where('nama', 'Antigravity')->first();
        $kiro = Tool::where('nama', 'Kiro')->first();
        $cuana = Tool::where('nama', 'Cuana')->first();
        $claude = Tool::where('nama', 'Claude')->first();
        $gemini = Tool::where('nama', 'Gemini')->first();
        $cursor = Tool::where('nama', 'Cursor')->first();

        // Account 1
        $account1 = Account::create([
            'email' => 'akun01@gmail.com',
            'password' => 'Password123!', // Akan di-encrypt otomatis
            'provider' => 'Google',
            'provider_login' => 'https://accounts.google.com',
            'recovery_email' => 'recovery01@gmail.com',
            'status' => 'Ready',
            'catatan' => 'Premium sementara, aktif hingga Desember 2026',
        ]);
        
        if ($antigravity && $kiro && $cuana && $claude) {
            $account1->tools()->attach([$antigravity->id, $kiro->id, $cuana->id, $claude->id]);
        }

        // Account 2
        $account2 = Account::create([
            'email' => 'akun02@outlook.com',
            'password' => 'SecurePass456!',
            'provider' => 'Microsoft',
            'provider_login' => 'https://login.live.com',
            'recovery_email' => 'backup02@outlook.com',
            'status' => 'In Use',
            'catatan' => 'Digunakan untuk testing',
        ]);
        
        if ($gemini && $cursor && $claude) {
            $account2->tools()->attach([$gemini->id, $cursor->id, $claude->id]);
        }

        // Account 3
        $account3 = Account::create([
            'email' => 'akun03@yahoo.com',
            'password' => 'MyPassword789!',
            'provider' => 'Custom',
            'recovery_email' => 'recovery03@yahoo.com',
            'status' => 'Ready',
            'catatan' => 'Account cadangan',
        ]);
        
        if ($kiro && $claude) {
            $account3->tools()->attach([$kiro->id, $claude->id]);
        }

        // Account 4
        $account4 = Account::create([
            'email' => 'akun04@gmail.com',
            'password' => 'TestPass2024!',
            'provider' => 'Google',
            'provider_login' => 'https://accounts.google.com',
            'status' => 'Suspended',
            'catatan' => 'Suspended karena aktivitas mencurigakan',
        ]);

        // Account 5
        $account5 = Account::create([
            'email' => 'akun05@gmail.com',
            'password' => 'ExpiredPass123!',
            'provider' => 'Google',
            'recovery_email' => 'recovery05@gmail.com',
            'status' => 'Expired',
            'catatan' => 'Trial period sudah habis',
        ]);
    }
}
