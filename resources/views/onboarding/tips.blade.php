@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>نصائح استخدام النظام</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        مرحباً بك في نظام وكالات السفر. هذه بعض النصائح لمساعدتك على البدء.
                    </div>
                    
                    <div class="row">
                        @foreach($tips as $tip)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="{{ $tip['icon'] }} fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">{{ $tip['title'] }}</h5>
                                    <p class="card-text">{{ $tip['content'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <h5 class="mt-5 mb-3 border-bottom pb-2">روابط المساعدة السريعة</h5>
                    <div class="row">
                        @foreach($helpLinks as $link)
                        <div class="col-md-3 mb-3">
                            <a href="{{ $link['url'] }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="{{ $link['icon'] }} fa-2x mb-2"></i>
                                <span>{{ $link['title'] }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ $helpUrl }}" class="btn btn-primary">
                            <i class="fas fa-book-open"></i>
                            الذهاب إلى دليل المستخدم الكامل
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
