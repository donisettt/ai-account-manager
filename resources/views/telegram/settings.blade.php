<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Telegram Settings - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar {
            background-color: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #212529;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin: 0.25rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .token-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 1rem;
            font-family: monospace;
            word-break: break-all;
        }
        .status-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
        }
        .instruction-step {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <x-layouts.sidebar />

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-telegram"></i> Pengaturan Telegram Bot</h2>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Connection Status -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Status Koneksi</h5>
                        <hr>
                        
                        @if($telegramUser && $telegramUser->user_id)
                            <div class="text-center py-3">
                                <span class="badge bg-success status-badge">
                                    <i class="bi bi-check-circle"></i> Terhubung
                                </span>
                                <div class="mt-3">
                                    <p class="mb-1"><strong>Nama:</strong> {{ $telegramUser->first_name }} {{ $telegramUser->last_name }}</p>
                                    @if($telegramUser->username)
                                        <p class="mb-1"><strong>Username:</strong> @{{ $telegramUser->username }}</p>
                                    @endif
                                    <p class="mb-1"><strong>Chat ID:</strong> {{ $telegramUser->chat_id }}</p>
                                    <p class="mb-1">
                                        <strong>Notifikasi:</strong> 
                                        <span class="badge {{ $telegramUser->notifications_enabled ? 'bg-success' : 'bg-secondary' }}" id="notifStatus">
                                            {{ $telegramUser->notifications_enabled ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-warning" id="toggleNotifBtn">
                                        <i class="bi bi-bell"></i> Toggle Notifikasi
                                    </button>
                                    <button type="button" class="btn btn-danger" id="disconnectBtn">
                                        <i class="bi bi-x-circle"></i> Putuskan Koneksi
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <span class="badge bg-secondary status-badge">
                                    <i class="bi bi-x-circle"></i> Belum Terhubung
                                </span>
                                <p class="mt-3 text-muted">Telegram bot belum terhubung dengan akun Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Generate Token Section -->
                @if(!$telegramUser || !$telegramUser->user_id)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Hubungkan Telegram</h5>
                        <hr>
                        
                        <div class="instruction-step">
                            <h6><i class="bi bi-1-circle-fill text-primary"></i> Generate Token</h6>
                            <p class="mb-2">Klik tombol di bawah untuk generate token autentikasi.</p>
                            <button type="button" class="btn btn-primary" id="generateTokenBtn">
                                <i class="bi bi-key"></i> Generate Token
                            </button>
                        </div>

                        <div id="tokenSection" style="display: none;">
                            <div class="instruction-step">
                                <h6><i class="bi bi-2-circle-fill text-primary"></i> Copy Token</h6>
                                <p class="mb-2">Token autentikasi Anda (valid selama 1 jam):</p>
                                <div class="token-box" id="tokenBox">
                                    <span id="tokenValue"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="copyTokenBtn">
                                    <i class="bi bi-clipboard"></i> Copy Token
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="revokeTokenBtn">
                                    <i class="bi bi-trash"></i> Revoke Token
                                </button>
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-clock"></i> Expires: <span id="tokenExpires"></span>
                                </p>
                            </div>

                            <div class="instruction-step">
                                <h6><i class="bi bi-3-circle-fill text-primary"></i> Buka Telegram Bot</h6>
                                <p class="mb-2">Cari bot Anda di Telegram dan mulai chat dengan perintah /start</p>
                                <a href="https://t.me/YOUR_BOT_USERNAME" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-telegram"></i> Buka Bot
                                </a>
                            </div>

                            <div class="instruction-step">
                                <h6><i class="bi bi-4-circle-fill text-primary"></i> Autentikasi</h6>
                                <p class="mb-2">Kirim perintah berikut di Telegram bot:</p>
                                <div class="token-box">
                                    /auth <span id="tokenCommand"></span>
                                </div>
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-info-circle"></i> Setelah autentikasi berhasil, halaman ini akan otomatis terupdate.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Available Commands -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Perintah Telegram Bot</h5>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Informasi</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/start</code> - Mulai menggunakan bot</li>
                                    <li class="mb-2"><code>/help</code> - Lihat semua perintah</li>
                                    <li class="mb-2"><code>/status</code> - Lihat status akun</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Autentikasi</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/auth [token]</code> - Login dengan token</li>
                                    <li class="mb-2"><code>/logout</code> - Logout dari bot</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Akun</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/accounts</code> - Lihat semua akun</li>
                                    <li class="mb-2"><code>/account [id]</code> - Detail akun</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Usage & Activity</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/usage</code> - Status penggunaan hari ini</li>
                                    <li class="mb-2"><code>/activity</code> - Log aktivitas terbaru</li>
                                    <li class="mb-2"><code>/log [account_id] [tool_id] [aktivitas]</code> - Quick log</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Update</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/update [account_id] [used] [total]</code> - Update usage</li>
                                    <li class="mb-2"><code>/quick [type]</code> - Quick actions</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Notifikasi</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><code>/notify</code> - Toggle notifikasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Generate Token
        document.getElementById('generateTokenBtn')?.addEventListener('click', async () => {
            const btn = document.getElementById('generateTokenBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
            
            try {
                const response = await fetch('{{ route("telegram.generate-token") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response not OK:', response.status, errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }

                const data = await response.json();

                if (data.success) {
                    document.getElementById('tokenValue').textContent = data.token;
                    document.getElementById('tokenCommand').textContent = data.token;
                    document.getElementById('tokenExpires').textContent = data.expires_at;
                    document.getElementById('tokenSection').style.display = 'block';
                    
                    alert('Token berhasil dibuat!');
                } else {
                    alert('Gagal membuat token: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat generate token.\n\nError: ' + error.message + '\n\nCek console untuk detail.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-key"></i> Generate Token';
            }
        });

        // Copy Token
        document.getElementById('copyTokenBtn')?.addEventListener('click', () => {
            const token = document.getElementById('tokenValue').textContent;
            navigator.clipboard.writeText(token).then(() => {
                alert('Token berhasil dicopy!');
            });
        });

        // Revoke Token
        document.getElementById('revokeTokenBtn')?.addEventListener('click', async () => {
            if (!confirm('Yakin ingin revoke token ini?')) return;

            try {
                const response = await fetch('{{ route("telegram.revoke-token") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('tokenSection').style.display = 'none';
                    alert('Token berhasil direvoke!');
                } else {
                    alert('Gagal revoke token: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat revoke token.');
            }
        });

        // Disconnect
        document.getElementById('disconnectBtn')?.addEventListener('click', async () => {
            if (!confirm('Yakin ingin memutuskan koneksi Telegram?')) return;

            try {
                const response = await fetch('{{ route("telegram.disconnect") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Koneksi berhasil diputuskan!');
                    window.location.reload();
                } else {
                    alert('Gagal memutuskan koneksi: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memutuskan koneksi.');
            }
        });

        // Toggle Notifications
        document.getElementById('toggleNotifBtn')?.addEventListener('click', async () => {
            try {
                const response = await fetch('{{ route("telegram.toggle-notifications") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const status = document.getElementById('notifStatus');
                    status.textContent = data.enabled ? 'Aktif' : 'Nonaktif';
                    status.className = data.enabled ? 'badge bg-success' : 'badge bg-secondary';
                    alert(data.message);
                } else {
                    alert('Gagal toggle notifikasi: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat toggle notifikasi.');
            }
        });
    </script>
</body>
</html>
