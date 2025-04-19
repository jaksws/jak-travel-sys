@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة المستخدمين</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus"></i> إضافة مستخدم جديد
        </a>
    </div>

    <!-- بطاقة البحث والتصفية -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">بحث وتصفية</h6>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-redo"></i> إعادة تعيين
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="ابحث بالاسم أو البريد الإلكتروني" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مسؤول</option>
                            <option value="agency" {{ request('role') == 'agency' ? 'selected' : '' }}>وكالة</option>
                            <option value="subagent" {{ request('role') == 'subagent' ? 'selected' : '' }}>سبوكيل</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>عميل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="order_by" class="form-select">
                            <option value="created_at" {{ request('order_by', 'created_at') == 'created_at' ? 'selected' : '' }}>تاريخ التسجيل</option>
                            <option value="name" {{ request('order_by') == 'name' ? 'selected' : '' }}>الاسم</option>
                            <option value="email" {{ request('order_by') == 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                            <option value="role" {{ request('order_by') == 'role' ? 'selected' : '' }}>نوع المستخدم</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المستخدمين -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">قائمة المستخدمين ({{ $users->total() }})</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>نوع المستخدم</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="badge bg-primary">مسؤول</span>
                                            @break
                                        @case('agency')
                                            <span class="badge bg-success">وكالة</span>
                                            @break
                                        @case('subagent')
                                            <span class="badge bg-info">سبوكيل</span>
                                            @break
                                        @case('customer')
                                            <span class="badge bg-warning">عميل</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $user->role }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">معطل</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-{{ $user->status === 'active' ? 'danger' : 'success' }}" 
                                                title="{{ $user->status === 'active' ? 'تعطيل' : 'تفعيل' }}"
                                                onclick="document.getElementById('toggle-form-{{ $user->id }}').submit();">
                                            <i class="fas fa-{{ $user->status === 'active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                        <form id="toggle-form-{{ $user->id }}" action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم العثور على أي مستخدمين</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- نموذج إضافة مستخدم جديد -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">نوع المستخدم</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="admin">مسؤول</option>
                                <option value="agency">وكالة</option>
                                <option value="subagent">سبوكيل</option>
                                <option value="customer" selected>عميل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" selected>نشط</option>
                                <option value="inactive">معطل</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
