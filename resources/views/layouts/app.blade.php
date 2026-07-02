<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Manajemen Aset BULOG')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Permanent inline fallback for collapsed sidebar to ensure immediate effect */
        .app-body.sidebar-collapsed { grid-template-columns: 0 minmax(0, 1fr); }
        .sidebar.sidebar-collapsed { width: 0 !important; padding: 0 !important; opacity: 0; visibility: hidden; pointer-events: none; transform: translateX(-6px); border-right: none !important; }
        .sidebar.sidebar-collapsed .sidebar-card strong { display: none; }
        .sidebar.sidebar-collapsed .sidebar-menu { gap: 0.25rem; }
        .sidebar.sidebar-collapsed .sidebar-link { opacity: 0; transform: translateX(-6px); }
        .sidebar.sidebar-collapsed .sidebar-logout-btn { opacity: 0; transform: translateX(-6px); }
        .app-topbar { z-index: 40; }
        .sidebar { z-index: 20; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <header class="app-topbar">
            <div class="app-topbar__inner">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" onclick="(function(btn){const s=document.getElementById('sidebar'), a=document.querySelector('.app-body'); if(!s||!a) return; const willCollapse=!s.classList.contains('sidebar-collapsed'); if(willCollapse){s.classList.add('sidebar-collapsed'); a.classList.add('sidebar-collapsed'); btn.setAttribute('aria-pressed','true'); localStorage.setItem('sidebarCollapsed','true');}else{s.classList.remove('sidebar-collapsed'); a.classList.remove('sidebar-collapsed'); btn.setAttribute('aria-pressed','false'); localStorage.setItem('sidebarCollapsed','false');}})(this)">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="brand-block">
                    <div class="brand-mark" aria-hidden="true">
                        <span>AB</span>
                    </div>
                    <div>
                        <p class="brand-title">Aset Bulog</p>
                        <p class="brand-subtitle">Manajemen Aset Perkantoran</p>
                    </div>
                </div>
                @hasSection('topbar-meta')
                    <div class="brand-subtitle brand-subtitle--meta">
                        @yield('topbar-meta')
                    </div>
                @endif
                <div style="margin-left: auto; display: flex; align-items: center; gap: 1rem;">
                    @if(auth()->check())
                        <span style="font-size: 0.875rem; color: #6b7280;">{{ auth()->user()->name }}</span>
                    @endif
                </div>
            </div>
        </header>

        <div class="app-body">
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-card">
                    <strong>Frontend Navigation</strong>
                    <nav class="sidebar-menu" aria-label="Navigasi frontend">
                        <a class="sidebar-link {{ request()->routeIs('frontend.dashboard') ? 'is-active' : '' }}" href="{{ route('frontend.dashboard') }}">Dashboard</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.assets.laptops') ? 'is-active' : '' }}" href="{{ route('frontend.assets.laptops') }}">Data Laptop</a>
                        <a class="sidebar-link {{ request()->routeIs('frontend.assets.printers') ? 'is-active' : '' }}" href="{{ route('frontend.assets.printers') }}">Data Printer</a>
                        @if(auth()->check() && auth()->user()->role !== 'manajemen')
                            <a class="sidebar-link {{ request()->routeIs('frontend.scan-qr') ? 'is-active' : '' }}" href="{{ route('frontend.scan-qr') }}">Scan QR Code</a>
                        @endif
                        @if(auth()->check() && in_array(auth()->user()->role, ['admin_it', 'manajemen'], true))
                            <a class="sidebar-link {{ request()->routeIs('frontend.reports.index') ? 'is-active' : '' }}" href="{{ route('frontend.reports.index') }}">Laporan</a>
                        @endif
                        @if(auth()->check() && auth()->user()->role === 'admin_it')
                            <a class="sidebar-link {{ request()->routeIs('frontend.settings') ? 'is-active' : '' }}" href="{{ route('frontend.settings') }}">Pengaturan</a>
                            <a class="sidebar-link {{ request()->routeIs('frontend.pics.index') ? 'is-active' : '' }}" href="{{ route('frontend.pics.index') }}">Manajemen PIC</a>
                        @endif
                        @if(auth()->check() && in_array(auth()->user()->role, ['admin_it', 'manajemen'], true))
                            <a class="sidebar-link {{ request()->routeIs('frontend.dashboard.management') ? 'is-active' : '' }}" href="{{ route('frontend.dashboard.management') }}">Dashboard Manajemen</a>
                        @endif
                    </nav>
                </div>
                @if(auth()->check())
                    <div class="sidebar-footer">
                        <form action="{{ route('frontend.logout') }}" method="POST" class="sidebar-logout-form">
                            @csrf
                            <button type="submit" class="sidebar-logout-btn">
                                <span>🚪</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @endif
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
    <script>
        // Ensure toggle reliably collapses/expands sidebar (fallback if module reloads detached listener)
        (function(){
            const btn = document.getElementById('sidebarToggle');
            if (!btn) return;
            btn.addEventListener('click', function(e){
                const sidebar = document.getElementById('sidebar');
                const appBody = document.querySelector('.app-body');
                if (!sidebar || !appBody) return;
                const willCollapse = !sidebar.classList.contains('sidebar-collapsed');
                if (willCollapse) {
                    sidebar.classList.add('sidebar-collapsed');
                    appBody.classList.add('sidebar-collapsed');
                    btn.setAttribute('aria-pressed','true');
                    localStorage.setItem('sidebarCollapsed','true');
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    appBody.classList.remove('sidebar-collapsed');
                    btn.setAttribute('aria-pressed','false');
                    localStorage.setItem('sidebarCollapsed','false');
                }
            }, {passive: true});
        })();
    </script>
</body>
</html>