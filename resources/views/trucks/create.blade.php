@extends('layouts.app')
@section('title','إضافة شاحنة')
@section('page-title','إضافة شاحنة جديدة')
@section('topbar-actions')
    <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-right me-1"></i> العودة</a>
@endsection
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-truck-front"></i> بيانات الشاحنة الجديدة</h6>
    <form method="POST" action="{{ route('trucks.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">رقم اللوحة <span class="text-danger">*</span></label>
                <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" dir="ltr" required>
                @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">نوع الشاحنة <span class="text-danger">*</span></label>
                <select name="type" class="form-select">
                    @foreach(['شاحنة نقل','مقطورة','بيك أب','شاحنة مبردة','شاحنة حاوية'] as $tp)
                    <option value="{{ $tp }}" {{ old('type')==$tp?'selected':'' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الحمولة (طن)</label>
                <input type="number" name="capacity" step="0.5" class="form-control" value="{{ old('capacity') }}" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status','active')=='active'?'selected':'' }}>نشطة</option>
                    <option value="maintenance" {{ old('status')=='maintenance'?'selected':'' }}>في الصيانة</option>
                    <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>غير نشطة</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">تخصيص للسائق</label>
                <select name="driver_id" class="form-select">
                    <option value="">— بدون سائق —</option>
                    @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ old('driver_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12 form-actions pt-2">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> حفظ</button>
                <a href="{{ route('trucks.index') }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </div>
    </form>
</div>
</div></div>
@endsection
