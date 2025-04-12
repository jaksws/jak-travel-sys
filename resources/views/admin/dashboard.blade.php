@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('لوحة تحكم المسؤول') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('المستخدمون') }}</h5>
                                    <p class="card-text display-4">{{ $stats['users_count'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('الوكالات') }}</h5>
                                    <p class="card-text display-4">{{ $stats['agencies_count'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('الخدمات') }}</h5>
                                    <p class="card-text display-4">{{ $stats['services_count'] }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('الطلبات') }}</h5>
                                    <p class="card-text display-4">{{ $stats['requests_count'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3>{{ __('الإجراءات السريعة') }}</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">{{ __('إدارة المستخدمين') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('إدارة الوكالات') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('إدارة الخدمات') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('عرض التقارير') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
