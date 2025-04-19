@extends('layouts.app')

@section('title', 'تحليلات وإحصائيات التطبيق')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">تحليلات وإحصائيات التطبيق</li>
@endsection

@section('styles')
<style>
    .stats-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .stats-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: bold;
    }
    .stats-card-body {
        padding: 20px;
    }
    .stat-number {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 0;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 20px;
        margin-right: 15px;
    }
    .bg-soft-primary {
        background-color: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }
    .bg-soft-warning {
        background-color: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }
    .bg-soft-success {
        background-color: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    .bg-soft-danger {
        background-color: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .chart-container {
        height: 300px;
        margin-bottom: 20px;
    }
    .top-pages-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .top-pages-item:last-child {
        border-bottom: none;
    }
    .top-page-path {
        font-weight: 500;
    }
    .top-page-visits {
        color: #6c757d;
    }
    .server-status-item {
        margin-bottom: 15px;
    }
    .server-status-label {
        margin-bottom: 5px;
        display: flex;
        justify-content: space-between;
    }
    .server-status-progress {
        height: 8px;
        border-radius: 4px;
    }
    .downloads-container {
        display: flex;
        gap: 10px;
    }
    .download-item {
        flex: 1;
        text-align: center;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
    }
    .download-item i {
        font-size: 24px;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-xl font-bold">تحليلات وإحصائيات التطبيق</h1>
    
    <!-- إحصائيات الزيارات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-primary">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <div class="stat-number">{{ number_format($visitorStats['total']) }}</div>
                            <p class="stat-label">إجمالي الزيارات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-number">{{ number_format($visitorStats['unique']) }}</div>
                            <p class="stat-label">الزوار الفريدون</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="stat-number">{{ round($visitorStats['average_time'] / 60, 1) }} د</div>
                            <p class="stat-label">متوسط وقت التصفح</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div>
                            <div class="stat-number">{{ $visitorStats['bounce_rate'] }}%</div>
                            <p class="stat-label">معدل المغادرة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- مخطط الزيارات -->
        <div class="col-lg-8 mb-4">
            <div class="stats-card h-100">
                <div class="stats-card-header">
                    الزيارات خلال آخر 7 أيام
                </div>
                <div class="stats-card-body">
                    <div class="chart-container">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- الصفحات الأكثر زيارة -->
        <div class="col-lg-4 mb-4">
            <div class="stats-card h-100">
                <div class="stats-card-header">
                    الصفحات الأكثر زيارة
                </div>
                <div class="stats-card-body">
                    @foreach ($topPages as $page)
                        <div class="top-pages-item">
                            <div class="top-page-path">
                                {{ $page['title'] }}
                                <div class="small text-muted">{{ $page['path'] }}</div>
                            </div>
                            <div class="top-page-visits">{{ number_format($page['visits']) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- إحصائيات المتصفحات -->
        <div class="col-md-4 mb-4">
            <div class="stats-card h-100">
                <div class="stats-card-header">
                    المتصفحات المستخدمة
                </div>
                <div class="stats-card-body">
                    <div style="height: 250px;">
                        <canvas id="browsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات الأجهزة -->
        <div class="col-md-4 mb-4">
            <div class="stats-card h-100">
                <div class="stats-card-header">
                    الأجهزة المستخدمة
                </div>
                <div class="stats-card-body">
                    <div style="height: 250px;">
                        <canvas id="devicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- تحميلات التطبيق -->
        <div class="col-md-4 mb-4">
            <div class="stats-card h-100">
                <div class="stats-card-header">
                    تحميلات التطبيق
                </div>
                <div class="stats-card-body">
                    <div class="downloads-container mb-4">
                        <div class="download-item">
                            <i class="fab fa-android text-success"></i>
                            <h4 class="mb-1">Android</h4>
                            <h2>{{ number_format($downloads['android']) }}</h2>
                        </div>
                        <div class="download-item">
                            <i class="fab fa-apple text-dark"></i>
                            <h4 class="mb-1">iOS</h4>
                            <h2>{{ number_format($downloads['ios']) }}</h2>
                        </div>
                    </div>
                    <div style="height: 150px;">
                        <canvas id="downloadsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- حالة الخادم -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="stats-card-header">
                    حالة الخادم
                </div>
                <div class="stats-card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="server-status-item">
                                <div class="server-status-label">
                                    <span>وقت التشغيل</span>
                                    <span class="text-primary">{{ $serverStatus['uptime'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ str_replace('%', '', $serverStatus['uptime']) }}%" 
                                         aria-valuenow="{{ str_replace('%', '', $serverStatus['uptime']) }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="server-status-item">
                                <div class="server-status-label">
                                    <span>وقت الاستجابة</span>
                                    <span class="text-success">{{ $serverStatus['response_time'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ (intval(str_replace('ms', '', $serverStatus['response_time'])) / 200) * 100 }}%" 
                                         aria-valuenow="{{ (intval(str_replace('ms', '', $serverStatus['response_time'])) / 200) * 100 }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="server-status-item">
                                <div class="server-status-label">
                                    <span>استخدام الذاكرة</span>
                                    <span class="text-warning">{{ $serverStatus['memory_usage'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ str_replace('%', '', $serverStatus['memory_usage']) }}%" 
                                         aria-valuenow="{{ str_replace('%', '', $serverStatus['memory_usage']) }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="server-status-item">
                                <div class="server-status-label">
                                    <span>استخدام القرص</span>
                                    <span class="text-info">{{ $serverStatus['disk_usage'] }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ str_replace('%', '', $serverStatus['disk_usage']) }}%" 
                                         aria-valuenow="{{ str_replace('%', '', $serverStatus['disk_usage']) }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // مخطط الزيارات
    const visitorsChart = new Chart(document.getElementById('visitorsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($visitorsData['dates']) !!},
            datasets: [{
                label: 'الزيارات',
                data: {!! json_encode($visitorsData['visits']) !!},
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // مخطط المتصفحات
    const browsersChart = new Chart(document.getElementById('browsersChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($browsers)) !!},
            datasets: [{
                data: {!! json_encode(array_values($browsers)) !!},
                backgroundColor: ['#3b82f6', '#f97316', '#10b981', '#6366f1', '#8b5cf6'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '65%'
        }
    });
    
    // مخطط الأجهزة
    const devicesChart = new Chart(document.getElementById('devicesChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($devices)) !!},
            datasets: [{
                data: {!! json_encode(array_values($devices)) !!},
                backgroundColor: ['#10b981', '#f97316', '#3b82f6', '#8b5cf6'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '65%'
        }
    });
    
    // مخطط التحميلات
    const downloadsChart = new Chart(document.getElementById('downloadsChart'), {
        type: 'bar',
        data: {
            labels: ['Android', 'iOS'],
            datasets: [{
                label: 'التحميلات',
                data: [{{ $downloads['android'] }}, {{ $downloads['ios'] }}],
                backgroundColor: ['#10b981', '#000000'],
                borderWidth: 0,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
@endpush