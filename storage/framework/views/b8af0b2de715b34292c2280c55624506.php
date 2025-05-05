<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('subagent.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('subagent.services.index')); ?>">الخدمات المتاحة</a></li>
    <li class="breadcrumb-item active"><?php echo e($service->name); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-cog me-2"></i> <?php echo e($service->name); ?></h2>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo e(route('subagent.requests.index', ['service_id' => $service->id])); ?>" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> عرض الطلبات المرتبطة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-1"></i> معلومات الخدمة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>اسم الخدمة:</span>
                                    <strong><?php echo e($service->name); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>نوع الخدمة:</span>
                                    <strong>
                                        <?php if($service->type == 'security_approval'): ?>
                                            موافقة أمنية
                                        <?php elseif($service->type == 'transportation'): ?>
                                            نقل بري
                                        <?php elseif($service->type == 'hajj_umrah'): ?>
                                            حج وعمرة
                                        <?php elseif($service->type == 'flight'): ?>
                                            تذاكر طيران
                                        <?php elseif($service->type == 'passport'): ?>
                                            إصدار جوازات
                                        <?php else: ?>
                                            أخرى
                                        <?php endif; ?>
                                    </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>نسبة العمولة المخصصة:</span>
                                    <strong><?php echo e($serviceSubagent->pivot->custom_commission_rate ?? $service->commission_rate); ?>%</strong>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <?php if($service->image_path): ?>
                                <img src="<?php echo e(asset('storage/' . $service->image_path)); ?>" alt="<?php echo e($service->name); ?>" class="img-fluid rounded">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>وصف الخدمة:</h5>
                        <div class="p-3 border rounded bg-light">
                            <?php echo e($service->description); ?>

                        </div>
                    </div>
                </div>
            </div>

            <?php if(!$requestsHistory->isEmpty()): ?>
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-1"></i> تاريخ عروض الأسعار</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>العميل</th>
                                        <th>السعر المقدم</th>
                                        <th>العمولة</th>
                                        <th>الحالة</th>
                                        <th>تاريخ التقديم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $requestsHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $quote = $request->quotes->where('subagent_id', auth()->id())->first();
                                        ?>
                                        <tr>
                                            <td>#<?php echo e($request->id); ?></td>
                                            <td><?php echo e($request->customer->name); ?></td>
                                            <td><?php echo e($quote->price ?? 'غير متوفر'); ?> ر.س</td>
                                            <td><?php echo e($quote->commission_amount ?? 'غير متوفر'); ?> ر.س</td>
                                            <td>
                                                <?php if(!$quote): ?>
                                                    <span class="badge bg-secondary">غير متوفر</span>
                                                <?php elseif($quote->status == 'pending'): ?>
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
                                            <td><?php echo e($request->created_at->format('Y-m-d')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-1"></i> الإحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-primary"><?php echo e($requestsHistory->count()); ?></h3>
                                <p class="mb-0">إجمالي الطلبات</p>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h3 class="text-success"><?php echo e($requestsHistory->sum(function($req) { return $req->quotes->where('subagent_id', auth()->id())->where('status', 'customer_approved')->count(); })); ?></h3>
                                <p class="mb-0">العروض المقبولة</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> معدل العمولة المخصص لك: <strong><?php echo e($serviceSubagent->pivot->custom_commission_rate ?? $service->commission_rate); ?>%</strong>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-1"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('subagent.requests.index', ['service_id' => $service->id])); ?>" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-search me-1"></i> استعراض الطلبات المتاحة
                    </a>
                    <a href="<?php echo e(route('subagent.quotes.index', ['service_id' => $service->id])); ?>" class="btn btn-info w-100">
                        <i class="fas fa-tag me-1"></i> عروض الأسعار المقدمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/subagent/services/show.blade.php ENDPATH**/ ?>