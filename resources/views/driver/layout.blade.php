<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بوابة السائق') — نظام إدارة الشحنات</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap RTL --}}
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap/bootstrap.rtl.min.css") }}">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap-icons/bootstrap-icons.min.css") }}">
    {{-- Unified design system (shared with admin) --}}
    <link href="{{ asset("css/design-system.css") }}" rel="stylesheet">

    <style>
    

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
        font-family: 'Cairo', sans-serif;
        background: var(--bg);
        color: var(--primary);
        font-size: 14px;
        line-height: 1.65;
        -webkit-font-smoothing: antialiased;
    }
    html, body {
        overflow-x: hidden;
        width: 100%;
        max-width: 100vw;
    }
    a { color: inherit; text-decoration: none; }

    /* SIDEBAR */
    .sidebar {
        position: fixed;
        right: 0; top: 0; bottom: 0;
        width: var(--sidebar-w);
        background: var(--primary);
        display: flex;
        flex-direction: column;
        z-index: 1050;
        box-shadow: -4px 0 30px rgba(0,0,0,0.22);
        transition: var(--tr-slow);
    }
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 22px 18px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        text-decoration: none;
        flex-shrink: 0;
    }
    .sidebar-brand .brand-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, rgba(199,166,103,0.18), rgba(199,166,103,0.08));
        border: 1.5px solid rgba(199,166,103,0.30);
        border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        color: var(--accent);
        flex-shrink: 0;
    }
    .sidebar-brand .brand-text {
        color: #FFFFFF;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.25;
    }
    .sidebar-brand .brand-text small {
        display: block;
        font-size: 0.72rem;
        font-weight: 400;
        color: var(--accent);
        margin-top: 2px;
    }

    .sidebar-nav {
        flex: 1;
        padding: 16px 12px;
        overflow-y: auto;
    }
    .nav-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: rgba(255,255,255,0.35);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px 6px;
    }
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: var(--r-sm);
        color: rgba(255,255,255,0.75);
        font-weight: 600;
        font-size: 0.88rem;
        margin-bottom: 4px;
        transition: var(--tr);
    }
    .sidebar-link:hover {
        background: rgba(255,255,255,0.07);
        color: #FFFFFF;
    }
    .sidebar-link.active {
        background: linear-gradient(90deg, rgba(199,166,103,0.22), rgba(199,166,103,0.08));
        color: var(--accent);
        border-right: 3px solid var(--accent);
    }
    .sidebar-link .link-icon {
        font-size: 1.15rem;
        width: 24px;
        display: flex;
        justify-content: center;
    }

    .sidebar-footer {
        padding: 16px 14px;
        border-top: 1px solid rgba(255,255,255,0.06);
        background: rgba(0,0,0,0.15);
    }
    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .su-avatar {
        width: 38px;
        height: 38px;
        background: var(--accent);
        color: var(--primary);
        font-weight: 800;
        font-size: 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .su-info {
        flex: 1;
        overflow: hidden;
    }
    .su-name {
        color: #FFFFFF;
        font-weight: 700;
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .su-role {
        font-size: 0.72rem;
        color: var(--accent);
    }
    .btn-logout-side {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(239,68,68,0.15);
        color: #fca5a5;
        border: 1px solid rgba(239,68,68,0.30);
        padding: 8px 12px;
        border-radius: var(--r-sm);
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--tr);
    }
    .btn-logout-side:hover {
        background: rgba(239,68,68,0.25);
        color: #fee2e2;
    }

    /* MAIN WRAPPER */
    .main-wrapper {
        margin-right: var(--sidebar-w);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: var(--tr-slow);
    }
    .topbar {
        height: var(--topbar-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: var(--sh-xs);
    }
    .topbar-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .topbar-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--secondary);
    }
    .topbar-breadcrumb .bc-home { color: var(--accent); }
    .topbar-breadcrumb .bc-sep { font-size: 0.75rem; color: var(--muted); }
    .topbar-breadcrumb .bc-current { color: var(--primary); font-weight: 700; }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .topbar-date {
        font-size: 0.82rem;
        color: var(--secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .content-area {
        flex: 1;
        padding: 24px;
    }

    /* FLASH TOAST */
    .flash-toast {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: var(--r-sm);
        margin-bottom: 20px;
        box-shadow: var(--sh-sm);
        animation: fadeIn .3s ease;
    }
    .flash-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }
    .flash-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .flash-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }
    .ft-close {
        margin-right: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
    }

    /* MOBILE TOGGLE */
    .sidebar-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--accent);
        border: 2px solid var(--accent);
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        z-index: 1100;
        box-shadow: var(--sh-lg);
        cursor: pointer;
    }
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(100%);
            max-width: 82vw;
            visibility: hidden;
        }
        .sidebar.open { transform: translateX(0); visibility: visible; }
        .main-wrapper { margin-right: 0; }
        .sidebar-toggle { display: flex; }
        .sidebar-overlay.open { display: block; }
        .topbar { padding: 0 16px; height: 56px; }
        .content-area { padding: 14px 12px 36px; }

        /* Tables horizontal scroll */
        .table-responsive {
            margin: 0 -12px !important;
            padding: 0 12px !important;
            -webkit-overflow-scrolling: touch;
            overflow-x: auto;
        }
        .table-responsive > table,
        .tbl {
            min-width: 620px;
        }

        /* Touch-friendly controls */
        .btn { touch-action: manipulation; }
        .form-control, .form-select { font-size: 14px !important; }
    }

    .sidebar-link.active::before { display: none; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
    @stack('styles')
</head>
<body>

{{-- MOBILE TOGGLE --}}
<button class="sidebar-toggle" id="sidebarToggle" aria-label="القائمة">
    <i class="bi bi-list" id="toggleIcon"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <a class="sidebar-brand" href="{{ route('driver.dashboard') }}">
        <div class="brand-icon">
            <i class="bi bi-truck-front-fill"></i>
        </div>
        <div class="brand-text">
            نظام إدارة الشحنات
            <small>بوابة السائق الميداني</small>
        </div>
    </a>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-label">الرئيسية</div>

        <a href="{{ route('driver.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-speedometer2"></i></span>
            <span class="link-text">لوحة التحكم</span>
        </a>

        <div class="nav-label">المهام والشحنات</div>

        <a href="{{ route('driver.shipments') }}"
           class="sidebar-link {{ request()->routeIs('driver.shipments*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-box-seam"></i></span>
            <span class="link-text">شحناتي المسندة</span>
        </a>

        <div class="nav-label">الحساب</div>

        <a href="{{ route('driver.profile') }}"
           class="sidebar-link {{ request()->routeIs('driver.profile') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-person-badge"></i></span>
            <span class="link-text">الملف الشخصي</span>
        </a>

    </nav>

    {{-- Footer — driver card + logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="su-avatar">
                {{ mb_substr(auth()->user()->name ?? 'س', 0, 1) }}
            </div>
            <div class="su-info">
                <div class="su-name">{{ auth()->user()->name ?? 'السائق' }}</div>
                <div class="su-role">سائق معتمد</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout-side">
                <i class="bi bi-box-arrow-left"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>

</aside>

{{-- MAIN WRAPPER --}}
<div class="main-wrapper" id="mainWrapper">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="topbar-left">
            <div class="topbar-breadcrumb">
                <i class="bi bi-house-fill bc-home"></i>
                <i class="bi bi-chevron-left bc-sep"></i>
                <span class="bc-current">@yield('page-title', 'لوحة التحكم')</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date d-none d-sm-flex">
                <i class="bi bi-calendar3"></i>
                <span id="topbar-date-display"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: var(--surface); color: var(--primary); font-weight: 700; border: 1px solid var(--border);">
                    <i class="bi bi-person-check text-success ms-1"></i> {{ auth()->user()->name ?? 'سائق' }}
                </span>
            </div>
        </div>
    </header>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flash-toast flash-success" id="flash-msg">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button class="ft-close" onclick="this.closest('.flash-toast').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @elseif(session('error'))
        <div class="flash-toast flash-error" id="flash-msg">
            <i class="bi bi-x-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button class="ft-close" onclick="this.closest('.flash-toast').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- CONTENT --}}
    <main class="content-area">
        @yield('content')
    </main>

</div>

{{-- Scripts --}}
<script src="{{ asset("vendor/bootstrap/bootstrap.bundle.min.js") }}"></script>
<script>
(function () {
    /* Date */
    const dateEl = document.getElementById('topbar-date-display');
    if (dateEl) {
        const now = new Date();
        dateEl.textContent = now.toLocaleDateString('ar-SA', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    /* Mobile sidebar toggle */
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');
    const icon     = document.getElementById('toggleIcon');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        if (icon) icon.className = 'bi bi-x-lg';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        if (icon) icon.className = 'bi bi-list';
    }

    if (toggle) toggle.addEventListener('click', () => {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (overlay) overlay.addEventListener('click', closeSidebar);

    /* Auto-close sidebar on nav link click (mobile) */
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) closeSidebar();
        });
    });

    /* Escape closes sidebar */
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
    });
})();
</script>
@stack('scripts')
</body>
</html>
