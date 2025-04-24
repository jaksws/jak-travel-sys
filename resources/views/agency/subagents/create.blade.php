@extends('layouts.app')

@section('title', 'إضافة سبوكيل جديد')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة سبوكيل جديد</h1>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('agency.subagents.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">اسم السبوكِيل</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </form>
        </div>
    </div>
</div>
@endsection