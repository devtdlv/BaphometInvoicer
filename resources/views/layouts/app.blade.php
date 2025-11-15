<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Baphomet Invoicer') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-primary: #f5f7fb;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f3f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent: #5b21b6;
            --accent-hover: #4c1d95;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #dc2626;
            --warning: #f59e0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
        }
        
        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--bg-secondary);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        .table th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .table tr:hover {
            background: var(--bg-tertiary);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }
        
        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }
        
        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }
        
        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }
        
        .badge-info {
            background: rgba(139, 92, 246, 0.2);
            color: var(--accent);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            color: var(--text-primary);
            font-size: 0.875rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
        }
    </style>
</head>
<body>
    <nav style="background: var(--bg-secondary); border-bottom: 1px solid var(--border); padding: 1rem 0;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 2rem;">
                <a href="{{ route('dashboard') }}" style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); text-decoration: none;">
                    {{ config('app.name') }}
                </a>
                @auth
                @php
                    $navLink = function ($routePatterns) {
                        $patterns = (array) $routePatterns;
                        $isActive = false;
                        foreach ($patterns as $pattern) {
                            if (request()->routeIs($pattern)) {
                                $isActive = true;
                                break;
                            }
                        }
                        $color = $isActive ? 'var(--accent)' : 'var(--text-secondary)';
                        $border = $isActive ? '2px solid var(--accent)' : '2px solid transparent';
                        return "color: {$color}; text-decoration: none; font-weight: 500; padding-bottom: 0.25rem; border-bottom: {$border}; transition: color 0.2s, border-color 0.2s;";
                    };
                @endphp
                <div style="display: flex; gap: 1.5rem;">
                    <a href="{{ route('dashboard') }}" style="{{ $navLink('dashboard') }}">Dashboard</a>
                    <a href="{{ route('invoices.index') }}" style="{{ $navLink(['invoices.*']) }}">Invoices</a>
                    <a href="{{ route('quotes.index') }}" style="{{ $navLink(['quotes.*']) }}">Quotes</a>
                    <a href="{{ route('clients.index') }}" style="{{ $navLink(['clients.*']) }}">Clients</a>
                    <a href="{{ route('reports.index') }}" style="{{ $navLink(['reports.*']) }}">Reports</a>
                    <a href="{{ route('settings.index') }}" style="{{ $navLink(['settings.*']) }}">Settings</a>
                </div>
                @endauth
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                @auth
                    <span style="color: var(--text-secondary);">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main style="padding: 2rem 0; min-height: calc(100vh - 80px);">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            
            @if(session('info'))
                <div class="alert" style="background: rgba(139, 92, 246, 0.1); border: 1px solid var(--accent); color: var(--accent);">
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>

