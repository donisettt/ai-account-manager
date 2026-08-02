<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        return view('profile.index', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        
        $data = $request->validated();
        
        // Check if email changed and unique
        if ($data['email'] !== $user->email) {
            $request->validate([
                'email' => 'unique:users,email,' . $user->id
            ]);
        }
        
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Profile berhasil diupdate');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ]);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Password berhasil diubah');
    }
}
