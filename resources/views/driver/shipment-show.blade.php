@extends('driver.layout')

@section('title', 'تفاصيل الشحنة')
@section('page-title', 'تفاصيل الشحنة')

@section('content')
<style>
.shipment-details-container {
    max-width: 900px;
    margin: 0 auto;
    padding-bottom: 40px;
}
.clean-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
    font-size: 14.5px;
}
.clean-info-row:last-child {
    border-bottom: none;
}
.clean-info-label {
    color: var(--muted);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.clean-info-value {
    color: var(--primary);
    font-weight: 800;
}
@media (max-width: 576px) {
    .clean-info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
}
.btn-clean-success {
    background-color: rgba(16,185,129,0.10);
    color: #065F46;
    border: 1px solid rgba(16,185,129,0.20);
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
    transition: all .2s ease;
}
.btn-clean-success:hover {
    background-color: rgba(16,185,129,0.20);
    color: #065F46;
}
.btn-clean-danger {
    background-color: rgba(239,68,68,0.10);
    color: #991B1B;
    border: 1px solid rgba(239,68,68,0.20);
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
    transition: all .2s ease;
}
.btn-clean-danger:hover {
    background-color: rgba(239,68,68,0.20);
    color: #991B1B;
}
</style>

<div class="shipment-details-container">

    <div class="mb-3">
        <a href="{{ route('driver.shipments') }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-chevron-right"></i> العودة لسجل الشحنات
        </a>
    </div>

    {{-- Main Shipment Card --}}
    <div class="section-card" style="border-top: 4px solid var(--secondary) !important;">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-box-seam-fill"></i> شحنة برقم:
                <span class="font-monospace text-navy">#{{ $shipment->shipment_number }}</span>
            </div>
            <span class="sbadge sbadge-{{ $shipment->status }}">
                <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                {{ \App\Models\Shipment::statusLabel($shipment->status) }}
            </span>
        </div>

        <div class="p-4">
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-archive text-accent"></i> نوع البضاعة</span>
                <span class="clean-info-value">{{ $shipment->cargo_type }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-sliders text-accent"></i> الكمية والوزن</span>
                <span class="clean-info-value font-monospace">
                    {{ $shipment->quantity ?? '-' }} {{ $shipment->unit }}
                    @if($shipment->weight)
                        ({{ $shipment->weight }} طن)
                    @endif
                </span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-building text-accent"></i> المصدر (ميناء المنشأ)</span>
                <span class="clean-info-value">{{ $shipment->origin_port }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-geo-alt-fill text-accent"></i> الوجهة النهائية</span>
                <span class="clean-info-value">{{ $shipment->destination->name ?? '-' }} — {{ $shipment->destination->city ?? '' }}</span>
            </div>
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-calendar3 text-accent"></i> تاريخ الإسناد</span>
                <span class="clean-info-value font-monospace">{{ $shipment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</span>
            </div>

            @if($shipment->started_at)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-truck text-accent"></i> بدء الرحلة والنقل</span>
                <span class="clean-info-value font-monospace">{{ $shipment->started_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            @if($shipment->delivered_at)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-check-circle text-accent"></i> تاريخ التسليم</span>
                <span class="clean-info-value font-monospace">{{ $shipment->delivered_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            @if($shipment->notes)
            <div class="clean-info-row">
                <span class="clean-info-label"><i class="bi bi-chat-text text-accent"></i> ملاحظات إضافية</span>
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
                        <button type="submit" class="btn btn-gold w-100 py-2">
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

    {{-- SHIPMENT LOGS --}}
    @if($shipment->logs->count() > 0)
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-clock-history"></i> سجل التحديثات وحركات الشحنة
            </div>
        </div>
        <div class="p-4">
            @foreach($shipment->logs as $log)
                <div class="clean-info-row">
                    <span class="clean-info-label font-monospace" style="min-width:140px;">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                    <span class="clean-info-value d-flex align-items-center gap-2">
                        <span class="sbadge sbadge-{{ $log->new_status }}">
                            <i class="bi {{ \App\Models\Shipment::statusIcon($log->new_status) }}"></i>
                            {{ $log->new_status_label }}
                        </span>
                        @if($log->notes)
                            <span class="text-muted fs-sm">{{ $log->notes }}</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
