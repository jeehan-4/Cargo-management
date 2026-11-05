@extends('driver.layout')

@section('title', 'تفاصيل الشحنة')
@section('page-title', 'تفاصيل الشحنة')

@section('content')
<style>
/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   REFINED CLEAN FLAT LOGISTICS SaaS — SHIPMENT DETAILS
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
:root {
    --primary-navy: #202833;
    --secondary-navy: #415A66;
    --gold-accent: #C7A667;
    --bg-ivory: #F7F5EF;
    --surface-white: #FFFFFF;
    --border-clean: #E2E8F0;
    --text-muted: #64748B;

    /* Status Tokens */
    --pending-bg: #FDF6EC; --pending-txt: #946E2D;
    --assigned-bg: #EAF1F8; --assigned-txt: #3E638F;
    --transit-bg: #E8F2EF; --transit-txt: #2D5A54;
    --delivered-bg: #EAF3E9; --delivered-txt: #3B6641;
    --failed-bg: #FDF2F1; --failed-txt: #98514D;
}

body {
    background-color: var(--bg-ivory) !important;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
    color: var(--primary-navy);
}

.shipment-details-container {
    max-width: 900px;
    margin: 0 auto;
    padding-bottom: 40px;
}

.clean-detail-card {
    background: var(--surface-white);
    border: 1px solid var(--border-clean);
    border-radius: 18px;
    padding: 28px;
    box-shadow: none !important;
    margin-bottom: 24px;
}

.clean-detail-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 22px;
    border-bottom: 1.5px solid var(--border-clean);
}

.clean-detail-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
}
.clean-detail-title i {
    color: var(--gold-accent);
    font-size: 20px;
}

/* Custom Info Rows */
.clean-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--border-clean);
    font-size: 14.5px;
}
.clean-info-row:last-child {
    border-bottom: none;
}
.clean-info-label {
    color: var(--text-muted);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.clean-info-value {
    color: var(--primary-navy);
    font-weight: 800;
}

/* Status Badges */
.clean-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 50rem;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
}

.s-pending { background-color: var(--pending-bg); color: var(--pending-txt); }
.s-assigned { background-color: var(--assigned-bg); color: var(--assigned-txt); }
.s-transit { background-color: var(--transit-bg); color: var(--transit-txt); }
.s-delivered { background-color: var(--delivered-bg); color: var(--delivered-txt); }
.s-failed { background-color: var(--failed-bg); color: var(--failed-txt); }

/* Custom Flat Buttons */
.btn-clean-gold {
    background-color: var(--gold-accent);
    color: var(--primary-navy) !important;
    border: none;
    font-weight: 800;
    font-size: 14px;
    padding: 12px 24px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    text-decoration: none;
    box-shadow: none !important;
}

.btn-clean-success {
    background-color: #EAF3E9;
    color: #3B6641;
    border: none;
    font-weight: 800;
    font-size: 14px;
    padding: 12px 24px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    width: 100%;
    box-shadow: none !important;
}

.btn-clean-danger {
    background-color: #FDF2F1;
    color: #98514D;
    border: none;
    font-weight: 800;
    font-size: 14px;
    padding: 12px 24px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    width: 100%;
    box-shadow: none !important;
}
</style>

<div class="shipment-details-container" dir="rtl">

    <div class="mb-3">
        <a href="{{ route('driver.shipments') }}" style="font-size: 14px; font-weight: 800; color: var(--secondary-navy); display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
            <i class="bi bi-chevron-right"></i> العودة لسجل الشحنات
        </a>
    </div>

    {{-- Main Shipment Card --}}
    <div class="clean-detail-card" style="border-top: 4px solid var(--secondary-navy) !important;">
        <div class="clean-detail-header">
            <div class="clean-detail-title">
                <i class="bi bi-box-seam-fill"></i> شحنة برقم: <span class="font-monospace text-dark">#{{ $shipment->shipment_number }}</span>
            </div>
            @php
                $badgeClass = match($shipment->status) {
                    'pending' => 's-pending',
                    'assigned' => 's-assigned',
                    'in_transit' => 's-transit',
                    'delivered' => 's-delivered',
                    'failed' => 's-failed',
                    default => 's-pending'
                };
                $badgeIcon = match($shipment->status) {
                    'pending' => 'bi-clock',
                    'assigned' => 'bi-person-check',
                    'in_transit' => 'bi-truck',
                    'delivered' => 'bi-check-circle',
                    'failed' => 'bi-x-circle',
                    default => 'bi-clock'
                };
            @endphp
            <span class="clean-badge {{ $badgeClass }}">
                <i class="bi {{ $badgeIcon }}"></i>
                {{ \App\Models\Shipment::statusLabel($shipment->status) }}
            </span>
        </div>

        <div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-archive text-warning"></i> نوع البضاعة</span>
                <span class="clean-info-value">{{ $shipment->cargo_type }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-sliders text-warning"></i> الكمية والوزن</span>
                <span class="clean-info-value font-monospace">
                    {{ $shipment->quantity ?? '-' }} {{ $shipment->unit }}
                    @if($shipment->weight)
                        ({{ $shipment->weight }} طن)
                    @endif
                </span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-building text-warning"></i> المصدر (ميناء المنشأ)</span>
                <span class="clean-info-value">{{ $shipment->origin_port }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-geo-alt-fill text-danger"></i> الوجهة النهائية</span>
                <span class="clean-info-value text-primary">{{ $shipment->destination->name ?? '-' }} — {{ $shipment->destination->city ?? '' }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-calendar3 text-warning"></i> تاريخ الإسناد</span>
                <span class="clean-info-value font-monospace">{{ $shipment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</span>
            </div>

            @if($shipment->started_at)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-truck text-warning"></i> بدء الرحلة والنقل</span>
                <span class="clean-info-value font-monospace">{{ $shipment->started_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            @if($shipment->delivered_at)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-check-circle text-warning"></i> تاريخ التسليم</span>
                <span class="clean-info-value font-monospace">{{ $shipment->delivered_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            @if($shipment->notes)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-chat-text text-warning"></i> ملاحظات إضافية</span>
                <span class="clean-info-value text-muted">{{ $shipment->notes }}</span>
            </div>
            @endif

            {{-- Status Update Form Actions --}}
            @if(in_array($shipment->status, ['assigned', 'in_transit']))
            <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-3">
                @if($shipment->status === 'assigned')
                    <form action="{{ route('driver.shipments.status', $shipment) }}" method="POST" class="w-100">
                        @csrf
                        <input type="hidden" name="status" value="in_transit">
                        <button type="submit" class="btn-clean-gold w-100">
                            <i class="bi bi-truck fs-5"></i> بدء الرحلة / الانتقال لقيد النقل
                        </button>
                    </form>
                @endif
                @if($shipment->status === 'in_transit')
                    <div class="row g-3 w-100 m-0">
                        <div class="col-md-6 p-0 pe-md-2">
                            <form action="{{ route('driver.shipments.status', $shipment) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn-clean-success">
                                    <i class="bi bi-check-circle-fill fs-5"></i> تم التسليم بنجاح
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 p-0 ps-md-2 mt-2 mt-md-0">
                            <form action="{{ route('driver.shipments.status', $shipment) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="failed">
                                <button type="submit" class="btn-clean-danger">
                                    <i class="bi bi-x-circle-fill fs-5"></i> متعذر التسليم
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ SHIPMENT LOGS ═══════════════════ --}}
    @if($shipment->logs->count() > 0)
    <div class="clean-detail-card">
        <div class="clean-detail-header">
            <div class="clean-detail-title">
                <i class="bi bi-clock-history"></i> سجل التحديثات وحركات الشحنة
            </div>
        </div>
        <div>
            @foreach($shipment->logs as $log)
                @php
                    $logBadgeClass = match($log->new_status) {
                        'pending' => 's-pending',
                        'assigned' => 's-assigned',
                        'in_transit' => 's-transit',
                        'delivered' => 's-delivered',
                        'failed' => 's-failed',
                        default => 's-pending'
                    };
                    $logBadgeIcon = match($log->new_status) {
                        'pending' => 'bi-clock',
                        'assigned' => 'bi-person-check',
                        'in_transit' => 'bi-truck',
                        'delivered' => 'bi-check-circle',
                        'failed' => 'bi-x-circle',
                        default => 'bi-clock'
                    };
                @endphp
                <div class="clean-info-row">
                    <span class="clean-info-label font-monospace" style="min-width:140px;">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                    <span class="clean-info-value d-flex align-items-center gap-2">
                        <span class="clean-badge {{ $logBadgeClass }}">
                            <i class="bi {{ $logBadgeIcon }}"></i>
                            {{ $log->new_status_label }}
                        </span>
                        @if($log->notes)
                            <span class="text-muted fs-8">{{ $log->notes }}</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection