@extends('layouts.app')
@section('title', 'إضافة وجهة')
@section('page-title', 'إضافة وجهة جديدة')
@section('title-icon', 'bi-geo-alt')

@section('topbar-actions')
    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i> العودة
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-geo-alt-fill"></i> بيانات الوجهة الجغرافية الجديدة</h6>
    <form method="POST" action="{{ route('destinations.store') }}">
        @csrf
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label fw-600">اسم الوجهة <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="مثال: مستودع العاصمة المركزي">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">المدينة <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required placeholder="مثال: صنعاء">
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-600">العنوان التفصيلي</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="الشارع، المنطقة، علامة مميزة...">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">اسم جهة الاتصال</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" placeholder="اسم المسؤول أو المندوب">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">هاتف جهة الاتصال</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" dir="ltr" placeholder="77xxxxxxx">
            </div>

            <div class="col-12">
                <label class="form-label fw-600">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="معلومات إضافية حول التوصيل أو الموقع...">{{ old('notes') }}</textarea>
            </div>

            <div class="col-12">
                <div class="form-check form-switch ps-0 d-flex align-items-center gap-3">
                    <input class="form-check-input m-0" type="checkbox" name="is_active" id="is_active" {{ old('is_active','1')=='1'?'checked':'' }}>
                    <label class="form-check-label m-0 fw-600" for="is_active">وجهة نشطة ومتاحة للاختيار</label>
                </div>
            </div>

            <div class="col-12 form-actions pt-3">
                <button type="submit" class="btn btn-navy px-4"><i class="bi bi-check-circle me-1"></i> حفظ الوجهة</button>
                <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
            </div>

        </div>
    </form>
</div>
</div>
</div>
@endsection
