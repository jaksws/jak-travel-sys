<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">لوحة التحكم</h1>
        <div>
            <?php if(Route::has('agency.reports.export')): ?>
                <a href="<?php echo e(route('agency.reports.export')); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50 ml-2"></i> تصدير التقرير
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('agency.reports.index')); ?>" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-chart-bar fa-sm text-white-50 ml-2"></i> التقارير
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('agency.requests.create')); ?>" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 ml-2"></i> إنشاء طلب جديد
            </a>
        </div>
    </div>

    <!-- شريط إجراءات سريع للاختبارات -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="mb-3 d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('agency.subagents.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة سبوكيل</a>
                <a href="<?php echo e(route('agency.services.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> إضافة خدمة</a>
                <a href="<?php echo e(route('agency.reports.export')); ?>" class="btn btn-info"><i class="fas fa-file-export"></i> تصدير</a>
                <a href="<?php echo e(route('agency.requests.index')); ?>" class="btn btn-light"><i class="fas fa-search"></i> بحث</a>
                <a href="<?php echo e(route('agency.subagents.index')); ?>" class="btn btn-outline-primary">إدارة السبوكلاء</a>
                <a href="<?php echo e(route('agency.services.index')); ?>" class="btn btn-outline-secondary">إدارة الخدمات</a>
                <a href="<?php echo e(route('agency.settings.index')); ?>" class="btn btn-outline-dark">إعدادات الوكالة</a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
                <a href="#" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل الخروج</a>
            </div>
        </div>
    </div>

    <!-- Content Row - Main Stats -->
    <div class="row">
        <!-- إجمالي الطلبات -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلبات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['requests_count']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الطلبات قيد التنفيذ -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">قيد التنفيذ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['in_progress_requests_count']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عدد العملاء -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">عدد العملاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['customers_count']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عدد السبوكلاء -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-right-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">عدد السبوكلاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['subagents_count']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- الطلبات حسب الشهور -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">الطلبات حسب الشهور</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="requestsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع حالات الطلبات -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع حالات الطلبات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> قيد الانتظار
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> قيد التنفيذ
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> مكتملة
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Top Services & Subagents -->
    <div class="row">
        <!-- الخدمات الأكثر طلباً -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الخدمات الأكثر طلباً</h6>
                </div>
                <div class="card-body">
                    <?php if($topServices->isEmpty()): ?>
                        <div class="text-center text-muted">
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $topServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <h4 class="small font-weight-bold">
                                <?php echo e($service->service->name); ?>

                                <span class="float-right"><?php echo e($service->count); ?> طلب</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo e(($service->count / $topServices->max('count')) * 100); ?>%" 
                                     aria-valuenow="<?php echo e($service->count); ?>" aria-valuemin="0" aria-valuemax="<?php echo e($topServices->max('count')); ?>"></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- السبوكلاء الأكثر نشاطاً -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">السبوكلاء الأكثر نشاطاً</h6>
                </div>
                <div class="card-body">
                    <?php if($topSubagents->isEmpty()): ?>
                        <div class="text-center text-muted">
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    <?php else: ?>
                        <?php $__currentLoopData = $topSubagents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subagent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <h4 class="small font-weight-bold">
                                <?php echo e($subagent->subagent->name); ?>

                                <span class="float-right"><?php echo e($subagent->count); ?> عرض</span>
                            </h4>
                            <div class="progress mb-4">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e(($subagent->count / $topSubagents->max('count')) * 100); ?>%" 
                                     aria-valuenow="<?php echo e($subagent->count); ?>" aria-valuemin="0" aria-valuemax="<?php echo e($topSubagents->max('count')); ?>"></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Latest Activities -->
    <div class="row">
        <!-- آخر الطلبات -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر الطلبات</h6>
                </div>
                <div class="card-body">
                    <?php if($latestRequests->isEmpty()): ?>
                        <div class="text-center text-muted">
                            <p>لا توجد طلبات حتى الآن</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>العميل</th>
                                        <th>الخدمة</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $latestRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><a href="<?php echo e(route('agency.requests.show', $request)); ?>">#<?php echo e($request->id); ?></a></td>
                                            <td><?php echo e($request->customer->name); ?></td>
                                            <td><?php echo e($request->service->name); ?></td>
                                            <td>
                                                <?php if($request->status == 'pending'): ?>
                                                    <span class="badge badge-warning">قيد الانتظار</span>
                                                <?php elseif($request->status == 'in_progress'): ?>
                                                    <span class="badge badge-info">قيد التنفيذ</span>
                                                <?php elseif($request->status == 'completed'): ?>
                                                    <span class="badge badge-success">مكتمل</span>
                                                <?php elseif($request->status == 'cancelled'): ?>
                                                    <span class="badge badge-danger">ملغي</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($request->created_at->format('Y-m-d')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?php echo e(route('agency.requests.index')); ?>" class="btn btn-sm btn-primary">عرض كل الطلبات</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- آخر عروض الأسعار -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر عروض الأسعار</h6>
                </div>
                <div class="card-body">
                    <?php if($latestQuotes->isEmpty()): ?>
                        <div class="text-center text-muted">
                            <p>لا توجد عروض أسعار حتى الآن</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>السبوكيل</th>
                                        <th>الخدمة</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $latestQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><a href="<?php echo e(route('agency.quotes.show', $quote)); ?>">#<?php echo e($quote->id); ?></a></td>
                                            <td><?php echo e($quote->subagent->name); ?></td>
                                            <td><?php echo e($quote->request->service->name); ?></td>
                                            <td><?php echo \App\Helpers\CurrencyHelper::formatPrice($quote->price, $quote->currency_code); ?></td>
                                            <td>
                                                <?php if($quote->status == 'pending'): ?>
                                                    <span class="badge badge-warning">قيد الانتظار</span>
                                                <?php elseif($quote->status == 'agency_approved'): ?>
                                                    <span class="badge badge-info">معتمد من الوكالة</span>
                                                <?php elseif($quote->status == 'agency_rejected'): ?>
                                                    <span class="badge badge-danger">مرفوض من الوكالة</span>
                                                <?php elseif($quote->status == 'customer_approved'): ?>
                                                    <span class="badge badge-success">مقبول من العميل</span>
                                                <?php elseif($quote->status == 'customer_rejected'): ?>
                                                    <span class="badge badge-danger">مرفوض من العميل</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?php echo e(route('agency.quotes.index')); ?>" class="btn btn-sm btn-primary">عرض كل العروض</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // الطلبات حسب الشهور
        var requestsCtx = document.getElementById('requestsChart');
        var requestsChart = new Chart(requestsCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php $__currentLoopData = $requestsByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        "<?php echo e($item['month']); ?>",
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [{
                    label: 'عدد الطلبات',
                    data: [
                        <?php $__currentLoopData = $requestsByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($item['count']); ?>,
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // توزيع حالات الطلبات
        var statusCtx = document.getElementById('requestStatusChart');
        var statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['قيد الانتظار', 'قيد التنفيذ', 'مكتمل'],
                datasets: [{
                    data: [
                        <?php echo e($stats['pending_requests_count']); ?>,
                        <?php echo e($stats['in_progress_requests_count']); ?>,
                        <?php echo e($stats['completed_requests_count']); ?>

                    ],
                    backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a'],
                    hoverBackgroundColor: ['#e0b033', '#2ca8bb', '#17a673'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)',
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/agency/dashboard.blade.php ENDPATH**/ ?>