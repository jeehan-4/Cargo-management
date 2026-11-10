@extends('layouts.app')
@section('title', 'توزيع الشحنات')
@section('page-title', 'لوحة توزيع الشحنات')

@section('topbar-actions')
    <a href="{{ route('shipments.index') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-list-ul me-1"></i> قائمة الشحنات
    </a>
@endsection

@section('content')
<style>
/* ─── مكونات خاصة بلوحة التوزيع (تعتمد على متغيرات التصميم الموحد) ─── */
.board-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* بطاقات الإحصائيات */
.stat-card-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 18px;
}
@media (max-width: 991.98px) {
    .stat-card-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575.98px) {
    .stat-card-grid { grid-template-columns: 1fr; }
}
.board-stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 20px;
    box-shadow: var(--sh-xs);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.board-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0; height: 3px;
    background: var(--accent); opacity: 0.5;
}
.stat-icon-wrapper {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}

/* مؤشر الخطوات */
.step-indicator-bar {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
    background: var(--white);
    padding: 16px 24px;
    border-radius: var(--r-md);
    border: 1px solid var(--border);
    box-shadow: var(--sh-xs);
}
.step-item {
    display: flex; align-items: center; gap: 10px; opacity: 0.4; transition: opacity 0.2s ease;
}
.step-item.active { opacity: 1; }
.step-item.completed { opacity: 0.85; }
.step-number {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--bg); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 14px; border: 1.5px solid var(--border-md);
}
.step-item.active .step-number {
    background: var(--accent); color: var(--primary); border-color: var(--accent);
}
.step-item.completed .step-number {
    background: #10B981; color: #fff; border-color: #10B981;
}

/* شريط البحث والتصفية */
.filter-toolbar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;
}
.search-input-group {
    flex: 1; min-width: 260px; position: relative;
}
.search-input-group input {
    background: #FAFAF8; border: 1px solid var(--border-md);
    border-radius: var(--r-sm); padding: 10px 14px 10px 40px; font-size: .86rem; width: 100%; color: var(--primary);
    font-family: 'Cairo', sans-serif;
}
.search-input-group input:focus {
    border-color: var(--secondary);
    box-shadow: 0 0 0 3px rgba(65,90,102,0.10);
    background: #fff;
    outline: none;
}
.search-input-group i {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);
}
.filter-select {
    background: #FAFAF8; border: 1px solid var(--border-md);
    border-radius: var(--r-sm); padding: 10px 16px; font-size: .86rem; color: var(--primary);
    font-family: 'Cairo', sans-serif;
}

/* الصف المحدد في جدول الشحنات */
.table.tbl tbody tr.selected-row {
    background-color: rgba(199, 166, 103, 0.08) !important;
    border-right: 4px solid var(--accent);
}

/* شبكة بطاقات السائقين */
.drivers-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px;
}
@media (max-width: 991.98px) { .drivers-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575.98px) { .drivers-grid { grid-template-columns: 1fr; } }

.driver-card {
    background: var(--white); border: 1.5px solid var(--border-md);
    border-radius: var(--r-md); padding: 18px; position: relative; transition: all 0.2s ease; cursor: pointer;
}
.driver-card:hover:not(.disabled) {
    border-color: var(--accent); box-shadow: 0 4px 16px rgba(199, 166, 103, 0.15);
}
.driver-card.selected-driver {
    border-color: var(--accent) !important; background: rgba(199, 166, 103, 0.04);
    box-shadow: 0 0 0 2px rgba(199, 166, 103, 0.2);
}
.driver-card.disabled {
    opacity: 0.5; background: #f9f9f9; cursor: not-allowed;
}
.check-icon-badge {
    position: absolute; top: 14px; left: 14px;
    background: var(--accent); color: var(--primary);
    width: 24px; height: 24px; border-radius: 50%;
    display: none; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;
}
.driver-card.selected-driver .check-icon-badge { display: flex; }

/* ملخص العملية */
.summary-box {
    background: var(--bg); border: 1px dashed var(--accent);
    border-radius: var(--r-md); padding: 20px 24px;
    display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; align-items: center;
}
@media (max-width: 991.98px) { .summary-box { grid-template-columns: repeat(2, 1fr); } }
.summary-item .label { font-size: 11.5px; color: var(--muted); font-weight: 600; margin-bottom: 4px; }
.summary-item .val { font-size: 14.5px; font-weight: 800; color: var(--primary); }

/* إشعار النجاح */
.toast-container-board { position: fixed; bottom: 30px; left: 30px; z-index: 3000; }
.board-toast {
    background: var(--primary); color: #fff; padding: 14px 24px; border-radius: var(--r-sm);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px;
    transform: translateY(100px); opacity: 0; transition: all 0.3s ease;
}
.board-toast.show { transform: translateY(0); opacity: 1; }
</style>

<div class="board-container" dir="rtl" id="distributionApp">

    <!-- مؤشر الخطوات -->
    <div class="step-indicator-bar">
        <div class="step-item active" id="stepIndicator1">
            <div class="step-number">1</div>
            <div>
                <div class="fw-bold fs-7 text-navy">الخطوة الأولى</div>
                <div class="fs-8 text-muted">اختيار الشحنة</div>
            </div>
        </div>
        <div class="step-item" id="stepIndicator2">
            <div class="step-number">2</div>
            <div>
                <div class="fw-bold fs-7 text-navy">الخطوة الثانية</div>
                <div class="fs-8 text-muted">اختيار السائق</div>
            </div>
        </div>
        <div class="step-item" id="stepIndicator3">
            <div class="step-number">3</div>
            <div>
                <div class="fw-bold fs-7 text-navy">الخطوة الثالثة</div>
                <div class="fs-8 text-muted">تأكيد التوزيع</div>
            </div>
        </div>
    </div>

    <!-- 4 بطاقات إحصائية -->
    <div class="stat-card-grid">
        <div class="board-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">الشحنات بانتظار التوزيع</div>
                <div class="fs-4 fw-900 text-navy" id="statPending">{{ $shipments->whereIn('status', ['pending', 'assigned'])->count() }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(245,158,11,0.10); color: #92400E;">
                <i class="bi bi-clock"></i>
            </div>
        </div>

        <div class="board-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">تعيين سائق لها</div>
                <div class="fs-4 fw-900 text-navy" id="statAssigned">{{ $shipments->where('status', 'assigned')->count() }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(59,130,246,0.10); color: #1E40AF;">
                <i class="bi bi-person-check"></i>
            </div>
        </div>

        <div class="board-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">السائقون المتاحون</div>
                <div class="fs-4 fw-900 text-navy" id="statAvailable">
                    {{ $drivers->filter(fn($d) => method_exists($d, 'isAvailable') ? $d->isAvailable() : $d->status === 'available')->count() }}
                </div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(16,185,129,0.10); color: #065F46;">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>

        <div class="board-stat-card">
            <div>
                <div class="text-muted fs-8 fw-semibold mb-1">السائقون المشغولون</div>
                <div class="fs-4 fw-900 text-navy" id="statBusy">
                    {{ $busyDrivers }}
                </div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(239,68,68,0.10); color: #991B1B;">
                <i class="bi bi-person-dash"></i>
            </div>
        </div>
    </div>

    <!-- الخطوة 1: جدول الشحنات -->
    <div class="section-card mb-4" id="step1Card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-1-circle-fill"></i> الشحنات بانتظار التوزيع
                <span class="pill-count">{{ $shipments->count() }}</span>
            </div>
            <span class="sbadge sbadge-pending">
                <i class="bi bi-clock"></i> بانتظار اتخاذ إجراء
            </span>
        </div>

        <!-- شريط البحث والتصفية -->
        <div class="p-4 pt-3">
            <div class="filter-toolbar mb-0">
                <div class="search-input-group">
                    <input type="text" id="shipmentSearch" placeholder="ابحث برقم الشحنة، الوجهة، أو نوع البضاعة..." onkeyup="filterShipments()">
                    <i class="bi bi-search"></i>
                </div>
                <div>
                    <select id="destinationFilter" class="filter-select" onchange="filterShipments()">
                        <option value="">جميع الوجهات</option>
                        @foreach($shipments->pluck('destination.name')->unique()->filter() as $dest)
                            <option value="{{ $dest }}">{{ $dest }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="cargoFilter" class="filter-select" onchange="filterShipments()">
                        <option value="">جميع أنواع البضائع</option>
                        @foreach($shipments->pluck('cargo_type')->unique()->filter() as $cargo)
                            <option value="{{ $cargo }}">{{ $cargo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0" id="shipmentsTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">تحديد</th>
                        <th>رقم الشحنة</th>
                        <th class="text-center">الوجهة</th>
                        <th class="text-center">نوع البضاعة</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">تاريخ الشحنة</th>
                        <th class="text-center">حالة الشحنة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $sh)
                    <tr onclick="selectShipment(this, '{{ $sh->id }}', '{{ $sh->shipment_number }}', '{{ $sh->destination?->name ?? 'غير محددة' }}', '{{ $sh->cargo_type }}', '{{ $sh->quantity ? $sh->quantity . ' ' . $sh->unit : '—' }}')"
                        id="shipment-row-{{ $sh->id }}"
                        data-number="{{ $sh->shipment_number }}"
                        data-destination="{{ $sh->destination?->name ?? '' }}"
                        data-cargo="{{ $sh->cargo_type }}">
                        <td class="text-center">
                            <input type="radio" name="selected_shipment" class="form-check-input shipment-radio" value="{{ $sh->id }}">
                        </td>
                        <td class="fw-bold font-monospace">#{{ $sh->shipment_number }}</td>
                        <td class="text-center text-muted-sm">{{ $sh->destination?->name ?? '—' }}</td>
                        <td class="text-center fw-semibold">{{ $sh->cargo_type }}</td>
                        <td class="text-center font-monospace">{{ $sh->quantity ? $sh->quantity . ' ' . $sh->unit : '—' }}</td>
                        <td class="text-center text-muted-sm">{{ $sh->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <span class="sbadge sbadge-{{ $sh->status }}">
                                <i class="bi {{ \App\Models\Shipment::statusIcon($sh->status) }}"></i>
                                {{ \App\Models\Shipment::statusLabel($sh->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-inbox es-icon"></i>
                                <div class="es-title">لا توجد شحنات بانتظار التوزيع حالياً</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- الخطوة 2: اختيار السائق -->
    <div class="section-card mb-4" id="step2Card" style="opacity: 0.6; pointer-events: none;">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-2-circle-fill"></i> اختيار السائق المناسب
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="availableOnlySwitch" onchange="toggleAvailableDrivers()">
                    <label class="form-check-label fs-8 fw-bold text-muted me-2" for="availableOnlySwitch">عرض المتاحين فقط</label>
                </div>
                <span class="text-muted fs-8" id="driverStepNotice">يرجى اختيار شحنة أولاً</span>
            </div>
        </div>

        <div class="p-4">
            <div class="drivers-grid" id="driversGrid">
                @forelse($drivers as $driver)
                    @php
                        $isAvailable = method_exists($driver, 'isAvailable') ? $driver->isAvailable() : ($driver->status === 'available');
                    @endphp
                    <div class="driver-card {{ !$isAvailable ? 'disabled' : '' }}"
                         id="driver-card-{{ $driver->id }}"
                         data-available="{{ $isAvailable ? 'true' : 'false' }}"
                         @if($isAvailable) onclick="selectDriver(this, '{{ $driver->id }}', '{{ $driver->name }}', '{{ $driver->truck?->plate_number ?? 'بدون شاحنة' }}')" @endif>

                        <div class="check-icon-badge"><i class="bi bi-check"></i></div>

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle" style="width:48px;height:48px;font-size:18px;">{{ mb_substr($driver->name, 0, 1) }}</div>
                                <div>
                                    <div class="fw-bold text-navy fs-6">{{ $driver->name }}</div>
                                    <small class="text-muted fs-8 font-monospace" dir="ltr">{{ $driver->phone }}</small>
                                </div>
                            </div>
                            <div>
                                @if($isAvailable)
                                    <span class="sbadge sbadge-available">
                                        <i class="bi bi-check-circle"></i> متاح
                                    </span>
                                @else
                                    <span class="sbadge sbadge-busy" onclick="alertBusyDriver(event)">
                                        <i class="bi bi-dash-circle"></i> مشغول
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-light p-2 rounded-3 d-flex align-items-center justify-content-between fs-8 text-muted">
                            <span><i class="bi bi-truck me-1"></i> رقم الشاحنة:</span>
                            <span class="fw-bold text-navy font-monospace">{{ $driver->truck?->plate_number ?? 'غير مرتبطة' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">لا توجد بيانات للسائقين.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- الخطوة 3: الملخص والتأكيد -->
    <div class="section-card" id="step3Card" style="opacity: 0.6; pointer-events: none;">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-3-circle-fill"></i> ملخص عملية التوزيع
            </div>
        </div>

        <div class="p-4">
            <div class="summary-box mb-4">
                <div class="summary-item">
                    <div class="label">رقم الشحنة</div>
                    <div class="val" id="summaryShipmentNum">—</div>
                </div>
                <div class="summary-item">
                    <div class="label">الوجهة</div>
                    <div class="val" id="summaryDestination">—</div>
                </div>
                <div class="summary-item">
                    <div class="label">نوع البضاعة</div>
                    <div class="val" id="summaryCargo">—</div>
                </div>
                <div class="summary-item">
                    <div class="label">الكمية</div>
                    <div class="val" id="summaryQuantity">—</div>
                </div>
                <div class="summary-item">
                    <div class="label">اسم السائق</div>
                    <div class="val text-accent" id="summaryDriverName">—</div>
                </div>
                <div class="summary-item">
                    <div class="label">رقم الشاحنة</div>
                    <div class="val" id="summaryTruck">—</div>
                </div>
            </div>

            <form action="" method="POST" id="assignmentForm">
                @csrf
                <input type="hidden" name="driver_id" id="formDriverId">

                <button type="button" class="btn btn-gold w-100 py-3" id="submitAssignBtn" disabled onclick="handleDistributionAction()">
                    <i class="bi bi-check2-all fs-5"></i> تعيين السائق للشحنة
                </button>
            </form>
        </div>
    </div>

</div>

<!-- نافذة تأكيد التعيين -->
<div class="custom-modal-overlay" id="confirmModal">
    <div class="custom-modal-dialog">
        <div class="p-4 border-bottom bg-light d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-800 text-navy d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-accent fs-4"></i> تأكيد تعيين السائق
            </h6>
            <button type="button" class="btn-close" onclick="closeConfirmModal()"></button>
        </div>
        <div class="p-4">
            <p class="text-muted fs-7 mb-4">أنت على وشك إسناد الشحنة المحددة للسائق المختار. سيتم تحديث حالات الشحنة والسائق تلقائياً.</p>

            <div class="bg-light p-3 rounded-3 mb-4 fs-7">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">رقم الشحنة:</span>
                    <span class="fw-bold font-monospace" id="modalShipmentNum"></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">وجهة الوصول:</span>
                    <span class="fw-bold" id="modalDestination"></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">السائق المكلف:</span>
                    <span class="fw-bold text-accent" id="modalDriverName"></span>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-navy btn-sm" onclick="closeConfirmModal()">إلغاء</button>
                <button type="button" class="btn btn-gold btn-sm" onclick="executeSuccessfulAssignment()">تأكيد الاعتماد</button>
            </div>
        </div>
    </div>
</div>

<!-- إشعار النجاح -->
<div class="toast-container-board">
    <div class="board-toast" id="successToast">
        <i class="bi bi-check-circle-fill text-success fs-4"></i>
        <div>
            <div class="fw-bold">تم التوزيع بنجاح!</div>
            <div class="fs-8 text-muted" id="toastMessage">تم تعيين السائق وتحديث حالات النظام بدقة.</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedShipmentId = null;
let selectedDriverId = null;
let selectedShipmentRow = null;

function selectShipment(row, id, number, destination, cargo, quantity) {
    document.querySelectorAll('#shipmentsTable tr').forEach(tr => tr.classList.remove('selected-row'));
    document.querySelectorAll('.shipment-radio').forEach(r => r.checked = false);

    row.classList.add('selected-row');
    const radio = row.querySelector('.shipment-radio');
    if (radio) radio.checked = true;

    selectedShipmentId = id;
    selectedShipmentRow = row;

    document.getElementById('summaryShipmentNum').innerText = '#' + number;
    document.getElementById('summaryDestination').innerText = destination;
    document.getElementById('summaryCargo').innerText = cargo;
    document.getElementById('summaryQuantity').innerText = quantity;

    const step2 = document.getElementById('step2Card');
    step2.style.opacity = '1';
    step2.style.pointerEvents = 'auto';
    document.getElementById('driverStepNotice').innerText = 'اختر سائقاً متاحاً أدناه';

    updateIndicators(2);
}

function selectDriver(card, id, name, truck) {
    if (card.classList.contains('disabled')) {
        showErrorValidation('عذراً، هذا السائق مشغول حالياً (في مهمة أخرى) ولا يمكن اختياره.');
        return;
    }

    if (!selectedShipmentId) {
        showErrorValidation('الرجاء اختيار الشحنة في الخطوة الأولى أولاً.');
        return;
    }

    document.querySelectorAll('.driver-card').forEach(c => c.classList.remove('selected-driver'));

    card.classList.add('selected-driver');
    selectedDriverId = id;
    document.getElementById('formDriverId').value = id;

    document.getElementById('summaryDriverName').innerText = name;
    document.getElementById('summaryTruck').innerText = truck;

    const step3 = document.getElementById('step3Card');
    step3.style.opacity = '1';
    step3.style.pointerEvents = 'auto';
    document.getElementById('submitAssignBtn').removeAttribute('disabled');

    updateIndicators(3);
}

function alertBusyDriver(e) {
    e.stopPropagation();
    showErrorValidation('هذا السائق مشغول حالياً في مهمة أخرى، زر التعيين له معطل.');
}

function updateIndicators(step) {
    const s1 = document.getElementById('stepIndicator1');
    const s2 = document.getElementById('stepIndicator2');
    const s3 = document.getElementById('stepIndicator3');

    s1.className = 'step-item completed';
    if (step >= 2) s2.className = 'step-item active';
    if (step >= 3) {
        s2.className = 'step-item completed';
        s3.className = 'step-item active';
    }
}

function filterShipments() {
    const searchVal = document.getElementById('shipmentSearch').value.toLowerCase();
    const destVal = document.getElementById('destinationFilter').value;
    const cargoVal = document.getElementById('cargoFilter').value;

    const rows = document.querySelectorAll('#shipmentsTable tbody tr');
    rows.forEach(row => {
        if (!row.dataset.number) return;
        const number = row.dataset.number.toLowerCase();
        const destination = row.dataset.destination;
        const cargo = row.dataset.cargo;

        const matchSearch = number.includes(searchVal) || destination.toLowerCase().includes(searchVal) || cargo.toLowerCase().includes(searchVal);
        const matchDest = !destVal || destination === destVal;
        const matchCargo = !cargoVal || cargo === cargoVal;

        if (matchSearch && matchDest && matchCargo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function toggleAvailableDrivers() {
    const onlyAvailable = document.getElementById('availableOnlySwitch').checked;
    const cards = document.querySelectorAll('.drivers-grid .driver-card');

    cards.forEach(card => {
        const isAvailable = card.dataset.available === 'true';
        if (onlyAvailable && !isAvailable) {
            card.style.display = 'none';
        } else {
            card.style.display = 'block';
        }
    });
}

function handleDistributionAction() {
    if (!selectedShipmentId) {
        showErrorValidation('الرجاء اختيار شحنة أولاً.');
        return;
    }
    if (!selectedDriverId) {
        showErrorValidation('الرجاء اختيار سائق لتنفيذ التوزيع.');
        return;
    }

    document.getElementById('modalShipmentNum').innerText = document.getElementById('summaryShipmentNum').innerText;
    document.getElementById('modalDestination').innerText = document.getElementById('summaryDestination').innerText;
    document.getElementById('modalDriverName').innerText = document.getElementById('summaryDriverName').innerText;

    document.getElementById('confirmModal').classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('active');
}

function executeSuccessfulAssignment() {
    closeConfirmModal();

    const toast = document.getElementById('successToast');
    toast.classList.add('show');

    if (selectedShipmentRow) {
        selectedShipmentRow.style.transition = 'all 0.4s ease';
        selectedShipmentRow.style.transform = 'scale(0.95)';
        selectedShipmentRow.style.opacity = '0';
        setTimeout(() => {
            selectedShipmentRow.remove();
            const tbody = document.querySelector('#shipmentsTable tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox display-5 opacity-25 d-block mb-2"></i><h6 class="fw-bold text-secondary">لا توجد شحنات بانتظار التوزيع حالياً</h6></td></tr>';
            }
        }, 400);
    }

    const statPending = document.getElementById('statPending');
    const statAssigned = document.getElementById('statAssigned');
    const statAvailable = document.getElementById('statAvailable');
    const statBusy = document.getElementById('statBusy');

    if (statPending) statPending.innerText = Math.max(0, parseInt(statPending.innerText) - 1);
    if (statAssigned) statAssigned.innerText = parseInt(statAssigned.innerText) + 1;
    if (statAvailable) statAvailable.innerText = Math.max(0, parseInt(statAvailable.innerText) - 1);
    if (statBusy) statBusy.innerText = parseInt(statBusy.innerText) + 1;

    if (selectedDriverId) {
        const driverCard = document.getElementById('driver-card-' + selectedDriverId);
        if (driverCard) {
            driverCard.classList.add('disabled');
            driverCard.removeAttribute('onclick');
            driverCard.dataset.available = 'false';
            const badge = driverCard.querySelector('.sbadge');
            if (badge) {
                badge.className = 'sbadge sbadge-busy';
                badge.innerHTML = '<i class="bi bi-dash-circle"></i> مشغول';
            }
        }
    }

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3500);

    setTimeout(() => {
        const form = document.getElementById('assignmentForm');
        form.action = '/shipments/' + selectedShipmentId + '/assign';
        form.submit();
    }, 1200);
}

function showErrorValidation(msg) {
    alert(msg);
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmModal();
    }
});
</script>
@endpush
@endsection
