@extends('layouts.app')
@section('title', $destination->name)
@section('page-title', $destination->name)
@section('title-icon', 'bi-geo-alt')

@section('topbar-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('destinations.edit', $destination) }}" class="btn-light-action">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('destinations.index') }}" class="btn-light-action">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>
@endsection

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

.destination-show-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* Minimalist Rounded Cards */
.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 26px 30px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
}

.saas-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border-light);
}

.saas-card-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.saas-card-title i {
    color: var(--gold-accent);
    font-size: 20px;
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
.saas-badge.active { color: #47734D; background-color: #EAF3E9; border-color: #C8E2C7; }
.saas-badge.inactive { color: #98514D; background-color: #F8EAE8; border-color: #ECC6C4; }

.saas-badge-pending { color: var(--status-pending-text); background-color: var(--status-pending-bg); border-color: var(--status-pending-border); }
.saas-badge-assigned { color: var(--status-assigned-text); background-color: var(--status-assigned-bg); border-color: var(--status-assigned-border); }
.saas-badge-transit { color: var(--status-transit-text); background-color: var(--status-transit-bg); border-color: var(--status-transit-border); }
.saas-badge-delivered { color: var(--status-delivered-text); background-color: var(--status-delivered-bg); border-color: var(--status-delivered-border); }
.saas-badge-cancelled { color: var(--status-cancelled-text); background-color: var(--status-cancelled-bg); border-color: var(--status-cancelled-border); }

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

/* Topbar Buttons */
.btn-light-action {
    background: #ffffff;
    color: var(--secondary-navy) !important;
    border: 1px solid var(--border-light);
    font-weight: 700;
    font-size: 13px;
    padding: 8px 16px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-light-action:hover {
    background: var(--bg-ivory);
    border-color: var(--primary-navy);
    color: var(--primary-navy) !important;
}
</style>

<div class="destination-show-container" dir="rtl">
    <div class="row g-4">
        
        {{-- 1. تفاصيل الوجهة (الجانب الأيمن) --}}
        <div class="col-lg-4">
            <div class="saas-card mb-0">
                <div class="saas-card-header">
                    <h5 class="saas-card-title">
                        <i class="bi bi-geo-alt-fill"></i> بيانات الوجهة
                    </h5>
                    <span class="saas-badge {{ $destination->is_active ? 'active' : 'inactive' }}">
                        <i class="bi {{ $destination->is_active ? 'bi-check-circle' : 'bi-dash-circle' }}"></i>
                        {{ $destination->is_active ? 'نشطة' : 'موقوفة' }}
                    </span>
                </div>

                <div class="text-center py-3 mb-3 border-bottom">
                    <div style="width: 64px; height: 64px; background: rgba(199, 166, 103, 0.12); border: 1.5px solid var(--gold-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: auto; font-size: 1.5rem; color: var(--gold-accent);">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="mt-3 mb-1 fw-bold text-dark">{{ $destination->name }}</h5>
                    <p class="text-muted fs-8 mb-0"><i class="bi bi-building me-1"></i>{{ $destination->city }}</p>
                </div>

                <div class="d-flex flex-column gap-3 fs-8">
                    @if($destination->address)
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-map me-1"></i>العنوان التفصيلي</div>
                        <div class="fw-bold text-dark">{{ $destination->address }}</div>
                    </div>
                    @endif

                    @if($destination->contact_name)
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-person me-1"></i>جهة الاتصال</div>
                        <div class="fw-bold text-dark">{{ $destination->contact_name }}</div>
                    </div>
                    @endif

                    @if($destination->contact_phone)
                    <div>
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-telephone me-1"></i>هاتف الاتصال</div>
                        <div class="fw-bold font-monospace text-dark" dir="ltr">{{ $destination->contact_phone }}</div>
                    </div>
                    @endif

                    @if($destination->notes)
                    <div>
                        <div class="text-muted fw-semibold mb-1">ملاحظات</div>
                        <div class="p-3 rounded-12 bg-light text-secondary border">{{ $destination->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. شحنات الوجهة (الجانب الأيسر) --}}
        <div class="col-lg-8">
            <div class="saas-card mb-0">
                <div class="saas-card-header">
                    <h5 class="saas-card-title">
                        <i class="bi bi-box-seam"></i> الشحنات المرتبطة <span class="fs-8 text-muted fw-normal">({{ $destination->shipments->count() }})</span>
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="saas-table">
                        <thead>
                            <tr>
                                <th>رقم الشحنة</th>
                                <th>نوع البضاعة</th>
                                <th>السائق</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($destination->shipments as $shipment)
                            <tr>
                                <td>
                                    <a href="{{ route('shipments.show', $shipment) }}" class="fw-bold font-monospace text-decoration-none" style="color: var(--secondary-navy);">
                                        #{{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td class="fw-semibold">{{ $shipment->cargo_type }}</td>
                                <td>
                                    @if($shipment->driver)
                                        <span class="text-dark fw-semibold">{{ $shipment->driver->name }}</span>
                                    @else
                                        <span class="text-muted fs-8">غير معيَّن</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($shipment->status) {
                                            'in_transit' => 'saas-badge-transit',
                                            'delivered' => 'saas-badge-delivered',
                                            'assigned' => 'saas-badge-assigned',
                                            'cancelled', 'failed' => 'saas-badge-cancelled',
                                            default => 'saas-badge-pending'
                                        };
                                        $badgeIcon = match($shipment->status) {
                                            'in_transit' => 'bi-truck',
                                            'delivered' => 'bi-check-circle',
                                            'assigned' => 'bi-user-check',
                                            'cancelled', 'failed' => 'bi-x-circle',
                                            default => 'bi-clock'
                                        };
                                    @endphp
                                    <span class="saas-badge {{ $badgeClass }}">
                                        <i class="bi {{ $badgeIcon }}"></i> {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-8 font-monospace">{{ $shipment->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-box-seam display-5 opacity-25 d-block mb-2"></i>
                                        <h6 class="fw-bold text-secondary">لا توجد شحنات مسجلة لهذه الوجهة بعد</h6>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection