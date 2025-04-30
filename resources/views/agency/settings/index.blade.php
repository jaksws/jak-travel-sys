@extends('layouts.app')

@section('title', 'إعدادات الوكالة')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">إعدادات الوكالة</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('agency.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="agency_name" class="form-label">اسم الوكالة</label>
            <input type="text" class="form-control" id="agency_name" name="agency_name" value="{{ auth()->user()->agency->name ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="agency_phone" class="form-label">رقم الهاتف</label>
            <input type="text" class="form-control" id="agency_phone" name="agency_phone" value="{{ auth()->user()->agency->phone ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="agency_address" class="form-label">العنوان</label>
            <input type="text" class="form-control" id="agency_address" name="agency_address" value="{{ auth()->user()->agency->address ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
</div>
@endsection
