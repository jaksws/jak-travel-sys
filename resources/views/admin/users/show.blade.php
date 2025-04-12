@extends('layouts.app')

@section('title', 'عرض بيانات المستخدم')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">إدارة المستخدمين</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المستخدم</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>

    <!-- معلومات المستخدم الأساسية -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold">المعلومات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto">
                            <span class="avatar-text">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <h4 class="mt-3">{{ $user->name }}</h4>
                        @switch($user->role)
                            @case('admin')
                                <span class="badge bg-primary fs-6">مسؤول</span>
                                @break
                            @case('agency')
                                <span class="badge bg-success fs-6">وكالة</span>
                                @break
                            @case('subagent')
                                <span class="badge bg-info fs-6">سبوكيل</span>
                                @break
                            @default
                                <span class="badge bg-warning fs-6">عميل</span>
                        @endswitch
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2 text-muted"></i> البريد الإلكتروني</span>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-toggle-on me-2 text-muted"></i> الحالة</span>
                            @if($user->status === 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">معطل</span>
                            @endif
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-alt me-2 text-muted"></i> تاريخ التسجيل</span>
                            <span>{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clock me-2 text-muted"></i> آخر تحديث</span>
                            <span>{{ $user->updated_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-{{ $user->status === 'active' ? 'danger' : 'success' }}" 
                                onclick="document.getElementById('toggle-form').submit();">
                            @if($user->status === 'active')
                                <i class="fas fa-ban me-1"></i> تعطيل الحساب
                            @else
                                <i class="fas fa-check me-1"></i> تفعيل الحساب
                            @endif
                        </button>
                        <form id="toggle-form" action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-none">
                            @csrf
                            @method('PATCH')
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- الطلبات -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">آخر الطلبات</h6>
                    <a href="#" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>الخدمة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($user->requests && $user->requests->count() > 0)
                                    @foreach($user->requests->take(5) as $request)
                                    <tr>
                                        <td>{{ $request->title }}</td>
                                        <td>{{ $request->service->name ?? 'غير متوفر' }}</td>
                                        <td>
                                            @switch($request->status)
                                                @case('pending')
                                                    <span class="badge bg-secondary">قيد الانتظار</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge bg-primary">قيد التنفيذ</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">مكتملة</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">ملغاة</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $request->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4">لا يوجد طلبات لهذا المستخدم</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- المعاملات المالية -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">آخر المعاملات المالية</h6>
                    <a href="#" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>رقم المعاملة</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($user->transactions && $user->transactions->count() > 0)
                                    @foreach($user->transactions->take(5) as $transaction)
                                    <tr>
                                        <td>{{ $transaction->reference_number }}</td>
                                        <td>{{ $transaction->amount }} {{ $transaction->currency }}</td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td>
                                            @switch($transaction->status)
                                                @case('completed')
                                                    <span class="badge bg-success">مكتملة</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">فشلت</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-4">لا يوجد معاملات مالية لهذا المستخدم</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-text {
        font-size: 48px;
        color: #fff;
        font-weight: bold;
    }
</style>
@endsection