<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">لوحة التحكم</h2>
            <p class="text-muted">مرحباً، <?php echo e(auth()->user()->name); ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <!-- شريط إجراءات سريع للاختبارات -->
            <div class="mb-3 d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('customer.services.index')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> طلب خدمة جديدة</a>
                <a href="<?php echo e(route('customer.requests.index')); ?>" class="btn btn-info">طلباتي</a>
                <a href="<?php echo e(route('customer.quotes.index')); ?>" class="btn btn-success">عروضي</a>
                <a href="<?php echo e(route('customer.requests.index')); ?>" class="btn btn-light"><i class="fas fa-search"></i> بحث</a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
                <a href="#" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل الخروج</a>
            </div>
        </div>
    </div>

    <!-- بطاقات إحصائية -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-bg bg-primary text-white">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">طلبات الخدمة</h6>
                        <h3 class="mb-0"><?php echo e($counts['requests'] ?? 0); ?></h3>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?php echo e(route('customer.requests.index')); ?>" class="text-decoration-none">
                        عرض الكل <i class="fas fa-arrow-left me-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-bg bg-success text-white">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">عروض الأسعار</h6>
                        <h3 class="mb-0"><?php echo e($counts['quotes'] ?? 0); ?></h3>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?php echo e(route('customer.quotes.index')); ?>" class="text-decoration-none">
                        عرض الكل <i class="fas fa-arrow-left me-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-bg bg-info text-white">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">الطلبات المكتملة</h6>
                        <h3 class="mb-0"><?php echo e($counts['completed'] ?? 0); ?></h3>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?php echo e(route('customer.requests.index', ['status' => 'completed'])); ?>" class="text-decoration-none">
                        عرض الكل <i class="fas fa-arrow-left me-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-bg bg-warning text-white">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">قيد التنفيذ</h6>
                        <h3 class="mb-0"><?php echo e($counts['in_progress'] ?? 0); ?></h3>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?php echo e(route('customer.requests.index', ['status' => 'in_progress'])); ?>" class="text-decoration-none">
                        عرض الكل <i class="fas fa-arrow-left me-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- الطلبات الأخيرة -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الطلبات الأخيرة</h5>
                    <a href="<?php echo e(route('customer.requests.index')); ?>" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <?php if(count($recentRequests ?? []) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">الخدمة</th>
                                    <th scope="col">الحالة</th>
                                    <th scope="col">تاريخ الطلب</th>
                                    <th scope="col">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($request->id); ?></td>
                                    <td><?php echo e($request->service->name); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($request->status_badge); ?>">
                                            <?php echo e($request->status_text); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($request->created_at->format('Y-m-d')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('customer.requests.show', $request)); ?>" class="btn btn-sm btn-outline-primary">عرض</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center p-4">
                        <img src="<?php echo e(asset('img/no-data.svg')); ?>" alt="لا توجد طلبات" width="120" class="mb-3">
                        <p class="text-muted">لا توجد طلبات حتى الآن</p>
                        <a href="<?php echo e(route('customer.services.index')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> طلب خدمة جديدة
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- عروض الأسعار الأخيرة والخدمات المقترحة -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">عروض الأسعار الأخيرة</h5>
                    <a href="<?php echo e(route('customer.quotes.index')); ?>" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <?php if(count($recentQuotes ?? []) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $recentQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('customer.quotes.show', $quote)); ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo e($quote->request->service->name); ?></h6>
                                <small class="text-muted"><?php echo e($quote->created_at->diffForHumans()); ?></small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary"><?php echo e(number_format($quote->price, 2)); ?> <?php echo e($quote->currency_code); ?></span>
                                <span class="badge bg-<?php echo e($quote->status_badge); ?>"><?php echo e($quote->status_text); ?></span>
                            </div>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center p-4">
                        <p class="text-muted">لا توجد عروض أسعار حتى الآن</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">خدمات مقترحة</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $suggestedServices ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('customer.services.show', $service)); ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo e($service->name); ?></h6>
                                <span class="text-primary"><?php echo e(number_format($service->base_price, 2)); ?> <?php echo e($service->currency_code); ?></span>
                            </div>
                            <small class="text-muted"><?php echo e(\App\Helpers\ServiceTypeHelper::getLocalizedType($service->type)); ?></small>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center p-4">
                            <p class="text-muted">لا توجد خدمات مقترحة حاليًا</p>
                            <a href="<?php echo e(route('customer.services.index')); ?>" class="btn btn-sm btn-outline-primary">
                                استعراض كافة الخدمات
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-bg {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/customer/dashboard.blade.php ENDPATH**/ ?>