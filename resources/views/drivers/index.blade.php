@extends('layouts.app')
@section('title', 'السائقون')
@section('page-title', 'إدارة السائقين')

@section('topbar-actions')
    <a href="{{ route('drivers.create') }}" class="btn btn-gold btn-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة سائق
    </a>
@endsection

@section('content')
<style>
/* ─── مكونات خاصة بصفحة السائقين (تعتمد على متغيرات التصميم الموحد) ─── */
.drv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 18px;
}
@media (max-width: 991.98px) {
    .drv-stats { grid-template-columns: repeat(2, 1fr); gap: 10px; }
}
.drv-stat-card {
    background: var(--white);
    border-radius: var(--r-md);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--sh-xs);
    border: 1px solid var(--border);
    transition: var(--tr);
}
.drv-stat-card:hover {
    box-shadow: var(--sh-sm);
    transform: translateY(-2px);
}
.drv-stat-icon {
    width: 46px; height: 46px;
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.drv-stat-icon.total    { background: rgba(32,40,51,0.08);  color: var(--primary); }
.drv-stat-icon.avail    { background: rgba(16,185,129,0.10); color: #059669; }
.drv-stat-icon.busy     { background: rgba(245,158,11,0.12); color: #D97706; }
.drv-stat-icon.inactive { background: rgba(239,68,68,0.10);  color: #DC2626; }
.drv-stat-num {
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
    color: var(--primary);
}
.drv-stat-lbl {
    font-size: 12px;
    color: var(--muted);
    margin-top: 3px;
    font-weight: 500;
}

/* بحث داخل حقل الإدخال */
.drv-search-wrap { position: relative; }
.drv-search-wrap .search-icon {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    left: 13px;
    color: var(--muted);
    font-size: 15px;
    pointer-events: none;
}
.drv-search-wrap .form-control { padding-left: 38px; }

/* عناصر الجدول الخاصة */
.drv-name-link {
    font-weight: 700;
    font-size: 14px;
    color: var(--primary);
    text-decoration: none;
    transition: color .15s;
}
.drv-name-link:hover { color: var(--accent); }
.drv-linked-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #065F46;
    background: rgba(16,185,129,0.08);
    border-radius: 10px;
    padding: 1px 8px;
    margin-top: 2px;
}
.drv-mono {
    font-size: 13px;
    color: var(--secondary);
    font-variant-numeric: tabular-nums;
    direction: ltr;
    display: inline-block;
}
.drv-truck-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--bg);
    border: 1px solid var(--border-md);
    border-radius: var(--r-xs);
    padding: 4px 10px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--secondary);
}
.drv-truck-badge i { color: var(--muted); }
</style>

@php
    $total     = $drivers->total();
    $available = \App\Models\Driver::where('status','available')->count();
    $busy      = \App\Models\Driver::where('status','busy')->count();
    $inactive  = \App\Models\Driver::where('status','inactive')->count();
@endphp

<div dir="rtl">

    {{-- شريط الإحصائيات --}}
    <div class="drv-stats">
        <div class="drv-stat-card">
            <div class="drv-stat-icon total"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="drv-stat-num">{{ \App\Models\Driver::count() }}</div>
                <div class="drv-stat-lbl">إجمالي السائقين</div>
            </div>
        </div>
        <div class="drv-stat-card">
            <div class="drv-stat-icon avail"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="drv-stat-num">{{ $available }}</div>
                <div class="drv-stat-lbl">متاح</div>
            </div>
        </div>
        <div class="drv-stat-card">
            <div class="drv-stat-icon busy"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="drv-stat-num">{{ $busy }}</div>
                <div class="drv-stat-lbl">مشغول</div>
            </div>
        </div>
        <div class="drv-stat-card">
            <div class="drv-stat-icon inactive"><i class="bi bi-pause-circle-fill"></i></div>
            <div>
                <div class="drv-stat-num">{{ $inactive }}</div>
                <div class="drv-stat-lbl">غير نشط</div>
            </div>
        </div>
    </div>

    {{-- لوحة التصفية --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('drivers.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-5 col-md-6">
                <label class="form-label">بحث في السائقين</label>
                <div class="drv-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search"
                           class="form-control"
                           placeholder="ابحث بالاسم، الهاتف، أو الرخصة..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label">حالة السائق</label>
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="available" {{ request('status')=='available'?'selected':'' }}>متاح</option>
                    <option value="busy"      {{ request('status')=='busy'     ?'selected':'' }}>مشغول</option>
                    <option value="inactive"  {{ request('status')=='inactive' ?'selected':'' }}>غير نشط</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-navy">
                    <i class="bi bi-search me-1"></i> بحث
                </button>
                @if(request('status') || request('search'))
                    <a href="{{ route('drivers.index') }}" class="btn btn-outline-navy" title="مسح">
                        <i class="bi bi-x-circle"></i> مسح
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- بطاقة الجدول --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-card-title">
                <i class="bi bi-person-lines-fill"></i> قائمة السائقين
                <span class="pill-count">{{ $drivers->total() }} سائق</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table tbl mb-0">
                <thead>
                    <tr>
                        <th>السائق</th>
                        <th class="text-center">رقم الهاتف</th>
                        <th class="text-center">رقم الرخصة</th>
                        <th class="text-center">الشاحنة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($drivers as $d)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle" style="width:42px;height:42px;font-size:16px;">{{ mb_substr($d->name, 0, 1) }}</div>
                                <div>
                                    <a href="{{ route('drivers.show', $d) }}" class="drv-name-link d-block">
                                        {{ $d->name }}
                                    </a>
                                    @if($d->user)
                                        <span class="drv-linked-badge">
                                            <i class="bi bi-check-circle-fill" style="font-size:10px;"></i>
                                            مرتبط بحساب
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            @if($d->phone)
                                <span class="drv-mono">{{ $d->phone }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($d->license_number)
                                <span class="drv-mono">{{ $d->license_number }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($d->truck)
                                <span class="drv-truck-badge">
                                    <i class="bi bi-truck"></i>
                                    {{ $d->truck->plate_number }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="status-badge badge-{{ $d->status }}">
                                {{ \App\Models\Driver::statusLabel($d->status) }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('drivers.show', $d) }}" class="btn btn-sm btn-outline-navy" title="عرض">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('drivers.edit', $d) }}" class="btn btn-sm btn-outline-navy" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="{{ route('drivers.destroy', $d) }}" class="d-inline"
                                      data-confirm="هل أنت متأكد من حذف هذا السائق نهائياً؟"
                                      data-confirm-title="حذف سائق">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-person-x es-icon"></i>
                                <div class="es-title">لا يوجد سائقون مسجلون</div>
                                <div class="es-sub">ابدأ بإضافة سائقين جدد لتراهم هنا</div>
                                <a href="{{ route('drivers.create') }}" class="btn btn-gold btn-sm mt-3">
                                    <i class="bi bi-plus-lg me-1"></i> إضافة أول سائق
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($drivers->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $drivers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
