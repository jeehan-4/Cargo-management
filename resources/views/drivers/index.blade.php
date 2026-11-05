@extends('layouts.app')
@section('title', 'السائقون')
@section('page-title', 'إدارة السائقين')
@section('title-icon', 'bi-person-badge')

@section('topbar-actions')
    <a href="{{ route('drivers.create') }}" class="drv-add-btn">
        <i class="bi bi-plus-lg"></i>
        <span>إضافة سائق</span>
    </a>
@endsection

@section('content')
<style>
/* ══════════════════════════════════════════
   DRIVERS PAGE — ELEGANT REDESIGN
══════════════════════════════════════════ */

/* Add Button */
.drv-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: linear-gradient(135deg, var(--accent) 0%, #b8924f 100%);
    color: #fff;
    font-weight: 700;
    font-size: 13.5px;
    padding: 9px 20px;
    border-radius: var(--r-sm);
    box-shadow: 0 4px 14px rgba(199,166,103,0.35);
    transition: var(--tr);
    border: none;
}
.drv-add-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(199,166,103,0.45);
    color: #fff;
}

/* ── Stats Strip ── */
.drv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 22px;
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
.drv-stat-icon.total   { background: rgba(32,40,51,0.08);  color: var(--primary); }
.drv-stat-icon.avail   { background: rgba(25,135,84,0.10); color: #198754; }
.drv-stat-icon.busy    { background: rgba(255,170,0,0.12); color: #cc8800; }
.drv-stat-icon.inactive{ background: rgba(220,53,69,0.10); color: #dc3545; }
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

/* ── Filter Panel ── */
.drv-filter {
    background: var(--white);
    border-radius: var(--r-md);
    padding: 20px 24px;
    box-shadow: var(--sh-xs);
    border: 1px solid var(--border);
    margin-bottom: 20px;
}
.drv-filter .form-control,
.drv-filter .form-select {
    background: var(--bg);
    border: 1.5px solid var(--border-md);
    border-radius: var(--r-sm);
    font-family: 'Cairo', sans-serif;
    font-size: 13.5px;
    color: var(--primary);
    padding: 9px 14px;
    transition: var(--tr-fast);
}
.drv-filter .form-control:focus,
.drv-filter .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(199,166,103,0.15);
    background: var(--white);
}
.drv-filter label {
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 6px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .5px;
}
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

.drv-btn-search {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--r-sm);
    padding: 9px 22px;
    font-family: 'Cairo', sans-serif;
    font-weight: 600;
    font-size: 13.5px;
    transition: var(--tr-fast);
    cursor: pointer;
}
.drv-btn-search:hover { background: var(--secondary); }

.drv-btn-reset {
    background: transparent;
    color: var(--muted);
    border: 1.5px solid var(--border-md);
    border-radius: var(--r-sm);
    padding: 9px 18px;
    font-family: 'Cairo', sans-serif;
    font-size: 13.5px;
    transition: var(--tr-fast);
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
}
.drv-btn-reset:hover {
    border-color: var(--muted);
    color: var(--primary);
    background: var(--bg);
}

/* ── Table Card ── */
.drv-table-card {
    background: var(--white);
    border-radius: var(--r-md);
    box-shadow: var(--sh-xs);
    border: 1px solid var(--border);
    overflow: hidden;
}
.drv-table-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
}
.drv-table-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 10px;
}
.drv-table-title i {
    width: 34px; height: 34px;
    background: var(--accent-lt);
    color: var(--accent);
    border-radius: var(--r-xs);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}
.drv-count-badge {
    background: var(--bg);
    color: var(--secondary);
    border: 1px solid var(--border-md);
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

/* Table */
.drv-table { width: 100%; border-collapse: collapse; }
.drv-table thead tr {
    background: var(--bg);
    border-bottom: 1.5px solid var(--border-md);
}
.drv-table thead th {
    padding: 12px 16px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    white-space: nowrap;
}
.drv-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.drv-table tbody tr:last-child { border-bottom: none; }
.drv-table tbody tr:hover { background: rgba(199,166,103,0.04); }
.drv-table td { padding: 14px 16px; vertical-align: middle; }

/* Avatar */
.drv-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(32,40,51,0.15);
}
.drv-name-link {
    font-weight: 700;
    font-size: 14px;
    color: var(--primary);
    transition: color .15s;
}
.drv-name-link:hover { color: var(--accent); }
.drv-linked-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #198754;
    background: rgba(25,135,84,0.08);
    border-radius: 10px;
    padding: 1px 8px;
    margin-top: 2px;
}

/* Phone / License */
.drv-mono {
    font-size: 13px;
    color: var(--secondary);
    font-variant-numeric: tabular-nums;
    direction: ltr;
    display: inline-block;
}
.drv-empty-val {
    color: var(--muted-lt);
    font-size: 18px;
    font-weight: 300;
}

/* Truck Badge */
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

/* Status Badge */
.drv-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}
.drv-status .dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.drv-status.available {
    background: rgba(25,135,84,0.10);
    color: #157347;
    border: 1px solid rgba(25,135,84,0.2);
}
.drv-status.available .dot { background: #198754; box-shadow: 0 0 0 2px rgba(25,135,84,0.2); }
.drv-status.busy {
    background: rgba(255,170,0,0.12);
    color: #996600;
    border: 1px solid rgba(255,170,0,0.25);
}
.drv-status.busy .dot { background: #ffaa00; box-shadow: 0 0 0 2px rgba(255,170,0,0.2); }
.drv-status.inactive {
    background: rgba(220,53,69,0.08);
    color: #b02a37;
    border: 1px solid rgba(220,53,69,0.18);
}
.drv-status.inactive .dot { background: #dc3545; box-shadow: 0 0 0 2px rgba(220,53,69,0.18); }

/* Action Buttons */
.drv-actions { display: flex; align-items: center; gap: 6px; justify-content: center; }
.drv-action-btn {
    width: 34px; height: 34px;
    border-radius: var(--r-xs);
    border: 1.5px solid var(--border-md);
    background: var(--white);
    color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    transition: var(--tr-fast);
    cursor: pointer;
    text-decoration: none;
}
.drv-action-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-lt); }
.drv-action-btn.danger:hover { border-color: #dc3545; color: #dc3545; background: rgba(220,53,69,0.08); }

/* Empty State */
.drv-empty {
    text-align: center;
    padding: 70px 20px;
}
.drv-empty-icon {
    width: 80px; height: 80px;
    background: var(--bg);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 34px;
    color: var(--muted-lt);
}
.drv-empty h6 {
    font-size: 16px;
    font-weight: 700;
    color: var(--secondary);
    margin-bottom: 8px;
}
.drv-empty p {
    font-size: 13px;
    color: var(--muted);
    margin: 0;
}

/* Pagination */
.drv-pagination {
    display: flex;
    justify-content: center;
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    background: var(--bg);
}

/* Responsive */
@media (max-width: 768px) {
    .drv-stats { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .drv-table thead { display: none; }
    .drv-table td { display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; border: none; }
    .drv-table td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
    .drv-table tr { display: block; border: 1px solid var(--border); border-radius: var(--r-sm); margin: 10px 12px; }
}
</style>

{{-- ── Stats Strip ── --}}
@php
    $total    = $drivers->total();
    $available = \App\Models\Driver::where('status','available')->count();
    $busy     = \App\Models\Driver::where('status','busy')->count();
    $inactive = \App\Models\Driver::where('status','inactive')->count();
@endphp
<div class="drv-stats" dir="rtl">
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

{{-- ── Filter Panel ── --}}
<div class="drv-filter" dir="rtl">
    <form method="GET" action="{{ route('drivers.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5 col-md-6">
                <label>بحث في السائقين</label>
                <div class="drv-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search"
                           class="form-control"
                           placeholder="ابحث بالاسم، الهاتف، أو الرخصة..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <label>حالة السائق</label>
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="available" {{ request('status')=='available'?'selected':'' }}>متاح</option>
                    <option value="busy"      {{ request('status')=='busy'     ?'selected':'' }}>مشغول</option>
                    <option value="inactive"  {{ request('status')=='inactive' ?'selected':'' }}>غير نشط</option>
                </select>
            </div>
            <div class="col-lg-4 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="drv-btn-search">
                    <i class="bi bi-search me-1"></i> بحث
                </button>
                @if(request('status') || request('search'))
                    <a href="{{ route('drivers.index') }}" class="drv-btn-reset">
                        <i class="bi bi-x-circle"></i> مسح
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ── Table Card ── --}}
<div class="drv-table-card" dir="rtl">

    {{-- Header --}}
    <div class="drv-table-head">
        <div class="drv-table-title">
            <i class="bi bi-person-lines-fill"></i>
            قائمة السائقين
        </div>
        <span class="drv-count-badge">{{ $drivers->total() }} سائق</span>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="drv-table">
            <thead>
                <tr>
                    <th style="padding-right:24px;">السائق</th>
                    <th class="text-center">رقم الهاتف</th>
                    <th class="text-center">رقم الرخصة</th>
                    <th class="text-center">الشاحنة</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center" style="padding-left:24px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($drivers as $d)
                @php
                    $initial = mb_substr($d->name, 0, 1);
                    $statusMap = [
                        'available' => ['class'=>'available','label'=>'متاح'],
                        'busy'      => ['class'=>'busy',     'label'=>'مشغول'],
                        'inactive'  => ['class'=>'inactive', 'label'=>'غير نشط'],
                    ];
                    $st = $statusMap[$d->status] ?? ['class'=>'inactive','label'=>$d->status];
                @endphp
                <tr>
                    {{-- Driver Info --}}
                    <td data-label="السائق" style="padding-right:24px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="drv-avatar">{{ $initial }}</div>
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

                    {{-- Phone --}}
                    <td data-label="الهاتف" class="text-center">
                        @if($d->phone)
                            <span class="drv-mono" dir="ltr">{{ $d->phone }}</span>
                        @else
                            <span class="drv-empty-val">—</span>
                        @endif
                    </td>

                    {{-- License --}}
                    <td data-label="الرخصة" class="text-center">
                        @if($d->license_number)
                            <span class="drv-mono" dir="ltr">{{ $d->license_number }}</span>
                        @else
                            <span class="drv-empty-val">—</span>
                        @endif
                    </td>

                    {{-- Truck --}}
                    <td data-label="الشاحنة" class="text-center">
                        @if($d->truck)
                            <span class="drv-truck-badge">
                                <i class="bi bi-truck"></i>
                                {{ $d->truck->plate_number }}
                            </span>
                        @else
                            <span class="drv-empty-val">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td data-label="الحالة" class="text-center">
                        <span class="drv-status {{ $st['class'] }}">
                            <span class="dot"></span>
                            {{ $st['label'] }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td data-label="الإجراءات" class="text-center" style="padding-left:24px;">
                        <div class="drv-actions">
                            <a href="{{ route('drivers.show', $d) }}" class="drv-action-btn" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('drivers.edit', $d) }}" class="drv-action-btn" title="تعديل">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form method="POST" action="{{ route('drivers.destroy', $d) }}" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا السائق؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="drv-action-btn danger" title="حذف">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="drv-empty">
                            <div class="drv-empty-icon">
                                <i class="bi bi-person-x"></i>
                            </div>
                            <h6>لا يوجد سائقون مسجلون</h6>
                            <p>ابدأ بإضافة سائقين جدد لتراهم هنا</p>
                            <a href="{{ route('drivers.create') }}" class="drv-add-btn mt-4 d-inline-flex">
                                <i class="bi bi-plus-lg"></i> إضافة أول سائق
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($drivers->hasPages())
        <div class="drv-pagination">
            {{ $drivers->links() }}
        </div>
    @endif

</div>
@endsection
