@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5>خطأ في الوصول للدليل</h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                    <h4>{{ $message }}</h4>
                    <p class="text-muted mt-3">يرجى التواصل مع الدعم الفني للمساعدة</p>
                    
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> العودة للرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
