@extends('layouts.app')
@section('title', 'توزيع الشحنات')
@section('page-title', 'لوحة توزيع الشحنات')
@section('title-icon', 'bi-kanban')

@section('content')
<style>
/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   DESIGN SYSTEM & STATUS COLOR TOKENS
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
:root {
    --primary-navy: #202833;
    --secondary-navy: #415A66;
    --gold-accent: #C7A667;
    --bg-ivory: #F7F5EF;
    --surface-white: #FFFFFF;
    --border-light: rgba(65, 90, 102, 0.12);
    --text-muted: #6c757d;

    /* Status Tokens: Shipments & Drivers */
    --status-pending-text: #8B682D;
    --status-pending-bg: #F8F0DF;
    --status-pending-border: #E8D5B5;

    --status-assigned-text: #3E638F;
    --status-assigned-bg: #EAF1F8;
    --status-assigned-border: #C8D7EC;

    --status-transit-text: #3F6A65;
    --status-transit-bg: #E8F2EF;
    --status-transit-border: #C4E0DC;

    --status-delivered-text: #47734D;
    --status-delivered-bg: #EAF3E9;
    --status-delivered-border: #C8E2C7;

    --status-cancelled-text: #98514D;
    --status-cancelled-bg: #F8EAE8;
    --status-cancelled-border: #ECC6C4;
}

body {
    background-color: var(--bg-ivory) !important;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}

.saas-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* Page Intro Header */
.page-intro-header {
    margin-bottom: 24px;
}
.page-intro-title {
    font-size: 22px;
    font-weight: 900;
    color: var(--primary-navy);
    margin: 0 0 4px;
}
.page-intro-desc {
    font-size: 13.5px;
    color: var(--text-muted);
    margin: 0;
}

/* 1. Stat Cards Grid (4 Cards) */
.stat-card-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 26px;
}
@media (max-width: 991px) {
    .stat-card-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
    .stat-card-grid { grid-template-columns: 1fr; }
}

.saas-stat-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(32, 40, 51, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.saas-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 3px;
    background: var(--gold-accent); opacity: 0.5;
}
.stat-icon-wrapper {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}

/* 2. Step Header Indicator */
.step-indicator-bar {
    display: flex;
    justify-content: space-between;
    margin-bottom: 24px;
    background: var(--surface-white);
    padding: 16px 24px;
    border-radius: 16px;
    border: 1px solid var(--border-light);
}
.step-item {
    display: flex; align-items: center; gap: 10px; opacity: 0.4; transition: opacity 0.2s ease;
}
.step-item.active { opacity: 1; }
.step-item.completed { opacity: 0.85; }
.step-number {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--bg-ivory); color: var(--primary-navy);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 14px; border: 1.5px solid var(--border-light);
}
.step-item.active .step-number {
    background: var(--gold-accent); color: var(--primary-navy); border-color: var(--gold-accent);
}
.step-item.completed .step-number {
    background: #198754; color: #fff; border-color: #198754;
}

/* 3. Saas Card Layout */
.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
    transition: all 0.3s ease;
}
.saas-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 16px; margin-bottom: 20px;
    border-bottom: 1px solid var(--border-light);
}
.saas-card-title {
    font-size: 17px; font-weight: 800; color: var(--primary-navy);
    display: flex; align-items: center; gap: 10px; margin: 0;
}
.saas-card-title i { color: var(--gold-accent); font-size: 20px; }

/* Filter Toolbar */
.filter-toolbar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;
}
.search-input-group {
    flex: 1; min-width: 260px; position: relative;
}
.search-input-group input {
    background: var(--bg-ivory); border: 1px solid var(--border-light);
    border-radius: 12px; padding: 10px 14px 10px 40px; font-size: 13.5px; width: 100%; color: var(--primary-navy);
}
.search-input-group i {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);
}
.filter-select {
    background: var(--bg-ivory); border: 1px solid var(--border-light);
    border-radius: 12px; padding: 10px 16px; font-size: 13.5px; color: var(--primary-navy);
}

/* 4. Table Design */
.saas-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
.saas-table th {
    background: var(--bg-ivory); color: var(--text-muted);
    font-size: 11.5px; font-weight: 700; text-transform: uppercase;
    padding: 14px 18px; border-bottom: 1.5px solid var(--border-light); letter-spacing: 0.3px;
}
.saas-table td {
    padding: 16px 18px; border-bottom: 1px solid rgba(65, 90, 102, 0.05);
    font-size: 13.5px; vertical-align: middle; color: var(--primary-navy);
}
.saas-table tbody tr { transition: background-color 0.15s ease; cursor: pointer; }
.saas-table tbody tr:hover { background-color: rgba(199, 166, 103, 0.03); }
.saas-table tbody tr.selected-row {
    background-color: rgba(199, 166, 103, 0.08) !important;
    border-right: 4px solid var(--gold-accent);
}

/* 5. Status Badges System */
.saas-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; line-height: 1.2;
    border: 1px solid transparent; white-space: nowrap;
}
.saas-badge i { font-size: 13px; }

.saas-badge-pending { color: var(--status-pending-text); background-color: var(--status-pending-bg); border-color: var(--status-pending-border); }
.saas-badge-assigned { color: var(--status-assigned-text); background-color: var(--status-assigned-bg); border-color: var(--status-assigned-border); }
.saas-badge-transit { color: var(--status-transit-text); background-color: var(--status-transit-bg); border-color: var(--status-transit-border); }
.saas-badge-delivered { color: var(--status-delivered-text); background-color: var(--status-delivered-bg); border-color: var(--status-delivered-border); }
.saas-badge-cancelled { color: var(--status-cancelled-text); background-color: var(--status-cancelled-bg); border-color: var(--status-cancelled-border); }

/* 6. Driver Cards Grid */
.drivers-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px;
}
@media (max-width: 991px) { .drivers-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575px) { .drivers-grid { grid-template-columns: 1fr; } }

.driver-card-saas {
    background: var(--surface-white); border: 1.5px solid var(--border-light);
    border-radius: 16px; padding: 18px; position: relative; transition: all 0.2s ease; cursor: pointer;
}
.driver-card-saas:hover:not(.disabled) {
    border-color: var(--gold-accent); box-shadow: 0 4px 16px rgba(199, 166, 103, 0.15);
}
.driver-card-saas.selected-driver {
    border-color: var(--gold-accent) !important; background: rgba(199, 166, 103, 0.04);
    box-shadow: 0 0 0 2px rgba(199, 166, 103, 0.2);
}
.driver-card-saas.disabled {
    opacity: 0.5; background: #f9f9f9; cursor: not-allowed;
}
.driver-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--secondary-navy) 100%);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 18px; border: 2px solid rgba(199, 166, 103, 0.3);
}
.check-icon-badge {
    position: absolute; top: 14px; left: 14px;
    background: var(--gold-accent); color: var(--primary-navy);
    width: 24px; height: 24px; border-radius: 50%;
    display: none; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;
}
.driver-card-saas.selected-driver .check-icon-badge { display: flex; }

/* 7. Summary & Assignment Button */
.summary-box-saas {
    background: var(--bg-ivory); border: 1px dashed var(--gold-accent);
    border-radius: 16px; padding: 20px 24px;
    display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; align-items: center;
}
@media (max-width: 991px) { .summary-box-saas { grid-template-columns: repeat(2, 1fr); } }
.summary-item .label { font-size: 11.5px; color: var(--text-muted); font-weight: 600; margin-bottom: 4px; }
.summary-item .val { font-size: 14.5px; font-weight: 800; color: var(--primary-navy); }

.btn-gold-saas {
    background: var(--gold-accent); color: var(--primary-navy) !important;
    border: none; font-weight: 800; font-size: 15px;
    padding: 14px 28px; border-radius: 14px;
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(199, 166, 103, 0.3);
    cursor: pointer; width: 100%;
}
.btn-gold-saas:hover:not(:disabled) { transform: translateY(-1px); background: #d4b273; }
.btn-gold-saas:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }

/* Toast & Modal */
.toast-container-saas { position: fixed; bottom: 30px; left: 30px; z-index: 3000; }
.saas-toast {
    background: var(--primary-navy); color: #fff; padding: 14px 24px; border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px;
    transform: translateY(100px); opacity: 0; transition: all 0.3s ease;
}
.saas-toast.show { transform: translateY(0); opacity: 1; }

.saas-modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(32, 40, 51, 0.6); backdrop-filter: blur(5px);
    z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px;
}
.saas-modal-overlay.active { display: flex; }
.saas-modal-box {
    background: var(--surface-white); border-radius: 24px; max-width: 540px; width: 100%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2); overflow: hidden;
}
</style>

<div class="saas-container" dir="rtl" id="distributionApp">

    <!-- Page Title & Description -->
    <div class="page-intro-header">
        <h1 class="page-intro-title">توزيع الشحنات</h1>
        <p class="page-intro-desc">إدارة وتكليف الشحنات المنتظرة للسائقين المتاحين بخطوات ذكية ومنظمة.</p>
    </div>

    <!-- Step Progress Indicator Bar -->
    <div class="step-indicator-bar">
        <div class="step-item active" id="stepIndicator1">
            <div class="step-number">1</div>
            <div>
                <div class="fw-bold fs-7 text-dark">الخطوة الأولى</div>
                <div class="fs-8 text-muted">اختيار الشحنة</div>
            </div>
        </div>
        <div class="step-item" id="stepIndicator2">
            <div class="step-number">2</div>
            <div>
                <div class="fw-bold fs-7 text-dark">الخطوة الثانية</div>
                <div class="fs-8 text-muted">اختيار السائق</div>
            </div>
        </div>
        <div class="step-item" id="stepIndicator3">
            <div class="step-number">3</div>
            <div>
                <div class="fw-bold fs-7 text-dark">الخطوة الثالثة</div>
                <div class="fs-8 text-muted">تأكيد التوزيع</div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="stat-card-grid">
        <div class="saas-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">الشحنات بانتظار التوزيع</div>
                <div class="fs-4 fw-black text-dark" id="statPending">{{ $shipments->whereIn('status', ['pending', 'assigned'])->count() }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: var(--status-pending-bg); color: var(--status-pending-text);">
                <i class="bi bi-clock"></i>
            </div>
        </div>

        <div class="saas-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">تعيين سائق لها</div>
                <div class="fs-4 fw-black text-dark" id="statAssigned">{{ $shipments->where('status', 'assigned')->count() }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: var(--status-assigned-bg); color: var(--status-assigned-text);">
                <i class="bi bi-person-check"></i>
            </div>
        </div>

        <div class="saas-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">السائقون المتاحون</div>
                <div class="fs-4 fw-black text-dark" id="statAvailable">
                    {{ isset($allDrivers) ? $allDrivers->filter(fn($d) => method_exists($d, 'isAvailable') ? $d->isAvailable() : $d->status === 'available')->count() : 0 }}
                </div>
            </div>
            <div class="stat-icon-wrapper" style="background: var(--status-delivered-bg); color: var(--status-delivered-text);">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>

        <div class="saas-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">السائقون المشغولون</div>
                <div class="fs-4 fw-black text-dark" id="statBusy">
                    {{ isset($allDrivers) ? $allDrivers->filter(fn($d) => !(method_exists($d, 'isAvailable') ? $d->isAvailable() : $d->status === 'available'))->count() : 0 }}
                </div>
            </div>
            <div class="stat-icon-wrapper" style="background: var(--status-cancelled-bg); color: var(--status-cancelled-text);">
                <i class="bi bi-person-dash"></i>
            </div>
        </div>
    </div>

    <!-- STEP 1: SHIPMENTS TABLE -->
    <div class="saas-card" id="step1Card">
        <div class="saas-card-header">
            <h5 class="saas-card-title">
                <i class="bi bi-1-circle-fill"></i> الشحنات بانتظار التوزيع
            </h5>
            <span class="saas-badge saas-badge-pending">
                <i class="bi bi-clock"></i> بانتظار اتخاذ إجراء
            </span>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="filter-toolbar">
            <div class="search-input-group">
                <input type="text" id="shipmentSearch" placeholder="ابحث برقم الشحنة، الوجهة، أو نوع البضاعة..." onkeyup="filterShipments()">
                <i class="bi bi-search"></i>
            </div>
            <div>
                <select id="destinationFilter" class="filter-select" onchange="filterShipments()">
                    <option value="">جميع الوجهات</option>
                    @foreach($shipments->pluck('destination.name')->unique()->filter() as $dest)
                        <option value="{{ $dest }}">{{ $dest }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="cargoFilter" class="filter-select" onchange="filterShipments()">
                    <option value="">جميع أنواع البضائع</option>
                    @foreach($shipments->pluck('cargo_type')->unique()->filter() as $cargo)
                        <option value="{{ $cargo }}">{{ $cargo }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="saas-table" id="shipmentsTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">تحديد</th>
                        <th class="text-end">رقم الشحنة</th>
                        <th class="text-center">الوجهة</th>
                        <th class="text-center">نوع البضاعة</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">تاريخ الشحنة</th>
                        <th class="text-center">حالة الشحنة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $sh)
                    <tr onclick="selectShipment(this, '{{ $sh->id }}', '{{ $sh->shipment_number }}', '{{ $sh->destination?->name ?? 'غير محددة' }}', '{{ $sh->cargo_type }}', '{{ $sh->quantity ? $sh->quantity . ' ' . $sh->unit : '—' }}')"
                        id="shipment-row-{{ $sh->id }}"
                        data-number="{{ $sh->shipment_number }}"
                        data-destination="{{ $sh->destination?->name ?? '' }}"
                        data-cargo="{{ $sh->cargo_type }}">
                        <td class="text-center">
                            <input type="radio" name="selected_shipment" class="form-check-input shipment-radio" value="{{ $sh->id }}">
                        </td>
                        <td class="text-end fw-bold font-monospace">#{{ $sh->shipment_number }}</td>
                        <td class="text-center text-muted">{{ $sh->destination?->name ?? '—' }}</td>
                        <td class="text-center fw-semibold">{{ $sh->cargo_type }}</td>
                        <td class="text-center font-monospace">{{ $sh->quantity ? $sh->quantity . ' ' . $sh->unit : '—' }}</td>
                        <td class="text-center text-muted fs-8">{{ $sh->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($sh->status) {
                                    'in_transit' => 'saas-badge-transit',
                                    'delivered' => 'saas-badge-delivered',
                                    'assigned' => 'saas-badge-assigned',
                                    'cancelled', 'failed' => 'saas-badge-cancelled',
                                    default => 'saas-badge-pending'
                                };
                                $badgeIcon = match($sh->status) {
                                    'in_transit' => 'bi-truck',
                                    'delivered' => 'bi-check-circle',
                                    'assigned' => 'bi-user-check',
                                    'cancelled', 'failed' => 'bi-x-circle',
                                    default => 'bi-clock'
                                };
                            @endphp
                            <span class="saas-badge {{ $badgeClass }}">
                                <i class="bi {{ $badgeIcon }}"></i> {{ \App\Models\Shipment::statusLabel($sh->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-5 opacity-25 d-block mb-2"></i>
                            <h6 class="fw-bold text-secondary">لا توجد شحنات بانتظار التوزيع حالياً</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 2: DRIVER SELECTION -->
    <div class="saas-card" id="step2Card" style="opacity: 0.6; pointer-events: none;">
        <div class="saas-card-header">
            <h5 class="saas-card-title">
                <i class="bi bi-2-circle-fill"></i> اختيار السائق المناسب
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="availableOnlySwitch" onchange="toggleAvailableDrivers()">
                    <label class="form-check-label fs-8 fw-bold text-muted me-2" for="availableOnlySwitch">عرض المتاحين فقط</label>
                </div>
                <span class="text-muted fs-8" id="driverStepNotice">يرجى اختيار شحنة أولاً</span>
            </div>
        </div>

        <div class="drivers-grid" id="driversGrid">
            @forelse($allDrivers ?? [] as $driver)
                @php
                    $isAvailable = method_exists($driver, 'isAvailable') ? $driver->isAvailable() : ($driver->status === 'available');
                @endphp
                <div class="driver-card-saas {{ !$isAvailable ? 'disabled' : '' }}" 
                     id="driver-card-{{ $driver->id }}"
                     data-available="{{ $isAvailable ? 'true' : 'false' }}"
                     @if($isAvailable) onclick="selectDriver(this, '{{ $driver->id }}', '{{ $driver->name }}', '{{ $driver->truck?->plate_number ?? 'بدون شاحنة' }}')" @endif>
                    
                    <div class="check-icon-badge"><i class="bi bi-check"></i></div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="driver-avatar">{{ mb_substr($driver->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $driver->name }}</div>
                                <small class="text-muted fs-8 font-monospace" dir="ltr">{{ $driver->phone }}</small>
                            </div>
                        </div>
                        <div>
                            @if($isAvailable)
                                <span class="saas-badge saas-badge-delivered">
                                    <i class="bi bi-check-circle"></i> متاح
                                </span>
                            @else
                                <span class="saas-badge saas-badge-cancelled" onclick="alertBusyDriver(event)">
                                    <i class="bi bi-dash-circle"></i> مشغول
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light p-2.5 rounded-3 d-flex align-items-center justify-content-between fs-8 text-muted">
                        <span><i class="bi bi-truck me-1"></i> رقم الشاحنة:</span>
                        <span class="fw-bold text-dark font-monospace">{{ $driver->truck?->plate_number ?? 'غير مرتبطة' }}</span>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">لا توجد بيانات للسائقين.</div>
            @endforelse
        </div>
    </div>

    <!-- STEP 3: SUMMARY & CONFIRMATION -->
    <div class="saas-card" id="step3Card" style="opacity: 0.6; pointer-events: none;">
        <div class="saas-card-header">
            <h5 class="saas-card-title">
                <i class="bi bi-3-circle-fill"></i> ملخص عملية التوزيع
            </h5>
        </div>

        <div class="summary-box-saas mb-4">
            <div class="summary-item">
                <div class="label">رقم الشحنة</div>
                <div class="val" id="summaryShipmentNum">—</div>
            </div>
            <div class="summary-item">
                <div class="label">الوجهة</div>
                <div class="val" id="summaryDestination">—</div>
            </div>
            <div class="summary-item">
                <div class="label">نوع البضاعة</div>
                <div class="val" id="summaryCargo">—</div>
            </div>
            <div class="summary-item">
                <div class="label">الكمية</div>
                <div class="val" id="summaryQuantity">—</div>
            </div>
            <div class="summary-item">
                <div class="label">اسم السائق</div>
                <div class="val text-warning" id="summaryDriverName">—</div>
            </div>
            <div class="summary-item">
                <div class="label">رقم الشاحنة</div>
                <div class="val" id="summaryTruck">—</div>
            </div>
        </div>

        {{-- تم تصحيح الـ action هنا ليتطابق مع الـ Route الموجود في النظام (shipments.assign) --}}
        <form action="" method="POST" id="assignmentForm">
            @csrf
            <input type="hidden" name="driver_id" id="formDriverId">
            
            <button type="button" class="btn-gold-saas" id="submitAssignBtn" disabled onclick="handleDistributionAction()">
                <i class="bi bi-check2-all fs-5"></i> تعيين السائق للشحنة
            </button>
        </form>
    </div>

</div>

<!-- Confirmation Modal -->
<div class="saas-modal-overlay" id="confirmModal">
    <div class="saas-modal-box">
        <div class="p-4 border-bottom bg-light d-flex align-items-center justify-content-between">
            <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-warning fs-4"></i> تأكيد تعيين السائق
            </h5>
            <button type="button" class="btn-close" onclick="closeConfirmModal()"></button>
        </div>
        <div class="p-4">
            <p class="text-muted fs-7 mb-4">أنت على وشك إسناد الشحنة المحددة للسائق المختار. سيتم تحديث حالات الشحنة والسائق تلقائياً.</p>
            
            <div class="bg-light p-3 rounded-3 mb-4 fs-7">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">رقم الشحنة:</span>
                    <span class="fw-bold font-monospace" id="modalShipmentNum"></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">وجهة الوصول:</span>
                    <span class="fw-bold" id="modalDestination"></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">السائق المكلف:</span>
                    <span class="fw-bold text-warning" id="modalDriverName"></span>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-light border px-4 py-2 rounded-3" onclick="closeConfirmModal()">إلغاء</button>
                <button type="button" class="btn-gold-saas py-2 px-4 w-auto" onclick="executeSuccessfulAssignment()">تأكيد الاعتماد</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div class="toast-container-saas">
    <div class="saas-toast" id="successToast">
        <i class="bi bi-check-circle-fill text-success fs-4"></i>
        <div>
            <div class="fw-bold">تم التوزيع بنجاح!</div>
            <div class="fs-8 text-muted" id="toastMessage">تم تعيين السائق وتحديث حالات النظام بدقة.</div>
        </div>
    </div>
</div>

<script>
let selectedShipmentId = null;
let selectedDriverId = null;
let selectedShipmentRow = null;

function selectShipment(row, id, number, destination, cargo, quantity) {
    document.querySelectorAll('#shipmentsTable tr').forEach(tr => tr.classList.remove('selected-row'));
    document.querySelectorAll('.shipment-radio').forEach(r => r.checked = false);

    row.classList.add('selected-row');
    const radio = row.querySelector('.shipment-radio');
    if (radio) radio.checked = true;

    selectedShipmentId = id;
    selectedShipmentRow = row;

    document.getElementById('summaryShipmentNum').innerText = '#' + number;
    document.getElementById('summaryDestination').innerText = destination;
    document.getElementById('summaryCargo').innerText = cargo;
    document.getElementById('summaryQuantity').innerText = quantity;

    // Activate Step 2
    const step2 = document.getElementById('step2Card');
    step2.style.opacity = '1';
    step2.style.pointerEvents = 'auto';
    document.getElementById('driverStepNotice').innerText = 'اختر سائقاً متاحاً أدناه';
    
    updateIndicators(2);
}

function selectDriver(card, id, name, truck) {
    if (card.classList.contains('disabled')) {
        showErrorValidation('عذراً، هذا السائق مشغول حالياً (في مهمة أخرى) ولا يمكن اختياره.');
        return;
    }

    if (!selectedShipmentId) {
        showErrorValidation('الرجاء اختيار الشحنة في الخطوة الأولى أولاً.');
        return;
    }

    document.querySelectorAll('.driver-card-saas').forEach(c => c.classList.remove('selected-driver'));

    card.classList.add('selected-driver');
    selectedDriverId = id;
    document.getElementById('formDriverId').value = id;

    document.getElementById('summaryDriverName').innerText = name;
    document.getElementById('summaryTruck').innerText = truck;

    // Activate Step 3
    const step3 = document.getElementById('step3Card');
    step3.style.opacity = '1';
    step3.style.pointerEvents = 'auto';
    document.getElementById('submitAssignBtn').removeAttribute('disabled');
    
    updateIndicators(3);
}

function alertBusyDriver(e) {
    e.stopPropagation();
    showErrorValidation('هذا السائق مشغول حالياً في مهمة أخرى، زر التعيين له معطل.');
}

function updateIndicators(step) {
    const s1 = document.getElementById('stepIndicator1');
    const s2 = document.getElementById('stepIndicator2');
    const s3 = document.getElementById('stepIndicator3');

    s1.className = 'step-item completed';
    if (step >= 2) s2.className = 'step-item active';
    if (step >= 3) {
        s2.className = 'step-item completed';
        s3.className = 'step-item active';
    }
}

function filterShipments() {
    const searchVal = document.getElementById('shipmentSearch').value.toLowerCase();
    const destVal = document.getElementById('destinationFilter').value;
    const cargoVal = document.getElementById('cargoFilter').value;

    const rows = document.querySelectorAll('#shipmentsTable tbody tr');
    rows.forEach(row => {
        if (!row.dataset.number) return;
        const number = row.dataset.number.toLowerCase();
        const destination = row.dataset.destination;
        const cargo = row.dataset.cargo;

        const matchSearch = number.includes(searchVal) || destination.toLowerCase().includes(searchVal) || cargo.toLowerCase().includes(searchVal);
        const matchDest = !destVal || destination === destVal;
        const matchCargo = !cargoVal || cargo === cargoVal;

        if (matchSearch && matchDest && matchCargo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function toggleAvailableDrivers() {
    const onlyAvailable = document.getElementById('availableOnlySwitch').checked;
    const cards = document.querySelectorAll('.drivers-grid .driver-card-saas');

    cards.forEach(card => {
        const isAvailable = card.dataset.available === 'true';
        if (onlyAvailable && !isAvailable) {
            card.style.display = 'none';
        } else {
            card.style.display = 'block';
        }
    });
}

function handleDistributionAction() {
    if (!selectedShipmentId) {
        showErrorValidation('الرجاء اختيار شحنة أولاً.');
        return;
    }
    if (!selectedDriverId) {
        showErrorValidation('الرجاء اختيار سائق لتنفيذ التوزيع.');
        return;
    }

    document.getElementById('modalShipmentNum').innerText = document.getElementById('summaryShipmentNum').innerText;
    document.getElementById('modalDestination').innerText = document.getElementById('summaryDestination').innerText;
    document.getElementById('modalDriverName').innerText = document.getElementById('summaryDriverName').innerText;

    document.getElementById('confirmModal').classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('active');
}

function executeSuccessfulAssignment() {
    closeConfirmModal();

    const toast = document.getElementById('successToast');
    toast.classList.add('show');

    if (selectedShipmentRow) {
        selectedShipmentRow.style.transition = 'all 0.4s ease';
        selectedShipmentRow.style.transform = 'scale(0.95)';
        selectedShipmentRow.style.opacity = '0';
        setTimeout(() => {
            selectedShipmentRow.remove();
            const tbody = document.querySelector('#shipmentsTable tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox display-5 opacity-25 d-block mb-2"></i><h6 class="fw-bold text-secondary">لا توجد شحنات بانتظار التوزيع حالياً</h6></td></tr>`;
            }
        }, 400);
    }

    // Update Stats counters
    const statPending = document.getElementById('statPending');
    const statAssigned = document.getElementById('statAssigned');
    const statAvailable = document.getElementById('statAvailable');
    const statBusy = document.getElementById('statBusy');

    if (statPending) statPending.innerText = Math.max(0, parseInt(statPending.innerText) - 1);
    if (statAssigned) statAssigned.innerText = parseInt(statAssigned.innerText) + 1;
    if (statAvailable) statAvailable.innerText = Math.max(0, parseInt(statAvailable.innerText) - 1);
    if (statBusy) statBusy.innerText = parseInt(statBusy.innerText) + 1;

    // Mark driver as busy visually
    if (selectedDriverId) {
        const driverCard = document.getElementById('driver-card-' + selectedDriverId);
        if (driverCard) {
            driverCard.classList.add('disabled');
            driverCard.removeAttribute('onclick');
            driverCard.dataset.available = 'false';
            const badge = driverCard.querySelector('.saas-badge');
            if (badge) {
                badge.className = 'saas-badge saas-badge-cancelled';
                badge.innerHTML = '<i class="bi bi-dash-circle"></i> مشغول';
            }
        }
    }

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3500);

    // توجيه الـ Form بالرابط الصحيح الموجود في النظام (shipments.assign)
    setTimeout(() => {
        const form = document.getElementById('assignmentForm');
        form.action = `/shipments/${selectedShipmentId}/assign`;
        form.submit();
    }, 1200);
}

function showErrorValidation(msg) {
    alert(msg);
}
</script>
@endsection