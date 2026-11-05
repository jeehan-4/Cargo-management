@extends('layouts.app')

@section('title', $driver->name)
@section('page-title', 'ملف السائق')
@section('title-icon', 'bi-person-badge')

@section('topbar-actions')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-sm d-flex align-items-center gap-1 px-3 py-2 fw-700 text-white shadow-sm" style="background-color: #202833; border-radius: 8px; transition: all 0.2s ease;">
            <i class="bi bi-pencil-square" style="color: #C7A667;"></i> تعديل البيانات
        </a>
        <a href="{{ route('drivers.index') }}" class="btn btn-sm d-flex align-items-center gap-1 px-3 py-2 fw-700 shadow-sm" style="background-color: #FFFFFF; color: #415A66; border: 1px solid #E2DDD5; border-radius: 8px; transition: all 0.2s ease;">
            <i class="bi bi-arrow-right"></i> رجوع للقائمة
        </a>
    </div>
@endsection

@section('content')
<div class="row g-4">

    {{-- العمود الأول: معلومات السائق والشاحنة --}}
    <div class="col-lg-4">
        <div class="table-card border-0 shadow-sm" style="background-color: #FFFFFF; border-radius: 12px; overflow: hidden; border-top: 4px solid #C7A667 !important;">
            
            {{-- ترويسة البطاقة الشخصية --}}
            <div class="p-4 text-center border-bottom" style="background: linear-gradient(135deg, rgba(32, 40, 51, 0.02) 0%, rgba(65, 90, 102, 0.05) 100%);">
                <div style="width: 85px; height: 85px; background: rgba(199, 166, 103, 0.12); border: 2px solid #C7A667; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; font-size: 2.2rem; color: #C7A667; box-shadow: 0 4px 12px rgba(199, 166, 103, 0.15);">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h4 class="fw-800 mb-2" style="color: #202833;">{{ $driver->name }}</h4>
                <div>
                    <span class="status-badge badge-{{ $driver->status }}" style="padding: 6px 14px; font-weight: 700; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="bi {{ \App\Models\Driver::statusIcon($driver->status ?? '') }}"></i>
                        {{ \App\Models\Driver::statusLabel($driver->status) }}
                    </span>
                </div>
            </div>

            {{-- تفاصيل البيانات (Information Rows) --}}
            <div class="p-4">
                <div class="d-flex flex-column gap-3">
                    
                    {{-- الهاتف --}}
                    <div class="p-3 rounded-3 d-flex align-items-center justify-content:between" style="background-color: #F7F5EF; border: 1px solid #EFECE4;">
                        <div class="d-flex align-items-center gap-2 text-muted fs-xs">
                            <div class="p-2 rounded-2 bg-white text-dark shadow-xs"><i class="bi bi-telephone"></i></div>
                            <span>الهاتف</span>
                        </div>
                        <div class="fw-700 text-dark" dir="ltr">{{ $driver->phone }}</div>
                    </div>

                    {{-- رقم الرخصة --}}
                    <div class="p-3 rounded-3 d-flex align-items-center justify-content:between" style="background-color: #F7F5EF; border: 1px solid #EFECE4;">
                        <div class="d-flex align-items-center gap-2 text-muted fs-xs">
                            <div class="p-2 rounded-2 bg-white text-dark shadow-xs"><i class="bi bi-card-text"></i></div>
                            <span>رقم الرخصة</span>
                        </div>
                        <div class="fw-700 text-dark" dir="ltr">{{ $driver->license_number }}</div>
                    </div>

                    {{-- الرقم الوطني --}}
                    @if($driver->national_id)
                    <div class="p-3 rounded-3 d-flex align-items-center justify-content:between" style="background-color: #F7F5EF; border: 1px solid #EFECE4;">
                        <div class="d-flex align-items-center gap-2 text-muted fs-xs">
                            <div class="p-2 rounded-2 bg-white text-dark shadow-xs"><i class="bi bi-person-vcard"></i></div>
                            <span>الرقم الوطني</span>
                        </div>
                        <div class="fw-700 text-dark" dir="ltr">{{ $driver->national_id }}</div>
                    </div>
                    @endif

                    {{-- بطاقة الشاحنة الحالية (Truck Info Card) --}}
                    <div class="p-3 rounded-3" style="background-color: rgba(32, 40, 51, 0.03); border: 1px dashed #C7A667;">
                        <div class="text-muted fs-xs mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-truck text-secondary" style="color: #C7A667;"></i> الشاحنة الحالية المرتبطة
                        </div>
                        @if($driver->truck)
                            <div class="d-flex align-items-center justify-content:between">
                                <span class="fw-800" style="color: #202833;">{{ $driver->truck->plate_number }}</span>
                                <span class="badge px-2 py-1" style="background-color: #C7A667; color: #FFFFFF; font-weight: 600; font-size: 0.75rem; border-radius: 4px;">{{ $driver->truck->type }}</span>
                            </div>
                        @else
                            <div class="text-muted fst-italic fs-sm py-1">لا توجد شاحنة مرتبطة حالياً</div>
                        @endif
                    </div>

                    {{-- الملاحظات الإضافية --}}
                    @if($driver->notes)
                    <div>
                        <div class="text-muted fs-xs mb-1">ملاحظات إضافية</div>
                        <div class="p-3 rounded-3 fs-sm text-dark" style="background-color: #F7F5EF; border: 1px solid #EFECE4; line-height: 1.6;">
                            {{ $driver->notes }}
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- العمود الثاني: سجل الشحنات --}}
    <div class="col-lg-8">
        <div class="table-card border-0 shadow-sm" style="background-color: #FFFFFF; border-radius: 12px; overflow: hidden;">
            
            {{-- ترويسة الجدول مع الـ Badge --}}
            <div class="table-header d-flex justify-content-between align-items-center py-3 px-4 bg-white border-bottom" style="border-color: #EFECE4 !important;">
                <h6 class="m-0 fw-800" style="color: #202833;">
                    <i class="bi bi-box-seam me-2" style="color: #C7A667;"></i> سجل الشحنات المكلف بها
                </h6>
                <span class="badge px-3 py-2 fw-700" style="background-color: #F7F5EF; color: #415A66; border-radius: 20px; border: 1px solid #EFECE4;">
                    إجمالي الشحنات: {{ $driver->shipments->count() }}
                </span>
            </div>

            {{-- جدول الشحنات الاحترافي --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr style="background-color: #FAF8F5; color: #415A66; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th class="py-3 px-4">رقم الشحنة</th>
                            <th class="py-3">نوع البضاعة</th>
                            <th class="py-3">الوجهة</th>
                            <th class="py-3">الحالة</th>
                            <th class="py-3 px-4">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($driver->shipments->sortByDesc('created_at') as $s)
                        <tr style="transition: background-color 0.15s ease;">
                            <td class="px-4">
                                <a href="{{ route('shipments.show', $s) }}"
                                   class="fw-800 text-decoration-none"
                                   style="color: #202833; transition: color 0.2s;">
                                    {{ $s->shipment_number }}
                                </a>
                            </td>
                            <td class="fw-600 text-dark">{{ $s->cargo_type }}</td>
                            <td>
                                <span class="d-flex align-items-center gap-1 text-secondary">
                                    <i class="bi bi-geo-alt-fill text-danger" style="font-size: 0.9rem;"></i> {{ $s->destination?->name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge badge-{{ $s->status }}" style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">
                                    <i class="bi {{ \App\Models\Shipment::statusIcon($s->status) }}"></i>
                                    {{ \App\Models\Shipment::statusLabel($s->status) }}
                                </span>
                            </td>
                            <td class="px-4 text-muted fs-xs">{{ $s->created_at->format('Y/m/d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state py-4">
                                    <div style="font-size: 3rem; color: #C7A667; margin-bottom: 12px; opacity: 0.8;">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h6 class="fw-700 text-dark mb-1">لا توجد شحنات مسندة</h6>
                                    <p class="text-muted fs-sm m-0">لم يتم إسناد أي شحنة لهذا السائق حتى هذه اللحظة.</p>
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