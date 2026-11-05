@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
/* ─── Welcome Banner ─── */
.welcome-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #2e3f50 55%, #1a2230 100%);
    border-radius: var(--r-lg);
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: var(--sh-md);
}
.welcome-banner::before {
    content: '';
    position: absolute;
    top: -40px; left: -40px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(199,166,103,0.10), transparent 70%);
    pointer-events: none;
}
.welcome-banner::after {
    content: '';
    position: absolute;
    bottom: -30px; right: 120px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(65,90,102,0.25), transparent 70%);
    pointer-events: none;
}
.wb-text { position: relative; z-index: 1; }
.wb-pre {
    font-size: .72rem;
    font-weight: 700;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 4px;
}
.wb-title {
    font-size: 1.55rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.2;
    letter-spacing: -.02em;
    margin-bottom: 6px;
}
.wb-subtitle {
    font-size: .86rem;
    color: rgba(255,255,255,0.60);
    font-weight: 500;
}
.wb-date-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: .78rem;
    font-weight: 600;
    color: rgba(255,255,255,0.65);
    margin-top: 10px;
}
.wb-date-badge i { color: var(--accent); }
.wb-actions {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.wb-icon-deco {
    position: absolute;
    left: 32px; top: 50%;
    transform: translateY(-50%);
    font-size: 7rem;
    color: rgba(255,255,255,0.03);
    pointer-events: none;
    z-index: 0;
}

/* ─── Stats Section ─── */
.stats-section { margin-bottom: 28px; }
.stats-section .section-label {
    font-size: .68rem;
    font-weight: 800;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

/* ─── Alert/Action Row ─── */
.alert-card {
    background: #FFFBEB;
    border: 1px solid rgba(245,158,11,0.25);
    border-radius: var(--r-md);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: .86rem;
    font-weight: 600;
    color: #78350F;
}
.alert-card i { font-size: 1.1rem; color: #D97706; flex-shrink: 0; }
.alert-card .alert-count {
    background: #D97706;
    color: #fff;
    font-size: .7rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 20px;
}

/* ─── Action Shipment Item ─── */
.action-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid rgba(65,90,102,0.05);
    transition: var(--tr-fast);
}
.action-item:last-child { border-bottom: none; }
.action-item:hover { background: rgba(65,90,102,0.022); }
.action-ship-num {
    font-weight: 800;
    font-size: .86rem;
    color: var(--secondary);
}
.action-ship-type {
    font-size: .80rem;
    color: var(--muted);
    font-weight: 500;
}
.action-item .ai-arrow {
    margin-right: auto;
    color: var(--accent);
    font-size: .9rem;
    opacity: 0;
    transition: var(--tr-fast);
}
.action-item:hover .ai-arrow { opacity: 1; }

/* ─── Day Summary ─── */
.day-summary-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    padding: 18px 20px;
}
.day-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 14px 10px;
    border-radius: var(--r-sm);
    background: var(--bg);
    border: 1px solid var(--border);
    transition: var(--tr);
}
.day-stat:hover { border-color: var(--accent); background: var(--accent-lt); }
.day-stat-num {
    font-size: 1.9rem;
    font-weight: 900;
    color: var(--primary);
    line-height: 1;
    margin-bottom: 4px;
    letter-spacing: -.03em;
}
.day-stat-label {
    font-size: .72rem;
    font-weight: 700;
    color: var(--muted);
    text-align: center;
    line-height: 1.3;
}

/* ─── Driver Cards ─── */
.driver-mini {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid rgba(65,90,102,0.05);
    transition: var(--tr-fast);
}
.driver-mini:last-child { border-bottom: none; }
.driver-mini:hover { background: rgba(65,90,102,0.022); }
.driver-ava {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--secondary), var(--primary));
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: .88rem;
    border: 2px solid rgba(199,166,103,0.30);
    flex-shrink: 0;
}
.driver-ava.busy {
    background: linear-gradient(135deg, #D97706, #B45309);
}
.driver-details { flex: 1; min-width: 0; }
.driver-name {
    font-weight: 700;
    font-size: .86rem;
    color: var(--primary);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.driver-sub {
    font-size: .74rem;
    color: var(--muted);
    font-weight: 500;
}

/* ─── Report Card ─── */
.report-card {
    background: var(--white);
    border-radius: var(--r-md);
    box-shadow: var(--sh-sm);
    border: 1px solid var(--border);
    padding: 22px 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: var(--tr);
}
.report-card:hover { box-shadow: var(--sh-md); }
.report-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--accent-lt), rgba(199,166,103,0.05));
    border: 1px solid rgba(199,166,103,0.25);
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    color: var(--accent);
    flex-shrink: 0;
}
.report-info { flex: 1; }
.report-title {
    font-size: .92rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 3px;
}
.report-sub {
    font-size: .78rem;
    color: var(--muted);
    font-weight: 500;
}
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════
     WELCOME BANNER
════════════════════════════════════ --}}
<div class="welcome-banner">
    <div class="wb-text">
        <div class="wb-pre">مرحبًا بك في النظام</div>
        <h2 class="wb-title">أهلاً، {{ explode(' ', auth()->user()->name ?? 'المسؤول')[0] }} </h2>
        <p class="wb-subtitle">إليك ملخص عمليات الشحن والنقل لهذا اليوم</p>
        <div class="wb-date-badge">
            <i class="bi bi-calendar-check"></i>
            <span id="wb-date"></span>
        </div>
    </div>
    <i class="bi bi-truck-front wb-icon-deco"></i>
    <div class="wb-actions">
        <a href="{{ route('shipments.create') }}" class="btn btn-gold btn-sm">
            <i class="bi bi-plus-lg"></i> إضافة شحنة جديدة
        </a>
        <a href="{{ route('reports.export') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.10);color:#fff;border:1px solid rgba(255,255,255,0.18);">
            <i class="bi bi-file-earmark-pdf"></i> تقرير PDF
        </a>
    </div>
</div>

{{-- ════════════════════════════════════
     ACTION ALERT (shipments needing attention)
════════════════════════════════════ --}}
@if($needsAction->count() > 0)
<div class="alert-card mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>
        <strong>{{ $needsAction->count() }} شحنة</strong> تحتاج إلى إجراء عاجل
        <span class="alert-count ms-2">انتبه</span>
    </span>
    <a href="{{ route('shipments.index', ['status' => 'pending']) }}" class="btn btn-sm ms-auto" style="background:#D97706;color:#fff;border:none;font-weight:700;">
        <i class="bi bi-arrow-left-circle"></i> اتخاذ إجراء
    </a>
</div>
@endif

{{-- ════════════════════════════════════
     STAT CARDS
════════════════════════════════════ --}}
<div class="stats-section">
    <div class="section-label">إحصائيات الشحنات</div>
    <div class="row g-3 mb-2">
        {{-- Total --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('shipments.index') }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(65,90,102,0.10); color:var(--secondary);">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">إجمالي الشحنات</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-meta">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;color:var(--muted-lt);"></i>
                        جميع السجلات
                    </div>
                </div>
            </div>
            </a>
        </div>
        {{-- Pending --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('shipments.index', ['status' => 'pending']) }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,158,11,0.10); color:#D97706;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">بانتظار التوزيع</div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                    <div class="stat-meta">
                        @if($stats['pending'] > 0)
                            <span class="down"><i class="bi bi-exclamation-circle-fill"></i> تحتاج تعيين سائق</span>
                        @else
                            <span class="up"><i class="bi bi-check-circle-fill"></i> لا توجد شحنات معلقة</span>
                        @endif
                    </div>
                </div>
            </div>
            </a>
        </div>
        {{-- In Transit --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('shipments.index', ['status' => 'in_transit']) }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(99,102,241,0.10); color:#4F46E5;">
                    <i class="bi bi-truck-front-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">قيد النقل</div>
                    <div class="stat-value">{{ $stats['in_transit'] }}</div>
                    <div class="stat-meta">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;color:#4F46E5;"></i>
                        {{ $stats['assigned'] }} مُعيَّنة
                    </div>
                </div>
            </div>
            </a>
        </div>
        {{-- Delivered --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('shipments.index', ['status' => 'delivered']) }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(16,185,129,0.10); color:#059669;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">تم التسليم</div>
                    <div class="stat-value">{{ $stats['delivered'] }}</div>
                    <div class="stat-meta">
                        @if($stats['failed'] > 0)
                            <span class="down"><i class="bi bi-x-circle-fill"></i> {{ $stats['failed'] }} متعذرة</span>
                        @else
                            <span class="up"><i class="bi bi-check-circle-fill"></i> لا مشكلات</span>
                        @endif
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>

    {{-- Row 2: failed + drivers --}}
    <div class="row g-3">
        {{-- Failed --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('shipments.index', ['status' => 'failed']) }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(239,68,68,0.10); color:#DC2626;">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">متعذر التسليم</div>
                    <div class="stat-value">{{ $stats['failed'] }}</div>
                    <div class="stat-meta">
                        @if($stats['failed'] > 0)
                            <span class="down"><i class="bi bi-exclamation-circle-fill"></i> تحتاج مراجعة</span>
                        @else
                            <span class="up"><i class="bi bi-check-circle-fill"></i> ممتاز</span>
                        @endif
                    </div>
                </div>
            </div>
            </a>
        </div>
        {{-- Today delivered --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(16,185,129,0.10); color:#059669;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">تسليم اليوم</div>
                    <div class="stat-value">{{ $today['delivered'] }}</div>
                    <div class="stat-meta">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;color:#059669;"></i>
                        شحنة سُلِّمت اليوم
                    </div>
                </div>
            </div>
        </div>
        {{-- Available Drivers --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('drivers.index') }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--accent-lt); color:var(--accent);">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">السائقون المتاحون</div>
                    <div class="stat-value">{{ $driverStats['available'] }}</div>
                    <div class="stat-meta">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;color:var(--accent);"></i>
                        من إجمالي {{ $driverStats['total'] }}
                    </div>
                </div>
            </div>
            </a>
        </div>
        {{-- Busy Drivers --}}
        <div class="col-6 col-lg-3">
            <a href="{{ route('drivers.index') }}" style="text-decoration:none;">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,158,11,0.10); color:#D97706;">
                    <i class="bi bi-person-fill-gear"></i>
                </div>
                <div class="stat-body">
                    <div class="stat-label">السائقون المشغولون</div>
                    <div class="stat-value">{{ $driverStats['busy'] }}</div>
                    <div class="stat-meta">
                        <i class="bi bi-circle-fill" style="font-size:.45rem;color:#D97706;"></i>
                        في مهمة نشطة
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════
     MAIN GRID: recent shipments + side panels
════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- LEFT: Recent Shipments Table (8/12) --}}
    <div class="col-lg-8">
        <div class="section-card h-100">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-clock-history"></i>
                    آخر الشحنات المسجّلة
                    @if($recentShipments->count())
                        <span class="pill-count">{{ $recentShipments->count() }}</span>
                    @endif
                </div>
                <a href="{{ route('shipments.index') }}" class="btn-text-accent">
                    عرض الكل <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table tbl">
                    <thead>
                        <tr>
                            <th>رقم الشحنة</th>
                            <th>نوع البضاعة</th>
                            <th>الوجهة</th>
                            <th>السائق</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentShipments as $s)
                        <tr>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" class="ship-num">
                                    {{ $s->shipment_number }}
                                </a>
                            </td>
                            <td class="fw-700">{{ $s->cargo_type }}</td>
                            <td>
                                @if($s->destination)
                                    <span class="d-flex align-items-center gap-1 text-muted fs-sm">
                                        <i class="bi bi-geo-alt-fill text-accent"></i>
                                        {{ $s->destination->name }}
                                    </span>
                                @else
                                    <span class="text-muted fs-xs">—</span>
                                @endif
                            </td>
                            <td>
                                @if($s->driver)
                                    <span class="d-flex align-items-center gap-1">
                                        <span style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,var(--secondary),var(--primary));display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.72rem;flex-shrink:0;">
                                            {{ mb_substr($s->driver->name, 0, 1) }}
                                        </span>
                                        <span class="fw-700 fs-sm">{{ $s->driver->name }}</span>
                                    </span>
                                @else
                                    <span class="sbadge" style="background:rgba(107,114,128,0.07);color:#6B7280;border:1px solid rgba(107,114,128,0.12);font-size:.72rem;">
                                        <i class="bi bi-person-dash"></i> غير معيَّن
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="sbadge sbadge-{{ $s->status }}">
                                    <i class="bi {{ \App\Models\Shipment::statusIcon($s->status) }}"></i>
                                    {{ \App\Models\Shipment::statusLabel($s->status) }}
                                </span>
                            </td>
                            <td class="fs-xs text-muted">{{ $s->created_at->format('Y/m/d') }}</td>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}"
                                   class="btn btn-sm btn-outline-navy"
                                   title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox es-icon"></i>
                                    <div class="es-title">لا توجد شحنات بعد</div>
                                    <div class="es-sub">ابدأ بإضافة أول شحنة للنظام</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT: Actions needed + Drivers --}}
    <div class="col-lg-4 d-flex flex-column gap-4">

        {{-- Needs Action --}}
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-exclamation-circle"></i>
                    تحتاج إجراء
                    @if($needsAction->count())
                        <span class="pill-count" style="background:rgba(239,68,68,0.1);color:#DC2626;">
                            {{ $needsAction->count() }}
                        </span>
                    @endif
                </div>
            </div>
            @forelse($needsAction->take(5) as $s)
            <div class="action-item">
                <div>
                    <div class="action-ship-num">{{ $s->shipment_number }}</div>
                    <div class="action-ship-type">{{ $s->cargo_type }}</div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-8">
                    @if($s->status === 'pending')
                        <span class="sbadge sbadge-pending"><i class="bi bi-clock"></i> معلقة</span>
                    @else
                        <span class="sbadge sbadge-failed"><i class="bi bi-x-circle"></i> فشل</span>
                    @endif
                    <a href="{{ route('shipments.show', $s) }}" class="ai-arrow ms-2">
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="empty-state py-4">
                <i class="bi bi-check2-all es-icon" style="color:#059669;opacity:.3;"></i>
                <div class="es-title">كل شيء على ما يرام!</div>
                <div class="es-sub">لا توجد شحنات تحتاج إجراء</div>
            </div>
            @endforelse
            @if($needsAction->count() > 5)
            <div class="p-3 text-center" style="border-top:1px solid var(--border);">
                <a href="{{ route('shipments.index', ['status' => 'pending']) }}" class="btn-text-accent">
                    عرض {{ $needsAction->count() - 5 }} أخرى <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            @endif
        </div>

        {{-- Drivers Mini List --}}
        <div class="section-card flex-grow-1">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-people"></i>
                    السائقون
                </div>
                <a href="{{ route('drivers.index') }}" class="btn-text-accent">
                    إدارة <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            {{-- Stats mini bar --}}
            <div class="p-3 pb-1" style="border-bottom:1px solid var(--border);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-xs fw-700" style="color:var(--muted);text-transform:uppercase;letter-spacing:.5px;">الحالة العامة</span>
                    <span class="fs-xs fw-700 text-accent">{{ $driverStats['total'] > 0 ? round($driverStats['available']/$driverStats['total']*100) : 0 }}% متاح</span>
                </div>
                <div class="prog-bar-wrap">
                    <div class="prog-bar-fill" style="width:{{ $driverStats['total'] > 0 ? round($driverStats['available']/$driverStats['total']*100) : 0 }}%; background:var(--accent);"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span class="fs-xs" style="color:#059669;font-weight:700;">
                        <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>{{ $driverStats['available'] }} متاح
                    </span>
                    <span class="fs-xs" style="color:#D97706;font-weight:700;">
                        {{ $driverStats['busy'] }} مشغول <i class="bi bi-circle-fill ms-1" style="font-size:.45rem;"></i>
                    </span>
                </div>
            </div>
            @forelse($recentDrivers as $driver)
            <div class="driver-mini">
                <div class="driver-ava {{ $driver->status === 'busy' ? 'busy' : '' }}">
                    {{ mb_substr($driver->name, 0, 1) }}
                </div>
                <div class="driver-details">
                    <div class="driver-name">{{ $driver->name }}</div>
                    <div class="driver-sub">{{ $driver->phone ?? 'لا يوجد رقم' }}</div>
                </div>
                <span class="sbadge sbadge-{{ $driver->status }}">
                    {{ \App\Models\Driver::statusLabel($driver->status) }}
                </span>
            </div>
            @empty
            <div class="empty-state py-3">
                <i class="bi bi-people es-icon"></i>
                <div class="es-sub">لا يوجد سائقون مسجلون</div>
            </div>
            @endforelse
        </div>

    </div>
</div>

{{-- ════════════════════════════════════
     BOTTOM ROW: Day Summary + Chart + Report
════════════════════════════════════ --}}
<div class="row g-4">

    {{-- Day Summary --}}
    <div class="col-lg-4">
        <div class="section-card h-100">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-calendar-day"></i>
                    ملخص اليوم
                </div>
                <span class="fs-xs text-muted fw-700">{{ now()->format('Y/m/d') }}</span>
            </div>
            <div class="day-summary-row">
                <div class="day-stat">
                    <div class="day-stat-num" style="color:var(--secondary);">{{ $today['registered'] }}</div>
                    <div class="day-stat-label">شحنة<br>سُجِّلت</div>
                </div>
                <div class="day-stat">
                    <div class="day-stat-num" style="color:#3B82F6;">{{ $today['assigned'] }}</div>
                    <div class="day-stat-label">عُيِّن<br>سائق لها</div>
                </div>
                <div class="day-stat">
                    <div class="day-stat-num" style="color:#7C3AED;">{{ $today['in_transit'] }}</div>
                    <div class="day-stat-label">بدأت<br>النقل</div>
                </div>
                <div class="day-stat">
                    <div class="day-stat-num" style="color:#059669;">{{ $today['delivered'] }}</div>
                    <div class="day-stat-label">تم<br>تسليمها</div>
                </div>
            </div>
            {{-- Divider --}}
            <div style="height:1px;background:var(--border);margin:0 18px;"></div>
            {{-- Status breakdown --}}
            <div class="p-4">
                @foreach([
                    ['pending',    'بانتظار التوزيع', '#F59E0B', $stats['pending']],
                    ['assigned',   'تم التكليف',      '#3B82F6', $stats['assigned']],
                    ['in_transit', 'قيد النقل',        '#7C3AED', $stats['in_transit']],
                    ['delivered',  'تم التسليم',       '#10B981', $stats['delivered']],
                    ['failed',     'متعذر التسليم',    '#EF4444', $stats['failed']],
                ] as [$key, $label, $color, $count])
                    @php $pct = $stats['total'] > 0 ? round($count / $stats['total'] * 100) : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-xs fw-700" style="color:var(--primary);">{{ $label }}</span>
                            <span class="fs-xs fw-800" style="color:{{ $color }};">{{ $count }} <span style="color:var(--muted);font-weight:600;">({{ $pct }}%)</span></span>
                        </div>
                        <div class="prog-bar-wrap">
                            <div class="prog-bar-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Monthly Chart --}}
    <div class="col-lg-5">
        <div class="section-card h-100">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-bar-chart-line"></i>
                    الشحنات الشهرية
                </div>
                <span class="fs-xs text-muted fw-700">آخر 6 أشهر</span>
            </div>
            <div class="p-4" style="height: 300px;">
                <canvas id="shipmentsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Report Card --}}
    <div class="col-lg-3 d-flex flex-column gap-4">
        <div class="report-card">
            <div class="report-icon">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
            </div>
            <div class="report-info">
                <div class="report-title">التقرير اليومي</div>
                <div class="report-sub">{{ now()->format('l، d F Y') }}</div>
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-navy">
                        <i class="bi bi-eye"></i> عرض
                    </a>
                    <a href="{{ route('reports.export') }}" class="btn btn-sm btn-gold">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="section-card flex-grow-1">
            <div class="section-card-header">
                <div class="section-card-title">
                    <i class="bi bi-lightning-charge"></i>
                    إجراءات سريعة
                </div>
            </div>
            <div class="p-2">
                @foreach([
                    [route('shipments.create'),   'bi-plus-circle',    'إضافة شحنة جديدة'],
                    [route('drivers.create'),      'bi-person-plus',    'إضافة سائق'],
                    [route('trucks.create'),       'bi-truck',          'إضافة شاحنة'],
                    [route('destinations.create'), 'bi-geo-alt',        'إضافة وجهة'],
                    [route('reports.index'),       'bi-bar-chart-line', 'عرض التقارير'],
                ] as [$url, $icon, $label])
                <a href="{{ $url }}"
                   class="d-flex align-items-center gap-10 px-3 py-2 rounded text-decoration-none"
                   style="gap:10px;color:var(--primary);font-size:.85rem;font-weight:600;border-radius:var(--r-sm);transition:var(--tr);"
                   onmouseover="this.style.background='var(--bg)';this.style.color='var(--accent)';"
                   onmouseout="this.style.background='transparent';this.style.color='var(--primary)';">
                    <i class="bi {{ $icon }}" style="color:var(--accent);width:18px;text-align:center;"></i>
                    {{ $label }}
                    <i class="bi bi-arrow-left ms-auto" style="font-size:.75rem;opacity:.4;"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    /* — Welcome date — */
    const d = document.getElementById('wb-date');
    if (d) d.textContent = new Date().toLocaleDateString('ar-SA', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    /* — Chart — */
    const labels = {!! json_encode(array_column($chartData, 'label')) !!};
    const data   = {!! json_encode(array_column($chartData, 'count')) !!};
    const ctx = document.getElementById('shipmentsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'عدد الشحنات',
                    data,
                    backgroundColor: (ctx) => {
                        const chart = ctx.chart;
                        const {ctx: c, chartArea} = chart;
                        if (!chartArea) return 'rgba(65,90,102,0.15)';
                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(199,166,103,0.70)');
                        gradient.addColorStop(1, 'rgba(65,90,102,0.20)');
                        return gradient;
                    },
                    borderColor: '#C7A667',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(199,166,103,0.85)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true,
                        backgroundColor: '#202833',
                        titleColor: '#C7A667',
                        bodyColor: '#fff',
                        borderColor: 'rgba(199,166,103,0.30)',
                        borderWidth: 1,
                        cornerRadius: 10,
                        padding: 12,
                        bodyFont: { family: 'Cairo', size: 13 },
                        titleFont: { family: 'Cairo', weight: '800', size: 12 },
                        callbacks: { label: ctx => ` ${ctx.parsed.y} شحنة` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Cairo', size: 12 }, color: '#8A96A3' },
                        grid: { color: 'rgba(65,90,102,0.06)', drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo', size: 12 }, color: '#8A96A3' }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
