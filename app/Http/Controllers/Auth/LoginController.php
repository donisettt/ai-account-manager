<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private AuthenticationService $authService
    ) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard.index');
        }
        
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($this->authService->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()
                ->intended(route('dashboard.index'))
                ->with('success', 'Login berhasil');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah'])
            ->withInput($request->only('email'));
    }
}
