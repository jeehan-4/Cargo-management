@extends('layouts.app')
@section('title', 'تفاصيل الشحنة ' . $shipment->shipment_number)
@section('page-title', 'تفاصيل الشحنة')
@section('title-icon', 'bi-box-seam')

@section('content')
<style>
/* ─── Minimalist Clean Dashboard Theme ─── */
.shipment-detail-wrapper {
    max-width: 1150px;
    margin: 0 auto;
}

/* Minimalist Rounded Cards */
.clean-card {
    background: #ffffff;
    border: 1px solid rgba(65, 90, 102, 0.08);
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 22px;
}

.clean-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(65, 90, 102, 0.06);
}

.clean-card-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.clean-card-title i {
    color: var(--accent);
    font-size: 19px;
}

/* Header Banner */
.shipment-main-num {
    font-size: 26px;
    font-weight: 900;
    color: var(--primary);
    margin: 6px 0 4px;
    letter-spacing: -0.5px;
}

/* Info Grid */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 991px) {
    .info-cards-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
    .info-cards-grid { grid-template-columns: 1fr; }
}

.info-item-box {
    background: var(--bg, #f8f9fa);
    border-radius: 14px;
    padding: 16px;
    border: 1px solid rgba(65, 90, 102, 0.04);
}
.info-item-label {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-item-val {
    font-size: 15px;
    font-weight: 800;
    color: var(--primary);
}

/* Driver Box Minimalist */
.driver-box-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: var(--bg, #f8f9fa);
    border-radius: 16px;
    padding: 18px 22px;
    border: 1px solid rgba(65, 90, 102, 0.06);
}
.driver-avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 800;
    flex-shrink: 0;
}
.driver-specs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-top: 16px;
}
@media (max-width: 768px) {
    .driver-specs-grid { grid-template-columns: 1fr; }
}
.driver-spec-box {
    padding: 12px 16px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid rgba(65, 90, 102, 0.06);
}

/* Buttons Minimalist */
.btn-primary-action {
    background: linear-gradient(135deg, var(--accent) 0%, #b38f4d 100%);
    color: #ffffff !important;
    border: none;
    font-weight: 700;
    font-size: 13px;
    padding: 9px 20px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(199, 166, 103, 0.22);
    cursor: pointer;
    text-decoration: none;
}
.btn-primary-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(199, 166, 103, 0.35);
}

.btn-light-action {
    background: #ffffff;
    color: var(--secondary) !important;
    border: 1px solid rgba(65, 90, 102, 0.15);
    font-weight: 700;
    font-size: 12.5px;
    padding: 8px 16px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-light-action:hover {
    background: var(--bg, #f8f9fa);
    border-color: var(--primary);
    color: var(--primary) !important;
}

/* Timeline Minimalist & Explicit Status Badge Colors Fix */
.timeline-minimal-box {
    position: relative;
    padding-right: 14px;
}
.timeline-item-row {
    display: flex;
    gap: 16px;
    position: relative;
    padding-bottom: 22px;
}
.timeline-item-row:last-child {
    padding-bottom: 0;
}
.timeline-item-row::before {
    content: '';
    position: absolute;
    right: 17px;
    top: 36px;
    bottom: 0;
    width: 2px;
    background: rgba(65, 90, 102, 0.1);
}
.timeline-item-row:last-child::before {
    display: none;
}
.timeline-dot-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 2px solid rgba(65, 90, 102, 0.15);
    color: var(--secondary);
    font-size: 13px;
    z-index: 1;
    flex-shrink: 0;
}
.timeline-dot-icon.active {
    background: rgba(199, 166, 103, 0.15);
    border-color: var(--accent);
    color: var(--accent);
}
.timeline-dot-icon.success {
    background: rgba(25, 135, 84, 0.12);
    border-color: #198754;
    color: #198754;
}
.timeline-content-card {
    flex: 1;
    background: var(--bg, #f8f9fa);
    border-radius: 14px;
    padding: 14px 18px;
    border: 1px solid rgba(65, 90, 102, 0.05);
}

/* تصحيح وضوح الأوسمة (Badges) وحالات بانتظار التوزيع وتَم التكليف لمنع اختفاء النص الأبيض */
.badge.badge-pending, .badge.badge-assigned, .badge-warning {
    background-color: #fff3cd !important;
    color: #856404 !important;
    border: 1px solid #ffeeba !important;
}
.badge.badge-in_transit {
    background-color: #cce5ff !important;
    color: #004085 !important;
    border: 1px solid #b8daff !important;
}
.badge.badge-delivered {
    background-color: #d4edda !important;
    color: #155724 !important;
    border: 1px solid #c3e6cb !important;
}

/* Table Clean */
.table-minimal {
    width: 100%;
    border-collapse: collapse;
}
.table-minimal th {
    background: var(--bg, #f8f9fa);
    color: var(--muted);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 10px 14px;
    border-bottom: 1px solid rgba(65, 90, 102, 0.08);
}
.table-minimal td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(65, 90, 102, 0.05);
    font-size: 13px;
    vertical-align: middle;
}
.table-minimal tr:last-child td {
    border-bottom: none;
}

/* Modal Minimalist */
.custom-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(32, 40, 51, 0.45);
    backdrop-filter: blur(5px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    transition: opacity .2s ease;
}
.custom-modal-overlay.active {
    display: flex;
    opacity: 1;
}
.custom-modal-dialog {
    background: #ffffff;
    border-radius: 22px;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    transform: scale(0.96);
    transition: transform .2s ease;
}
.custom-modal-overlay.active .custom-modal-dialog {
    transform: scale(1);
}

.driver-select-card {
    border: 1.5px solid rgba(65, 90, 102, 0.12);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
}
.driver-select-card:hover {
    border-color: var(--accent);
    background: var(--bg, #f8f9fa);
}
.driver-select-card.disabled {
    opacity: 0.5;
    background: #f8f9fa;
    cursor: not-allowed;
}
.driver-select-card:has(input[type="radio"]:checked) {
    border-color: var(--accent);
    background: rgba(199, 166, 103, 0.06);
}

@media print {
    .sidebar, .topbar, .btn-primary-action, .btn-light-action, .custom-modal-overlay, a {
        display: none !important;
    }
    body, .main-wrapper, .content-area {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .clean-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
}
</style>

<div class="shipment-detail-wrapper" dir="rtl">

    {{-- ═══════════════════ 1. SHIPMENT HEADER CARD ═══════════════════ --}}
    <div class="clean-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge status-badge badge-{{ $shipment->status }} px-3 py-1.5 rounded-pill fs-8 fw-bold">
                        <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }} me-1"></i>
                        {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                    </span>
                    <span class="text-muted fs-8 font-monospace">• الشحنات البرية</span>
                </div>
                <div class="shipment-main-num">
                    شحنة رقم #{{ $shipment->shipment_number }}
                </div>
                <div class="text-muted fs-8 d-flex align-items-center gap-3 mt-1">
                    <span><i class="bi bi-calendar3 me-1"></i> {{ $shipment->created_at->format('Y-m-d') }}</span>
                    <span><i class="bi bi-clock me-1"></i> {{ $shipment->created_at->format('h:i A') }}</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(!in_array($shipment->status, ['delivered', 'cancelled', 'archived']))
                    <a href="{{ route('shipments.edit', $shipment) }}" class="btn-light-action">
                        <i class="bi bi-pencil-square"></i> تعديل
                    </a>
                @endif

                {{-- زر الأرشفة للشحنات المكتملة أو الملغاة --}}
                @if(in_array($shipment->status, ['delivered', 'failed', 'cancelled']))
                    <form method="POST" action="{{ route('shipments.archive', $shipment) }}" onsubmit="return confirm('هل تريد بالتأكيد نقل هذه الشحنة إلى الأرشيف؟');" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-light-action text-info" title="أرشفة الشحنة">
                            <i class="bi bi-archive"></i> أرشفة
                        </button>
                    </form>
                @endif

                {{-- زر إلغاء الشحنة (من الميناء / الإدارة) --}}
                @if(!in_array($shipment->status, ['delivered', 'cancelled', 'archived']))
                    <button type="button" class="btn-light-action text-danger" onclick="openCancelModal()" title="إلغاء الشحنة مع حفظ بياناتها">
                        <i class="bi bi-slash-circle"></i> إلغاء الشحنة
                    </button>
                @endif

                {{-- زر الحذف النهائي: متاح فقط للشحنة المضافة بالخطأ ولم تُسند لأي سائق --}}
                @if($shipment->status === 'pending' && !$shipment->driver_id)
                    <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" onsubmit="return confirm('تنبيه: هذه الشحنة مضافة بالخطأ ولم تسند، هل أنت متأكد من حذفها نهائياً؟');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-light-action text-danger" title="حذف الشحنة المضافة بالخطأ">
                            <i class="bi bi-trash"></i> حذف
                        </button>
                    </form>
                @endif

                <button type="button" onclick="window.print()" class="btn-light-action">
                    <i class="bi bi-printer"></i> طباعة
                </button>
                <a href="{{ route('shipments.index') }}" class="btn-light-action">
                    <i class="bi bi-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ 2. SHIPMENT INFORMATION CARD ═══════════════════ --}}
    <div class="clean-card">
        <div class="clean-card-header">
            <h5 class="clean-card-title">
                <i class="bi bi-box-seam"></i> بيانات ومعلومات الشحنة
            </h5>
            <span class="text-muted fs-8 font-monospace bg-light px-2.5 py-1 rounded-pill border">ID: {{ $shipment->id }}</span>
        </div>

        <div class="info-cards-grid mb-3">
            <div class="info-item-box">
                <div class="info-item-label">
                    <i class="bi bi-tag"></i> نوع البضاعة
                </div>
                <div class="info-item-val">{{ $shipment->cargo_type }}</div>
            </div>

            <div class="info-item-box">
                <div class="info-item-label">
                    <i class="bi bi-boxes"></i> الكمية المسجلة
                </div>
                <div class="info-item-val">
                    {{ $shipment->quantity ? $shipment->quantity . ' ' . $shipment->unit : '—' }}
                </div>
            </div>

            <div class="info-item-box">
                <div class="info-item-label">
                    <i class="bi bi-speedometer2"></i> الوزن الإجمالي
                </div>
                <div class="info-item-val">
                    {{ $shipment->weight ? $shipment->weight . ' طن' : '—' }}
                </div>
            </div>

            <div class="info-item-box">
                <div class="info-item-label">
                    <i class="bi bi-geo-alt"></i> ميناء المنشأ (المصدر)
                </div>
                <div class="info-item-val">{{ $shipment->origin_port }}</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="info-item-box h-100">
                    <div class="info-item-label">
                        <i class="bi bi-building-check"></i> الوجهة المقصودة
                    </div>
                    <div class="info-item-val">
                        {{ $shipment->destination?->name ?? 'غير محددة' }}
                        @if($shipment->destination?->city)
                            <span class="text-muted fs-7 fw-normal">({{ $shipment->destination->city }})</span>
                        @endif
                    </div>
                    @if($shipment->destination?->address)
                        <div class="text-muted fs-8 mt-1"><i class="bi bi-signpost-2 me-1"></i>{{ $shipment->destination->address }}</div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-item-box h-100">
                    <div class="info-item-label">
                        <i class="bi bi-chat-left-text"></i> ملاحظات الشحنة
                    </div>
                    <div class="fs-7 text-dark fw-500 pt-1">
                        {{ $shipment->notes ?: 'لا توجد ملاحظات إضافية مسجلة لهذه الشحنة.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ 3. ASSIGNED DRIVER CARD ═══════════════════ --}}
    <div class="clean-card">
        <div class="clean-card-header">
            <h5 class="clean-card-title">
                <i class="bi bi-person-badge"></i> السائق المكلف بالرحلة
            </h5>

            <div class="d-flex align-items-center gap-2">
                @if(!in_array($shipment->status, ['delivered', 'failed', 'cancelled', 'archived']))
                    <button type="button" class="btn-primary-action" onclick="openDriverModal()">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>{{ $shipment->driver ? 'تغيير السائق' : 'تعيين سائق' }}</span>
                    </button>
                    @if($shipment->driver)
                        <form method="POST" action="{{ route('shipments.unassign', $shipment) }}" onsubmit="return confirm('هل أنت متأكد من إلغاء إسناد الشحنة لهذا السائق وتحريره؟');" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-light-action text-danger" title="إلغاء الإسناد وتحرير السائق">
                                <i class="bi bi-person-x"></i> إلغاء الإسناد
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        @if($shipment->driver)
            <div class="driver-box-container">
                <div class="d-flex align-items-center gap-3">
                    <div class="driver-avatar-circle">
                        {{ mb_substr($shipment->driver->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $shipment->driver->name }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge status-badge badge-{{ $shipment->driver->status }} fs-8">
                                {{ \App\Models\Driver::statusLabel($shipment->driver->status) }}
                            </span>
                            <span class="text-muted fs-8 font-monospace" dir="ltr">#DRV-{{ $shipment->driver->id }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="{{ route('drivers.show', $shipment->driver) }}" class="btn-light-action">
                        <i class="bi bi-person-lines-fill"></i> استعراض ملف السائق
                    </a>
                </div>
            </div>

            <div class="driver-specs-grid">
                <div class="driver-spec-box">
                    <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-telephone me-1"></i> رقم الهاتف</div>
                    <div class="fw-bold fs-7 text-dark" dir="ltr">{{ $shipment->driver->phone }}</div>
                </div>

                <div class="driver-spec-box">
                    <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-truck me-1"></i> الشاحنة والمركبة</div>
                    <div class="fw-bold fs-7 text-dark">
                        {{ $shipment->driver->truck?->type ?? 'غير مرتبطة' }}
                    </div>
                </div>

                <div class="driver-spec-box">
                    <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-card-text me-1"></i> لوحة الشاحنة</div>
                    <div class="fw-bold fs-7 text-dark">
                        {{ $shipment->driver->truck?->plate_number ?? '—' }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                <i class="bi bi-person-exclamation display-5 text-muted opacity-50 d-block mb-3"></i>
                <h6 class="fw-bold text-dark mb-1">لم يتم تعيين سائق لهذه الشحنة حتى الآن</h6>
                <p class="text-muted fs-8 mb-3">اختر وتكليف سائق مناسب من قائمة السائقين المتاحين.</p>
                @if(!in_array($shipment->status, ['delivered', 'failed']))
                <button type="button" class="btn-primary-action" onclick="openDriverModal()">
                    <i class="bi bi-plus-lg"></i> تعيين سائق الآن
                </button>
                @endif
            </div>
        @endif
    </div>

    {{-- ═══════════════════ 4. TIMELINE & ASSIGNMENT HISTORY ═══════════════════ --}}
    <div class="row g-4">
        {{-- Tracking Timeline --}}
        <div class="col-lg-7">
            <div class="clean-card h-100 mb-0">
                <div class="clean-card-header">
                    <h5 class="clean-card-title">
                        <i class="bi bi-clock-history"></i> الجدول الزمني لمراحل الشحنة
                    </h5>
                    <span class="badge bg-light text-secondary border rounded-pill px-3">{{ $shipment->logs->count() }} مراحل</span>
                </div>

                <div class="timeline-minimal-box">
                    @forelse($shipment->logs as $log)
                    @php
                        $statusTextClass = match($log->new_status) {
                            'pending', 'assigned' => 'text-dark fw-bold',
                            'in_transit'          => 'text-primary fw-bold',
                            'delivered'           => 'text-success fw-bold',
                            'failed', 'cancelled' => 'text-danger fw-bold',
                            default               => 'text-dark'
                        };
                    @endphp
                    <div class="timeline-item-row">
                        <div class="timeline-dot-icon {{ $log->new_status === 'delivered' ? 'success' : ($log->new_status === 'in_transit' ? 'active' : '') }}">
                            <i class="bi {{ \App\Models\Shipment::statusIcon($log->new_status) }}"></i>
                        </div>
                        <div class="timeline-content-card">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge status-badge badge-{{ $log->new_status }} px-2.5 py-1 fs-8">
                                    {{ \App\Models\Shipment::statusLabel($log->new_status) }}
                                </span>
                                <small class="text-muted fs-8 font-monospace">
                                    {{ $log->created_at->format('Y-m-d — h:i A') }}
                                </small>
                            </div>
                            @if($log->notes)
                                <div class="fs-7 mt-1 {{ $statusTextClass }}">{{ $log->notes }}</div>
                            @endif
                            @if($log->user)
                                <div class="text-muted fs-8 mt-1">
                                    <i class="bi bi-person me-1"></i> المنفذ: {{ $log->user->name }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted fs-8">
                        لا توجد مراحل مسجلة لهذه الشحنة بعد.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Assignment History --}}
        <div class="col-lg-5">
            <div class="clean-card h-100 mb-0">
                <div class="clean-card-header">
                    <h5 class="clean-card-title">
                        <i class="bi bi-journal-text"></i> سجل إسناد وتغيير السائقين
                    </h5>
                </div>

                @if($assignmentLogs->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table-minimal">
                            <thead>
                                <tr>
                                    <th>التاريخ والوقت</th>
                                    <th>البيان والملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignmentLogs as $aLog)
                                <tr>
                                    <td class="text-muted fs-8 text-nowrap">
                                        {{ $aLog->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark fs-7">{{ $aLog->notes }}</div>
                                        @if($aLog->user)
                                            <small class="text-muted fs-8 d-block mt-0.5">بواسطة: {{ $aLog->user->name }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted fs-8">
                        <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i>
                        لا توجد عمليات تغيير سابقة للسائق.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════ DRIVER REASSIGNMENT MODAL ═══════════════════ --}}
<div class="custom-modal-overlay" id="driverModalOverlay" onclick="handleBackdropClick(event)">
    <div class="custom-modal-dialog">
        <form method="POST" action="{{ route('shipments.assign', $shipment) }}" id="reassignForm" onsubmit="return confirmReassignment(event)">
            @csrf
            
            <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white">
                <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2 fs-6">
                    <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                    <span>{{ $shipment->driver ? 'تغيير السائق المكلف' : 'إسناد الشحنة لسائق' }}</span>
                </h5>
                <button type="button" class="btn-close" onclick="closeDriverModal()"></button>
            </div>

            <div class="p-4" style="max-height: 65vh; overflow-y: auto;">
                @if($shipment->driver)
                <div class="p-3 mb-3 bg-light rounded-3 border">
                    <div class="fs-8 text-muted fw-bold mb-1">السائق الحالي للشحنة:</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-bold text-dark fs-6">{{ $shipment->driver->name }}</div>
                        <span class="badge bg-secondary-subtle text-secondary border">مكلف حالياً</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">هاتف: {{ $shipment->driver->phone }}</div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fs-7 fw-bold text-dark mb-2">اختر السائق الجديد من القائمة:</label>
                    <div>
                        @forelse($allDrivers as $drv)
                            @php
                                $isAvailable = $drv->isAvailable();
                                $isCurrent = $shipment->driver_id === $drv->id;
                                $isDisabled = !$isAvailable || $isCurrent;
                            @endphp
                            <label class="driver-select-card {{ $isDisabled ? 'disabled' : '' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="driver_id" value="{{ $drv->id }}" 
                                           class="form-check-input mt-0"
                                           {{ $isDisabled ? 'disabled' : '' }} required>
                                    <div>
                                        <div class="text-dark fw-bold fs-7">{{ $drv->name }}</div>
                                        <small class="text-muted fs-8 font-monospace" dir="ltr">{{ $drv->phone }}</small>
                                        @if($drv->truck)
                                            <small class="text-muted fs-8 ms-2">| شاحنة: {{ $drv->truck->plate_number }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    @if($isCurrent)
                                        <span class="badge bg-primary text-white fs-8">السائق الحالي</span>
                                    @elseif($isAvailable)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-8">
                                            <i class="bi bi-check-circle me-1"></i> متاح
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-8">
                                            <i class="bi bi-hourglass-split me-1"></i> مشغول
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-3 text-muted">لا يوجد سائقون مسجلون في النظام.</div>
                        @endforelse
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label fs-7 fw-bold text-dark mb-1">سبب تغيير أو إسناد السائق (اختياري):</label>
                    <textarea name="reason" class="form-control fs-7 rounded-3" rows="2" placeholder="أدخل سبب التغيير لتوثيقه في سجل العمليات..."></textarea>
                </div>
            </div>

            <div class="p-3 bg-light border-top d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-light border px-3 py-2 rounded-3" onclick="closeDriverModal()">إلغاء</button>
                <button type="submit" class="btn-primary-action py-2">
                    <i class="bi bi-check-circle"></i> تأكيد تعيين السائق
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════ CANCEL SHIPMENT MODAL ═══════════════════ --}}
<div class="custom-modal-overlay" id="cancelModalOverlay" onclick="handleCancelBackdrop(event)">
    <div class="custom-modal-dialog">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title text-danger">
                <i class="bi bi-slash-circle me-1"></i> إلغاء الشحنة
            </h5>
            <button type="button" class="btn-close" onclick="closeCancelModal()"></button>
        </div>

        <form method="POST" action="{{ route('shipments.cancel', $shipment) }}" id="cancelForm">
            @csrf
            <div class="custom-modal-body">
                <div class="alert alert-warning border-0 fs-8 d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-6 text-warning"></i>
                    <div>
                        سيتم تغيير حالة الشحنة إلى <strong>ملغاة</strong> مع الاحتفاظ بكافة بياناتها وسجلاتها، وسيتم تحرير السائق المرتبط بها فوراً إن وُجد.
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fs-7 fw-bold text-dark mb-1">سبب الإلغاء (من الميناء / الإدارة): <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control fs-7 rounded-3" rows="3" required placeholder="يرجى كتابة سبب الإلغاء (مثل: إلغاء من الميناء، خطأ في الحجز، اعتذار العميل...)..."></textarea>
                </div>
            </div>

            <div class="p-3 bg-light border-top d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-light border px-3 py-2 rounded-3" onclick="closeCancelModal()">تراجع</button>
                <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 fw-bold">
                    <i class="bi bi-check-circle"></i> تأكيد إلغاء الشحنة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelModal() {
    const modal = document.getElementById('cancelModalOverlay');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModalOverlay');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleCancelBackdrop(event) {
    if (event.target.id === 'cancelModalOverlay') {
        closeCancelModal();
    }
}

function openDriverModal() {
    const modal = document.getElementById('driverModalOverlay');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDriverModal() {
    const modal = document.getElementById('driverModalOverlay');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function handleBackdropClick(event) {
    if (event.target.id === 'driverModalOverlay') {
        closeDriverModal();
    }
}

function confirmReassignment(e) {
    const selected = document.querySelector('input[name="driver_id"]:checked');
    if (!selected) {
        alert('الرجاء اختيار سائق من القائمة المتاحة.');
        e.preventDefault();
        return false;
    }
    return confirm('هل أنت متأكد من رغبتك في إسناد هذه الشحنة للسائق المختار؟');
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDriverModal();
        closeCancelModal();
    }
});
</script>
@endsection