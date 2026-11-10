@extends('layouts.app')
@section('title', 'شحنة جديدة')
@section('page-title', 'إنشاء شحنة جديدة')
@section('title-icon', 'bi-plus-circle')

@section('topbar-actions')
    <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card">
    <h6 class="form-card-title"><i class="bi bi-box-seam-fill"></i> إضافة وتغيير بيانات الشحنة الجديدة</h6>
    <form method="POST" action="{{ route('shipments.store') }}">
        @csrf
        <div class="row g-3">

            {{-- نوع البضاعة --}}
            <div class="col-md-6">
                <label class="form-label fw-600">
                    نوع البضاعة <span class="text-danger">*</span>
                </label>
                <select name="cargo_type" class="form-select @error('cargo_type') is-invalid @enderror">
                    <option value="">اختر نوع البضاعة...</option>
                    @foreach(['حبوب','رز','سكر','بر'] as $t)
                    <option value="{{ $t }}" {{ old('cargo_type')==$t?'selected':'' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('cargo_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- الوجهة --}}
            <div class="col-md-6">
                <label class="form-label fw-600">
                    الوجهة <span class="text-danger">*</span>
                </label>
                <select name="destination_id" class="form-select @error('destination_id') is-invalid @enderror">
                    <option value="">اختر الوجهة...</option>
                    @foreach($destinations as $d)
                    <option value="{{ $d->id }}" {{ old('destination_id')==$d->id?'selected':'' }}>
                        {{ $d->name }}
                    </option>
                    @endforeach
                </select>
                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- الكمية --}}
            <div class="col-md-4">
                <label class="form-label fw-600">الكمية</label>
                <input type="number" name="quantity"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="0" placeholder="مثال: 500">
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- الوحدة --}}
            <div class="col-md-4">
                <label class="form-label fw-600">
                    الوحدة <span class="text-danger">*</span>
                </label>
                <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                    @foreach(['طن','كرتون','صندوق','حاوية','قطعة'] as $u)
                    <option value="{{ $u }}" {{ old('unit','حاوية')==$u?'selected':'' }}>{{ $u }}</option>
                    @endforeach
                </select>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- الوزن --}}
            <div class="col-md-4">
                <label class="form-label fw-600">الوزن (ط)</label>
                <input type="number" name="weight" step="0.01"
                       class="form-control @error('weight') is-invalid @enderror"
                       value="{{ old('weight') }}" min="0" placeholder="مثال: 25.5">
                @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ميناء المنشأ --}}
            <div class="col-12">
                <label class="form-label fw-600">
                    ميناء المنشأ <span class="text-danger">*</span>
                </label>
                <input type="text" name="origin_port"
                       class="form-control @error('origin_port') is-invalid @enderror"
                       value="{{ old('origin_port', \App\Models\Setting::get('default_origin_port')) }}">
                @error('origin_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ملاحظات --}}
            <div class="col-12">
                <label class="form-label fw-600">ملاحظات الشحنة</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="أدخل أي معلومات إضافية أو تعليمات خاصة بالشحنة...">{{ old('notes') }}</textarea>
            </div>

            {{-- أزرار الحفظ والإلغاء --}}
            <div class="col-12 form-actions pt-3">
                <button type="submit" class="btn btn-navy px-4" id="submitBtn"
                        onclick="this.disabled=true; this.innerHTML='<i class=\'bi bi-hourglass-split\'></i> جاري الحفظ...'; this.closest('form').submit();">
                    <i class="bi bi-check-circle me-1"></i> حفظ الشحنة
                </button>
                <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
            </div>

        </div>
    </form>
</div>
</div>
</div>
@endsection
