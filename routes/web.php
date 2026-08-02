<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tools Management
    Route::resource('tools', \App\Http\Controllers\Tool\ToolController::class);
    Route::post('/tools/{tool}/toggle-status', [\App\Http\Controllers\Tool\ToolController::class, 'toggleStatus'])
        ->name('tools.toggle-status');
    
    // Accounts Management
    Route::resource('accounts', \App\Http\Controllers\Account\AccountController::class);
    
    // Usage Logs Management
    Route::resource('usage-logs', \App\Http\Controllers\UsageLog\UsageLogController::class);
    
    // Activity Logs Management
    Route::resource('activity-logs', \App\Http\Controllers\ActivityLog\ActivityLogController::class);
    Route::post('/activity-logs-quick', [\App\Http\Controllers\ActivityLog\ActivityLogController::class, 'quickLog'])
        ->name('activity-logs.quick');
    Route::get('/activity-timeline', [\App\Http\Controllers\ActivityLog\ActivityLogController::class, 'timeline'])
        ->name('activity-logs.timeline');
    
    // Telegram Settings
    Route::get('/telegram/settings', [\App\Http\Controllers\Telegram\TelegramSettingsController::class, 'index'])
        ->name('telegram.settings');
    Route::post('/telegram/generate-token', [\App\Http\Controllers\Telegram\TelegramSettingsController::class, 'generateToken'])
        ->name('telegram.generate-token');
    Route::post('/telegram/revoke-token', [\App\Http\Controllers\Telegram\TelegramSettingsController::class, 'revokeToken'])
        ->name('telegram.revoke-token');
    Route::post('/telegram/disconnect', [\App\Http\Controllers\Telegram\TelegramSettingsController::class, 'disconnect'])
        ->name('telegram.disconnect');
    Route::post('/telegram/toggle-notifications', [\App\Http\Controllers\Telegram\TelegramSettingsController::class, 'toggleNotifications'])
        ->name('telegram.toggle-notifications');
    
    Route::post('/logout', LogoutController::class)->name('logout');
});

// Telegram Webhook (outside auth middleware)
Route::post('/telegram/webhook/{secret}', [\App\Http\Controllers\Telegram\WebhookController::class, 'handle'])
    ->name('telegram.webhook');

