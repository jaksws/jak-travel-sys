<?php $__env->startSection('title', 'تفاصيل عرض السعر'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">الرئيسية</a></li>
        <li class="breadcrumb-item active">عرض السعر #<?php echo e($quote->id); ?></li>
    </ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل عرض السعر #<?php echo e($quote->id); ?></h5>
                    <span class="badge badge-<?php echo e($quote->status_badge); ?>"><?php echo e($quote->status_text); ?></span>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">معلومات عرض السعر</h6>
                                <hr>
                                <p><strong>رقم الطلب:</strong> #<?php echo e($quote->request_id); ?></p>
                                <p><strong>السعر:</strong> <?php echo e($quote->price); ?> <?php echo e($quote->currency_code ?? 'SAR'); ?></p>
                                <p><strong>العمولة:</strong> <?php echo e($quote->commission_amount ?? 0); ?> <?php echo e($quote->currency_code ?? 'SAR'); ?></p>
                                <p><strong>صالح حتى:</strong> <?php echo e($quote->valid_until ? $quote->valid_until->format('Y-m-d') : 'غير محدد'); ?></p>
                                <p><strong>الحالة:</strong> <?php echo e($quote->status_text); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="font-weight-bold">معلومات مقدم العرض</h6>
                                <hr>
                                <p><strong>المقدم:</strong> <?php echo e($quote->user ? $quote->user->name : 'غير محدد'); ?></p>
                                <p><strong>الوكيل الفرعي:</strong> <?php echo e($quote->subagent ? $quote->subagent->name : 'غير محدد'); ?></p>
                                <p><strong>تاريخ الإنشاء:</strong> <?php echo e($quote->created_at->format('Y-m-d H:i')); ?></p>
                                <p><strong>آخر تحديث:</strong> <?php echo e($quote->updated_at->format('Y-m-d H:i')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">وصف عرض السعر</h6>
                        <hr>
                        <p><?php echo e($quote->description ?? 'لا يوجد وصف'); ?></p>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">التفاصيل</h6>
                        <hr>
                        <p><?php echo e($quote->details ?? 'لا توجد تفاصيل إضافية'); ?></p>
                    </div>

                    <?php if(isset($quote->rejection_reason)): ?>
                    <div class="mb-4">
                        <h6 class="font-weight-bold">سبب الرفض</h6>
                        <hr>
                        <p><?php echo e($quote->rejection_reason); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($quote->attachments && $quote->attachments->count() > 0): ?>
                    <div class="mb-4">
                        <h6 class="font-weight-bold">المرفقات</h6>
                        <hr>
                        <ul class="list-group">
                            <?php $__currentLoopData = $quote->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e($attachment->name); ?>

                                <a href="<?php echo e(url('attachments/download/' . $attachment->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <?php if($quote->status === 'pending' && auth()->user()->id === $quote->request->user_id): ?>
                            <div class="d-flex justify-content-between">
                                <form action="<?php echo e(route('quotes.accept', $quote->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-success">قبول العرض</button>
                                </form>
                                
                                <form action="<?php echo e(route('quotes.reject', $quote->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-danger">رفض العرض</button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if($quote->status === 'accepted'): ?>
                            <a href="<?php echo e(url('payments/create?quote_id=' . $quote->id)); ?>" class="btn btn-primary">
                                إتمام الدفع
                            </a>
                        <?php endif; ?>

                        <a href="<?php echo e(url('dashboard')); ?>" class="btn btn-secondary mt-2">
                            العودة للوحة التحكم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/quotes/show.blade.php ENDPATH**/ ?>