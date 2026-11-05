@extends('layouts.app')
@section('title','تعديل الشحنة '.$shipment->shipment_number)
@section('page-title','تعديل الشحنة: '.$shipment->shipment_number)
@section('title-icon','bi-pencil-square')
@section('topbar-actions')
    <a href="{{ route('shipments.show',$shipment) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="form-card">
    <form method="POST" action="{{ route('shipments.update',$shipment) }}">
        @csrf @method('PUT')
        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">نوع البضاعة <span class="text-danger">*</span></label>
                <select name="cargo_type"
                        class="form-select @error('cargo_type') is-invalid @enderror">
                    <option value="">اختر نوع البضاعة...</option>
                    @foreach(['حبوب','منتجات','أثاث','معدات','خشب','قطن','منتجات زراعية'] as $t)
                    <option value="{{ $t }}" {{ old('cargo_type',$shipment->cargo_type)==$t?'selected':'' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('cargo_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">الوجهة <span class="text-danger">*</span></label>
                <select name="destination_id"
                        class="form-select @error('destination_id') is-invalid @enderror">
                    <option value="">اختر الوجهة...</option>
                    @foreach($destinations as $d)
                    <option value="{{ $d->id }}" {{ old('destination_id',$shipment->destination_id)==$d->id?'selected':'' }}>
                        {{ $d->name }} — {{ $d->city }}
                    </option>
                    @endforeach
                </select>
                @error('destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">الكمية</label>
                <input type="number" name="quantity"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity',$shipment->quantity) }}" min="0">
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">الوحدة <span class="text-danger">*</span></label>
                <select name="unit" class="form-select">
                    @foreach(['ط','كرتون','صندوق','حاوية','قطعة'] as $u)
                    <option value="{{ $u }}" {{ old('unit',$shipment->unit)==$u?'selected':'' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">الوزن (ط)</label>
                <input type="number" name="weight" step="0.01"
                       class="form-control @error('weight') is-invalid @enderror"
                       value="{{ old('weight',$shipment->weight) }}" min="0">
                @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label">ميناء المنشأ <span class="text-danger">*</span></label>
                <input type="text" name="origin_port"
                       class="form-control @error('origin_port') is-invalid @enderror"
                       value="{{ old('origin_port',$shipment->origin_port) }}">
                @error('origin_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes',$shipment->notes) }}</textarea>
            </div>

            <div class="col-12 d-flex gap-2 pt-2 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> حفظ التعديلات
                </button>
                <a href="{{ route('shipments.show',$shipment) }}" class="btn btn-outline-secondary">إلغاء</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
@endsection
