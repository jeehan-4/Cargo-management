@extends('layouts.app')
@section('title', 'قائمة الشحنات')
@section('page-title', 'إدارة الشحنات')
@section('title-icon', 'bi-box-seam')

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

    /* Status Tokens */
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

.shipments-index-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* Minimalist Card Containers */
.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
}

/* Filters Form Elements */
.filter-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--secondary-navy);
    margin-bottom: 6px;
    display: block;
}

.form-control-saas, .form-select-saas {
    background-color: var(--bg-ivory);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 9px 14px;
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

/* Table Header & Actions */
.saas-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border-light);
}
.saas-table-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.saas-table-title i {
    color: var(--gold-accent);
    font-size: 20px;
}

/* Clean Data Table */
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

/* Status Badges System */
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
.saas-badge i { font-size: 13px; }

.saas-badge-pending { color: var(--status-pending-text); background-color: var(--status-pending-bg); border-color: var(--status-pending-border); }
.saas-badge-assigned { color: var(--status-assigned-text); background-color: var(--status-assigned-bg); border-color: var(--status-assigned-border); }
.saas-badge-transit { color: var(--status-transit-text); background-color: var(--status-transit-bg); border-color: var(--status-transit-border); }
.saas-badge-delivered { color: var(--status-delivered-text); background-color: var(--status-delivered-bg); border-color: var(--status-delivered-border); }
.saas-badge-cancelled { color: var(--status-cancelled-text); background-color: var(--status-cancelled-bg); border-color: var(--status-cancelled-border); }

/* Buttons Style */
.btn-gold-action {
    background: linear-gradient(135deg, var(--gold-accent) 0%, #b38f4d 100%);
    color: #ffffff !important;
    border: none;
    font-weight: 700;
    font-size: 13px;
    padding: 8px 18px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(199, 166, 103, 0.25);
    text-decoration: none;
}
.btn-gold-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(199, 166, 103, 0.35);
}

.btn-filter-action {
    background: var(--primary-navy);
    color: #ffffff !important;
    border: none;
    font-weight: 700;
    font-size: 13px;
    padding: 9px 16px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    width: 100%;
}
.btn-filter-action:hover {
    background: var(--secondary-navy);
}

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: 1px solid var(--border-light);
    background: #ffffff;
    color: var(--secondary-navy);
}
.btn-action-icon:hover {
    background: var(--bg-ivory);
    border-color: var(--primary-navy);
    color: var(--primary-navy);
}
.btn-action-icon.danger:hover {
    background: #F8EAE8;
    border-color: #ECC6C4;
    color: #98514D;
}
</style>

<div class="shipments-index-container" dir="rtl">

    {{-- FILTERS CARD --}}
    <div class="saas-card">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="filter-label">الحالة</label>
                <select name="status" class="form-select-saas">
                    <option value="">كل الحالات</option>
                    @foreach(['pending'=>'بانتظار التوزيع','assigned'=>'قيد التوصيل','delivered'=>'تم التسليم','cancelled'=>'ملغاة','archived'=>'مؤرشفة','failed'=>'متعذر التسليم'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="filter-label">السائق</label>
                <select name="driver_id" class="form-select-saas">
                    <option value="">كل السائقين</option>
                    @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ request('driver_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="filter-label">من تاريخ</label>
                <input type="date" name="from" class="form-control-saas" value="{{ request('from') }}">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="filter-label">إلى تاريخ</label>
                <input type="date" name="to" class="form-control-saas" value="{{ request('to') }}">
            </div>
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn-filter-action">
                    <i class="bi bi-funnel"></i> تصفية
                </button>
                <a href="{{ route('shipments.index') }}" class="btn btn-light border rounded-12 px-3 d-flex align-items-center justify-content-center text-muted" title="إعادة تعيين">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="saas-card">
        <div class="saas-table-header">
            <h6 class="saas-table-title">
                <i class="bi bi-list-ul"></i> الشحنات <span class="fs-8 text-muted fw-normal">({{ $shipments->total() }})</span>
            </h6>
            <div class="d-flex gap-2">
                @if(isset($archivedCount) && $archivedCount > 0)
                <a href="{{ route('shipments.index', ['status' => 'archived']) }}" class="btn btn-light border text-info fw-bold fs-8 rounded-12 px-3 py-2 d-inline-flex align-items-center gap-1.5" title="عرض الشحنات المؤرشفة">
                    <i class="bi bi-archive-fill"></i>
                    <span>الأرشيف ({{ $archivedCount }})</span>
                </a>
                @endif
                <a href="{{ route('shipments.export') }}" class="btn btn-light border text-success fw-bold fs-8 rounded-12 px-3 py-2 d-inline-flex align-items-center gap-1">
                    <i class="bi bi-file-earmark-excel"></i> تصدير إكسل
                </a>
                <a href="{{ route('shipments.create') }}" class="btn-gold-action">
                    <i class="bi bi-plus-lg"></i> شحنة جديدة
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>نوع البضاعة</th>
                        <th>الكمية</th>
                        <th>الوجهة</th>
                        <th>السائق</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>
                            <a href="{{ route('shipments.show',$s) }}" class="fw-bold font-monospace text-decoration-none" style="color: var(--secondary-navy);">
                                #{{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="fw-semibold">{{ $s->cargo_type }}</td>
                        <td class="text-muted font-monospace">
                            {{ $s->quantity ? $s->quantity.' '.$s->unit : ($s->weight ? $s->weight.' طن' : '—') }}
                        </td>
                        <td>
                            @if($s->destination)
                                <i class="bi bi-geo-alt text-muted me-1"></i>{{ $s->destination->name }}
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
                                <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 fs-9">غير معيَّن</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($s->status) {
                                    'in_transit' => 'saas-badge-transit',
                                    'delivered' => 'saas-badge-delivered',
                                    'assigned' => 'saas-badge-assigned',
                                    'cancelled', 'failed' => 'saas-badge-cancelled',
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
                        <td class="text-muted fs-8 font-monospace">{{ $s->created_at->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1.5 justify-content-center">
                                <a href="{{ route('shipments.show',$s) }}" class="btn-action-icon" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('shipments.edit',$s) }}" class="btn-action-icon" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($s->status === 'pending' && !$s->driver_id)
                                <form method="POST" action="{{ route('shipments.destroy', $s) }}" onsubmit="return confirm('تنبيه حذف نهائي:\nهذه الشحنة مضافة بالخطأ ولم تسند لأي سائق.\nهل أنت متأكد من حذفها نهائياً من النظام؟')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-action-icon danger" title="حذف الشحنة المضافة بالخطأ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @elseif(in_array($s->status, ['delivered', 'failed', 'cancelled']))
                                <form method="POST" action="{{ route('shipments.archive', $s) }}" onsubmit="return confirm('هل تريد نقل هذه الشحنة المكتملة/الملغاة إلى الأرشيف لحفظ السجلات؟')" class="d-inline">
                                    @csrf
                                    <button class="btn-action-icon text-info" title="أرشفة الشحنة (نقل إلى الأرشيف)">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-5 opacity-25 d-block mb-2"></i>
                                <h6 class="fw-bold text-secondary">لا توجد شحنات تطابق نتائج البحث</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="pt-4 border-top d-flex justify-content-center">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection