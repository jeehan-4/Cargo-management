@extends('driver.layout')

@section('title', 'سجل الشحنات')
@section('page-title', 'سجل الشحنات')

@section('content')
<style>
/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   DRIVER SHIPMENTS INDEX — SAAS REFINED DESIGN
   الخط المستخدم: Cairo, IBM Plex Sans Arabic
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.shipments-container {
    max-width: 1200px;
    margin: 0 auto;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}
.shipments-container * {
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}

.saas-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    box-shadow: var(--sh-sm);
    margin-bottom: 24px;
    overflow: hidden;
    transition: var(--tr);
}
.saas-card:hover {
    box-shadow: var(--sh-md);
}

.saas-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    background: var(--white);
}

.saas-card-title {
    font-size: .95rem;
    font-weight: 900;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.saas-card-title i {
    color: var(--accent);
    font-size: 1.2rem;
}

/* Custom Table Styles */
.table-clean {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.table-clean th {
    background: #F8F6F0;
    color: var(--muted);
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    text-align: right;
}
.table-clean td {
    padding: 15px 18px;
    vertical-align: middle;
    border-bottom: 1px solid rgba(65,90,102,0.06);
    font-size: .88rem;
    color: var(--primary);
    font-weight: 600;
    text-align: right;
}
.table-clean tbody tr {
    transition: background-color 0.15s ease;
}
.table-clean tbody tr:hover {
    background-color: rgba(199,166,103,0.04);
}
.table-clean tbody tr:last-child td {
    border-bottom: none;
}
</style>

<div class="shipments-container" dir="rtl">
    <div class="saas-card">
        <div class="saas-card-header">
            <div class="saas-card-title">
                <i class="bi bi-box-seam"></i> جميع شحناتي المسجلة
            </div>
            <span style="font-size:.8rem; font-weight:800; color:var(--muted); background: rgba(65,90,102,0.06); padding: 5px 12px; border-radius: 50rem;">
                إجمالي: {{ $shipments->total() }} شحنة
            </span>
        </div>

        <div class="p-0">
            @if($shipments->count() > 0)
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>رقم الشحنة</th>
                                <th>نوع البضاعة</th>
                                <th>الكمية</th>
                                <th>المصدر</th>
                                <th>الوجهة</th>
                                <th>تاريخ الإسناد</th>
                                <th>الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $shipment)
                            <tr>
                                <td>
                                    <span style="font-weight:900; color:var(--secondary);" class="font-monospace">
                                        #{{ $shipment->shipment_number }}
                                    </span>
                                </td>
                                <td style="font-weight:700;">{{ $shipment->cargo_type }}</td>
                                <td class="font-monospace text-muted">{{ $shipment->quantity ?? '-' }} {{ $shipment->unit }}</td>
                                <td>{{ $shipment->origin_port }}</td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1.5">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        <span>{{ $shipment->destination->name ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="font-monospace text-muted">{{ $shipment->assigned_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <span class="sbadge sbadge-{{ $shipment->status }}">
                                        <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                                        {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('driver.shipments.show', $shipment) }}" class="btn btn-outline-navy btn-sm" style="font-size:.78rem; font-weight:800; padding: 5px 12px; border-radius: var(--r-xs);">
                                        <i class="bi bi-eye"></i> تفاصيل
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($shipments->hasPages())
                    <div class="d-flex justify-content-center py-4 border-top">
                        {{ $shipments->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox es-icon"></i>
                    <div class="es-title">لا توجد شحنات مسجلة</div>
                    <div class="es-sub">عند إسناد شحنات جديدة لك من قبل الإدارة ستظهر هنا فوراً.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection