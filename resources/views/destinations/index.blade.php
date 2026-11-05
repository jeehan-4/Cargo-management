@extends('layouts.app')
@section('title', 'الوجهات')
@section('page-title', 'إدارة الوجهات')
@section('title-icon', 'bi-geo-alt')

@section('topbar-actions')
    <a href="{{ route('destinations.create') }}" class="btn-gold-action">
        <i class="bi bi-plus-lg"></i> إضافة وجهة
    </a>
@endsection

@section('content')
<style>
/* ─── Premium Modern SaaS Dashboard Theme ─── */
:root {
    --primary-navy: #202833;
    --secondary-navy: #415A66;
    --gold-accent: #C7A667;
    --bg-ivory: #F7F5EF;
    --surface-white: #FFFFFF;
    --border-light: rgba(65, 90, 102, 0.12);
    --text-muted: #6c757d;
}

body {
    background-color: var(--bg-ivory) !important;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}

.destinations-index-container {
    max-width: 1280px;
    margin: 0 auto;
}

/* Minimalist Rounded Cards */
.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
}

.saas-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border-light);
}

.saas-table-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.saas-table-title i {
    color: var(--gold-accent);
    font-size: 20px;
}

/* Clean Data Table */
.saas-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.saas-table th {
    background: var(--bg-ivory);
    color: var(--text-muted);
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 14px 18px;
    border-bottom: 1.5px solid var(--border-light);
    letter-spacing: 0.3px;
}
.saas-table td {
    padding: 16px 18px;
    border-bottom: 1px solid rgba(65, 90, 102, 0.05);
    font-size: 13.5px;
    vertical-align: middle;
    color: var(--primary-navy);
}
.saas-table tbody tr {
    transition: background-color 0.15s ease;
}
.saas-table tbody tr:hover {
    background-color: rgba(199, 166, 103, 0.03);
}

/* Status Badges */
.saas-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    border: 1px solid transparent;
    white-space: nowrap;
}
.saas-badge.active {
    color: #47734D;
    background-color: #EAF3E9;
    border-color: #C8E2C7;
}
.saas-badge.inactive {
    color: #98514D;
    background-color: #F8EAE8;
    border-color: #ECC6C4;
}

/* Action Buttons */
.btn-gold-action {
    background: linear-gradient(135deg, var(--gold-accent) 0%, #b38f4d 100%);
    color: #ffffff !important;
    border: none;
    font-weight: 700;
    font-size: 13px;
    padding: 8px 18px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(199, 166, 103, 0.25);
    text-decoration: none;
}
.btn-gold-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(199, 166, 103, 0.35);
}

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: 1px solid var(--border-light);
    background: #ffffff;
    color: var(--secondary-navy);
}
.btn-action-icon:hover {
    background: var(--bg-ivory);
    border-color: var(--primary-navy);
    color: var(--primary-navy);
}
.btn-action-icon.danger:hover {
    background: #F8EAE8;
    border-color: #ECC6C4;
    color: #98514D;
}
</style>

<div class="destinations-index-container" dir="rtl">
    <div class="saas-card">
        <div class="saas-table-header">
            <h6 class="saas-table-title">
                <i class="bi bi-geo-alt-fill"></i> قائمة الوجهات الجغرافية <span class="fs-8 text-muted fw-normal">({{ $destinations->total() }})</span>
            </h6>
        </div>

        <div class="table-responsive">
            <table class="saas-table">
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
                        <td class="fw-bold">
                            {{ $d->name }}
                        </td>
                        <td class="text-muted">{{ $d->city }}</td>
                        <td>
                            @if($d->contact_name)
                                <div class="fw-semibold">{{ $d->contact_name }}</div>
                                <div class="text-muted fs-8 font-monospace" dir="ltr">{{ $d->contact_phone }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-1 fw-bold font-monospace" style="background: rgba(65, 90, 102, 0.08); color: var(--secondary-navy);">
                                {{ $d->shipments_count }}
                            </span>
                        </td>
                        <td>
                            <span class="saas-badge {{ $d->is_active ? 'active' : 'inactive' }}">
                                <i class="bi {{ $d->is_active ? 'bi-check-circle' : 'bi-dash-circle' }}"></i>
                                {{ $d->is_active ? 'نشطة' : 'موقوفة' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1.5 justify-content-center">
                                <a href="{{ route('destinations.edit', $d) }}" class="btn-action-icon" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('destinations.destroy', $d) }}" onsubmit="return confirm('هل تريد حذف هذه الوجهة؟')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-action-icon danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-geo display-5 opacity-25 d-block mb-2"></i>
                                <h6 class="fw-bold text-secondary">لا توجد وجهات مضافة حالياً</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($destinations->hasPages())
        <div class="pt-4 border-top d-flex justify-content-center">
            {{ $destinations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection