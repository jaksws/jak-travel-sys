@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">لوحة تحكم المسؤول</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5>المستخدمين</h5>
                                    <h2>{{ $stats['users_count'] }}</h2>
                                    <a href="{{ route('admin.users.index', [], false) }}" class="btn btn-sm btn-outline-primary mt-2">إدارة المستخدمين</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5>الوكالات</h5>
                                    <h2>{{ $stats['agencies_count'] }}</h2>
                                    <a href="{{ route('admin.users.index', [], false) }}" class="btn btn-sm btn-outline-primary mt-2">إدارة الوكالات</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <h5>سجل النظام</h5>
                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                    <br>
                                    <a href="{{ route('admin.system.logs', [], false) }}" class="btn btn-sm btn-outline-primary mt-2">عرض السجلات</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    أحدث النشاطات
                                </div>
                                <div class="card-body">
                                    <p>لا توجد نشاطات حديثة</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
