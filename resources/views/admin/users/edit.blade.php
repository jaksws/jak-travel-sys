@extends('layouts.app')

@section('title', 'تعديل بيانات المستخدم')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">إدارة المستخدمين</a></li>
    <li class="breadcrumb-item active">تعديل {{ $user->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل بيانات المستخدم</h1>
        <div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i> عرض التفاصيل
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold">بيانات المستخدم</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="اتركها فارغة للاحتفاظ بكلمة المرور الحالية">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">اترك هذا الحقل فارغًا إذا كنت لا تريد تغيير كلمة المرور</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="role" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مسؤول</option>
                            <option value="agency" {{ old('role', $user->role) == 'agency' ? 'selected' : '' }}>وكالة</option>
                            <option value="subagent" {{ old('role', $user->role) == 'subagent' ? 'selected' : '' }}>سبوكيل</option>
                            <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>عميل</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row g-3" id="agency-details" {{ $user->role === 'agency' ? '' : 'style=display:none;' }}>
                    <div class="col-12">
                        <h4>معلومات الوكالة</h4>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_name" class="form-label">اسم الوكالة</label>
                        <input type="text" class="form-control" id="agency_name" name="agency_name" value="{{ $user->agency->name ?? old('agency_name') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_address" class="form-label">العنوان</label>
                        <input type="text" class="form-control" id="agency_address" name="agency_address" value="{{ $user->agency->address ?? old('agency_address') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="agency_phone" name="agency_phone" value="{{ $user->agency->phone ?? old('agency_phone') }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_license_number" class="form-label">رقم الترخيص</label>
                        <input type="text" class="form-control" id="agency_license_number" name="agency_license_number" value="{{ $user->agency->license_number ?? old('agency_license_number') }}">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        <i class="fas fa-trash me-1"></i> حذف المستخدم
                    </button>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نموذج تأكيد الحذف -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    هل أنت متأكد من رغبتك في حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه وسيؤدي إلى حذف جميع البيانات المرتبطة به.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">نعم، قم بالحذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // عرض حقول الوكالة عند اختيار نوع المستخدم كوكالة
        const roleSelect = document.getElementById('role');
        const agencyDetails = document.getElementById('agency-details');
        
        roleSelect.addEventListener('change', function() {
            if (this.value === 'agency') {
                agencyDetails.style.display = 'flex';
            } else {
                agencyDetails.style.display = 'none';
            }
        });
        
        // تبديل إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type');
            passwordInput.setAttribute('type', type === 'password' ? 'text' : 'password');
            
            // تغيير الأيقونة
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-eye')) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endpush
@endsection