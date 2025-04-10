@extends('layouts.app')

@section('title', 'نصائح استخدام النظام')

@section('breadcrumb')
<div class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></div>
<div class="breadcrumb-item active">نصائح استخدام النظام</div>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5>نصائح استخدام النظام - {{ ucfirst($userRole) }}</h5>
                    <div>
                        <button class="btn btn-sm btn-light" id="toggle-tips-view">
                            <i class="fas fa-th-large"></i>
                            تغيير طريقة العرض
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        مرحباً بك في نظام وكالات السفر. هذه بعض النصائح المخصصة لمساعدتك على البدء كـ <strong>{{ __('roles.'.$userRole) }}</strong>.
                    </div>
                    
                    <div class="row" id="tips-container">
                        @forelse($tips as $tip)
                        <div class="col-md-4 mb-4 tip-item">
                            <div class="card h-100 border-{{ $loop->iteration % 3 == 1 ? 'primary' : ($loop->iteration % 3 == 2 ? 'success' : 'info') }}">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="{{ $tip['icon'] }} fa-3x text-{{ $loop->iteration % 3 == 1 ? 'primary' : ($loop->iteration % 3 == 2 ? 'success' : 'info') }}"></i>
                                    </div>
                                    <h5 class="card-title">{{ $tip['title'] }}</h5>
                                    <p class="card-text">{{ $tip['content'] }}</p>
                                    
                                    @if(isset($tip['action_url']))
                                    <a href="{{ $tip['action_url'] }}" class="btn btn-sm btn-outline-{{ $loop->iteration % 3 == 1 ? 'primary' : ($loop->iteration % 3 == 2 ? 'success' : 'info') }} mt-2">
                                        {{ $tip['action_text'] ?? 'معرفة المزيد' }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center">
                            <div class="alert alert-warning">
                                لا توجد نصائح متاحة حالياً
                            </div>
                        </div>
                        @endforelse
                    </div>
                    
                    <h5 class="mt-5 mb-3 border-bottom pb-2">روابط المساعدة السريعة</h5>
                    <div class="row">
                        @if(isset($helpLinks) && count($helpLinks) > 0)
                            @foreach($helpLinks as $link)
                            <div class="col-md-3 mb-3">
                                <a href="{{ $link['url'] }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                    <i class="{{ $link['icon'] }} fa-2x mb-2"></i>
                                    <span>{{ $link['title'] }}</span>
                                </a>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center">
                                <div class="alert alert-info">
                                    لم يتم تكوين روابط المساعدة لدورك بعد
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ $helpUrl ?? route('help.index') }}" class="btn btn-primary">
                            <i class="fas fa-book-open"></i>
                            الذهاب إلى دليل المستخدم الكامل
                        </a>
                    </div>

                    @if($userRole != 'guest')
                    <div class="mt-4 text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" id="showTipsOnLogin" 
                                {{ auth()->user()->show_tips ? 'checked' : '' }}>
                            <label class="form-check-label" for="showTipsOnLogin">
                                عرض النصائح عند تسجيل الدخول
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تبديل طريقة العرض بين البطاقات والقائمة
        const toggleButton = document.getElementById('toggle-tips-view');
        const tipsContainer = document.getElementById('tips-container');
        const tipItems = document.querySelectorAll('.tip-item');
        
        toggleButton.addEventListener('click', function() {
            if (tipsContainer.classList.contains('row')) {
                // تحويل إلى عرض القائمة
                tipsContainer.classList.remove('row');
                tipItems.forEach(item => {
                    item.classList.remove('col-md-4');
                    item.classList.add('mb-3');
                });
                toggleButton.innerHTML = '<i class="fas fa-th"></i> عرض البطاقات';
            } else {
                // تحويل إلى عرض البطاقات
                tipsContainer.classList.add('row');
                tipItems.forEach(item => {
                    item.classList.add('col-md-4');
                });
                toggleButton.innerHTML = '<i class="fas fa-th-list"></i> عرض القائمة';
            }
        });

        // حفظ تفضيل عرض النصائح عند تسجيل الدخول
        const showTipsCheckbox = document.getElementById('showTipsOnLogin');
        if (showTipsCheckbox) {
            showTipsCheckbox.addEventListener('change', function() {
                fetch('{{ route("user.preferences.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        show_tips: this.checked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // إظهار رسالة نجاح
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                        alertDiv.role = 'alert';
                        alertDiv.innerHTML = `
                            <i class="fas fa-check-circle"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                        `;
                        showTipsCheckbox.closest('.mt-4').appendChild(alertDiv);
                        
                        // إخفاء الرسالة بعد 3 ثواني
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 3000);
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
