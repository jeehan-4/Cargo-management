@extends('layouts.app')
@section('title', 'الوجهات')
@section('page-title', 'إدارة الوجهات')
@section('title-icon', 'bi-geo-alt')

@section('topbar-actions')
    <a href="{{ route('destinations.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة وجهة
    </a>
@endsection

@section('content')
<div class="destinations-index-container">

    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-geo-alt-fill"></i> قائمة الوجهات الجغرافية
                <span class="pill-count">{{ $destinations->total() }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>المدينة</th>
                        <th>جهة الاتصال</th>
                        <th class="text-center">عدد الشحنات</th>
                        <th>الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($destinations as $d)
                    <tr>
                        <td class="fw-700">{{ $d->name }}</td>
                        <td class="text-muted-sm">{{ $d->city }}</td>
                        <td>
                            @if($d->contact_name)
                                <div class="fw-600">{{ $d->contact_name }}</div>
                                <div class="text-muted-sm fs-xs font-monospace" dir="ltr">{{ $d->contact_phone }}</div>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="pill-count">{{ $d->shipments_count }}</span>
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $d->is_active ? 'active' : 'inactive' }}">
                                <i class="bi {{ $d->is_active ? 'bi-check-circle' : 'bi-dash-circle' }}"></i>
                                {{ $d->is_active ? 'نشطة' : 'موقوفة' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('destinations.edit', $d) }}" class="btn btn-sm btn-outline-navy" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('destinations.destroy', $d) }}"
                                      data-confirm="هل تريد حذف هذه الوجهة؟"
                                      data-confirm-title="حذف وجهة"
                                      class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-geo es-icon"></i>
                                <div class="es-title">لا توجد وجهات مضافة حالياً</div>
                                <div class="es-sub">أضف وجهة جديدة لبدء توجيه الشحنات إليها</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($destinations->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $destinations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
