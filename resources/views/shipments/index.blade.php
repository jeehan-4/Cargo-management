@extends('layouts.app')
@section('title', 'قائمة الشحنات')
@section('page-title', 'إدارة الشحنات')
@section('title-icon', 'bi-box-seam')

@section('topbar-actions')
    <a href="{{ route('shipments.export') }}" class="btn btn-outline-navy btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> تصدير إكسل
    </a>
    <a href="{{ route('shipments.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-plus-lg me-1"></i> شحنة جديدة
    </a>
@endsection

@section('content')
<div class="shipments-index-container">

    {{-- FILTERS CARD --}}
    <div class="filter-bar">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">كل الحالات</option>
                    @foreach(['pending'=>'بانتظار التوزيع','assigned'=>'قيد التوصيل','delivered'=>'تم التسليم','cancelled'=>'ملغاة','archived'=>'مؤرشفة','failed'=>'متعذر التسليم'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">السائق</label>
                <select name="driver_id" class="form-select">
                    <option value="">كل السائقين</option>
                    @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ request('driver_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
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
            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="bi bi-funnel"></i> تصفية
                </button>
                <a href="{{ route('shipments.index') }}" class="btn btn-outline-navy" title="إعادة تعيين">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-list-ul"></i> الشحنات
                <span class="pill-count">{{ $shipments->total() }}</span>
            </div>
            <div class="d-flex gap-2">
                @if(isset($archivedCount) && $archivedCount > 0)
                <a href="{{ route('shipments.index', ['status' => 'archived']) }}" class="btn btn-outline-navy btn-sm" title="عرض الشحنات المؤرشفة">
                    <i class="bi bi-archive-fill"></i> الأرشيف ({{ $archivedCount }})
                </a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0">
                <thead>
                    <tr>
                        <th>رقم الشحنة</th>
                        <th>نوع البضاعة</th>
                        <th>الكمية</th>
                        <th>الوجهة</th>
                        <th>السائق</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>
                            <a href="{{ route('shipments.show',$s) }}" class="ship-num" dir="ltr">
                                {{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="fw-600">{{ $s->cargo_type }}</td>
                        <td class="text-muted-sm">
                            {{ $s->quantity ? $s->quantity.' '.$s->unit : ($s->weight ? $s->weight.' طن' : '—') }}
                        </td>
                        <td>
                            @if($s->destination)
                                <i class="bi bi-geo-alt text-muted me-1"></i>{{ $s->destination->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($s->driver)
                                <span class="d-flex align-items-center gap-1 fw-600">
                                    <i class="bi bi-person-circle text-muted"></i> {{ $s->driver->name }}
                                </span>
                            @else
                                <span class="pill-count">غير معيَّن</span>
                            @endif
                        </td>
                        <td>
                            <span class="sbadge sbadge-{{ $s->status }}">
                                <i class="bi {{ \App\Models\Shipment::statusIcon($s->status) }}"></i>
                                {{ \App\Models\Shipment::statusLabel($s->status) }}
                            </span>
                        </td>
                        <td class="text-muted-sm">{{ $s->created_at->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('shipments.show',$s) }}" class="btn btn-sm btn-outline-navy" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('shipments.edit',$s) }}" class="btn btn-sm btn-outline-navy" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($s->status === 'pending' && !$s->driver_id)
                                <form method="POST" action="{{ route('shipments.destroy', $s) }}"
                                      data-confirm="تنبيه حذف نهائي: هذه الشحنة مضافة بالخطأ ولم تسند لأي سائق. هل أنت متأكد من حذفها نهائياً من النظام؟"
                                      data-confirm-title="حذف الشحنة نهائياً"
                                      class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="حذف الشحنة المضافة بالخطأ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @elseif(in_array($s->status, ['delivered', 'failed', 'cancelled']))
                                <form method="POST" action="{{ route('shipments.archive', $s) }}"
                                      data-confirm="هل تريد نقل هذه الشحنة المكتملة أو الملغاة إلى الأرشيف لحفظ السجلات؟"
                                      data-confirm-title="أرشفة الشحنة"
                                      data-confirm-danger="false"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-navy" title="أرشفة الشحنة (نقل إلى الأرشيف)">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-inbox es-icon"></i>
                                <div class="es-title">لا توجد شحنات تطابق نتائج البحث</div>
                                <div class="es-sub">جرّب تغيير الفلاتر أو أضف شحنة جديدة</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
