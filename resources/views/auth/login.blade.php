<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f3f4f6; /* Light gray background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .login-header {
            padding: 40px 40px 20px 40px;
            text-align: center;
        }
        .logo-circle {
            width: 64px;
            height: 64px;
            background-color: #eff6ff; /* Light blue */
            color: #2563eb; /* Primary blue */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            font-size: 28px;
        }
        .login-header h3 {
            font-weight: 700;
            color: #111827;
            font-size: 1.5rem;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        .login-body {
            padding: 0 40px 40px 40px;
        }
        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .form-control {
            padding: 0.6rem 1rem;
            border-color: #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #111827;
        }
        .form-control::placeholder {
            color: #9ca3af;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .input-group .btn {
            border-color: #d1d5db;
            color: #6b7280;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .input-group .btn:hover {
            background-color: #f9fafb;
            color: #374151;
        }
        .input-group:focus-within .form-control,
        .input-group:focus-within .btn {
            border-color: #2563eb;
        }
        .btn-login {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        .btn-login:hover {
            background-color: #1d4ed8;
            color: white;
            transform: translateY(-1px);
        }
        .btn-login:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
        }
        .form-check-label {
            font-size: 0.9rem;
            color: #4b5563;
        }
        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            border-color: #2563eb;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-card mx-3">
        <div class="login-header">
            <div class="logo-circle">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h3>AI Account Manager</h3>
            <p>Sistem Monitoring Akun AI</p>
        </div>
        
        <div class="login-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show bg-success text-white" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show bg-danger text-white" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="nama@email.com" 
                           required 
                           autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control border-end-0 @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan password" 
                               required>
                        <button class="btn btn-outline-secondary bg-white border-start-0" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Login
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
