@extends('layouts.app')
@section('title', $destination->name)
@section('page-title', $destination->name)
@section('title-icon', 'bi-geo-alt')

@section('topbar-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('destinations.edit', $destination) }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-pencil me-1"></i> تعديل
        </a>
        <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>
@endsection

@section('content')
<div class="destination-show-container">
    <div class="row g-4">

        {{-- 1. تفاصيل الوجهة (الجانب الأيمن) --}}
        <div class="col-lg-4">
            <div class="section-card mb-0 h-100">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="bi bi-geo-alt-fill"></i> بيانات الوجهة
                    </div>
                    <span class="status-badge badge-{{ $destination->is_active ? 'active' : 'inactive' }}">
                        <i class="bi {{ $destination->is_active ? 'bi-check-circle' : 'bi-dash-circle' }}"></i>
                        {{ $destination->is_active ? 'نشطة' : 'موقوفة' }}
                    </span>
                </div>

                <div class="text-center py-3 mb-3 border-bottom">
                    <div style="width: 64px; height: 64px; background: rgba(199, 166, 103, 0.12); border: 1.5px solid var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: auto; font-size: 1.5rem; color: var(--accent);">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="mt-3 mb-1 fw-bold text-dark">{{ $destination->name }}</h5>
                    <p class="text-muted-sm mb-0"><i class="bi bi-building me-1"></i>{{ $destination->city }}</p>
                </div>

                <div class="d-flex flex-column gap-3 fs-sm">
                    @if($destination->address)
                    <div>
                        <div class="text-muted-sm fw-600 mb-1"><i class="bi bi-map me-1"></i>العنوان التفصيلي</div>
                        <div class="fw-bold text-dark">{{ $destination->address }}</div>
                    </div>
                    @endif

                    @if($destination->contact_name)
                    <div>
                        <div class="text-muted-sm fw-600 mb-1"><i class="bi bi-person me-1"></i>جهة الاتصال</div>
                        <div class="fw-bold text-dark">{{ $destination->contact_name }}</div>
                    </div>
                    @endif

                    @if($destination->contact_phone)
                    <div>
                        <div class="text-muted-sm fw-600 mb-1"><i class="bi bi-telephone me-1"></i>هاتف الاتصال</div>
                        <div class="fw-bold font-monospace text-dark" dir="ltr">{{ $destination->contact_phone }}</div>
                    </div>
                    @endif

                    @if($destination->notes)
                    <div>
                        <div class="text-muted-sm fw-600 mb-1">ملاحظات</div>
                        <div class="p-3 rounded-3 bg-light text-secondary border">{{ $destination->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. شحنات الوجهة (الجانب الأيسر) --}}
        <div class="col-lg-8">
            <div class="section-card mb-0 h-100">
                <div class="section-card-header">
                    <div class="section-card-title">
                        <i class="bi bi-box-seam"></i> الشحنات المرتبطة
                        <span class="pill-count">{{ $destination->shipments->count() }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table tbl mb-0">
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
                                    <a href="{{ route('shipments.show', $shipment) }}" class="ship-num" dir="ltr">
                                        #{{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td class="fw-600">{{ $shipment->cargo_type }}</td>
                                <td>
                                    @if($shipment->driver)
                                        <span class="text-dark fw-600">{{ $shipment->driver->name }}</span>
                                    @else
                                        <span class="pill-count">غير معيَّن</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="sbadge sbadge-{{ $shipment->status }}">
                                        <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                                        {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted-sm fs-xs font-monospace">{{ $shipment->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam es-icon"></i>
                                        <div class="es-title">لا توجد شحنات مسجلة لهذه الوجهة بعد</div>
                                        <div class="es-sub">ستظهر هنا الشحنات المرتبطة بهذه الوجهة تلقائياً</div>
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
