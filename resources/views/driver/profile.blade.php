@extends('driver.layout')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@section('content')
<style>
/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   DRIVER PROFILE — SAAS REFINED DESIGN
   الخط المستخدم: Cairo, IBM Plex Sans Arabic
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */
.profile-container {
    max-width: 850px;
    margin: 0 auto;
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}
.profile-container * {
    font-family: 'Cairo', 'IBM Plex Sans Arabic', sans-serif !important;
}

.saas-profile-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 28px;
    box-shadow: var(--sh-sm);
    margin-bottom: 24px;
    transition: var(--tr);
}
.saas-profile-card:hover {
    box-shadow: var(--sh-md);
}

.saas-profile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--border);
}

.saas-profile-title {
    font-size: .95rem;
    font-weight: 800;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
}
.saas-profile-title i {
    color: var(--accent);
    font-size: 1.1rem;
}

/* Custom Info Row for Profile */
.profile-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 0;
    border-bottom: 1px solid rgba(65,90,102,0.06);
    font-size: .88rem;
}
.profile-info-row:last-child {
    border-bottom: none;
}
.profile-info-label {
    color: var(--muted);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}
.profile-info-value {
    color: var(--primary);
    font-weight: 800;
}
</style>

<div class="profile-container" dir="rtl">
    <div class="saas-profile-card">
        <div class="saas-profile-header">
            <div class="saas-profile-title">
                <i class="bi bi-person-circle"></i> بيانات السائق الشخصية
            </div>
            <span class="sbadge sbadge-{{ $driver->status }}">
                <i class="bi {{ $driver->status === 'available' ? 'bi-check-circle' : 'bi-clock' }}"></i>
                {{ \App\Models\Driver::statusLabel($driver->status) }}
            </span>
        </div>

        <div class="saas-profile-body">
            {{-- Avatar & Name Section --}}
            <div class="text-center mb-4 pb-3 border-bottom">
                <div style="width:78px; height:78px; background:linear-gradient(135deg, var(--secondary), var(--primary));
                    border-radius:50%; display:inline-flex; align-items:center; justify-content:center;
                    color:#fff; font-weight:900; font-size:1.8rem;
                    box-shadow: 0 4px 15px rgba(32,40,51,0.15);
                    border:3px solid rgba(199,166,103,0.4);">
                    {{ mb_substr($driver->name, 0, 1) }}
                </div>
                <h4 style="font-weight:900; color:var(--primary); margin-top:12px; margin-bottom:2px;">{{ $driver->name }}</h4>
                <p style="font-size:.8rem; color:var(--muted); font-weight:600; margin:0;">سائق معتمد بنظام إدارة الشحنات</p>
            </div>

            {{-- Driver Information Rows --}}
            <div class="profile-info-row">
                <span class="profile-info-label"><i class="bi bi-person text-accent"></i> الاسم الكامل</span>
                <span class="profile-info-value">{{ $driver->name }}</span>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-label"><i class="bi bi-telephone text-accent"></i> رقم الهاتف</span>
                <span class="profile-info-value font-monospace" dir="ltr">{{ $driver->phone }}</span>
            </div>
            <div class="profile-info-row">
                <span class="profile-info-label"><i class="bi bi-card-text text-accent"></i> رقم الرخصة</span>
                <span class="profile-info-value font-monospace">{{ $driver->license_number }}</span>
            </div>
            @if($driver->national_id)
            <div class="profile-info-row">
                <span class="profile-info-label"><i class="bi bi-credit-card text-accent"></i> الهوية الوطنية</span>
                <span class="profile-info-value font-monospace">{{ $driver->national_id }}</span>
            </div>
            @endif

            {{-- Truck Section --}}
            @if($driver->truck)
            <div class="mt-4 pt-3 border-top">
                <h6 style="font-weight:900; font-size:.9rem; color:var(--primary); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-truck text-accent fs-5"></i> بيانات الشاحنة المرتبطة
                </h6>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="bi bi-hash text-accent"></i> رقم اللوحة</span>
                    <span class="profile-info-value font-monospace">{{ $driver->truck->plate_number }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="bi bi-truck text-accent"></i> نوع الشاحنة</span>
                    <span class="profile-info-value">{{ $driver->truck->type }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="bi bi-speedometer text-accent"></i> الحمولة القصوى</span>
                    <span class="profile-info-value font-monospace">{{ $driver->truck->capacity }} طن</span>
                </div>
            </div>
            @endif

            {{-- Notes Section --}}
            @if($driver->notes)
            <div class="mt-4 pt-3 border-top">
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="bi bi-chat-text text-accent"></i> ملاحظات إضافية</span>
                    <span class="profile-info-value text-muted">{{ $driver->notes }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection