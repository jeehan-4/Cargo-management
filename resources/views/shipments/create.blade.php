@extends('layouts.app')
@section('title', 'شحنة جديدة')
@section('page-title', 'إنشاء شحنة جديدة')
@section('title-icon', 'bi-plus-circle')

@section('topbar-actions')
    <a href="{{ route('shipments.index') }}" class="btn-light-action">
        <i class="bi bi-arrow-right"></i> رجوع
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

.create-shipment-wrapper {
    max-width: 900px;
    margin: 0 auto;
}

/* Minimalist Rounded Cards */
.saas-card {
    background: var(--surface-white);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 32px 36px;
    box-shadow: 0 4px 20px rgba(32, 40, 51, 0.03);
    margin-bottom: 24px;
}

.saas-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 18px;
    margin-bottom: 26px;
    border-bottom: 1px solid var(--border-light);
}

.saas-card-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--primary-navy);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.saas-card-title i {
    color: var(--gold-accent);
    font-size: 22px;
}

/* Custom Clean Form Elements */
.form-label-saas {
    font-size: 13px;
    font-weight: 700;
    color: var(--secondary-navy);
    margin-bottom: 8px;
    display: block;
}

.form-control-saas {
    background-color: var(--bg-ivory);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 11px 16px;
    font-size: 14px;
    color: var(--primary-navy);
    transition: all 0.2s ease;
    width: 100%;
}

.form-control-saas:focus {
    background-color: #ffffff;
    border-color: var(--gold-accent);
    box-shadow: 0 0 0 3px rgba(199, 166, 103, 0.15);
    outline: none;
}

/* Elegant & Custom Designed Dropdown Selects */
.form-select-saas {
    background-color: var(--bg-ivory);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 11px 40px 11px 16px;
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-navy);
    transition: all 0.2s ease;
    width: 100%;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    /* Custom Elegant Down Arrow Icon */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23415A66' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 14px center;
    background-size: 16px;
}

.form-select-saas:hover {
    border-color: rgba(199, 166, 103, 0.5);
    background-color: #faf8f3;
}

.form-select-saas:focus {
    background-color: #ffffff;
    border-color: var(--gold-accent);
    box-shadow: 0 0 0 3px rgba(199, 166, 103, 0.15);
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23C7A667' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='18 15 12 9 6 15'%3E%3C/polyline%3E%3C/svg%3E");
}

.form-select-saas option {
    background-color: #ffffff;
    color: var(--primary-navy);
    padding: 12px;
    font-weight: 600;
}

/* Action Buttons */
.btn-gold-action {
    background: linear-gradient(135deg, var(--gold-accent) 0%, #b38f4d 100%);
    color: #ffffff !important;
    border: none;
    font-weight: 800;
    font-size: 14.5px;
    padding: 11px 28px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(199, 166, 103, 0.3);
    cursor: pointer;
    text-decoration: none;
}
.btn-gold-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(199, 166, 103, 0.4);
}

.btn-light-action {
    background: #ffffff;
    color: var(--secondary-navy) !important;
    border: 1px solid var(--border-light);
    font-weight: 700;
    font-size: 13px;
    padding: 9px 18px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-light-action:hover {
    background: var(--bg-ivory);
    border-color: var(--primary-navy);
    color: var(--primary-navy) !important;
}

.form-section-divider {
    border-top: 1px solid var(--border-light);
    margin-top: 10px;
    padding-top: 22px;
}
</style>

<div class="create-shipment-wrapper" dir="rtl">
    <div class="saas-card">
        <div class="saas-card-header">
            <h5 class="saas-card-title">
                <i class="bi bi-box-seam-fill"></i> إضافة وتغيير بيانات الشحنة الجديدة
            </h5>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fs-8">النظام اللوجستي</span>
        </div>

        <form method="POST" action="{{ route('shipments.store') }}">
            @csrf
            <div class="row g-4">

                {{-- نوع البضاعة --}}
                <div class="col-md-6">
                    <label class="form-label-saas">
                        نوع البضاعة <span class="text-danger">*</span>
                    </label>
                    <select name="cargo_type"
                            class="form-select-saas @error('cargo_type') is-invalid @enderror">
                        <option value="">اختر نوع البضاعة...</option>
                        @foreach(['حبوب','رز','سكر','بر'] as $t)
                        <option value="{{ $t }}" {{ old('cargo_type')==$t?'selected':'' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('cargo_type')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- الوجهة --}}
                <div class="col-md-6">
                    <label class="form-label-saas">
                        الوجهة <span class="text-danger">*</span>
                    </label>
                    <select name="destination_id"
                            class="form-select-saas @error('destination_id') is-invalid @enderror">
                        <option value="">اختر الوجهة...</option>
                        @foreach($destinations as $d)
                        <option value="{{ $d->id }}" {{ old('destination_id')==$d->id?'selected':'' }}>
                            {{ $d->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('destination_id')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- الكمية --}}
                <div class="col-md-4">
                    <label class="form-label-saas">
                        الكمية
                    </label>
                    <input type="number" name="quantity"
                           class="form-control-saas @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity') }}" min="0" placeholder="مثال: 500">
                    @error('quantity')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- الوحدة --}}
                <div class="col-md-4">
                    <label class="form-label-saas">
                        الوحدة <span class="text-danger">*</span>
                    </label>
                    <select name="unit" class="form-select-saas @error('unit') is-invalid @enderror">
                        @foreach(['طن','كرتون','صندوق','حاوية','قطعة'] as $u)
                        <option value="{{ $u }}" {{ old('unit','حاوية')==$u?'selected':'' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                    @error('unit')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- الوزن --}}
                <div class="col-md-4">
                    <label class="form-label-saas">
                        الوزن (ط)
                    </label>
                    <input type="number" name="weight" step="0.01"
                           class="form-control-saas @error('weight') is-invalid @enderror"
                           value="{{ old('weight') }}" min="0" placeholder="مثال: 25.5">
                    @error('weight')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- ميناء المنشأ --}}
                <div class="col-12">
                    <label class="form-label-saas">
                        ميناء المنشأ <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="origin_port"
                           class="form-control-saas @error('origin_port') is-invalid @enderror"
                           value="{{ old('origin_port','ميناء  الحديدة') }}">
                    @error('origin_port')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                {{-- ملاحظات --}}
                <div class="col-12">
                    <label class="form-label-saas">
                        ملاحظات الشحنة
                    </label>
                    <textarea name="notes" class="form-control-saas" rows="3"
                              placeholder="أدخل أي معلومات إضافية أو تعليمات خاصة بالشحنة...">{{ old('notes') }}</textarea>
                </div>

                {{-- أزرار الحفظ والإلغاء --}}
                <div class="col-12 form-section-divider d-flex align-items-center gap-3">
                    <button type="submit" class="btn-gold-action" id="submitBtn"
                            onclick="this.disabled=true; this.innerHTML='<i class=\'bi bi-hourglass-split\'></i> جاري الحفظ...'; this.closest('form').submit();">
                        <i class="bi bi-check-circle"></i> حفظ الشحنة
                    </button>
                    <a href="{{ route('shipments.index') }}" class="btn-light-action">إلغاء</a>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection