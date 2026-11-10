@extends('layouts.app')
@section('title','تعديل بيانات السائق')
@section('page-title','تعديل — '.$driver->name)
@section('topbar-actions')
    <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-right me-1"></i> العودة</a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-person-gear"></i> تعديل بيانات السائق — {{ $driver->name }}</h6>
    <form method="POST" action="{{ route('drivers.update',$driver) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$driver->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone',$driver->phone) }}" dir="ltr" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم رخصة القيادة <span class="text-danger">*</span></label>
                <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number',$driver->license_number) }}" dir="ltr" required>
                @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">رقم الهوية الوطنية</label>
                <input type="text" name="national_id" class="form-control" value="{{ old('national_id',$driver->national_id) }}" dir="ltr">
            </div>
            <div class="col-md-6">
                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                <select name="status" class="form-select">
                    <option value="available" {{ old('status',$driver->status)=='available'?'selected':'' }}>متاح</option>
                    <option value="busy" {{ old('status',$driver->status)=='busy'?'selected':'' }}>مشغول</option>
                    <option value="inactive" {{ old('status',$driver->status)=='inactive'?'selected':'' }}>غير نشط</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الشاحنة المخصصة</label>
                <select name="truck_id" class="form-select">
                    <option value="">— بدون شاحنة —</option>
                    @foreach($trucks as $t)
                    <option value="{{ $t->id }}" {{ old('truck_id',$driver->truck?->id)==$t->id?'selected':'' }}>{{ $t->plate_number }} ({{ $t->type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes',$driver->notes) }}</textarea>
            </div>
            <div class="col-12 form-actions pt-2">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> حفظ التعديلات</button>
                <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
@endsection
