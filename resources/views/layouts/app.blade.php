<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @stack('styles')
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
        }
        .navbar-brand {
            font-weight: 600;
            color: #667eea !important;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: white;
            box-shadow: 2px 0 4px rgba(0,0,0,.08);
        }
        .nav-link {
            color: #6c757d;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .nav-link:hover {
            color: #667eea;
            background-color: #f8f9fa;
            border-left-color: #667eea;
        }
        .nav-link.active {
            color: #667eea;
            background-color: #f8f9fa;
            border-left-color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <x-layouts.navbar />

    <div class="container-fluid">
        <div class="row">
            <x-layouts.sidebar />

            <div class="col-md-9 col-lg-10 px-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
