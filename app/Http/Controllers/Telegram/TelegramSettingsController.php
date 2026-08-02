<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $telegramUser = null;
        
        if ($user->telegram_user_id) {
            $telegramUser = TelegramUser::find($user->telegram_user_id);
        }
        
        return view('telegram.settings', compact('telegramUser'));
    }

    public function generateToken(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Generate random token
            $token = Str::random(32);
            $expiresAt = now()->addHours(1); // Token valid for 1 hour
            
            // Store token temporarily
            // telegram_id and chat_id will be filled when user runs /auth command in Telegram
            
            $telegramUser = TelegramUser::where('user_id', $user->id)->first();
            
            if ($telegramUser) {
                // Update existing telegram user
                $telegramUser->auth_token = $token;
                $telegramUser->token_expires_at = $expiresAt;
                $telegramUser->save();
            } else {
                // Create temporary record with minimal data
                // telegram_id and chat_id are nullable now
                $telegramUser = TelegramUser::create([
                    'user_id' => $user->id,
                    'auth_token' => $token,
                    'token_expires_at' => $expiresAt,
                    'is_active' => false,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'message' => 'Token berhasil dibuat. Gunakan /auth ' . $token . ' di Telegram.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Generate token error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revokeToken(Request $request)
    {
        $user = auth()->user();
        
        $telegramUser = TelegramUser::where('user_id', $user->id)->first();
        
        if ($telegramUser) {
            $telegramUser->auth_token = null;
            $telegramUser->token_expires_at = null;
            $telegramUser->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Token berhasil dicabut.'
        ]);
    }

    public function disconnect(Request $request)
    {
        $user = auth()->user();
        
        if ($user->telegram_user_id) {
            $telegramUser = TelegramUser::find($user->telegram_user_id);
            
            if ($telegramUser) {
                $telegramUser->user_id = null;
                $telegramUser->auth_token = null;
                $telegramUser->token_expires_at = null;
                $telegramUser->save();
            }
            
            $user->telegram_user_id = null;
            $user->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Telegram berhasil diputuskan dari akun.'
        ]);
    }

    public function toggleNotifications(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->telegram_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram belum terhubung.'
            ], 400);
        }
        
        $telegramUser = TelegramUser::find($user->telegram_user_id);
        
        if ($telegramUser) {
            $telegramUser->notifications_enabled = !$telegramUser->notifications_enabled;
            $telegramUser->save();
            
            return response()->json([
                'success' => true,
                'enabled' => $telegramUser->notifications_enabled,
                'message' => 'Notifikasi ' . ($telegramUser->notifications_enabled ? 'diaktifkan' : 'dinonaktifkan') . '.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Telegram user tidak ditemukan.'
        ], 404);
    }
}
