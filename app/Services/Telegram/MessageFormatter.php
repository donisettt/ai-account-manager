<?php

namespace App\Services\Telegram;

use Illuminate\Support\Collection;
use App\Models\{Account, UsageLog, ActivityLog};

class MessageFormatter
{
    public function formatHelp(): string
    {
        return "📚 <b>Daftar Command</b>\n\n" .
               "<b>Authentication:</b>\n" .
               "/start - Mulai bot\n" .
               "/auth TOKEN - Autentikasi dengan token\n" .
               "/logout - Logout dari bot\n\n" .
               
               "<b>Monitoring:</b>\n" .
               "/status - Overview semua akun\n" .
               "/accounts - List semua accounts\n" .
               "/account EMAIL - Detail account\n" .
               "/usage - Usage logs hari ini\n" .
               "/activity - Activity logs terbaru\n\n" .
               
               "<b>Action:</b>\n" .
               "/log - Log aktivitas (interactive)\n" .
               "/quick EMAIL TOOL AKTIVITAS - Quick log\n" .
               "/update EMAIL TOOL USED/TOTAL - Update usage\n" .
               "/reset EMAIL TOOL DD/MM/YYYY - Set reset date\n\n" .
               
               "<b>Utility:</b>\n" .
               "/notify [on|off] - Toggle notifikasi\n" .
               "/help - Tampilkan help\n\n" .
               
               "<i>Contoh:</i>\n" .
               "<code>/quick akun01@gmail.com Kiro Dipakai</code>\n" .
               "<code>/update akun01@gmail.com Kiro 3.6/50</code>\n" .
               "<code>/reset akun01@gmail.com Kiro 15/08/2026</code>";
    }

    public function formatStatus(Collection $accounts): string
    {
        if ($accounts->isEmpty()) {
            return "❌ Belum ada account terdaftar.";
        }

        $message = "📊 <b>Status Akun - Overview</b>\n\n";
        
        foreach ($accounts as $account) {
            // Get usage logs untuk semua tools dari account ini (latest per tool)
            $toolUsages = [];
            $hasLimit = false;
            $hasWarning = false;
            $totalTools = 0;
            $limitTools = 0;
            
            foreach ($account->tools as $tool) {
                $latestUsage = \App\Models\UsageLog::where('account_id', $account->id)
                    ->where('tool_id', $tool->id)
                    ->latest('tanggal')
                    ->first();
                
                if ($latestUsage) {
                    $toolUsages[$tool->id] = [
                        'tool' => $tool,
                        'usage' => $latestUsage,
                        'percentage' => $latestUsage->usage_percentage
                    ];
                    
                    $totalTools++;
                    
                    // Check status per tool
                    if ($latestUsage->usage_percentage >= 100 || $latestUsage->status === 'Limit') {
                        $hasLimit = true;
                        $limitTools++;
                    } elseif ($latestUsage->usage_percentage >= 80) {
                        $hasWarning = true;
                    }
                }
            }
            
            // Determine overall account status text
            if ($totalTools > 0 && $limitTools === $totalTools) {
                // Semua tools limit
                $statusText = 'All Limit';
            } elseif ($hasLimit) {
                // Ada beberapa tools yang limit
                $statusText = "Partial Limit ({$limitTools}/{$totalTools})";
            } elseif ($hasWarning) {
                // Ada tools yang warning (>80%)
                $statusText = 'Warning';
            } else {
                // Default ke status account dari database
                $statusText = $account->status;
            }

            $message .= "📧 <b>{$account->email}</b>\n";
            $message .= "   Status: {$statusText}\n";
            
            // Display usage per tool
            if (!empty($toolUsages)) {
                $message .= "   Usage:\n";
                foreach ($toolUsages as $toolUsage) {
                    $tool = $toolUsage['tool'];
                    $usage = $toolUsage['usage'];
                    $percentage = $toolUsage['percentage'];
                    
                    // Status emoji per tool
                    $toolEmoji = $percentage >= 100 ? '🔴' : ($percentage >= 80 ? '🟡' : '🟢');
                    
                    $message .= "      {$toolEmoji} {$tool->nama}: {$usage->limit_used}/{$usage->limit_total} ";
                    $message .= "(" . number_format($percentage, 1) . "%)\n";
                }
            }
            
            // Tools with reset info
            if ($account->tools->count() > 0) {
                $message .= "   Reset Schedule:\n";
                foreach ($account->tools as $tool) {
                    $message .= "      • {$tool->nama}";
                    
                    // Add reset info if available
                    if ($tool->pivot->next_reset) {
                        $nextReset = \Carbon\Carbon::parse($tool->pivot->next_reset);
                        $daysUntilReset = (int) now()->diffInDays($nextReset, false);
                        
                        if ($daysUntilReset > 0) {
                            $message .= " → {$nextReset->format('d/m')} ({$daysUntilReset} hari)";
                        } elseif ($daysUntilReset === 0) {
                            $message .= " → ⚡ Hari ini";
                        } else {
                            $message .= " → ⚠️ Lewat";
                        }
                    } else {
                        $message .= " → Belum diset";
                    }
                    
                    $message .= "\n";
                }
            }
            
            $message .= "\n";
        }

        return $message;
    }

    public function formatAccounts(Collection $accounts): string
    {
        if ($accounts->isEmpty()) {
            return "❌ Belum ada account terdaftar.";
        }

        $message = "📋 <b>Daftar Accounts</b>\n\n";
        
        foreach ($accounts as $index => $account) {
            $num = $index + 1;
            $message .= "{$num}. <b>{$account->email}</b>\n";
            $message .= "   Provider: {$account->provider}\n";
            $message .= "   Status: {$account->status}\n";
            
            if ($account->tools->count() > 0) {
                $message .= "   Tools:\n";
                foreach ($account->tools as $tool) {
                    $message .= "      • {$tool->nama}";
                    
                    // Add reset info
                    if ($tool->pivot->next_reset) {
                        $nextReset = \Carbon\Carbon::parse($tool->pivot->next_reset);
                        $daysUntilReset = (int) now()->diffInDays($nextReset, false);
                        
                        if ($daysUntilReset > 0) {
                            $message .= " (Reset {$nextReset->format('d/m')}, {$daysUntilReset} hari)";
                        } elseif ($daysUntilReset === 0) {
                            $message .= " (⚡ Reset hari ini)";
                        } else {
                            $message .= " (⚠️ Lewat)";
                        }
                    }
                    
                    $message .= "\n";
                }
            }
            
            $message .= "\n";
        }

        $message .= "💡 Gunakan <code>/account EMAIL</code> untuk detail";
        
        return $message;
    }

    public function formatAccountDetail(Account $account): string
    {
        $message = "👤 <b>Detail Account</b>\n\n";
        $message .= "<b>Email:</b> {$account->email}\n";
        $message .= "<b>Provider:</b> {$account->provider}\n";
        $message .= "<b>Status:</b> {$account->status}\n";
        
        if ($account->recovery_email) {
            $message .= "<b>Recovery:</b> {$account->recovery_email}\n";
        }
        
        if ($account->catatan) {
            $message .= "<b>Catatan:</b> {$account->catatan}\n";
        }
        
        // Tools with reset info
        if ($account->tools->count() > 0) {
            $message .= "\n<b>🛠️ Tools & Reset Schedule:</b>\n";
            foreach ($account->tools as $tool) {
                $status = $tool->status_aktif ? '✅' : '❌';
                $message .= "  {$status} <b>{$tool->nama}</b>";
                
                // Add detailed reset info
                if ($tool->pivot->next_reset) {
                    $nextReset = \Carbon\Carbon::parse($tool->pivot->next_reset);
                    $daysUntilReset = (int) now()->diffInDays($nextReset, false);
                    
                    $message .= "\n      🔄 Reset: {$nextReset->format('d/m/Y')}";
                    
                    if ($daysUntilReset > 0) {
                        $message .= " ({$daysUntilReset} hari lagi)";
                    } elseif ($daysUntilReset === 0) {
                        $message .= " ⚡ <b>HARI INI!</b>";
                    } else {
                        $absDays = abs($daysUntilReset);
                        $message .= " ⚠️ <b>Lewat {$absDays} hari</b>";
                    }
                } else {
                    $message .= "\n      ⏰ Reset: <i>Belum diset</i>";
                }
                
                $message .= "\n";
            }
        }
        
        // Latest usage logs
        $recentUsage = $account->usageLogs()->latest('tanggal')->limit(3)->get();
        if ($recentUsage->count() > 0) {
            $message .= "\n<b>📈 Usage Logs Terbaru:</b>\n";
            foreach ($recentUsage as $usage) {
                $percentage = $usage->usage_percentage;
                $emoji = $usage->status_emoji;
                $message .= "  {$emoji} {$usage->tanggal->format('d/m')} - ";
                $message .= "{$usage->tool->nama}: ";
                $message .= "{$usage->limit_used}/{$usage->limit_total} ";
                $message .= "(" . number_format($percentage, 1) . "%)\n";
            }
        }
        
        // Latest activities
        $recentActivities = $account->activityLogs()->latest('waktu')->limit(3)->get();
        if ($recentActivities->count() > 0) {
            $message .= "\n<b>🕐 Aktivitas Terbaru:</b>\n";
            foreach ($recentActivities as $activity) {
                $emoji = match($activity->aktivitas) {
                    'Dipakai' => '▶️',
                    'Limit' => '🔴',
                    'Reset' => '🔄',
                    'Login' => '🔓',
                    'Logout' => '🔒',
                    default => '•',
                };
                $message .= "  {$emoji} {$activity->waktu->format('d/m H:i')} - ";
                $message .= "{$activity->tool->nama}: {$activity->aktivitas}\n";
            }
        }
        
        return $message;
    }

    public function formatUsageLogs(Collection $usageLogs): string
    {
        if ($usageLogs->isEmpty()) {
            return "❌ Belum ada usage log hari ini.";
        }

        $message = "📈 <b>Usage Logs Hari Ini</b>\n";
        $message .= "<i>" . now()->isoFormat('dddd, D MMMM Y') . "</i>\n\n";
        
        foreach ($usageLogs as $log) {
            $percentage = $log->usage_percentage;
            $emoji = $log->status_emoji;
            
            $message .= "{$emoji} <b>{$log->account->email}</b>\n";
            $message .= "   Tool: {$log->tool->nama}\n";
            $message .= "   Usage: {$log->limit_used}/{$log->limit_total} ";
            $message .= "(" . number_format($percentage, 1) . "%)\n";
            $message .= "   Status: {$log->status}\n";
            
            // Add reset info
            $account = $log->account;
            $tool = $account->tools->find($log->tool_id);
            
            if ($tool && $tool->pivot->next_reset) {
                $nextReset = \Carbon\Carbon::parse($tool->pivot->next_reset);
                $daysUntilReset = (int) now()->diffInDays($nextReset, false);
                
                $message .= "   🔄 Reset: {$nextReset->format('d/m/Y')}";
                
                if ($daysUntilReset > 0) {
                    $message .= " ({$daysUntilReset} hari lagi)\n";
                } elseif ($daysUntilReset === 0) {
                    $message .= " (⚡ Hari ini!)\n";
                } else {
                    $message .= " (⚠️ Sudah lewat)\n";
                }
            }
            
            if ($log->catatan) {
                $message .= "   Note: {$log->catatan}\n";
            }
            
            $message .= "\n";
        }
        
        return $message;
    }

    public function formatActivityLogs(Collection $activityLogs): string
    {
        if ($activityLogs->isEmpty()) {
            return "❌ Belum ada activity log.";
        }

        $message = "🕐 <b>Activity Logs Terbaru</b>\n\n";
        
        foreach ($activityLogs as $log) {
            $emoji = match($log->aktivitas) {
                'Dipakai' => '▶️',
                'Limit' => '🔴',
                'Reset' => '🔄',
                'Login' => '🔓',
                'Logout' => '🔒',
                'Error' => '⚠️',
                'Maintenance' => '🔧',
                default => '•',
            };
            
            $message .= "{$emoji} <b>{$log->waktu->format('H:i')}</b> - ";
            $message .= "{$log->account->email}\n";
            $message .= "   Tool: {$log->tool->nama}\n";
            $message .= "   Aktivitas: {$log->aktivitas}\n";
            
            if ($log->keterangan) {
                $message .= "   Note: {$log->keterangan}\n";
            }
            
            $message .= "\n";
        }
        
        return $message;
    }
}
