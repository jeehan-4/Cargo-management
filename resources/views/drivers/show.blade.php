@extends('layouts.app')

@section('title', $driver->name)
@section('page-title', 'ملف السائق')
@section('title-icon', 'bi-person-badge')

@section('topbar-actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-navy btn-sm">
            <i class="bi bi-pencil-square"></i> تعديل البيانات
        </a>
        <a href="{{ route('drivers.index') }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-arrow-right"></i> رجوع للقائمة
        </a>
    </div>
@endsection

@section('content')
<div class="row g-4">

    {{-- بطاقة بيانات السائق --}}
    <div class="col-lg-4">
        <div class="section-card h-100">
            <div class="text-center pb-3 mb-3 border-bottom">
                <div class="avatar-circle mx-auto mb-3" style="width:80px; height:80px; font-size:2rem; background:linear-gradient(135deg, var(--secondary), var(--primary)); color:#fff; border:3px solid var(--accent-md);">
                    {{ mb_substr($driver->name, 0, 1) }}
                </div>
                <h5 class="fw-800 text-navy mb-2">{{ $driver->name }}</h5>
                <span class="status-badge badge-{{ $driver->status }}">
                    <i class="bi {{ \App\Models\Driver::statusIcon($driver->status ?? '') }}"></i>
                    {{ \App\Models\Driver::statusLabel($driver->status) }}
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted-sm d-flex align-items-center gap-2"><i class="bi bi-telephone text-accent"></i> الهاتف</span>
                    <span class="fw-700" dir="ltr">{{ $driver->phone }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted-sm d-flex align-items-center gap-2"><i class="bi bi-card-text text-accent"></i> رقم الرخصة</span>
                    <span class="fw-700" dir="ltr">{{ $driver->license_number }}</span>
                </div>
                @if($driver->national_id)
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted-sm d-flex align-items-center gap-2"><i class="bi bi-person-vcard text-accent"></i> الرقم الوطني</span>
                    <span class="fw-700" dir="ltr">{{ $driver->national_id }}</span>
                </div>
                @endif

                <div class="p-3 rounded-3" style="background:var(--bg); border:1px dashed var(--accent-md);">
                    <div class="text-muted-sm mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-truck text-accent"></i> الشاحنة المرتبطة
                    </div>
                    @if($driver->truck)
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-800 text-navy" dir="ltr">{{ $driver->truck->plate_number }}</span>
                            <span class="sbadge sbadge-active">{{ $driver->truck->type }}</span>
                        </div>
                    @else
                        <div class="text-muted-sm fst-italic">لا توجد شاحنة مرتبطة حالياً</div>
                    @endif
                </div>

                @if($driver->notes)
                <div>
                    <div class="text-muted-sm mb-1">ملاحظات إضافية</div>
                    <div class="p-3 rounded-3 fs-sm" style="background:var(--bg); border:1px solid var(--border);">{{ $driver->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- سجل الشحنات --}}
    <div class="col-lg-8">
        <div class="table-card h-100">
            <div class="table-header">
                <h6 class="section-card-title mb-0"><i class="bi bi-box-seam"></i> سجل الشحنات المكلف بها</h6>
                <span class="pill-count">{{ $driver->shipments->count() }} شحنة</span>
            </div>
            <div class="table-responsive">
                <table class="table tbl mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>رقم الشحنة</th>
                            <th>نوع البضاعة</th>
                            <th>الوجهة</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($driver->shipments->sortByDesc('created_at') as $s)
                        <tr>
                            <td><a href="{{ route('shipments.show', $s) }}" class="ship-num" dir="ltr">{{ $s->shipment_number }}</a></td>
                            <td class="fw-600">{{ $s->cargo_type }}</td>
                            <td><span class="d-inline-flex align-items-center gap-1"><i class="bi bi-geo-alt text-accent"></i> {{ $s->destination?->name ?? '—' }}</span></td>
                            <td>
                                <span class="sbadge sbadge-{{ $s->status }}">
                                    <i class="bi {{ \App\Models\Shipment::statusIcon($s->status) }}"></i>
                                    {{ \App\Models\Shipment::statusLabel($s->status) }}
                                </span>
                            </td>
                            <td class="text-muted-sm">{{ $s->created_at->format('Y/m/d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="es-icon"><i class="bi bi-inbox"></i></div>
                                    <div class="es-title">لا توجد شحنات مسندة</div>
                                    <div class="es-sub">لم يتم إسناد أي شحنة لهذا السائق حتى هذه اللحظة.</div>
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
@endsection
