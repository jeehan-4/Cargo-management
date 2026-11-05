@extends('layouts.app')
@section('title', $truck->plate_number)
@section('page-title', 'بيانات الشاحنة ' . $truck->plate_number)
@section('title-icon','bi-truck')
@section('topbar-actions')
    <a href="{{ route('trucks.edit', $truck) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-pencil me-1"></i>تعديل
    </a>
    <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="table-card">
            <div class="table-header">
                <h6><i class="bi bi-truck-front"></i> بطاقة الشاحنة</h6>
                <span class="status-badge badge-{{ $truck->status }}">
                    {{ \App\Models\Truck::statusLabel($truck->status) }}
                </span>
            </div>
            <div class="p-4">
                <div class="text-center mb-4">
                    <div style="width:75px;height:75px;background:var(--accent-lt);border:2px solid var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:auto;font-size:2rem;color:var(--accent);">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5 class="mt-3 mb-1 fw-800" dir="ltr">{{ $truck->plate_number }}</h5>
                    <p class="text-muted mb-0">{{ $truck->type }}</p>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted fs-xs">رقم اللوحة</div>
                        <div class="fw-700" dir="ltr">{{ $truck->plate_number }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-xs">نوع الشاحنة</div>
                        <div class="fw-700">{{ $truck->type }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-xs">السعة والحمولة</div>
                        <div class="fw-700">{{ $truck->capacity ? $truck->capacity . ' طن' : '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted fs-xs">السائق المعين</div>
                        <div class="fw-700">
                            @if($truck->driver)
                                <a href="{{ route('drivers.show', $truck->driver) }}" class="text-decoration-none" style="color:var(--secondary);">
                                    <i class="bi bi-person me-1"></i>{{ $truck->driver->name }}
                                </a>
                            @else
                                <span class="text-muted">غير معين</span>
                            @endif
                        </div>
                    </div>
                    @if($truck->notes)
                    <div class="col-12">
                        <div class="text-muted fs-xs">ملاحظات</div>
                        <div class="p-2 rounded fs-sm" style="background:var(--bg);">{{ $truck->notes }}</div>
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('trucks.edit', $truck) }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-pencil me-1"></i>تعديل الشاحنة
                    </a>
                    <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
