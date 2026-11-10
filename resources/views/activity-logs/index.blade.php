@extends('layouts.app')
@section('title', 'سجل العمليات')
@section('page-title', 'سجل العمليات')
@section('title-icon', 'bi-clock-history')

@section('content')
<div class="shipments-index-container">

    {{-- FILTERS CARD --}}
    <div class="filter-bar">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">نوع العملية</label>
                <select name="action_type" class="form-select">
                    <option value="">كل العمليات</option>
                    @foreach($actionTypes as $key => $label)
                    <option value="{{ $key }}" {{ request('action_type')==$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">المستخدم</label>
                <select name="user_id" class="form-select">
                    <option value="">كل المستخدمين</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-lg-2 form-actions">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="bi bi-funnel"></i> تصفية
                </button>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-navy" title="إعادة تعيين">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-clock-history"></i> العمليات المسجلة
                <span class="pill-count">{{ $logs->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0">
                <thead>
                    <tr>
                        <th>التاريخ والوقت</th>
                        <th>نوع العملية</th>
                        <th>العنصر</th>
                        <th>المستخدم</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted-sm" dir="ltr">{{ $log->created_at?->format('Y/m/d H:i') }}</td>
                        <td>
                            <span class="sbadge {{ $log->action_badge }}">
                                <i class="bi {{ $log->action_icon }}"></i>
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td class="fw-600">
                            @if($log->shipment_id && $log->shipment)
                                <a href="{{ route('shipments.show', $log->shipment) }}" class="ship-num" dir="ltr">{{ $log->entity_name }}</a>
                            @else
                                {{ $log->entity_name }}
                            @endif
                        </td>
                        <td>
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-person-circle text-muted"></i> {{ $log->user?->name ?? 'النظام' }}
                            </span>
                        </td>
                        <td class="text-muted-sm">{{ $log->changes_summary }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-inbox es-icon"></i>
                                <div class="es-title">لا توجد عمليات مسجلة</div>
                                <div class="es-sub">ستظهر هنا عمليات الإضافة والتعديل والحذف تلقائياً</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection