<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') — نظام إدارة الشحنات</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap RTL --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Cairo Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
    /* ══════════════════════════════════════════
       DESIGN TOKENS
    ══════════════════════════════════════════ */
    :root {
        --primary:      #202833;
        --secondary:    #415A66;
        --accent:       #C7A667;
        --accent-lt:    rgba(199,166,103,0.12);
        --accent-md:    rgba(199,166,103,0.25);
        --bg:           #F7F5EF;
        --surface:      #EAE0C8;
        --white:        #FFFFFF;
        --border:       rgba(65,90,102,0.10);
        --border-md:    rgba(65,90,102,0.18);
        --muted:        #8A96A3;
        --muted-lt:     #B8C2CC;
        --sidebar-w:    248px;
        --topbar-h:     62px;
        --r-xs:         6px;
        --r-sm:         10px;
        --r-md:         14px;
        --r-lg:         18px;
        --sh-xs:        0 1px 4px rgba(32,40,51,0.05);
        --sh-sm:        0 2px 10px rgba(32,40,51,0.07);
        --sh-md:        0 6px 24px rgba(32,40,51,0.10);
        --sh-lg:        0 12px 40px rgba(32,40,51,0.14);
        --tr-fast:      all .15s ease;
        --tr:           all .22s ease;
        --tr-slow:      all .35s ease;
    }

    /* ══════════════════════════════════════════
       RESET & BASE
    ══════════════════════════════════════════ */
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

    a { color: inherit; text-decoration: none; }
    img { max-width: 100%; }

    /* ══════════════════════════════════════════
       SIDEBAR
    ══════════════════════════════════════════ */
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

    /* — Brand — */
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
        transition: var(--tr);
    }
    .sidebar-brand:hover .brand-icon {
        background: rgba(199,166,103,0.22);
        border-color: rgba(199,166,103,0.50);
    }
    .sidebar-brand .brand-text {
        font-size: .88rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.25;
        letter-spacing: -.01em;
    }
    .sidebar-brand .brand-text small {
        display: block;
        font-size: .70rem;
        font-weight: 500;
        color: var(--muted);
        margin-top: 1px;
    }
    .sidebar-brand .brand-badge {
        margin-right: auto;
        background: var(--accent-lt);
        color: var(--accent);
        font-size: .62rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 20px;
        border: 1px solid rgba(199,166,103,0.25);
        letter-spacing: .3px;
        flex-shrink: 0;
    }

    /* — Nav — */
    .sidebar-nav { flex: 1; padding: 10px 12px 10px; overflow-y: auto; }
    .sidebar-nav::-webkit-scrollbar { width: 3px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }

    .nav-label {
        font-size: .62rem;
        font-weight: 800;
        color: rgba(138,150,163,0.7);
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 16px 10px 5px;
    }
    .nav-label:first-child { padding-top: 4px; }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 14px;
        border-radius: var(--r-sm);
        color: rgba(255,255,255,0.60);
        font-size: .86rem;
        font-weight: 600;
        transition: var(--tr);
        margin-bottom: 1px;
        position: relative;
        border: 1px solid transparent;
    }
    .sidebar-link .link-icon {
        width: 22px; height: 22px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: var(--tr);
    }
    .sidebar-link .link-text { flex: 1; }
    .sidebar-link .link-badge {
        background: rgba(199,166,103,0.18);
        color: var(--accent);
        font-size: .64rem;
        font-weight: 800;
        padding: 1px 7px;
        border-radius: 20px;
        border: 1px solid rgba(199,166,103,0.25);
        flex-shrink: 0;
    }
    .sidebar-link:hover {
        background: rgba(255,255,255,0.065);
        color: rgba(255,255,255,0.90);
        border-color: rgba(255,255,255,0.04);
    }
    .sidebar-link.active {
        background: linear-gradient(135deg, rgba(199,166,103,0.18), rgba(199,166,103,0.08));
        color: var(--accent);
        border-color: rgba(199,166,103,0.22);
    }
    .sidebar-link.active .link-icon { color: var(--accent); }
    .sidebar-link.active::before {
        content: '';
        position: absolute;
        right: 0; top: 6px; bottom: 6px;
        width: 3px;
        background: var(--accent);
        border-radius: 3px 0 0 3px;
    }

    /* — Sidebar Divider — */
    .sidebar-divider {
        height: 1px;
        background: rgba(255,255,255,0.05);
        margin: 8px 10px;
    }

    /* — Footer — */
    .sidebar-footer {
        padding: 12px;
        border-top: 1px solid rgba(255,255,255,0.06);
        flex-shrink: 0;
    }

    /* — User card in footer — */
    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 10px;
        border-radius: var(--r-sm);
        margin-bottom: 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.04);
    }
    .sidebar-user .su-avatar {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: .9rem;
        border: 2px solid rgba(199,166,103,0.35);
        flex-shrink: 0;
    }
    .sidebar-user .su-info { flex: 1; min-width: 0; }
    .sidebar-user .su-name {
        font-size: .82rem;
        font-weight: 700;
        color: rgba(255,255,255,0.90);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar-user .su-role {
        font-size: .68rem;
        color: var(--muted);
    }

    .btn-logout-side {
        width: 100%;
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px;
        border-radius: var(--r-sm);
        background: transparent;
        border: 1px solid rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.50);
        font-family: 'Cairo', sans-serif;
        font-size: .84rem; font-weight: 700;
        transition: var(--tr);
        cursor: pointer;
        text-align: right;
    }
    .btn-logout-side:hover {
        background: rgba(239,68,68,0.10);
        border-color: rgba(239,68,68,0.25);
        color: #FCA5A5;
    }
    .btn-logout-side i { font-size: .95rem; }

    /* ══════════════════════════════════════════
       MOBILE TOGGLE BUTTON
    ══════════════════════════════════════════ */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 14px;
        right: 14px;
        z-index: 1100;
        width: 42px; height: 42px;
        background: var(--primary);
        border: none;
        border-radius: var(--r-sm);
        color: #fff;
        font-size: 1.15rem;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: var(--sh-md);
        transition: var(--tr);
    }
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,15,20,0.55);
        z-index: 1040;
        backdrop-filter: blur(2px);
    }

    /* ══════════════════════════════════════════
       MAIN WRAPPER
    ══════════════════════════════════════════ */
    .main-wrapper {
        margin-right: var(--sidebar-w);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-right var(--tr-slow);
    }

    /* ══════════════════════════════════════════
       TOPBAR
    ══════════════════════════════════════════ */
    .topbar {
        height: var(--topbar-h);
        background: var(--white);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 28px;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: var(--sh-xs);
        flex-shrink: 0;
    }
    .topbar-left { display: flex; align-items: center; gap: 14px; }
    .topbar-breadcrumb {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .82rem;
        color: var(--muted);
    }
    .topbar-breadcrumb .bc-sep { color: var(--muted-lt); font-size: .75rem; }
    .topbar-breadcrumb .bc-current {
        color: var(--primary);
        font-weight: 700;
    }
    .topbar-breadcrumb .bc-home {
        color: var(--accent);
        font-size: .9rem;
    }
    .topbar-right { display: flex; align-items: center; gap: 10px; }

    /* — Topbar Icon Buttons — */
    .topbar-icon-btn {
        width: 36px; height: 36px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: transparent;
        color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--tr);
        position: relative;
    }
    .topbar-icon-btn:hover {
        background: var(--bg);
        color: var(--primary);
        border-color: var(--border-md);
    }
    .topbar-icon-btn .notif-dot {
        position: absolute;
        top: 6px; left: 8px;
        width: 8px; height: 8px;
        background: var(--accent);
        border-radius: 50%;
        border: 2px solid #fff;
    }

    /* — User Avatar — */
    .topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 10px 5px 5px;
        border-radius: var(--r-sm);
        border: 1px solid var(--border);
        background: transparent;
        cursor: pointer;
        transition: var(--tr);
    }
    .topbar-user:hover {
        background: var(--bg);
        border-color: var(--border-md);
    }
    .topbar-user .tu-avatar {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, var(--secondary), var(--primary));
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: .88rem;
        border: 2px solid rgba(199,166,103,0.35);
        flex-shrink: 0;
    }
    .topbar-user .tu-info { line-height: 1.2; }
    .topbar-user .tu-name {
        font-size: .82rem;
        font-weight: 700;
        color: var(--primary);
    }
    .topbar-user .tu-role {
        font-size: .68rem;
        color: var(--muted);
    }

    /* — Date Badge — */
    .topbar-date {
        font-size: .78rem;
        font-weight: 600;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 0 12px;
        border-right: 1px solid var(--border);
    }
    .topbar-date i { color: var(--accent); font-size: .85rem; }

    /* ══════════════════════════════════════════
       CONTENT AREA
    ══════════════════════════════════════════ */
    .content-area {
        flex: 1;
        padding: 28px 28px 50px;
    }

    /* ══════════════════════════════════════════
       PAGE HEADER
    ══════════════════════════════════════════ */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 26px;
        flex-wrap: wrap;
        gap: 14px;
    }
    .page-title-group {}
    .page-pre-title {
        font-size: .72rem;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 3px;
    }
    .page-title {
        font-size: 1.45rem;
        font-weight: 900;
        color: var(--primary);
        line-height: 1.2;
        letter-spacing: -.02em;
    }
    .page-subtitle {
        font-size: .82rem;
        color: var(--muted);
        margin-top: 3px;
        font-weight: 500;
    }
    .page-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* ══════════════════════════════════════════
       STAT CARDS
    ══════════════════════════════════════════ */
    .stat-card {
        background: var(--white);
        border-radius: var(--r-md);
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--sh-sm);
        border: 1px solid var(--border);
        transition: var(--tr);
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 3px;
        height: 100%;
        background: transparent;
        border-radius: 0 var(--r-md) var(--r-md) 0;
        transition: var(--tr);
    }
    .stat-card:hover {
        box-shadow: var(--sh-md);
        transform: translateY(-2px);
        border-color: var(--border-md);
    }
    .stat-card:hover::after { background: var(--accent); }

    .stat-icon {
        width: 52px; height: 52px;
        border-radius: var(--r-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .stat-body { flex: 1; min-width: 0; }
    .stat-label {
        font-size: .74rem;
        font-weight: 700;
        color: var(--muted);
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .stat-value {
        font-size: 2.1rem;
        font-weight: 900;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 4px;
        letter-spacing: -.03em;
    }
    .stat-meta {
        font-size: .74rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
    }
    .stat-meta .up { color: #059669; }
    .stat-meta .down { color: #DC2626; }

    /* ══════════════════════════════════════════
       SECTION CARDS
    ══════════════════════════════════════════ */
    .section-card {
        background: var(--white);
        border-radius: var(--r-md);
        box-shadow: var(--sh-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .section-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
    }
    .section-card-title {
        font-size: .92rem;
        font-weight: 800;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-card-title i { color: var(--accent); font-size: 1rem; }
    .section-card-body { padding: 0; }

    /* — Link variant — */
    .btn-text-accent {
        font-size: .80rem;
        font-weight: 700;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 4px;
        transition: var(--tr);
        border: none;
        background: none;
        cursor: pointer;
    }
    .btn-text-accent:hover { color: #b8914e; text-decoration: underline; }

    /* ══════════════════════════════════════════
       TABLE
    ══════════════════════════════════════════ */
    .tbl thead th {
        background: #F8F6F0;
        color: var(--muted);
        font-size: .70rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
        border-bottom: 1px solid var(--border) !important;
        padding: 11px 18px;
        white-space: nowrap;
    }
    .tbl tbody td {
        padding: 13px 18px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(65,90,102,0.05) !important;
        font-size: .86rem;
    }
    .tbl tbody tr:last-child td { border-bottom: none !important; }
    .tbl tbody tr:hover td { background: rgba(65,90,102,0.022); }
    .tbl { margin-bottom: 0; }

    /* Shipment number link */
    .ship-num {
        font-weight: 800;
        font-size: .86rem;
        color: var(--secondary);
        transition: var(--tr-fast);
    }
    .ship-num:hover { color: var(--accent); }

    /* ══════════════════════════════════════════
       STATUS BADGES
    ══════════════════════════════════════════ */
    .sbadge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: .74rem;
        font-weight: 700;
        white-space: nowrap;
        letter-spacing: .15px;
    }
    .sbadge i { font-size: .78rem; }

    .sbadge-pending    { background: rgba(245,158,11,0.10); color: #92400E; border: 1px solid rgba(245,158,11,0.20); }
    .sbadge-assigned   { background: rgba(59,130,246,0.10); color: #1E40AF; border: 1px solid rgba(59,130,246,0.20); }
    .sbadge-in_transit { background: rgba(99,102,241,0.10); color: #3730A3; border: 1px solid rgba(99,102,241,0.20); }
    .sbadge-delivered  { background: rgba(16,185,129,0.10); color: #065F46; border: 1px solid rgba(16,185,129,0.20); }
    .sbadge-failed     { background: rgba(239,68,68,0.10);  color: #991B1B; border: 1px solid rgba(239,68,68,0.20); }
    .sbadge-available  { background: rgba(16,185,129,0.10); color: #065F46; border: 1px solid rgba(16,185,129,0.20); }
    .sbadge-busy       { background: rgba(245,158,11,0.10); color: #92400E; border: 1px solid rgba(245,158,11,0.20); }
    .sbadge-inactive   { background: rgba(107,114,128,0.10);color: #374151; border: 1px solid rgba(107,114,128,0.20); }
    .sbadge-active     { background: rgba(16,185,129,0.10); color: #065F46; border: 1px solid rgba(16,185,129,0.20); }
    .sbadge-maintenance{ background: rgba(245,158,11,0.10); color: #92400E; border: 1px solid rgba(245,158,11,0.20); }

    /* ══════════════════════════════════════════
       FORM ELEMENTS
    ══════════════════════════════════════════ */
    .fc-card {
        background: var(--white);
        border-radius: var(--r-md);
        box-shadow: var(--sh-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .fc-card-header {
        padding: 17px 22px;
        border-bottom: 1px solid var(--border);
        font-weight: 800;
        font-size: .96rem;
        color: var(--primary);
        display: flex; align-items: center; gap: 9px;
        background: #FAFAF8;
    }
    .fc-card-header i { color: var(--accent); }
    .fc-card-body { padding: 26px 22px; }

    .form-label {
        font-weight: 700;
        font-size: .83rem;
        color: var(--primary);
        margin-bottom: 6px;
    }
    .form-label .req { color: #DC2626; margin-right: 2px; }
    .form-control, .form-select {
        border: 1px solid var(--border-md);
        border-radius: var(--r-sm);
        padding: 10px 14px;
        font-family: 'Cairo', sans-serif;
        font-size: .86rem;
        color: var(--primary);
        background: #FAFAF8;
        transition: var(--tr);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(65,90,102,0.10);
        background: #fff;
        outline: none;
    }
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #EF4444;
        box-shadow: none;
    }
    .form-control::placeholder { color: #C0C8D0; font-size: .82rem; }
    .form-text { font-size: .75rem; color: var(--muted); margin-top: 4px; }

    /* ══════════════════════════════════════════
       BUTTONS
    ══════════════════════════════════════════ */
    .btn {
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        border-radius: var(--r-sm);
        transition: var(--tr);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        letter-spacing: .1px;
    }

    /* Gold / Accent */
    .btn-gold {
        background: var(--accent);
        color: var(--primary);
        border: 1px solid transparent;
        font-weight: 800;
    }
    .btn-gold:hover {
        background: #b8914e;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(199,166,103,0.35);
    }

    /* Secondary / Navy outline */
    .btn-navy {
        background: var(--primary);
        color: #fff;
        border: 1px solid var(--primary);
    }
    .btn-navy:hover {
        background: var(--secondary);
        border-color: var(--secondary);
        color: #fff;
    }

    .btn-outline-navy {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--border-md);
    }
    .btn-outline-navy:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    /* Override bootstrap primary */
    .btn-primary {
        background: var(--secondary) !important;
        border-color: var(--secondary) !important;
        font-weight: 700 !important;
        font-family: 'Cairo', sans-serif !important;
    }
    .btn-primary:hover {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
    }
    .btn-primary:focus { box-shadow: 0 0 0 3px rgba(65,90,102,0.20) !important; }

    /* ══════════════════════════════════════════
       FLASH TOASTS
    ══════════════════════════════════════════ */
    .flash-toast {
        position: fixed;
        top: calc(var(--topbar-h) + 16px);
        left: 50%;
        transform: translateX(-50%);
        z-index: 3000;
        min-width: 320px;
        max-width: 480px;
        border-radius: var(--r-md);
        box-shadow: var(--sh-lg);
        border: none;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: .88rem;
        animation: flashIn .3s cubic-bezier(.22,.61,.36,1);
    }
    .flash-toast .ft-icon { font-size: 1.2rem; flex-shrink: 0; }
    .flash-toast .ft-close {
        margin-right: auto;
        background: none; border: none;
        cursor: pointer;
        opacity: .6;
        padding: 0;
        font-size: 1rem;
        color: inherit;
        line-height: 1;
    }
    .flash-toast .ft-close:hover { opacity: 1; }

    .flash-success { background: #ECFDF5; color: #065F46; border-right: 4px solid #10B981; }
    .flash-error   { background: #FEF2F2; color: #991B1B; border-right: 4px solid #EF4444; }
    .flash-warning { background: #FFFBEB; color: #92400E; border-right: 4px solid #F59E0B; }
    .flash-info    { background: #EFF6FF; color: #1E40AF; border-right: 4px solid #3B82F6; }

    @keyframes flashIn {
        from { opacity: 0; transform: translateX(-50%) translateY(-18px); }
        to   { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* ══════════════════════════════════════════
       VALIDATION ERRORS
    ══════════════════════════════════════════ */
    .errors-box {
        background: #FEF2F2;
        color: #991B1B;
        border-radius: var(--r-md);
        border-right: 4px solid #EF4444;
        padding: 14px 18px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 20px;
        font-size: .86rem;
    }
    .errors-box i { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
    .errors-box ul { margin: 5px 0 0; padding-right: 18px; }

    /* ══════════════════════════════════════════
       PAGINATION
    ══════════════════════════════════════════ */
    .pagination .page-link {
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        color: var(--secondary);
        border-color: var(--border);
        border-radius: var(--r-sm) !important;
        margin: 0 2px;
        font-size: .82rem;
        transition: var(--tr-fast);
    }
    .pagination .page-link:hover { background: var(--bg); border-color: var(--border-md); }
    .pagination .page-item.active .page-link {
        background: var(--secondary);
        border-color: var(--secondary);
        color: #fff;
    }

    /* ══════════════════════════════════════════
       EMPTY STATE
    ══════════════════════════════════════════ */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--muted);
    }
    .empty-state .es-icon {
        font-size: 3rem;
        opacity: .18;
        display: block;
        margin-bottom: 12px;
    }
    .empty-state .es-title {
        font-size: .92rem;
        font-weight: 700;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .empty-state .es-sub {
        font-size: .80rem;
        color: var(--muted-lt);
    }

    /* ══════════════════════════════════════════
       FILTER BAR
    ══════════════════════════════════════════ */
    .filter-bar {
        background: var(--white);
        border-radius: var(--r-md);
        padding: 14px 18px;
        box-shadow: var(--sh-xs);
        border: 1px solid var(--border);
        margin-bottom: 18px;
    }

    /* ══════════════════════════════════════════
       TIMELINE
    ══════════════════════════════════════════ */
    .timeline { position: relative; padding-right: 30px; }
    .timeline::before {
        content: '';
        position: absolute;
        right: 12px; top: 8px; bottom: 8px;
        width: 2px;
        background: var(--border);
    }
    .timeline-item { position: relative; padding-bottom: 22px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
        position: absolute;
        right: -30px; top: 3px;
        width: 24px; height: 24px;
        background: var(--accent);
        border: 3px solid #fff;
        border-radius: 50%;
        box-shadow: 0 0 0 2px var(--accent);
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; color: var(--primary);
    }

    /* ══════════════════════════════════════════
       PROGRESS BAR
    ══════════════════════════════════════════ */
    .prog-bar-wrap { background: rgba(65,90,102,0.08); border-radius: 99px; height: 6px; overflow: hidden; }
    .prog-bar-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }

    /* ══════════════════════════════════════════
       UTILITY CLASSES
    ══════════════════════════════════════════ */
    .text-accent   { color: var(--accent) !important; }
    .text-navy     { color: var(--primary) !important; }
    .text-muted-sm { color: var(--muted); font-size: .80rem; }
    .fw-700        { font-weight: 700 !important; }
    .fw-800        { font-weight: 800 !important; }
    .fw-900        { font-weight: 900 !important; }
    .fs-xs         { font-size: .72rem !important; }
    .fs-sm         { font-size: .82rem !important; }
    .pill-count {
        background: rgba(65,90,102,0.10);
        color: var(--secondary);
        border-radius: 20px;
        padding: 2px 10px;
        font-size: .72rem;
        font-weight: 800;
    }
    .divider-v {
        width: 1px;
        height: 24px;
        background: var(--border);
        display: inline-block;
        vertical-align: middle;
    }

    /* ══════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════ */
    @media (max-width: 991px) {
        .content-area { padding: 18px 16px 40px; }
        .topbar { padding: 0 16px; }
        .topbar-date { display: none; }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(var(--sidebar-w));
            transition: transform .32s cubic-bezier(.22,.61,.36,1);
        }
        .sidebar.open { transform: translateX(0); }
        .main-wrapper { margin-right: 0; }
        .sidebar-toggle { display: flex; }
        .sidebar-overlay.open { display: block; }
        .topbar { padding-right: 68px; }
        .stat-value { font-size: 1.7rem; }
    }

    @stack('styles-inline')
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════ MOBILE TOGGLE ═══════════════════ --}}
<button class="sidebar-toggle" id="sidebarToggle" aria-label="القائمة">
    <i class="bi bi-list" id="toggleIcon"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <a class="sidebar-brand" href="{{ route('dashboard') }}">
        <div class="brand-icon">
            <i class="bi bi-truck-front-fill"></i>
        </div>
        <div class="brand-text">
            نظام إدارة الشحنات
            <small>Cargo Management System</small>
        </div>
    </a>

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
            <div class="topbar-breadcrumb">
                <i class="bi bi-house-fill bc-home"></i>
                <i class="bi bi-chevron-left bc-sep"></i>
                <span class="bc-current">@yield('page-title', 'لوحة التحكم')</span>
            </div>
        </div>
        <div class="topbar-right">
            @yield('topbar-actions')
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

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggle   = document.getElementById('sidebarToggle');
    const icon     = document.getElementById('toggleIcon');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        icon.className = 'bi bi-x-lg';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        icon.className = 'bi bi-list';
    }

    if (toggle) toggle.addEventListener('click', () => {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    if (overlay) overlay.addEventListener('click', closeSidebar);
})();
</script>
@stack('scripts')
</body>
</html>
