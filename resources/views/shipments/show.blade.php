@extends('layouts.app')
@section('title', 'تفاصيل الشحنة ' . $shipment->shipment_number)
@section('page-title', 'تفاصيل الشحنة')
@section('topbar-actions')
    <a href="{{ route('shipments.index') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-right me-1"></i> العودة للقائمة</a>
@endsection

@section('content')
<style>
/* مكونات خاصة بصفحة تفاصيل الشحنة (تعتمد على متغيرات التصميم الموحد) */
.shipment-detail-wrapper {
    max-width: 1150px;
    margin: 0 auto;
}

.shipment-main-num {
    font-size: 1.55rem;
    font-weight: 900;
    color: var(--primary);
    margin: 6px 0 4px;
    letter-spacing: -0.5px;
}

/* شبكة معلومات الشحنة */
.info-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}
@media (max-width: 991.98px) {
    .info-cards-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575.98px) {
    .info-cards-grid { grid-template-columns: 1fr; }
}
.info-item-box {
    background: #FAFAF8;
    border-radius: 14px;
    padding: 14px 16px;
    border: 1px solid var(--border);
}
.info-item-label {
    font-size: .74rem;
    font-weight: 700;
    color: var(--muted);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-item-val {
    font-size: .95rem;
    font-weight: 800;
    color: var(--primary);
}

/* بطاقة السائق المكلف */
.driver-box-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    background: #FAFAF8;
    border-radius: 16px;
    padding: 16px 20px;
    border: 1px solid var(--border);
}
.driver-specs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 14px;
}
@media (max-width: 767.98px) {
    .driver-specs-grid { grid-template-columns: 1fr; }
}
.driver-spec-box {
    padding: 12px 16px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid var(--border);
}

/* بطاقة محتوى الجدول الزمني */
.timeline-content-card {
    background: #FAFAF8;
    border-radius: 14px;
    padding: 12px 16px;
    border: 1px solid var(--border);
}
.timeline-dot.done {
    background: #10B981;
    color: #ffffff;
    box-shadow: 0 0 0 2px #10B981;
}

.driver-select-card {
    border: 1.5px solid var(--border-md);
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: var(--tr);
    background: #ffffff;
}
.driver-select-card:hover {
    border-color: var(--accent);
    background: #FAFAF8;
}
.driver-select-card.disabled {
    opacity: .55;
    background: #FAFAF8;
    cursor: not-allowed;
}
.driver-select-card:has(input[type="radio"]:checked) {
    border-color: var(--accent);
    background: rgba(199, 166, 103, 0.06);
}

@media (max-width: 767.98px) {
    .shipment-main-num { font-size: 1.3rem; }
}

@media print {
    .sidebar, .topbar, .btn, .custom-modal-overlay, .confirm-backdrop {
        display: none !important;
    }
    body, .main-wrapper, .content-area {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .section-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
}
</style>

<div class="shipment-detail-wrapper" dir="rtl">

    {{-- 1. بطاقة رأس الشحنة --}}
    <div class="section-card mb-4">
        <div class="p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="sbadge sbadge-{{ $shipment->status }} px-3 py-1">
                            <i class="bi {{ \App\Models\Shipment::statusIcon($shipment->status) }}"></i>
                            {{ \App\Models\Shipment::statusLabel($shipment->status) }}
                        </span>
                        <span class="text-muted fs-8 font-monospace">• الشحنات البرية</span>
                    </div>
                    <div class="shipment-main-num">
                        شحنة رقم #{{ $shipment->shipment_number }}
                    </div>
                    <div class="text-muted fs-8 d-flex align-items-center gap-3 mt-1 flex-wrap">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ $shipment->created_at->format('Y-m-d') }}</span>
                        <span><i class="bi bi-clock me-1"></i> {{ $shipment->created_at->format('h:i A') }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(!in_array($shipment->status, ['delivered', 'cancelled', 'archived']))
                        <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-outline-navy btn-sm">
                            <i class="bi bi-pencil-square"></i> تعديل
                        </a>
                    @endif

                    {{-- زر الأرشفة للشحنات المكتملة أو الملغاة --}}
                    @if(in_array($shipment->status, ['delivered', 'failed', 'cancelled']))
                        <form method="POST" action="{{ route('shipments.archive', $shipment) }}" class="d-inline"
                              data-confirm="هل تريد بالتأكيد نقل هذه الشحنة إلى الأرشيف؟"
                              data-confirm-title="أرشفة الشحنة"
                              data-confirm-danger="false">
                            @csrf
                            <button type="submit" class="btn btn-outline-navy btn-sm" title="أرشفة الشحنة">
                                <i class="bi bi-archive"></i> أرشفة
                            </button>
                        </form>
                    @endif

                    {{-- زر إلغاء الشحنة (من الميناء / الإدارة) --}}
                    @if(!in_array($shipment->status, ['delivered', 'cancelled', 'archived']))
                        <button type="button" class="btn btn-outline-navy btn-sm text-danger" onclick="openCancelModal()" title="إلغاء الشحنة مع حفظ بياناتها">
                            <i class="bi bi-slash-circle"></i> إلغاء الشحنة
                        </button>
                    @endif

                    {{-- زر الحذف النهائي: متاح فقط للشحنة المضافة بالخطأ ولم تسند لأي سائق --}}
                    @if($shipment->status === 'pending' && !$shipment->driver_id)
                        <form method="POST" action="{{ route('shipments.destroy', $shipment) }}" class="d-inline"
                              data-confirm="تنبيه: هذه الشحنة مضافة بالخطأ ولم تسند لأي سائق، هل أنت متأكد من حذفها نهائياً؟"
                              data-confirm-title="حذف نهائي"
                              data-confirm-ok="حذف نهائياً">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-navy btn-sm text-danger" title="حذف الشحنة المضافة بالخطأ">
                                <i class="bi bi-trash"></i> حذف
                            </button>
                        </form>
                    @endif

                    <button type="button" onclick="window.print()" class="btn btn-outline-navy btn-sm">
                        <i class="bi bi-printer"></i> طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. بطاقة بيانات الشحنة --}}
    <div class="section-card mb-4">
        <div class="section-card-header">
            <h6 class="section-card-title">
                <i class="bi bi-box-seam"></i> بيانات ومعلومات الشحنة
            </h6>
            <span class="pill-count font-monospace" dir="ltr">ID: {{ $shipment->id }}</span>
        </div>

        <div class="p-4">
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
                        <div class="fs-7 text-navy fw-semibold pt-1">
                            {{ $shipment->notes ?: 'لا توجد ملاحظات إضافية مسجلة لهذه الشحنة.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. بطاقة السائق المكلف --}}
    <div class="section-card mb-4">
        <div class="section-card-header">
            <h6 class="section-card-title">
                <i class="bi bi-person-badge"></i> السائق المكلف بالرحلة
            </h6>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(!in_array($shipment->status, ['delivered', 'failed', 'cancelled', 'archived']))
                    <button type="button" class="btn btn-gold btn-sm" onclick="openDriverModal()">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>{{ $shipment->driver ? 'تغيير السائق' : 'تعيين سائق' }}</span>
                    </button>
                    @if($shipment->driver)
                        <form method="POST" action="{{ route('shipments.unassign', $shipment) }}" class="d-inline"
                              data-confirm="هل أنت متأكد من إلغاء إسناد الشحنة لهذا السائق وتحريره؟"
                              data-confirm-title="إلغاء الإسناد"
                              data-confirm-ok="نعم، إلغاء الإسناد">
                            @csrf
                            <button type="submit" class="btn btn-outline-navy btn-sm text-danger" title="إلغاء الإسناد وتحرير السائق">
                                <i class="bi bi-person-x"></i> إلغاء الإسناد
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <div class="p-4">
            @if($shipment->driver)
                <div class="driver-box-container">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle" style="width:50px;height:50px;font-size:1.25rem;">{{ mb_substr($shipment->driver->name, 0, 1) }}</div>
                        <div>
                            <div class="fw-bold fs-5 text-navy">{{ $shipment->driver->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                <span class="status-badge badge-{{ $shipment->driver->status }}">
                                    {{ \App\Models\Driver::statusLabel($shipment->driver->status) }}
                                </span>
                                <span class="text-muted fs-8 font-monospace" dir="ltr">#DRV-{{ $shipment->driver->id }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('drivers.show', $shipment->driver) }}" class="btn btn-outline-navy btn-sm">
                            <i class="bi bi-person-lines-fill"></i> استعراض ملف السائق
                        </a>
                    </div>
                </div>

                <div class="driver-specs-grid">
                    <div class="driver-spec-box">
                        <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-telephone me-1"></i> رقم الهاتف</div>
                        <div class="fw-bold fs-7 text-navy" dir="ltr">{{ $shipment->driver->phone }}</div>
                    </div>

                    <div class="driver-spec-box">
                        <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-truck me-1"></i> الشاحنة والمركبة</div>
                        <div class="fw-bold fs-7 text-navy">
                            {{ $shipment->driver->truck?->type ?? 'غير مرتبطة' }}
                        </div>
                    </div>

                    <div class="driver-spec-box">
                        <div class="text-muted fs-8 fw-semibold mb-1"><i class="bi bi-card-text me-1"></i> لوحة الشاحنة</div>
                        <div class="fw-bold fs-7 text-navy">
                            {{ $shipment->driver->truck?->plate_number ?? '—' }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                    <i class="bi bi-person-exclamation display-5 text-muted opacity-50 d-block mb-3"></i>
                    <h6 class="fw-bold text-navy mb-1">لم يتم تعيين سائق لهذه الشحنة حتى الآن</h6>
                    <p class="text-muted fs-8 mb-3">اختر وكلف سائقاً مناسباً من قائمة السائقين المتاحين.</p>
                    @if(!in_array($shipment->status, ['delivered', 'failed']))
                    <button type="button" class="btn btn-gold btn-sm" onclick="openDriverModal()">
                        <i class="bi bi-plus-lg"></i> تعيين سائق الآن
                    </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- 4. الجدول الزمني وسجل الإسناد --}}
    <div class="row g-4">
        {{-- الجدول الزمني لتتبع الشحنة --}}
        <div class="col-lg-7">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-clock-history"></i> الجدول الزمني لمراحل الشحنة
                    </h6>
                    <span class="pill-count">{{ $shipment->logs->count() }} مراحل</span>
                </div>

                <div class="p-4">
                    <div class="timeline">
                        @forelse($shipment->logs as $log)
                        @php
                            $dotClass = $log->new_status === 'delivered' ? 'done' : '';
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $dotClass }}">
                                <i class="bi {{ \App\Models\Shipment::statusIcon($log->new_status) }}"></i>
                            </div>
                            <div class="timeline-content-card">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <span class="sbadge sbadge-{{ $log->new_status }}">
                                        {{ \App\Models\Shipment::statusLabel($log->new_status) }}
                                    </span>
                                    <small class="text-muted fs-8 font-monospace">
                                        {{ $log->created_at->format('Y-m-d — h:i A') }}
                                    </small>
                                </div>
                                @if($log->notes)
                                    <div class="fs-7 mt-1 fw-semibold text-navy">{{ $log->notes }}</div>
                                @endif
                                @if($log->user)
                                    <div class="text-muted fs-8 mt-1">
                                        <i class="bi bi-person me-1"></i> المنفذ: {{ $log->user->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="empty-state py-4">
                            <i class="bi bi-clock-history es-icon"></i>
                            <div class="es-title">لا توجد مراحل مسجلة لهذه الشحنة بعد.</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- سجل إسناد وتغيير السائقين --}}
        <div class="col-lg-5">
            <div class="section-card h-100">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-journal-text"></i> سجل إسناد وتغيير السائقين
                    </h6>
                </div>

                @if($assignmentLogs->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table tbl">
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
                                        <div class="fw-semibold text-navy fs-7">{{ $aLog->notes }}</div>
                                        @if($aLog->user)
                                            <small class="text-muted fs-8 d-block mt-1">بواسطة: {{ $aLog->user->name }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-clock-history es-icon"></i>
                        <div class="es-title">لا توجد عمليات تغيير سابقة للسائق.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- نافذة تغيير/تعيين السائق --}}
<div class="custom-modal-overlay" id="driverModalOverlay" onclick="handleBackdropClick(event)">
    <div class="custom-modal-dialog">
        <form method="POST" action="{{ route('shipments.assign', $shipment) }}" id="reassignForm"
              data-confirm="هل أنت متأكد من رغبتك في إسناد هذه الشحنة للسائق المختار؟"
              data-confirm-title="تأكيد إسناد السائق"
              data-confirm-ok="نعم، تأكيد التعيين"
              data-confirm-danger="false">
            @csrf

            <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white">
                <h6 class="m-0 fw-800 text-navy d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat text-accent fs-5"></i>
                    <span>{{ $shipment->driver ? 'تغيير السائق المكلف' : 'إسناد الشحنة لسائق' }}</span>
                </h6>
                <button type="button" class="btn-close" onclick="closeDriverModal()"></button>
            </div>

            <div class="p-4" style="max-height: 65vh; overflow-y: auto;">
                @if($shipment->driver)
                <div class="p-3 mb-3 bg-light rounded-3 border">
                    <div class="fs-8 text-muted fw-bold mb-1">السائق الحالي للشحنة:</div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="fw-bold text-navy fs-6">{{ $shipment->driver->name }}</div>
                        <span class="pill-count">مكلف حالياً</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">هاتف: {{ $shipment->driver->phone }}</div>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fs-7 fw-bold text-navy mb-2">اختر السائق الجديد من القائمة:</label>
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
                                        <div class="text-navy fw-bold fs-7">{{ $drv->name }}</div>
                                        <small class="text-muted fs-8 font-monospace" dir="ltr">{{ $drv->phone }}</small>
                                        @if($drv->truck)
                                            <small class="text-muted fs-8 ms-2">| شاحنة: {{ $drv->truck->plate_number }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    @if($isCurrent)
                                        <span class="status-badge badge-busy">السائق الحالي</span>
                                    @elseif($isAvailable)
                                        <span class="status-badge badge-available">
                                            <i class="bi bi-check-circle me-1"></i> متاح
                                        </span>
                                    @else
                                        <span class="status-badge badge-busy">
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
                    <label class="form-label fs-7 fw-bold text-navy mb-1">سبب تغيير أو إسناد السائق (اختياري):</label>
                    <textarea name="reason" class="form-control fs-7" rows="2" placeholder="أدخل سبب التغيير لتوثيقه في سجل العمليات..."></textarea>
                </div>
            </div>

            <div class="p-3 bg-light border-top d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-outline-navy btn-sm" onclick="closeDriverModal()">إلغاء</button>
                <button type="submit" class="btn btn-gold btn-sm">
                    <i class="bi bi-check-circle"></i> تأكيد تعيين السائق
                </button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة إلغاء الشحنة --}}
<div class="custom-modal-overlay" id="cancelModalOverlay" onclick="handleCancelBackdrop(event)">
    <div class="custom-modal-dialog">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-800 text-navy d-flex align-items-center gap-2">
                <i class="bi bi-slash-circle text-danger fs-5"></i> إلغاء الشحنة
            </h6>
            <button type="button" class="btn-close" onclick="closeCancelModal()"></button>
        </div>

        <form method="POST" action="{{ route('shipments.cancel', $shipment) }}" id="cancelForm">
            @csrf
            <div class="p-4">
                <div class="alert alert-warning border-0 fs-8 d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-6 text-warning"></i>
                    <div>
                        سيتم تغيير حالة الشحنة إلى <strong>ملغاة</strong> مع الاحتفاظ بكافة بياناتها وسجلاتها، وسيتم تحرير السائق المرتبط بها فوراً إن وُجد.
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fs-7 fw-bold text-navy mb-1">سبب الإلغاء (من الميناء / الإدارة): <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control fs-7" rows="3" required placeholder="يرجى كتابة سبب الإلغاء (مثل: إلغاء من الميناء، خطأ في الحجز، اعتذار العميل...)..."></textarea>
                </div>
            </div>

            <div class="p-3 bg-light border-top d-flex align-items-center justify-content-end gap-2">
                <button type="button" class="btn btn-outline-navy btn-sm" onclick="closeCancelModal()">تراجع</button>
                <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 fw-bold">
                    <i class="bi bi-check-circle"></i> تأكيد إلغاء الشحنة
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDriverModal();
        closeCancelModal();
    }
});
</script>
@endpush
@endsection
