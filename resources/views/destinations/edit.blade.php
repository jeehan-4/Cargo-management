@extends('layouts.app')
@section('title', 'تعديل الوجهة')
@section('page-title', 'تعديل — '.$destination->name)
@section('title-icon', 'bi-pencil-square')

@section('topbar-actions')
    <a href="{{ route('destinations.index') }}" class="btn-light-action">
        <i class="bi bi-arrow-right"></i> العودة
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

.edit-destination-wrapper {
    max-width: 860px;
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

/* Form Controls */
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

/* Custom Switch Toggle */
.form-check.form-switch {
    padding-right: 2.5em;
    padding-left: 0;
}
.form-check-input {
    width: 2.3em;
    height: 1.2em;
    margin-top: 0.15em;
    cursor: pointer;
    background-color: #d1d5db;
    border-color: transparent;
}
.form-check-input:checked {
    background-color: var(--gold-accent);
    border-color: var(--gold-accent);
}
.form-check-label {
    font-weight: 700;
    color: var(--primary-navy);
    font-size: 13.5px;
    cursor: pointer;
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

<div class="edit-destination-wrapper" dir="rtl">
    <div class="saas-card">
        <div class="saas-card-header">
            <h5 class="saas-card-title">
                <i class="bi bi-pencil-square"></i> تعديل بيانات الوجهة الجغرافية
            </h5>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fs-8">إدارة الوجهات</span>
        </div>

        <form method="POST" action="{{ route('destinations.update', $destination) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label-saas">اسم الوجهة <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control-saas @error('name') is-invalid @enderror" value="{{ old('name', $destination->name) }}" required>
                    @error('name')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label-saas">المدينة <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control-saas @error('city') is-invalid @enderror" value="{{ old('city', $destination->city) }}" required>
                    @error('city')<div class="invalid-feedback d-block mt-1 fs-8">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label-saas">العنوان التفصيلي</label>
                    <input type="text" name="address" class="form-control-saas" value="{{ old('address', $destination->address) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label-saas">اسم جهة الاتصال</label>
                    <input type="text" name="contact_name" class="form-control-saas" value="{{ old('contact_name', $destination->contact_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label-saas">هاتف جهة الاتصال</label>
                    <input type="text" name="contact_phone" class="form-control-saas" value="{{ old('contact_phone', $destination->contact_phone) }}" dir="ltr">
                </div>

                <div class="col-12">
                    <label class="form-label-saas">ملاحظات</label>
                    <textarea name="notes" class="form-control-saas" rows="2">{{ old('notes', $destination->notes) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch p-0 d-flex align-items-center gap-3">
                        <input class="form-check-input m-0" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $destination->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label m-0" for="is_active">وجهة نشطة ومتاحة للاختيار</label>
                    </div>
                </div>

                <div class="col-12 form-section-divider d-flex align-items-center gap-3">
                    <button type="submit" class="btn-gold-action">
                        <i class="bi bi-check-circle"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('destinations.index') }}" class="btn-light-action">إلغاء</a>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection