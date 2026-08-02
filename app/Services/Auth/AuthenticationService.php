<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public function attempt(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function check(): bool
    {
        return Auth::check();
    }

    public function user()
    {
        return Auth::user();
    }
}
