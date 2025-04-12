@extends('layouts.app')

@section('title', 'لوحة تحكم المسؤول')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-xl font-bold">لوحة تحكم المسؤول</h1>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-primary-100 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">المستخدمين</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['users'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-success-100 text-success">
                        <i class="fas fa-building"></i>
                    </div>
                    <h5 class="card-title">الوكالات</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['agencies'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-info-100 text-info">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h5 class="card-title">الخدمات</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['services'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-warning-100 text-warning">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title">الطلبات</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['requests'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-danger-100 text-danger">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h5 class="card-title">عروض الأسعار</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['quotes'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-secondary-100 text-secondary">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h5 class="card-title">المعاملات</h5>
                    <h3 class="mb-0 font-weight-bold">{{ $stats['transactions'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- بيانات المستخدمين -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">إحصائيات المستخدمين</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:200px;">
                        <canvas id="userStatsChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>الأدمن</span>
                            <span class="badge bg-primary">{{ $userStats['admins'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>وكالات</span>
                            <span class="badge bg-success">{{ $userStats['agencies'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>سبوكلاء</span>
                            <span class="badge bg-info">{{ $userStats['subagents'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>عملاء</span>
                            <span class="badge bg-warning">{{ $userStats['customers'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حالة الطلبات -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">حالة الطلبات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:200px;">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>قيد الانتظار</span>
                            <span class="badge bg-secondary">{{ $requestStats['pending'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>قيد التنفيذ</span>
                            <span class="badge bg-primary">{{ $requestStats['in_progress'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>مكتملة</span>
                            <span class="badge bg-success">{{ $requestStats['completed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>ملغاة</span>
                            <span class="badge bg-danger">{{ $requestStats['cancelled'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإيرادات -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">الإيرادات (الستة أشهر الأخيرة)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث المستخدمين والطلبات -->
    <div class="row">
        <!-- أحدث المستخدمين -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between bg-light">
                    <h5 class="mb-0 font-weight-bold">أحدث المستخدمين</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>النوع</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestUsers as $user)
                                <tr>
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
                                            @default
                                                <span class="badge bg-warning">عميل</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا يوجد مستخدمين حديثين</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between bg-light">
                    <h5 class="mb-0 font-weight-bold">أحدث الطلبات</h5>
                    <a href="{{ route('admin.requests.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestRequests as $request)
                                <tr>
                                    <td>{{ $request->title }}</td>
                                    <td>{{ $request->user->name }}</td>
                                    <td>
                                        @switch($request->status)
                                            @case('pending')
                                                <span class="badge bg-secondary">قيد الانتظار</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-primary">قيد التنفيذ</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">مكتملة</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">ملغاة</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $request->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا يوجد طلبات حديثة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-users me-2"></i> إدارة المستخدمين</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('admin.requests.index') }}" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-clipboard-list me-2"></i> إدارة الطلبات</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('admin.system.logs') }}" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-file-alt me-2"></i> سجلات النظام</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="/admin/settings" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-cog me-2"></i> إعدادات النظام</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني لإحصائيات المستخدمين
    const userChart = new Chart(document.getElementById('userStatsChart'), {
        type: 'doughnut',
        data: {
            labels: ['الأدمن', 'وكالات', 'سبوكلاء', 'عملاء'],
            datasets: [{
                data: [
                    {{ $userStats['admins'] }}, 
                    {{ $userStats['agencies'] }}, 
                    {{ $userStats['subagents'] }}, 
                    {{ $userStats['customers'] }}
                ],
                backgroundColor: ['#3b82f6', '#10b981', '#3b82f6', '#f59e0b'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // رسم بياني لحالة الطلبات
    const requestChart = new Chart(document.getElementById('requestStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['قيد الانتظار', 'قيد التنفيذ', 'مكتملة', 'ملغاة'],
            datasets: [{
                data: [
                    {{ $requestStats['pending'] }}, 
                    {{ $requestStats['in_progress'] }}, 
                    {{ $requestStats['completed'] }}, 
                    {{ $requestStats['cancelled'] }}
                ],
                backgroundColor: ['#9ca3af', '#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // رسم بياني للإيرادات
    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueData['months']) !!},
            datasets: [{
                label: 'الإيرادات',
                data: {!! json_encode($revenueData['revenue']) !!},
                backgroundColor: '#3b82f6',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
