@extends('layouts.app')
@section('title','الشاحنات')
@section('page-title','إدارة الشاحنات')
@section('title-icon','bi-truck')
@section('topbar-actions')
    <a href="{{ route('trucks.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg me-1"></i>إضافة شاحنة
    </a>
@endsection

@section('content')
<div class="table-card">
    <div class="table-header">
        <h6><i class="bi bi-truck-front"></i> الشاحنات ({{ $trucks->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>رقم اللوحة</th>
                    <th>النوع</th>
                    <th>الطاقة الاستيعابية</th>
                    <th>السائق</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trucks as $t)
                <tr>
                    <td class="fw-800" dir="ltr" style="color:var(--secondary);">
                        <i class="bi bi-truck me-1 text-muted"></i>{{ $t->plate_number }}
                    </td>
                    <td>{{ $t->type }}</td>
                    <td>{{ $t->capacity ? $t->capacity.' ط' : '—' }}</td>
                    <td>
                        @if($t->driver)
                            <a href="{{ route('drivers.show',$t->driver) }}"
                               class="text-decoration-none fw-600"
                               style="color:var(--primary);">
                                <i class="bi bi-person-circle text-muted me-1"></i>{{ $t->driver->name }}
                            </a>
                        @else
                            <span class="text-muted fs-xs">غير مرتبطة</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge badge-{{ $t->status }}">
                            {{ \App\Models\Truck::statusLabel($t->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('trucks.edit',$t) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('trucks.destroy',$t) }}"
                                  data-confirm="هل تريد حذف هذه الشاحنة نهائياً؟">
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
                            <i class="bi bi-truck empty-icon"></i>
                            <p>لا توجد شاحنات مضافة</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($trucks->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">{{ $trucks->links() }}</div>
    @endif
</div>
@endsection
