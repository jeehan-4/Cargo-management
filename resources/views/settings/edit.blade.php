@extends('layouts.app')
@section('title', 'الإعدادات')
@section('page-title', 'إعدادات النظام')
@section('title-icon', 'bi-gear')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-building"></i> بيانات الشركة والتقارير</h6>
    <p class="text-muted small mb-3">تُستخدم هذه البيانات في عناوين التقارير وملفات Excel، ويُعبّأ ميناء المنشأ تلقائياً عند إضافة شحنة جديدة.</p>
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label fw-600">اسم الشركة <span class="text-danger">*</span></label>
                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $settings['company_name']) }}" required>
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">هاتف الشركة</label>
                <input type="text" name="company_phone" class="form-control @error('company_phone') is-invalid @enderror" value="{{ old('company_phone', $settings['company_phone']) }}" dir="ltr">
                @error('company_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-600">عنوان الشركة</label>
                <input type="text" name="company_address" class="form-control @error('company_address') is-invalid @enderror" value="{{ old('company_address', $settings['company_address']) }}">
                @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">ميناء المنشأ الافتراضي <span class="text-danger">*</span></label>
                <input type="text" name="default_origin_port" class="form-control @error('default_origin_port') is-invalid @enderror" value="{{ old('default_origin_port', $settings['default_origin_port']) }}" required>
                @error('default_origin_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-600">تذييل التقارير</label>
                <textarea name="report_footer" class="form-control @error('report_footer') is-invalid @enderror" rows="2" placeholder="نص يظهر أسفل ملخص التقرير (اختياري)">{{ old('report_footer', $settings['report_footer']) }}</textarea>
                @error('report_footer')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 form-actions pt-3">
                <button type="submit" class="btn btn-navy px-4"><i class="bi bi-check-circle me-1"></i> حفظ الإعدادات</button>
            </div>

        </div>
    </form>
</div>
</div>
</div>
@endsection