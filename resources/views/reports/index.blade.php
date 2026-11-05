@extends('layouts.app')
@section('title', 'تقارير الشحنات')
@section('page-title', 'تقارير الشحنات والعمليات')
@section('title-icon', 'bi-bar-chart-line')

@section('content')
<style>
/* ─── Premium Modern SaaS Dashboard Theme & Reports Styles ─── */
:root {
    --primary-navy: #202833;
    --secondary-navy: #415A66;
    --gold-accent: #C7A667;
    --bg-ivory: #F7F5EF;
    --surface-white: #FFFFFF;
    --border-light: rgba(65, 90, 102, 0.12);
    --text-muted: #6c757d;
}

body {
    background-color: var(--bg-ivory) !important;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}

.reports-container {
    max-width: 1280px;
    margin: 0 auto;
}

.rpt-top-bar {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.btn-export-pdf {
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(220,53,69,0.25);
}
.btn-export-pdf:hover {
    background: #bb2d3b;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(220,53,69,0.35);
}

.btn-print-report {
    background: var(--primary-navy);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(32,40,51,0.20);
}
.btn-print-report:hover {
    background: var(--secondary-navy);
    color: #fff;
    transform: translateY(-1px);
}

.rpt-period-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
}
.rpt-period-btn {
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid var(--border-light);
    background: var(--surface-white);
    color: var(--secondary-navy);
    transition: all 0.2s ease;
}
.rpt-period-btn:hover {
    border-color: var(--gold-accent);
    color: var(--gold-accent);
    background: var(--bg-ivory);
}
.rpt-period-btn.active {
    background: var(--primary-navy);
    border-color: var(--primary-navy);
    color: #fff;
    box-shadow: 0 3px 10px rgba(32, 40, 51, 0.15);
}

.rpt-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 991px) {
    .rpt-stat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
    .rpt-stat-grid { grid-template-columns: 1fr; }
}

.rpt-stat-box {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    transition: all 0.2s ease;
}
.rpt-stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(32, 40, 51, 0.06);
}
.rpt-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.rpt-stat-icon.registered  { background: rgba(32,40,51,0.08);  color: var(--primary-navy); }
.rpt-stat-icon.delivered   { background: rgba(71,115,77,0.12); color: #47734D; }
.rpt-stat-icon.in-progress { background: rgba(199,166,103,0.15); color: #B38F4D; }
.rpt-stat-icon.failed      { background: rgba(152,81,77,0.12); color: #98514D; }

.rpt-stat-val {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--primary-navy);
}
.rpt-stat-lbl {
    font-size: 13px;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 6px;
}

.rpt-filter-box {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
}

.form-control-saas, .form-select-saas {
    background-color: var(--bg-ivory);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 13.5px;
    color: var(--primary-navy);
    transition: all 0.2s ease;
    width: 100%;
}
.form-control-saas:focus, .form-select-saas:focus {
    background-color: #ffffff;
    border-color: var(--gold-accent);
    box-shadow: 0 0 0 3px rgba(199, 166, 103, 0.15);
    outline: none;
}

.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
}

.saas-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border-light);
}

.saas-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.saas-table th {
    background: var(--bg-ivory);
    color: var(--text-muted);
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 14px 16px;
    border-bottom: 1.5px solid var(--border-light);
    letter-spacing: 0.3px;
}
.saas-table td {
    padding: 15px 16px;
    border-bottom: 1px solid rgba(65, 90, 102, 0.05);
    font-size: 13.5px;
    vertical-align: middle;
    color: var(--primary-navy);
}
.saas-table tbody tr {
    transition: background-color 0.15s ease;
}
.saas-table tbody tr:hover {
    background-color: rgba(199, 166, 103, 0.03);
}

/* Status Badges */
.saas-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    border: 1px solid transparent;
    white-space: nowrap;
}
.saas-badge-pending   { color: #8B682D; background-color: #F8F0DF; border-color: #E8D5B5; }
.saas-badge-assigned  { color: #3E638F; background-color: #EAF1F8; border-color: #C8D7EC; }
.saas-badge-in_transit{ color: #3F6A65; background-color: #E8F2EF; border-color: #C4E0DC; }
.saas-badge-delivered { color: #47734D; background-color: #EAF3E9; border-color: #C8E2C7; }
.saas-badge-failed, .saas-badge-cancelled { color: #98514D; background-color: #F8EAE8; border-color: #ECC6C4; }

/* ─── PRINT SPECIFIC STYLES ─── */
@media print {
    .sidebar, 
    .topbar, 
    .btn-group-actions,
    .rpt-period-tabs, 
    .rpt-filter-box, 
    .table-actions-btns,
    .pagination, 
    .card-footer, 
    .btn,
    .td-actions,
    .th-actions {
        display: none !important;
    }
    
    body {
        background: #fff !important;
        color: #000 !important;
        font-size: 10pt !important;
    }

    .reports-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .rpt-top-bar {
        border: none !important;
        border-bottom: 2px solid var(--gold-accent) !important;
        border-radius: 0 !important;
        padding: 10px 0 !important;
        margin-bottom: 15px !important;
        box-shadow: none !important;
    }

    .saas-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 10px !important;
    }

    .rpt-stat-grid {
        display: flex !important;
        flex-direction: row !important;
        gap: 10px !important;
        margin-bottom: 15px !important;
    }

    .rpt-stat-box {
        flex: 1 !important;
        border: 1px solid #ddd !important;
        padding: 10px !important;
        box-shadow: none !important;
        border-radius: 8px !important;
    }

    .saas-table {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    .saas-table th, .saas-table td {
        border: 1px solid #ddd !important;
        padding: 6px 8px !important;
    }
}
</style>

<div class="reports-container" dir="rtl">

    {{-- ── 1. Top Action Banner ── --}}
    <div class="rpt-top-bar">
        <div>
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-bar-graph text-primary" style="color: var(--gold-accent) !important;"></i> تقرير الشحنات والعمليات
            </h5>
            <div class="text-muted fs-8">
                الفترة الحالية: <strong class="text-dark">{{ $report['summary']['period_label'] }}</strong>
                <span class="mx-2">•</span>
                إجمالي السجلات: <strong class="text-dark">{{ $shipments->total() }}</strong>
            </div>
        </div>
        
        {{-- أزرار التصدير والطباعة --}}
        <div class="btn-group-actions d-flex align-items-center gap-2">
            <a href="{{ route('reports.export', request()->query()) }}" class="btn-export-pdf" title="تحميل التقرير كملف PDF">
                <i class="bi bi-file-earmark-pdf fs-6"></i>
                <span>تصدير PDF</span>
            </a>
            <button type="button" onclick="window.print()" class="btn-print-report" title="طباعة مباشرة أو حفظ كـ PDF من المتصفح">
                <i class="bi bi-printer fs-6"></i>
                <span>طباعة التقرير</span>
            </button>
        </div>
    </div>

    {{-- ── 2. Period Selector Tabs ── --}}
    <div class="rpt-period-tabs">
        <a href="{{ route('reports.index', array_merge(request()->except(['period', 'from', 'to']), ['period' => 'all'])) }}"
           class="rpt-period-btn {{ $periodType === 'all' ? 'active' : '' }}">
            <i class="bi bi-calendar-range me-1"></i> جميع الفترات
        </a>
        <a href="{{ route('reports.index', array_merge(request()->except(['period', 'from', 'to']), ['period' => 'today'])) }}"
           class="rpt-period-btn {{ $periodType === 'today' ? 'active' : '' }}">
            <i class="bi bi-calendar-day me-1"></i> اليوم
        </a>
        <a href="{{ route('reports.index', array_merge(request()->except(['period', 'from', 'to']), ['period' => 'week'])) }}"
           class="rpt-period-btn {{ $periodType === 'week' ? 'active' : '' }}">
            <i class="bi bi-calendar-week me-1"></i> هذا الأسبوع
        </a>
        <a href="{{ route('reports.index', array_merge(request()->except(['period', 'from', 'to']), ['period' => 'month'])) }}"
           class="rpt-period-btn {{ $periodType === 'month' ? 'active' : '' }}">
            <i class="bi bi-calendar-month me-1"></i> هذا الشهر
        </a>
    </div>

    {{-- ── 3. Summary Statistics Cards ── --}}
    <div class="rpt-stat-grid">
        <div class="rpt-stat-box">
            <div class="rpt-stat-icon registered">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="rpt-stat-val">{{ $report['summary']['registered'] ?? 0 }}</div>
                <div class="rpt-stat-lbl">إجمالي الشحنات المسجلة</div>
            </div>
        </div>
        <div class="rpt-stat-box">
            <div class="rpt-stat-icon delivered">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="rpt-stat-val">{{ $report['summary']['delivered'] ?? 0 }}</div>
                <div class="rpt-stat-lbl">شحنات تم تسليمها</div>
            </div>
        </div>
        <div class="rpt-stat-box">
            <div class="rpt-stat-icon in-progress">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="rpt-stat-val">{{ $report['summary']['in_progress'] ?? 0 }}</div>
                <div class="rpt-stat-lbl">شحنات قيد النقل / التنفيذ</div>
            </div>
        </div>
        <div class="rpt-stat-box">
            <div class="rpt-stat-icon failed">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <div class="rpt-stat-val">{{ $report['summary']['failed'] ?? 0 }}</div>
                <div class="rpt-stat-lbl">تعذر التسليم</div>
            </div>
        </div>
    </div>

    {{-- ── 4. Filter Bar ── --}}
    <div class="rpt-filter-box">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <input type="hidden" name="period" value="{{ $periodType }}">

            <div class="col-lg-3 col-md-6">
                <label class="form-label text-muted fs-8 fw-bold mb-1">بحث برقم الشحنة أو البضاعة</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control-saas"
                           placeholder="ابحث بالرقم أو نوع البضاعة..."
                           value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label text-muted fs-8 fw-bold mb-1">حالة الشحنة</label>
                <select name="status" class="form-select-saas">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>منتظرة التعيين</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>تم التكليف</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>قيد النقل</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>تعذر التسليم</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label text-muted fs-8 fw-bold mb-1">السائق</label>
                <select name="driver_id" class="form-select-saas">
                    <option value="">جميع السائقين</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label text-muted fs-8 fw-bold mb-1">الوجهة</label>
                <select name="destination_id" class="form-select-saas">
                    <option value="">جميع الوجهات</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>
                            {{ $dest->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($periodType === 'custom')
            <div class="col-lg-3 col-md-3 col-sm-6">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label text-muted fs-8 fw-bold mb-1">من تاريخ</label>
                        <input type="date" name="from" class="form-control-saas" value="{{ request('from', $from->format('Y-m-d')) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted fs-8 fw-bold mb-1">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control-saas" value="{{ request('to', $to->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            @endif

            <div class="col-lg-3 col-md-3 d-flex gap-2">
                <button type="submit" class="btn text-white fw-bold px-3 flex-grow-1" style="background: var(--primary-navy); border-radius: 12px; font-size: 13.5px;">
                    <i class="bi bi-funnel me-1"></i> تطبيق التصفية
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary px-3 d-flex align-items-center justify-content-center" style="border-radius: 12px;" title="إعادة تعيين">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- ── 5. Main Data Table ── --}}
    <div class="saas-card mb-4">
        <div class="saas-table-header">
            <h6 class="mb-0 fw-bold" style="color: var(--primary-navy);">
                <i class="bi bi-list-check me-1 text-primary"></i> الشحنات الفعلية في التقرير
                <span class="badge bg-light text-primary border rounded-pill ms-2">{{ $shipments->total() }}</span>
            </h6>
            
            <div class="table-actions-btns d-flex align-items-center gap-2">
                <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1 shadow-sm rounded-12 px-3">
                    <i class="bi bi-file-earmark-pdf"></i> تصدير PDF
                </a>
                <button onclick="window.print()" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1 shadow-sm rounded-12 px-3">
                    <i class="bi bi-printer"></i> طباعة التقرير
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>نوع البضاعة</th>
                        <th>الكمية / الوزن</th>
                        <th>المصدر</th>
                        <th>وجهة التوصيل</th>
                        <th>اسم السائق</th>
                        <th class="text-center">حالة الشحنة</th>
                        <th class="text-center">تاريخ التسجيل</th>
                        <th class="text-center th-actions">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>
                            <a href="{{ route('shipments.show', $s) }}" class="fw-bold font-monospace text-decoration-none" style="color: var(--primary-navy);">
                                #{{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="fw-semibold">{{ $s->cargo_type }}</td>
                        <td class="text-muted font-monospace">
                            @if($s->quantity)
                                {{ $s->quantity }} {{ $s->unit }}
                            @endif
                            @if($s->weight)
                                <small class="d-block text-secondary">({{ $s->weight }} طن)</small>
                            @endif
                            @if(!$s->quantity && !$s->weight)
                                —
                            @endif
                        </td>
                        <td>{{ $s->origin_port ?? '—' }}</td>
                        <td>
                            @if($s->destination)
                                <span class="fw-semibold">{{ $s->destination->name }}</span>
                                <small class="text-muted d-block fs-9">{{ $s->destination->city }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($s->driver)
                                <span class="d-flex align-items-center gap-1.5 fw-semibold text-dark">
                                    <i class="bi bi-person-circle text-muted"></i> {{ $s->driver->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 fs-9">غير معين</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($s->status) {
                                    'in_transit' => 'saas-badge-in_transit',
                                    'delivered' => 'saas-badge-delivered',
                                    'assigned' => 'saas-badge-assigned',
                                    'cancelled', 'failed' => 'saas-badge-failed',
                                    default => 'saas-badge-pending'
                                };
                                $badgeIcon = match($s->status) {
                                    'in_transit' => 'bi-truck',
                                    'delivered' => 'bi-check-circle',
                                    'assigned' => 'bi-user-check',
                                    'cancelled', 'failed' => 'bi-x-circle',
                                    default => 'bi-clock'
                                };
                            @endphp
                            <span class="saas-badge {{ $badgeClass }}">
                                <i class="bi {{ $badgeIcon }}"></i> {{ \App\Models\Shipment::statusLabel($s->status) }}
                            </span>
                        </td>
                        <td class="text-center text-muted fs-8 font-monospace">
                            {{ $s->created_at ? $s->created_at->format('Y-m-d') : '—' }}
                        </td>
                        <td class="text-center td-actions">
                            <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-light border p-1.5 px-2.5 rounded-10 text-secondary" title="عرض تفاصيل الشحنة">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open display-5 opacity-25 d-block mb-2"></i>
                                <h6 class="fw-bold text-secondary">لا توجد شحنات مسجلة مطابقة لهذه الفترة أو الفلاتر</h6>
                                <p class="fs-8 text-muted mb-0">يمكنك تعديل الفترة الزمنية أو إزالة الفلاتر لعرض الشحنات.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- تم ربط الـ Pagination بـ Bootstrap 5 بنجاح --}}
        @if($shipments->hasPages())
        <div class="pt-4 border-top d-flex justify-content-center">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

    {{-- ── 6. Driver Performance Summary (If data exists) ── --}}
    @if(isset($report['driverSummary']) && $report['driverSummary']->count() > 0)
    <div class="saas-card mb-4">
        <div class="saas-table-header">
            <h6 class="mb-0 fw-bold" style="color: var(--primary-navy);"><i class="bi bi-people-fill me-1 text-primary"></i> ملخص أداء السائقين خلال الفترة</h6>
        </div>
        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>السائق</th>
                        <th class="text-center">إجمالي الشحنات</th>
                        <th class="text-center">تم التسليم</th>
                        <th class="text-center">قيد التنفيذ</th>
                        <th class="text-center">تعذر التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['driverSummary'] as $dRow)
                    <tr>
                        <td class="fw-bold">{{ $dRow['driver']->name ?? '—' }}</td>
                        <td class="text-center fw-bold font-monospace">{{ $dRow['total'] }}</td>
                        <td class="text-center text-success fw-bold font-monospace">{{ $dRow['delivered'] }}</td>
                        <td class="text-center text-warning fw-bold font-monospace">{{ $dRow['active'] }}</td>
                        <td class="text-center text-danger fw-bold font-monospace">{{ $dRow['failed'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection