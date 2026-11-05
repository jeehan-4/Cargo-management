@extends('driver.layout')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة تحكم السائق')
@section('title-icon', 'bi-speedometer2')

@section('content')
<style>
/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   UNIFIED FLAT MODERN DESIGN SYSTEM
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

.driver-portal-wrapper {
    max-width: 1140px;
    margin: 0 auto;
    padding-bottom: 40px;
}

/* 1. قسم الترحيب الرئيسي بتصميم جديد أنيق */
.hero-welcome-section {
    background-color: var(--secondary-navy);
    border-radius: 16px;
    padding: 24px 30px;
    color: #FFFFFF;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    border-right: 5px solid var(--gold-accent);
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
    background: var(--primary-navy);
    color: var(--gold-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border: 1px solid rgba(199, 166, 103, 0.4);
}

/* 2. الـ Div الموحد الرئيسي للشحنة الحالية والإحصائيات */
.unified-main-card {
    background: var(--surface-white);
    border: 1px solid var(--border-clean);
    border-radius: 18px;
    padding: 28px;
    margin-bottom: 24px;
}

.card-title-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 20px;
    border-bottom: 1.5px solid var(--border-clean);
}

.title-text {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.title-text i {
    color: var(--gold-accent);
    font-size: 20px;
}

/* مصغرات الإحصائيات السريعة داخل نفس الـ Div */
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
    border: 1px solid var(--border-clean);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* تفاصيل الشحنة داخل الـ Div الموحد */
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
    border: 1px solid var(--border-clean);
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
    color: var(--gold-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

/* الجدول */
.clean-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.clean-table th {
    background: #FAF8F5;
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 14px 18px;
    border-bottom: 2px solid var(--border-clean);
}
.clean-table td {
    padding: 16px 18px;
    border-bottom: 1px solid var(--border-clean);
    font-size: 14px;
    vertical-align: middle;
    color: var(--primary-navy);
    font-weight: 600;
}
.clean-table tr:last-child td { border-bottom: none; }

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

/* Buttons */
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
    width: 100%;
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
}
</style>

<div class="driver-portal-wrapper" dir="rtl">

    {{-- 1. HERO WELCOME SECTION (تصميم ترحيبي أنيق ومناسب) --}}
    <div class="hero-welcome-section">
        <div class="hero-driver-info">
            <div class="hero-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h4 class="fw-900 text-white m-0">أهلاً بك، {{ $driver->name }}</h4>
                <div class="text-white-50 fs-8 fw-semibold mt-1">بوابة المتابعة والإنجاز الميداني الشاملة</div>
            </div>
        </div>
        <div>
            @php
                $driverBadgeClass = match($driver->status) {
                    'available' => 's-delivered',
                    'busy' => 's-transit',
                    default => 's-pending'
                };
                $driverBadgeIcon = match($driver->status) {
                    'available' => 'bi-check-circle-fill',
                    'busy' => 'bi-truck',
                    default => 'bi-clock'
                };
            @endphp
            <span class="clean-badge {{ $driverBadgeClass }}" style="background-color: var(--primary-navy); color: #FFFFFF; border: 1px solid var(--gold-accent); padding: 8px 18px; font-size: 13.5px;">
                <i class="bi {{ $driverBadgeIcon }} text-warning"></i>
                {{ \App\Models\Driver::statusLabel($driver->status) }}
            </span>
        </div>
    </div>

    {{-- 2. DIV موحد رئيسي يجمع الإحصائيات والشحنة الحالية بأكملها --}}
    <div class="unified-main-card">
        
        {{-- شريط الإحصائيات المدمج --}}
        <div class="stats-inline-strip">
            <div class="stat-strip-item">
                <div>
                    <div class="text-muted fs-8 fw-bold mb-1">الشحنات المسندة قيد التنفيذ</div>
                    <div class="fs-3 fw-900 text-dark">{{ $assignedCount }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #EAF1F8; color: #3E638F; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>

            <div class="stat-strip-item">
                <div>
                    <div class="text-muted fs-8 fw-bold mb-1">إجمالي الشحنات المسلّمة بنجاح</div>
                    <div class="fs-3 fw-900 text-dark">{{ $deliveredCount }}</div>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: #EAF3E9; color: #3B6641; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>

        {{-- تفاصيل الشحنة الحالية (داخل نفس الـ Div) --}}
        <div class="card-title-bar">
            <h5 class="title-text">
                <i class="bi bi-lightning-charge-fill text-warning"></i> الشحنة الحالية (المهمة النشطة)
            </h5>
            @if($currentShipment)
                @php
                    $badgeClass = match($currentShipment->status) {
                        'pending' => 's-pending',
                        'assigned' => 's-assigned',
                        'in_transit' => 's-transit',
                        'delivered' => 's-delivered',
                        'failed' => 's-failed',
                        default => 's-pending'
                    };
                    $badgeIcon = match($currentShipment->status) {
                        'pending' => 'bi-clock',
                        'assigned' => 'bi-person-check',
                        'in_transit' => 'bi-truck',
                        'delivered' => 'bi-check-circle',
                        'failed' => 'bi-x-circle',
                        default => 'bi-clock'
                    };
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <span class="font-monospace fw-900 text-dark fs-6 bg-light px-3 py-1 rounded border">#{{ $currentShipment->shipment_number }}</span>
                    <span class="clean-badge {{ $badgeClass }}">
                        <i class="bi {{ $badgeIcon }}"></i> {{ \App\Models\Shipment::statusLabel($currentShipment->status) }}
                    </span>
                </div>
            @endif
        </div>

        <div>
            @if($currentShipment)
                <div class="destination-strip">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary-navy); color: var(--gold-accent); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-8 fw-bold mb-1">وجهة الوصول النهائية</div>
                        <div class="fs-5 fw-900 text-dark">
                            {{ $currentShipment->destination->name ?? '-' }} 
                            @if($currentShipment->destination?->city)
                                <span class="text-muted fs-6 fw-semibold">({{ $currentShipment->destination->city }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="shipment-details-matrix">
                    <div class="matrix-item">
                        <div class="matrix-item-icon"><i class="bi bi-archive-fill"></i></div>
                        <div class="d-flex flex-column">
                            <span class="text-muted fs-8 fw-bold">نوع البضاعة</span>
                            <span class="fs-6 fw-800 text-dark">{{ $currentShipment->cargo_type }}</span>
                        </div>
                    </div>

                    <div class="matrix-item">
                        <div class="matrix-item-icon"><i class="bi bi-sliders"></i></div>
                        <div class="d-flex flex-column">
                            <span class="text-muted fs-8 fw-bold">الكمية والوزن</span>
                            <span class="fs-6 fw-800 text-dark font-monospace">
                                {{ $currentShipment->quantity ?? '-' }} {{ $currentShipment->unit }}
                                @if($currentShipment->weight) ({{ $currentShipment->weight }} طن) @endif
                            </span>
                        </div>
                    </div>

                    <div class="matrix-item">
                        <div class="matrix-item-icon"><i class="bi bi-building"></i></div>
                        <div class="d-flex flex-column">
                            <span class="text-muted fs-8 fw-bold">ميناء المنشأ</span>
                            <span class="fs-6 fw-800 text-dark">{{ $currentShipment->origin_port }}</span>
                        </div>
                    </div>

                    <div class="matrix-item">
                        <div class="matrix-item-icon"><i class="bi bi-calendar-check"></i></div>
                        <div class="d-flex flex-column">
                            <span class="text-muted fs-8 fw-bold">تاريخ الإسناد</span>
                            <span class="fs-6 fw-800 text-dark font-monospace">{{ $currentShipment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-3">
                    @if($currentShipment->status === 'assigned')
                        <form action="{{ route('driver.shipments.status', $currentShipment) }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="status" value="in_transit">
                            <button type="submit" class="btn-clean-gold">
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
                <div class="text-center py-4 text-muted">
                    <div style="font-size: 2.2rem; color: var(--gold-accent); margin-bottom: 8px;">
                        <i class="bi bi-clipboard2-check"></i>
                    </div>
                    <h6 class="fw-800 text-dark mb-1">لا توجد شحنات مسندة إليك حالياً</h6>
                    <p class="fs-8 text-muted m-0">عند إسناد شحنة جديدة لك، ستظهر كافة تفاصيلها هنا فوراً.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- 3. جدول أرشيف الشحنات السابقة --}}
    <div class="unified-main-card">
        <div class="card-title-bar">
            <h5 class="title-text">
                <i class="bi bi-clock-history"></i> أرشيف الشحنات السابقة والمكتملة
            </h5>
            @if($previousShipments->count() > 0)
                <a href="{{ route('driver.shipments') }}" class="text-decoration-none fw-bold fs-8 d-flex align-items-center gap-1" style="color: var(--secondary-navy);">
                    عرض الكل <i class="bi bi-chevron-left"></i>
                </a>
            @endif
        </div>

        <div class="p-0">
            @if($previousShipments->count() > 0)
                <div class="table-responsive">
                    <table class="clean-table">
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
                                    <a href="{{ route('driver.shipments.show', $shipment) }}" class="fw-900 font-monospace text-decoration-none" style="color: var(--primary-navy);">
                                        #{{ $shipment->shipment_number }}
                                    </a>
                                </td>
                                <td>{{ $shipment->cargo_type }}</td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1.5 text-secondary">
                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                        <span>{{ $shipment->destination->name ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="font-monospace text-muted">{{ $shipment->delivered_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>
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
                                        <i class="bi {{ $badgeIcon }}"></i> {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <div style="font-size: 2.2rem; color: var(--gold-accent); margin-bottom: 8px;">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h6 class="fw-800 text-dark mb-1">لا توجد شحنات سابقة في الأرشيف</h6>
                    <p class="fs-8 text-muted m-0">الشحنات التي تنجح في تسليمها ستظهر سجلاتها هنا تباعاً.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection