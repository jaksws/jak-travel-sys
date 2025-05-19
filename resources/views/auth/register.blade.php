@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>تسجيل حساب جديد</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم الكامل</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">تأكيد كلمة المرور</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">نوع المستخدم</label>
                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">اختر نوع المستخدم</option>
                                <option value="agency" {{ old('role') == 'agency' ? 'selected' : '' }}>وكيل رئيسي</option>
                                <option value="subagent" {{ old('role') == 'subagent' ? 'selected' : '' }}>سبوكيل</option>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>عميل</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3" id="license_number_group" style="display: none;">
                            <label for="license_number" class="form-label">رقم الترخيص للوكالة</label>
                            <input id="license_number" type="text" class="form-control @error('license_number') is-invalid @enderror" name="license_number" value="{{ old('license_number') }}">
                            @error('license_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3" id="agency_id_group" style="display: none;">
                            <label for="agency_id" class="form-label">اختر الوكالة الرئيسية</label>
                            <select id="agency_id" name="agency_id" class="form-control @error('agency_id') is-invalid @enderror">
                                <option value="">-- اختر الوكالة --</option>
                                @foreach(\App\Models\Agency::orderBy('name')->get() as $agency)
                                    <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                @endforeach
                            </select>
                            @error('agency_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var roleSelect = document.getElementById('role');
                                var licenseGroup = document.getElementById('license_number_group');
                                var agencyGroup = document.getElementById('agency_id_group');
                                function toggleFields() {
                                    if (roleSelect.value === 'agency') {
                                        licenseGroup.style.display = '';
                                        document.getElementById('license_number').setAttribute('required', 'required');
                                        agencyGroup.style.display = 'none';
                                        document.getElementById('agency_id').removeAttribute('required');
                                    } else if (roleSelect.value === 'subagent') {
                                        licenseGroup.style.display = 'none';
                                        document.getElementById('license_number').removeAttribute('required');
                                        agencyGroup.style.display = '';
                                        document.getElementById('agency_id').setAttribute('required', 'required');
                                    } else {
                                        licenseGroup.style.display = 'none';
                                        document.getElementById('license_number').removeAttribute('required');
                                        agencyGroup.style.display = 'none';
                                        document.getElementById('agency_id').removeAttribute('required');
                                    }
                                }
                                roleSelect.addEventListener('change', toggleFields);
                                toggleFields(); // Initial call
                            });
                        </script>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                تسجيل
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ route('login') }}">سجل الدخول</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
