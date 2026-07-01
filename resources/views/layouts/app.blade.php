<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Manajemen Aset BULOG')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <header class="app-topbar">
            <div class="app-topbar__inner">
                <div class="brand-block">
                    <div class="brand-mark" aria-hidden="true">
                        <span>AB</span>
                    </div>
                    <div>
                        <p class="brand-title">Aset Bulog</p>
                        <p class="brand-subtitle">Manajemen Aset Perkantoran</p>
                    </div>
                </div>
                <div class="brand-subtitle brand-subtitle--meta">
                    @yield('topbar-meta', 'Pondasi visual dan layout dasar')
                </div>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 1rem;">
                    @if(auth()->check())
                        <span style="font-size: 0.875rem; color: #6b7280;">{{ auth()->user()->name }}</span>
                        <form action="{{ route('frontend.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #0ea5e9; cursor: pointer; text-decoration: underline; font-size: 0.875rem;">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('frontend.login') }}" style="color: #0ea5e9; text-decoration: none; font-size: 0.875rem;">Login</a>
                    @endif
                </div>
            </div>
        </header>

        <div class="app-body">
            <aside class="sidebar">
                <div class="sidebar-card">
                    <strong>Frontend Navigation</strong>
                    <nav class="sidebar-menu" aria-label="Navigasi frontend">
                        <a class="sidebar-link {{ request()->routeIs('frontend.dashboard') ? 'is-active' : '' }}" href="{{ route('frontend.dashboard') }}">Dashboard</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.login') ? 'is-active' : '' }}" href="{{ route('frontend.login') }}">Login</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.assets.laptops') ? 'is-active' : '' }}" href="{{ route('frontend.assets.laptops') }}">Data Laptop</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.assets.printers') ? 'is-active' : '' }}" href="{{ route('frontend.assets.printers') }}">Data Printer</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.scan-qr') ? 'is-active' : '' }}" href="{{ route('frontend.scan-qr') }}">Scan QR Code</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.reports.index') ? 'is-active' : '' }}" href="{{ route('frontend.reports.index') }}">Laporan</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.settings') ? 'is-active' : '' }}" href="{{ route('frontend.settings') }}">Pengaturan</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.pics.index') ? 'is-active' : '' }}" href="{{ route('frontend.pics.index') }}">Manajemen PIC</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.dashboard.management') ? 'is-active' : '' }}" href="{{ route('frontend.dashboard.management') }}">Dashboard Manajemen</a>
                    </nav>
                </div>
            </aside>

            <main class="content-area">
                @if(session('success'))
                    <div class="alert-ui alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert-ui alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="page-grid">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>