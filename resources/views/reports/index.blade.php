@extends('layouts.app')
@section('title', 'تقارير الشحنات')
@section('page-title', 'تقارير الشحنات والعمليات')

@section('topbar-actions')
    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> تصدير Excel
    </a>
    <button type="button" onclick="window.print()" class="btn btn-navy btn-sm">
        <i class="bi bi-printer me-1"></i> طباعة التقرير
    </button>
@endsection

@section('content')
<style>
/* ─── مكونات خاصة بصفحة التقارير (تعتمد على متغيرات التصميم الموحد) ─── */
.reports-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* أزرار الفترات الزمنية */
.rpt-period-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
}
.rpt-period-btn {
    padding: 9px 18px;
    border-radius: var(--r-sm);
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid var(--border-md);
    background: var(--white);
    color: var(--secondary);
    transition: var(--tr);
}
.rpt-period-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--bg);
}
.rpt-period-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
}

/* بطاقات الإحصائيات */
.rpt-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 18px;
}
@media (max-width: 991.98px) {
    .rpt-stat-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575.98px) {
    .rpt-stat-grid { grid-template-columns: 1fr; }
}
.rpt-stat-box {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: var(--sh-xs);
    transition: var(--tr);
}
.rpt-stat-box:hover {
    transform: translateY(-2px);
    box-shadow: var(--sh-sm);
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
.rpt-stat-icon.registered  { background: rgba(32,40,51,0.08);  color: var(--primary); }
.rpt-stat-icon.delivered   { background: rgba(16,185,129,0.10); color: #065F46; }
.rpt-stat-icon.in-progress { background: rgba(199,166,103,0.15); color: #B38F4D; }
.rpt-stat-icon.failed      { background: rgba(239,68,68,0.10);  color: #991B1B; }
.rpt-stat-val {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--primary);
}
.rpt-stat-lbl {
    font-size: 13px;
    color: var(--muted);
    font-weight: 600;
    margin-top: 6px;
}

/* أنماط الطباعة */
@media print {
    .sidebar, .topbar, .btn, .rpt-period-tabs, .filter-bar, .pagination,
    .th-actions, .td-actions {
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
    .section-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        border-radius: 0 !important;
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
    .tbl {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    .tbl th, .tbl td {
        border: 1px solid #ddd !important;
        padding: 6px 8px !important;
    }
}
</style>

<div class="reports-container" dir="rtl">

    {{-- 1. البطاقة العلوية --}}
    <div class="section-card mb-4">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-file-earmark-bar-graph"></i> تقرير الشحنات والعمليات
            </div>
            <div class="text-muted fs-8">
                الفترة الحالية: <strong class="text-navy">{{ $report['summary']['period_label'] }}</strong>
                <span class="mx-2">•</span>
                إجمالي السجلات: <strong class="text-navy">{{ $shipments->total() }}</strong>
            </div>
        </div>
        <div class="p-4 pb-0">
            {{-- 2. أزرار الفترات الزمنية --}}
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

            {{-- 3. بطاقات الإحصائيات --}}
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
        </div>
    </div>

    {{-- 4. شريط التصفية --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <input type="hidden" name="period" value="{{ $periodType }}">

            <div class="col-lg-2 col-md-6">
                <label class="form-label">بحث برقم الشحنة أو البضاعة</label>
                <input type="text" name="search" class="form-control"
                       placeholder="ابحث بالرقم أو نوع البضاعة..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label">حالة الشحنة</label>
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>منتظرة التعيين</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>تم التكليف</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>قيد النقل</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>تعذر التسليم</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label">السائق</label>
                <select name="driver_id" class="form-select">
                    <option value="">جميع السائقين</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <label class="form-label">الوجهة</label>
                <select name="destination_id" class="form-select">
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
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from', $from->format('Y-m-d')) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to', $to->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            @endif

            <div class="col-lg-4 col-md-9 form-actions">
                <button type="submit" class="btn btn-navy flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> تطبيق التصفية
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-navy" title="إعادة تعيين">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- 5. جدول البيانات الرئيسي --}}
    <div class="section-card mb-4">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-list-check"></i> الشحنات الفعلية في التقرير
                <span class="pill-count">{{ $shipments->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0">
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
                            <a href="{{ route('shipments.show', $s) }}" class="ship-num" dir="ltr">
                                #{{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="fw-semibold">{{ $s->cargo_type }}</td>
                        <td class="text-muted-sm">
                            @if($s->quantity)
                                {{ $s->quantity }} {{ $s->unit }}
                            @endif
                            @if($s->weight)
                                <small class="d-block text-muted">({{ $s->weight }} طن)</small>
                            @endif
                            @if(!$s->quantity && !$s->weight)
                                —
                            @endif
                        </td>
                        <td>{{ $s->origin_port ?? '—' }}</td>
                        <td>
                            @if($s->destination)
                                <span class="fw-semibold">{{ $s->destination->name }}</span>
                                <small class="text-muted d-block">{{ $s->destination->city }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($s->driver)
                                <span class="d-flex align-items-center gap-1 fw-semibold text-navy">
                                    <i class="bi bi-person-circle text-muted"></i> {{ $s->driver->name }}
                                </span>
                            @else
                                <span class="pill-count">غير معين</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="sbadge sbadge-{{ $s->status }}">
                                <i class="bi {{ \App\Models\Shipment::statusIcon($s->status) }}"></i>
                                {{ \App\Models\Shipment::statusLabel($s->status) }}
                            </span>
                        </td>
                        <td class="text-center text-muted-sm">
                            {{ $s->created_at ? $s->created_at->format('Y-m-d') : '—' }}
                        </td>
                        <td class="text-center td-actions">
                            <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-outline-navy" title="عرض تفاصيل الشحنة">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-folder2-open es-icon"></i>
                                <div class="es-title">لا توجد شحنات مسجلة مطابقة لهذه الفترة أو الفلاتر</div>
                                <div class="es-sub">يمكنك تعديل الفترة الزمنية أو إزالة الفلاتر لعرض الشحنات.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

    {{-- 6. ملخص أداء السائقين --}}
    @if(isset($report['driverSummary']) && $report['driverSummary']->count() > 0)
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-people-fill"></i> ملخص أداء السائقين خلال الفترة
            </div>
        </div>
        <div class="table-responsive">
            <table class="table tbl mb-0">
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
