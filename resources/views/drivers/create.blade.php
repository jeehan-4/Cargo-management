@extends('layouts.app')
@section('title', 'إضافة سائق')
@section('page-title', 'إضافة سائق جديد')
@section('topbar-actions')
    <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-right me-1"></i> العودة</a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card p-4 shadow-sm" style="background: #FFFFFF; border-radius: 12px;">
    <form method="POST" action="{{ route('drivers.store') }}">
        @csrf
        <div class="row g-3">
            
            {{-- معلومات الحساب للدخول --}}
            <div class="col-12">
                <h6 class="fw-800 text-dark mb-2 pb-2 border-bottom" style="color: #202833;"><i class="bi bi-shield-lock me-1 text-warning" style="color: #C7A667 !important;"></i> بيانات تسجيل الدخول</h6>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">البريد الإلكتروني (لتسجيل الدخول) <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" dir="ltr" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

            <div class="col-md-6">
                <label class="form-label fw-600">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" dir="ltr" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- البيانات الشخصية --}}
            <div class="col-12 mt-4">
                <h6 class="fw-800 text-dark mb-2 pb-2 border-bottom" style="color: #202833;"><i class="bi bi-person-badge me-1" style="color: #C7A667 !important;"></i> البيانات الشخصية والمهنية</h6>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">الاسم الكامل <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">رقم الهاتف <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" dir="ltr" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">رقم رخصة القيادة <span class="text-danger">*</span></label>
                <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}" dir="ltr" required>
                @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">رقم الهوية الوطنية</label>
                <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}" dir="ltr">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">الحالة التشغيلية <span class="text-danger">*</span></label>
                <select name="status" class="form-select">
                    <option value="available" {{ old('status','available')=='available'?'selected':'' }}>متاح</option>
                    <option value="busy" {{ old('status')=='busy'?'selected':'' }}>مشغول</option>
                    <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>غير نشط</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-600">الشاحنة المخصصة</label>
                <select name="truck_id" class="form-select">
                    <option value="">— بدون شاحنة —</option>
                    @foreach($trucks as $t)
                    <option value="{{ $t->id }}" {{ old('truck_id')==$t->id?'selected':'' }}>{{ $t->plate_number }} ({{ $t->type }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-600">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div class="col-12 d-flex gap-2 pt-3">
                <button type="submit" class="btn text-white px-4 fw-700" style="background-color: #202833; border-radius: 8px;"><i class="bi bi-save me-1"></i> حفظ السائق وإنشاء الحساب</button>
                <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary px-4 fw-700" style="border-radius: 8px;">إلغاء</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
@endsection