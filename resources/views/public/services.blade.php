@extends('layouts.app')

@section('title', 'الخدمات المتاحة')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1><i class="fas fa-cogs me-2"></i> خدماتنا المتاحة</h1>
            <p class="lead text-muted">استعرض مجموعة الخدمات المتنوعة التي نقدمها لعملائنا الكرام</p>
        </div>
    </div>

    @if(isset($services) && !$services->isEmpty())
        @foreach($services as $type => $typeServices)
            <div class="mb-5">
                <h3 class="mb-4 text-center border-bottom pb-2">
                    @if($type == 'security_approval')
                        <i class="fas fa-shield-alt me-2 text-primary"></i> خدمات الموافقات الأمنية
                    @elseif($type == 'transportation')
                        <i class="fas fa-bus me-2 text-primary"></i> خدمات النقل البري
                    @elseif($type == 'hajj_umrah')
                        <i class="fas fa-kaaba me-2 text-primary"></i> خدمات الحج والعمرة
                    @elseif($type == 'flight')
                        <i class="fas fa-plane me-2 text-primary"></i> خدمات تذاكر الطيران
                    @elseif($type == 'passport')
                        <i class="fas fa-passport me-2 text-primary"></i> خدمات الجوازات
                    @else
                        <i class="fas fa-cog me-2 text-primary"></i> خدمات أخرى
                    @endif
                </h3>
                <div class="row justify-content-center">
                    @foreach($typeServices as $service)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm feature-card">
                                @if($service->image_path)
                                    <img src="{{ asset('storage/' . $service->image_path) }}" class="card-img-top" alt="{{ $service->name }}" style="height: 220px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $service->name }}</h5>
                                    <p class="card-text text-muted flex-grow-1">{{ Str::limit($service->description, 120) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="fw-bold text-primary fs-5">{{ $service->base_price }} ر.س</span>
                                        {{-- Add a link to service details page if needed, or a contact/request button --}}
                                        {{-- Example: <a href="#" class="btn btn-sm btn-outline-primary">طلب الخدمة</a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i> لا توجد خدمات متاحة للعرض حالياً.
        </div>
    @endif

    <div class="text-center mt-5">
        <p class="text-muted">هل أنت وكالة أو سبوكيل؟</p>
        <a href="{{ route('register') }}" class="btn btn-primary me-2">
            <i class="fas fa-user-plus me-1"></i> سجل الآن
        </a>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
            <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .feature-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
