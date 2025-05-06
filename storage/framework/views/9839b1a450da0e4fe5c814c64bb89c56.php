<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">لوحة تحكم السبوكيل</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">لوحة تحكم السبوكيل</h1>
            
            <div class="row mb-4">
                <div class="col-12">
                    <!-- شريط إجراءات سريع للاختبارات -->
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        <a href="<?php echo e(route('subagent.requests.index')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> تقديم عرض سعر</a>
                        <a href="<?php echo e(route('subagent.requests.index')); ?>" class="btn btn-info">طلباتي</a>
                        <a href="<?php echo e(route('subagent.quotes.index')); ?>" class="btn btn-success">عروضي</a>
                        <a href="<?php echo e(route('subagent.requests.index')); ?>" class="btn btn-light"><i class="fas fa-search"></i> بحث</a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
                        <a href="#" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">تسجيل الخروج</a>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- إحصائيات سريعة -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">الخدمات المتاحة</h5>
                            <p class="card-text display-4"><?php echo e($services); ?></p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="<?php echo e(route('subagent.services.index')); ?>">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-4">
                        <div class="card-body">
                            <h5 class="card-title">العروض المعتمدة</h5>
                            <p class="card-text display-4"><?php echo e($approvedQuotes); ?></p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="<?php echo e(route('subagent.quotes.index', ['status' => 'approved'])); ?>">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-4">
                        <div class="card-body">
                            <h5 class="card-title">العروض المعلقة</h5>
                            <p class="card-text display-4"><?php echo e($pendingQuotes); ?></p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="<?php echo e(route('subagent.quotes.index', ['status' => 'pending'])); ?>">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-4">
                        <div class="card-body">
                            <h5 class="card-title">طلبات متاحة</h5>
                            <p class="card-text display-4"><?php echo e($availableRequests); ?></p>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="<?php echo e(route('subagent.requests.index')); ?>">عرض التفاصيل</a>
                            <div class="small text-white"><i class="fas fa-angle-left"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-tag me-2"></i> آخر عروض الأسعار</h5>
                        </div>
                        <div class="card-body">
                            <?php if($recentQuotes->isEmpty()): ?>
                                <div class="alert alert-info">
                                    لم تقم بتقديم أي عروض أسعار حتى الآن.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الطلب</th>
                                                <th>الخدمة</th>
                                                <th>السعر</th>
                                                <th>الحالة</th>
                                                <th>تاريخ التقديم</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $recentQuotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($quote->id); ?></td>
                                                    <td>#<?php echo e($quote->request_id); ?></td>
                                                    <td><?php echo e($quote->request->service->name); ?></td>
                                                    <td><?php echo e($quote->price); ?> ر.س</td>
                                                    <td>
                                                        <?php if($quote->status == 'pending'): ?>
                                                            <span class="badge bg-warning">بانتظار الموافقة</span>
                                                        <?php elseif($quote->status == 'agency_approved'): ?>
                                                            <span class="badge bg-info">معتمد من الوكالة</span>
                                                        <?php elseif($quote->status == 'agency_rejected'): ?>
                                                            <span class="badge bg-danger">مرفوض من الوكالة</span>
                                                        <?php elseif($quote->status == 'customer_approved'): ?>
                                                            <span class="badge bg-success">مقبول من العميل</span>
                                                        <?php elseif($quote->status == 'customer_rejected'): ?>
                                                            <span class="badge bg-danger">مرفوض من العميل</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($quote->created_at->format('Y-m-d')); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('subagent.quotes.show', $quote)); ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <a href="<?php echo e(route('subagent.quotes.index')); ?>" class="btn btn-outline-primary">عرض كل العروض</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i> إجراءات سريعة</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <a href="<?php echo e(route('subagent.requests.index')); ?>" class="btn btn-primary w-100 py-3">
                                    <i class="fas fa-search me-2"></i> استعراض الطلبات المتاحة
                                </a>
                            </div>
                            <div class="mb-4">
                                <a href="<?php echo e(route('subagent.services.index')); ?>" class="btn btn-info w-100 py-3">
                                    <i class="fas fa-cogs me-2"></i> استعراض الخدمات المتاحة
                                </a>
                            </div>
                            <div>
                                <a href="<?php echo e(route('subagent.quotes.index')); ?>" class="btn btn-warning w-100 py-3">
                                    <i class="fas fa-tag me-2"></i> عروض الأسعار المقدمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i> مرحباً <?php echo e(auth()->user()->name); ?>!</h5>
                <p>من هنا يمكنك إدارة عروض الأسعار المقدمة منك ومتابعة حالتها واستعراض الطلبات المتاحة وتقديم عروض جديدة.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/subagent/dashboard.blade.php ENDPATH**/ ?>