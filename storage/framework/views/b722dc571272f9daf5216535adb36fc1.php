<?php $__env->startSection('title', 'سجلات النظام'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">سجلات النظام</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" dusk="system-logs-page">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">سجلات النظام</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header">ملفات السجلات</div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php $__empty_1 = true; $__currentLoopData = $logFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <a href="<?php echo e(route('admin.system.logs', ['log' => basename($logFile)])); ?>" 
                                       class="list-group-item list-group-item-action <?php echo e($selectedLog == basename($logFile) ? 'active' : ''); ?>">
                                        <?php echo e(basename($logFile)); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="list-group-item">لا توجد ملفات سجلات</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <?php if($selectedLog): ?>
                                محتوى ملف: <?php echo e($selectedLog); ?>

                            <?php else: ?>
                                محتوى السجل
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if($logContent): ?>
                                <pre class="bg-light p-3" style="max-height: 500px; overflow-y: auto; direction: ltr; text-align: left;"><?php echo e($logContent); ?></pre>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    الرجاء اختيار ملف سجل من القائمة على اليمين لعرض محتواه.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/admin/logs.blade.php ENDPATH**/ ?>