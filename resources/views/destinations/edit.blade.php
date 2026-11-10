@extends('layouts.app')
@section('title', 'تعديل الوجهة')
@section('page-title', 'تعديل — '.$destination->name)
@section('title-icon', 'bi-pencil-square')

@section('topbar-actions')
    <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i> العودة
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-pencil-square"></i> تعديل بيانات الوجهة الجغرافية</h6>
    <form method="POST" action="{{ route('destinations.update', $destination) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label fw-600">اسم الوجهة <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $destination->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">المدينة <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $destination->city) }}" required>
                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-600">العنوان التفصيلي</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $destination->address) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">اسم جهة الاتصال</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $destination->contact_name) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">هاتف جهة الاتصال</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $destination->contact_phone) }}" dir="ltr">
            </div>

            <div class="col-12">
                <label class="form-label fw-600">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $destination->notes) }}</textarea>
            </div>

            <div class="col-12">
                <div class="form-check form-switch ps-0 d-flex align-items-center gap-3">
                    <input class="form-check-input m-0" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $destination->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label m-0 fw-600" for="is_active">وجهة نشطة ومتاحة للاختيار</label>
                </div>
            </div>

            <div class="col-12 form-actions pt-3">
                <button type="submit" class="btn btn-navy px-4"><i class="bi bi-check-circle me-1"></i> حفظ التعديلات</button>
                <a href="{{ route('destinations.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
            </div>

        </div>
    </form>
</div>
</div>
</div>
@endsection
