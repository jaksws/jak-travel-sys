<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('subagent.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">الخدمات المتاحة</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-cogs me-2"></i> الخدمات المتاحة</h2>
            <p class="text-muted">استعرض الخدمات المتاحة لك وقدم عروض أسعار للطلبات المرتبطة بها</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-end">
            <a href="<?php echo e(route('subagent.services.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> إضافة خدمة جديدة
            </a>
        </div>
    </div>

    <?php if(auth()->check()): ?>
        <div class="alert alert-info">
            نوع المستخدم الحالي: <?php echo e(auth()->user()->role); ?>

        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($services->isEmpty()): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> لا توجد خدمات متاحة لك حالياً. يرجى التواصل مع الوكالة للحصول على المزيد من المعلومات.
        </div>
    <?php else: ?>
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $typeServices): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <?php if($type == 'security_approval'): ?>
                            <i class="fas fa-shield-alt me-1"></i> خدمات الموافقات الأمنية
                        <?php elseif($type == 'transportation'): ?>
                            <i class="fas fa-bus me-1"></i> خدمات النقل البري
                        <?php elseif($type == 'hajj_umrah'): ?>
                            <i class="fas fa-kaaba me-1"></i> خدمات الحج والعمرة
                        <?php elseif($type == 'flight'): ?>
                            <i class="fas fa-plane me-1"></i> خدمات تذاكر الطيران
                        <?php elseif($type == 'passport'): ?>
                            <i class="fas fa-passport me-1"></i> خدمات الجوازات
                        <?php else: ?>
                            <i class="fas fa-cog me-1"></i> خدمات أخرى
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $typeServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <?php if($service->image_path): ?>
                                        <img src="<?php echo e(asset('storage/' . $service->image_path)); ?>" class="card-img-top" alt="<?php echo e($service->name); ?>" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light text-center py-5">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo e($service->name); ?></h5>
                                        <p class="card-text"><?php echo e(Str::limit($service->description, 100)); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>نسبة العمولة: <strong><?php echo e($service->pivot->custom_commission_rate ?? $service->commission_rate); ?>%</strong></span>
                                            <a href="<?php echo e(route('subagent.services.show', $service)); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle me-1"></i> التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/subagent/services/index.blade.php ENDPATH**/ ?>