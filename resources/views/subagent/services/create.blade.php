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
                    <form method="POST" action="{{ route('subagent.services.store') }}">
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
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> حفظ الخدمة</button>
                        <a href="{{ route('subagent.services.index') }}" class="btn btn-secondary ms-2">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
