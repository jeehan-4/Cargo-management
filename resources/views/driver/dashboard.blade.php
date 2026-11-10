@extends('driver.layout')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة تحكم السائق')
@section('title-icon', 'bi-speedometer2')

@section('content')
<style>
.driver-portal-wrapper {
    max-width: 1140px;
    margin: 0 auto;
    padding-bottom: 40px;
}
.hero-welcome-section {
    background-color: var(--secondary);
    border-radius: 16px;
    padding: 24px 30px;
    color: #FFFFFF;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    border-right: 5px solid var(--accent);
}
.hero-driver-info {
    display: flex;
    align-items: center;
    gap: 16px;
}
.hero-avatar {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: var(--primary);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border: 1px solid rgba(199, 166, 103, 0.4);
}
.stats-inline-strip {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .stats-inline-strip { grid-template-columns: 1fr; }
}
.stat-strip-item {
    background: #FAF8F5;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.destination-strip {
    background: rgba(65, 90, 102, 0.04);
    border: 1px solid rgba(199, 166, 103, 0.4);
    border-radius: 12px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}
.shipment-details-matrix {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 768px) {
    .shipment-details-matrix { grid-template-columns: 1fr; }
}
.matrix-item {
    background: #FAF8F5;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.matrix-item-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(199, 166, 103, 0.15);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
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
.btn-clean-success:hover { background-color: rgba(16,185,129,0.20); color: #065F46; }
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
.btn-clean-danger:hover { background-color: rgba(239,68,68,0.20); color: #991B1B; }
</style>

<div class="driver-portal-wrapper">

    {{-- 1. HERO WELCOME SECTION --}}
    <div class="hero-welcome-section">
        <div class="hero-driver-info">
            <div class="hero-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h4 class="fw-900 text-white m-0">أهلاً بك، {{ $driver->name }}</h4>
                <div class="text-white-50 fw-semibold mt-1" style="font-size:.85rem;">بوابة المتابعة والإنجاز الميداني الشاملة</div>
            </div>
        </div>
        <div>
            <span class="sbadge sbadge-{{ $driver->status }}" style="background-color: var(--primary); color: #FFFFFF; border: 1px solid var(--accent); padding: 8px 18px; font-size: 13.5px;">
                <i class="bi {{ $driver->status === 'available' ? 'bi-check-circle-fill' : ($driver->status === 'busy' ? 'bi-truck' : 'bi-clock') }} text-accent"></i>
                {{ \App\Models\Driver::statusLabel($driver->status) }}
            </span>
        </div>
    </div>

    {{-- 2. الإحصائيات والشحنة الحالية --}}
    <div class="section-card">
        <div class="p-4">
            <div class="stats-inline-strip">
                <div class="stat-strip-item">
                    <div>
                        <div class="text-muted-sm fw-700 mb-1">الشحنات المسندة قيد التنفيذ</div>
                        <div class="fs-3 fw-900 text-navy">{{ $assignedCount }}</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59,130,246,0.10); color: #1E40AF; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>

                <div class="stat-strip-item">
                    <div>
                        <div class="text-muted-sm fw-700 mb-1">إجمالي الشحنات المسلّمة بنجاح</div>
                        <div class="fs-3 fw-900 text-navy">{{ $deliveredCount }}</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.10); color: #065F46; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>

            {{-- تفاصيل الشحنة الحالية --}}
            <div class="section-card-header px-0 pt-0">
                <div class="section-card-title">
                    <i class="bi bi-lightning-charge-fill"></i> الشحنة الحالية (المهمة النشطة)
                </div>
                @if($currentShipment)
                    <div class="d-flex align-items-center gap-2">
                        <span class="font-monospace fw-900 text-navy fs-6 bg-light px-3 py-1 rounded border">#{{ $currentShipment->shipment_number }}</span>
                        <span class="sbadge sbadge-{{ $currentShipment->status }}">
                            <i class="bi {{ \App\Models\Shipment::statusIcon($currentShipment->status) }}"></i>
                            {{ \App\Models\Shipment::statusLabel($currentShipment->status) }}
                        </span>
                    </div>
                @endif
            </div>

            <div>
                @if($currentShipment)
                    <div class="destination-strip">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted-sm fw-700 mb-1">وجهة الوصول النهائية</div>
                            <div class="fs-5 fw-900 text-navy">
                                {{ $currentShipment->destination->name ?? '-' }}
                                @if($currentShipment->destination?->city)
                                    <span class="text-muted fs-6 fw-600">({{ $currentShipment->destination->city }})</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="shipment-details-matrix">
                        <div class="matrix-item">
                            <div class="matrix-item-icon"><i class="bi bi-archive-fill"></i></div>
                            <div class="d-flex flex-column">
                                <span class="text-muted-sm fw-700">نوع البضاعة</span>
                                <span class="fs-6 fw-800 text-navy">{{ $currentShipment->cargo_type }}</span>
                            </div>
                        </div>

                        <div class="matrix-item">
                            <div class="matrix-item-icon"><i class="bi bi-sliders"></i></div>
                            <div class="d-flex flex-column">
                                <span class="text-muted-sm fw-700">الكمية والوزن</span>
                                <span class="fs-6 fw-800 text-navy font-monospace">
                                    {{ $currentShipment->quantity ?? '-' }} {{ $currentShipment->unit }}
                                    @if($currentShipment->weight) ({{ $currentShipment->weight }} طن) @endif
                                </span>
                            </div>
                        </div>

                        <div class="matrix-item">
                            <div class="matrix-item-icon"><i class="bi bi-building"></i></div>
                            <div class="d-flex flex-column">
                                <span class="text-muted-sm fw-700">ميناء المنشأ</span>
                                <span class="fs-6 fw-800 text-navy">{{ $currentShipment->origin_port }}</span>
                            </div>
                        </div>

                        <div class="matrix-item">
                            <div class="matrix-item-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="d-flex flex-column">
                                <span class="text-muted-sm fw-700">تاريخ الإسناد</span>
                                <span class="fs-6 fw-800 text-navy font-monospace">{{ $currentShipment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-3">
                        @if($currentShipment->status === 'assigned')
                            <form action="{{ route('driver.shipments.status', $currentShipment) }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="status" value="in_transit">
                                <button type="submit" class="btn btn-gold w-100 py-2">
                                    <i class="bi bi-truck"></i> بدء الرحلة / الانتقال لقيد النقل
                                </button>
                            </form>
                        @endif
                        @if($currentShipment->status === 'in_transit')
                            <div class="row g-3 w-100 m-0">
                                <div class="col-md-6 p-0 pe-md-2">
                                    <form action="{{ route('driver.shipments.status', $currentShipment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="btn-clean-success">
                                            <i class="bi bi-check-circle-fill"></i> تم التسليم بنجاح
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6 p-0 ps-md-2 mt-2 mt-md-0">
                                    <form action="{{ route('driver.shipments.status', $currentShipment) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="failed">
                                        <button type="submit" class="btn-clean-danger">
                                            <i class="bi bi-x-circle-fill"></i> متعذر التسليم
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-muted-sm">
                        <div style="font-size: 2.2rem; color: var(--accent); margin-bottom: 8px;">
                            <i class="bi bi-clipboard2-check"></i>
                        </div>
                        <h6 class="fw-800 text-navy mb-1">لا توجد شحنات مسندة إليك حالياً</h6>
                        <p class="text-muted-sm m-0">عند إسناد شحنة جديدة لك، ستظهر كافة تفاصيلها هنا فوراً.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. جدول أرشيف الشحنات السابقة --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-clock-history"></i> أرشيف الشحنات السابقة والمكتملة
            </div>
            @if($previousShipments->count() > 0)
                <a href="{{ route('driver.shipments') }}" class="text-decoration-none fw-700 fs-sm d-flex align-items-center gap-1 text-navy">
                    عرض الكل <i class="bi bi-chevron-left"></i>
                </a>
            @endif
        </div>

        <div class="p-0">
            @if($previousShipments->count() > 0)
                <div class="table-responsive">
                    <table class="table tbl">
                        <thead>
                            <tr>
                                <th>رقم الشحنة</th>
                                <th>نوع البضاعة</th>
                                <th>الوجهة</th>
                                <th>تاريخ التسليم</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previousShipments as $shipment)
                            <tr>
                                <td>
                                    <a href="{{ route('driver.shipments.show', $shipment) }}" class="ship-num font-monospace">
                                        #{{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td>{{ $shipment->cargo_type }}</td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1 text-secondary">
                                        <i class="bi bi-geo-alt-fill text-accent"></i>
                                        <span>{{ $shipment->destination->name ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="font-monospace text-muted-sm">{{ $shipment->delivered_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <span class="sbadge sbadge-{{ $shipment->status }}">
                                        <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                                        {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted-sm">
                    <div style="font-size: 2.2rem; color: var(--accent); margin-bottom: 8px;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6 class="fw-800 text-navy mb-1">لا توجد شحنات سابقة في الأرشيف</h6>
                    <p class="text-muted-sm m-0">الشحنات التي تنجح في تسليمها ستظهر سجلاتها هنا تباعاً.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
