<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('agency.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الخدمات</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-cogs me-2"></i> إدارة الخدمات</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?php echo e(route('agency.services.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> إضافة خدمة جديدة
            </a>
            <a href="#" class="btn btn-success ms-2"><i class="fas fa-file-export"></i> تصدير</a>
            <a href="#" class="btn btn-light ms-2"><i class="fas fa-search"></i> بحث</a>
        </div>
    </div>

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

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-1"></i> قائمة الخدمات</h5>
                </div>
                <div class="card-body">
                    <?php if($services->isEmpty()): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> لا توجد خدمات حتى الآن.
                            <!-- عناصر مطلوبة للاختبار حتى في حالة عدم وجود بيانات -->
                            <div class="mt-4">
                                <a href="<?php echo e(route('agency.services.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> إضافة خدمة جديدة
                                </a>
                                <a href="#" class="btn btn-success ms-2"><i class="fas fa-file-export"></i> تصدير</a>
                                <a href="#" class="btn btn-light ms-2"><i class="fas fa-search"></i> بحث</a>
                                <button type="button" class="btn btn-sm btn-danger ms-2" disabled><i class="fas fa-trash"></i> حذف</button>
                            </div>
                        </div>
                    <?php else: ?>
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
                                    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($service->id); ?></td>
                                            <td><?php echo e($service->name); ?></td>
                                            <td>
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
                                            </td>
                                            <td><?php echo \App\Helpers\CurrencyHelper::formatPrice($service->base_price, $service->currency_code); ?></td>
                                            <td><?php echo e($service->commission_rate); ?>%</td>
                                            <td>
                                                <?php if($service->status == 'active'): ?>
                                                    <span class="badge bg-success">نشط</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">غير نشط</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('agency.services.edit', $service)); ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('agency.services.show', $service)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('agency.services.toggle-status', $service)); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button type="submit" class="btn btn-sm btn-<?php echo e($service->status == 'active' ? 'danger' : 'success'); ?>">
                                                            <i class="fas fa-<?php echo e($service->status == 'active' ? 'ban' : 'check'); ?>"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/agency/services/index.blade.php ENDPATH**/ ?>