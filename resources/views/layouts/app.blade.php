<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') — نظام إدارة الشحنات</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap RTL --}}
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap/bootstrap.rtl.min.css") }}">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="{{ asset("vendor/bootstrap-icons/bootstrap-icons.min.css") }}">
    {{-- Unified design system: tokens + components + responsive + Cairo (local) --}}
    <link rel="stylesheet" href="{{ asset("css/design-system.css") }}">

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-icon">
                <i class="bi bi-truck-front-fill"></i>
            </div>
            <div class="brand-text">
                نظام إدارة الشحنات
                <small>Cargo Management System</small>
            </div>
        </a>
        <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="إغلاق القائمة">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-label">الرئيسية</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-speedometer2"></i></span>
            <span class="link-text">لوحة التحكم</span>
        </a>

        <div class="nav-label">الشحنات</div>

        <a href="{{ route('shipments.index') }}"
           class="sidebar-link {{ request()->routeIs('shipments.index') && !request()->has('view') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-box-seam"></i></span>
            <span class="link-text">جميع الشحنات</span>
        </a>

        <a href="{{ route('shipments.create') }}"
           class="sidebar-link {{ request()->routeIs('shipments.create') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-plus-circle"></i></span>
            <span class="link-text">إضافة شحنة</span>
        </a>

        <a href="{{ route('shipments.board') }}"
           class="sidebar-link {{ request()->routeIs('shipments.board') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-kanban"></i></span>
            <span class="link-text">توزيع الشحنات</span>
        </a>

        <div class="nav-label">الموارد</div>

        <a href="{{ route('drivers.index') }}"
           class="sidebar-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-people"></i></span>
            <span class="link-text">السائقون</span>
        </a>

        <a href="{{ route('trucks.index') }}"
           class="sidebar-link {{ request()->routeIs('trucks.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-truck"></i></span>
            <span class="link-text">الشاحنات</span>
        </a>

        <a href="{{ route('destinations.index') }}"
           class="sidebar-link {{ request()->routeIs('destinations.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-geo-alt"></i></span>
            <span class="link-text">الوجهات</span>
        </a>

        <div class="nav-label">التحليل</div>

        <a href="{{ route('reports.index') }}"
           class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-bar-chart-line"></i></span>
            <span class="link-text">التقارير</span>
        </a>

        <a href="{{ route('activity-logs.index') }}"
           class="sidebar-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-clock-history"></i></span>
            <span class="link-text">سجل العمليات</span>
        </a>

        <a href="{{ route('settings.edit') }}"
           class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="link-icon"><i class="bi bi-gear"></i></span>
            <span class="link-text">الإعدادات</span>
        </a>

    </nav>

    {{-- Footer —  user card + logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="su-avatar">
                {{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}
            </div>
            <div class="su-info">
                <div class="su-name">{{ auth()->user()->name ?? 'المسؤول العام' }}</div>
                <div class="su-role">مدير النظام</div>
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

{{-- ═══════════════════ MAIN WRAPPER ═══════════════════ --}}
<div class="main-wrapper" id="mainWrapper">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="topbar-left">
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="القائمة">
                <i class="bi bi-list" id="toggleIcon"></i>
            </button>
            <div class="topbar-breadcrumb">
                <i class="bi bi-house-fill bc-home"></i>
                <i class="bi bi-chevron-left bc-sep"></i>
                <span class="bc-current">@yield('page-title', 'لوحة التحكم')</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-actions-wrap">
                @yield('topbar-actions')
            </div>
            <div class="topbar-date">
                <i class="bi bi-calendar3"></i>
                <span id="topbar-date-display"></span>
            </div>
            <div class="divider-v d-none d-md-inline-block"></div>
            <div class="topbar-user">
                <div class="tu-info text-end d-none d-md-block">
                    <div class="tu-name">{{ auth()->user()->name ?? 'المسؤول العام' }}</div>
                    <div class="tu-role">مدير النظام</div>
                </div>
                <div class="tu-avatar">
                    {{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </div>
    </header>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="flash-toast flash-success" id="flash-msg">
            <i class="bi bi-check-circle-fill ft-icon"></i>
            <span>{{ session('success') }}</span>
            <button class="ft-close" onclick="this.closest('.flash-toast').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @elseif(session('error'))
        <div class="flash-toast flash-error" id="flash-msg">
            <i class="bi bi-x-circle-fill ft-icon"></i>
            <span>{{ session('error') }}</span>
            <button class="ft-close" onclick="this.closest('.flash-toast').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @elseif(session('warning'))
        <div class="flash-toast flash-warning" id="flash-msg">
            <i class="bi bi-exclamation-triangle-fill ft-icon"></i>
            <span>{{ session('warning') }}</span>
            <button class="ft-close" onclick="this.closest('.flash-toast').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- CONTENT --}}
    <main class="content-area">

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="errors-box">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>يرجى تصحيح الأخطاء التالية:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')

    </main>
</div>

{{-- CONFIRM MODAL --}}
<div class="confirm-backdrop" id="confirmBackdrop">
    <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <div class="cm-icon"><i class="bi bi-question-circle"></i></div>
        <div class="cm-title" id="confirmTitle">تأكيد العملية</div>
        <div class="cm-message" id="confirmMessage"></div>
        <div class="cm-actions">
            <button type="button" class="btn btn-outline-navy" id="confirmCancelBtn">إلغاء</button>
            <button type="button" class="btn-danger-confirm" id="confirmOkBtn">تأكيد</button>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="{{ asset("vendor/bootstrap/bootstrap.bundle.min.js") }}"></script>
<script>
(function () {
    /* — Auto-dismiss flash — */
    const msg = document.getElementById('flash-msg');
    if (msg) {
        setTimeout(() => {
            msg.style.transition = 'opacity .5s, transform .5s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateX(-50%) translateY(-10px)';
            setTimeout(() => msg.remove(), 500);
        }, 4500);
    }

    /* — Topbar date — */
    const dateEl = document.getElementById('topbar-date-display');
    if (dateEl) {
        const now = new Date();
        dateEl.textContent = now.toLocaleDateString('ar-SA', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    /* — Mobile sidebar — */
    const sidebar    = document.getElementById('sidebar');
    const overlay    = document.getElementById('sidebarOverlay');
    const toggle     = document.getElementById('sidebarToggle');
    const closeBtn   = document.getElementById('sidebarCloseBtn');
    const icon       = document.getElementById('toggleIcon');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.classList.add('sidebar-open');
        if (icon) icon.className = 'bi bi-x-lg';
    }
    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.classList.remove('sidebar-open');
        if (icon) icon.className = 'bi bi-list';
    }

    if (toggle) toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Auto-close on nav link click on mobile screens
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                closeSidebar();
            }
        });
    });

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSidebar();
            hideConfirm();
        }
    });

    /* — Unified confirm modal (replaces native confirm()) — */
    const backdrop   = document.getElementById('confirmBackdrop');
    const cmMessage  = document.getElementById('confirmMessage');
    const cmTitle    = document.getElementById('confirmTitle');
    const cmOk       = document.getElementById('confirmOkBtn');
    const cmCancel   = document.getElementById('confirmCancelBtn');
    let pendingSubmit = null;

    function showConfirm(message, title, okLabel, danger) {
        if (!backdrop) return;
        cmMessage.textContent = message;
        cmTitle.textContent = title || 'تأكيد العملية';
        cmOk.textContent = okLabel || 'تأكيد';
        cmOk.style.background = danger === false ? 'var(--secondary)' : '#EF4444';
        cmOk.style.borderColor = danger === false ? 'var(--secondary)' : '#EF4444';
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function hideConfirm() {
        if (!backdrop) return;
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
        pendingSubmit = null;
    }

    document.addEventListener('submit', (e) => {
        const form = e.target;
        const message = form.getAttribute('data-confirm');
        if (!message || pendingSubmit) return;
        e.preventDefault();
        pendingSubmit = form;
        const title = form.getAttribute('data-confirm-title');
        const okLabel = form.getAttribute('data-confirm-ok');
        const danger = form.getAttribute('data-confirm-danger') !== 'false';
        showConfirm(message, title, okLabel, danger);
    }, true);

    if (cmOk && backdrop) {
        cmOk.addEventListener('click', () => {
            const form = pendingSubmit;
            if (!form) return hideConfirm();
            hideConfirm();
            form.submit();
        });
    }
    if (cmCancel && backdrop) {
        cmCancel.addEventListener('click', hideConfirm);
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) hideConfirm();
        });
    }
})();
</script>
@stack('scripts')
</body>
</html>
