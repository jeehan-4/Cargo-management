@extends('driver.layout')

@section('title', 'سجل الشحنات')
@section('page-title', 'سجل الشحنات')

@section('content')
<div class="shipments-container">
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-box-seam"></i> جميع شحناتي المسجلة
                <span class="pill-count">{{ $shipments->total() }} شحنة</span>
            </div>
        </div>

        <div class="p-0">
            @if($shipments->count() > 0)
                <div class="table-responsive">
                    <table class="table tbl">
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
                                    <a href="{{ route('driver.shipments.show', $shipment) }}" class="ship-num font-monospace">
                                        #{{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td class="fw-700">{{ $shipment->cargo_type }}</td>
                                <td class="font-monospace text-muted-sm">{{ $shipment->quantity ?? '-' }} {{ $shipment->unit }}</td>
                                <td>{{ $shipment->origin_port }}</td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt text-accent"></i>
                                        <span>{{ $shipment->destination->name ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="font-monospace text-muted-sm">{{ $shipment->assigned_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <span class="sbadge sbadge-{{ $shipment->status }}">
                                        <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                                        {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('driver.shipments.show', $shipment) }}" class="btn btn-outline-navy btn-sm">
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
