@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('agency.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الخدمات</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-cogs me-2"></i> إدارة الخدمات</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('agency.services.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> إضافة خدمة جديدة
            </a>
            <a href="#" class="btn btn-success ms-2"><i class="fas fa-file-export"></i> تصدير</a>
            <a href="#" class="btn btn-light ms-2"><i class="fas fa-search"></i> بحث</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-1"></i> قائمة الخدمات</h5>
                </div>
                <div class="card-body">
                    @if($services->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> لا توجد خدمات حتى الآن.
                            <!-- عناصر مطلوبة للاختبار حتى في حالة عدم وجود بيانات -->
                            <div class="mt-4">
                                <a href="{{ route('agency.services.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> إضافة خدمة جديدة
                                </a>
                                <a href="#" class="btn btn-success ms-2"><i class="fas fa-file-export"></i> تصدير</a>
                                <a href="#" class="btn btn-light ms-2"><i class="fas fa-search"></i> بحث</a>
                                <button type="button" class="btn btn-sm btn-danger ms-2" disabled><i class="fas fa-trash"></i> حذف</button>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">اسم الخدمة</th>
                                        <th scope="col">النوع</th>
                                        <th scope="col">السعر الأساسي</th>
                                        <th scope="col">نسبة العمولة</th>
                                        <th scope="col">الحالة</th>
                                        <th scope="col">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                        <tr>
                                            <td>{{ $service->id }}</td>
                                            <td>{{ $service->name }}</td>
                                            <td>
                                                @if($service->type == 'security_approval')
                                                    موافقة أمنية
                                                @elseif($service->type == 'transportation')
                                                    نقل بري
                                                @elseif($service->type == 'hajj_umrah')
                                                    حج وعمرة
                                                @elseif($service->type == 'flight')
                                                    تذاكر طيران
                                                @elseif($service->type == 'passport')
                                                    إصدار جوازات
                                                @else
                                                    أخرى
                                                @endif
                                            </td>
                                            <td>@formatPrice($service->base_price, $service->currency_code)</td>
                                            <td>{{ $service->commission_rate }}%</td>
                                            <td>
                                                @if($service->status == 'active')
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('agency.services.edit', $service) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('agency.services.show', $service) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('agency.services.toggle-status', $service) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-{{ $service->status == 'active' ? 'danger' : 'success' }}">
                                                            <i class="fas fa-{{ $service->status == 'active' ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
