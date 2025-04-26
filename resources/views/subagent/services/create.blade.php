@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subagent.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subagent.services.index') }}">الخدمات المتاحة</a></li>
    <li class="breadcrumb-item active">إضافة خدمة جديدة</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-plus me-2"></i> إضافة خدمة جديدة</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('subagent.services.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع الخدمة <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">-- اختر نوع الخدمة --</option>
                                <option value="security_approval" {{ old('type') == 'security_approval' ? 'selected' : '' }}>موافقة أمنية</option>
                                <option value="transportation" {{ old('type') == 'transportation' ? 'selected' : '' }}>نقل بري</option>
                                <option value="hajj_umrah" {{ old('type') == 'hajj_umrah' ? 'selected' : '' }}>حج وعمرة</option>
                                <option value="flight" {{ old('type') == 'flight' ? 'selected' : '' }}>تذاكر طيران</option>
                                <option value="passport" {{ old('type') == 'passport' ? 'selected' : '' }}>جوازات</option>
                                <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>فنادق</option>
                                <option value="visa" {{ old('type') == 'visa' ? 'selected' : '' }}>تأشيرات</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الخدمة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="base_price" class="form-label">السعر الأساسي</label>
                            <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price') }}">
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">السعر النهائي</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="currency_id" class="form-label">العملة <span class="text-danger">*</span></label>
                            <select class="form-select @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" required>
                                <option value="">-- اختر العملة --</option>
                                @foreach(\App\Models\Currency::all() as $currency)
                                    <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol }})</option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="commission_rate" class="form-label">نسبة العمولة (%)</label>
                            <input type="number" step="0.01" class="form-control @error('commission_rate') is-invalid @enderror" id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}">
                            @error('commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">صورة الخدمة</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشطة</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> حفظ الخدمة</button>
                        <a href="{{ route('subagent.services.index') }}" class="btn btn-secondary ms-2">إلغاء</a>
                    </form>