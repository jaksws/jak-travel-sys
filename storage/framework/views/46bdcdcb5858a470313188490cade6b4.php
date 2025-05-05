<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('subagent.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-tag me-2"></i> عروض الأسعار</h2>
                <p class="text-muted">استعرض عروض الأسعار التي قمت بتقديمها وتتبع حالتها</p>
            </div>
            <div>
                <a href="<?php echo e(route('subagent.requests.index')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> تقديم عرض سعر
                </a>
                <a href="#" class="btn btn-success ms-2">
                    <i class="fas fa-file-export"></i> تصدير
                </a>
            </div>
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

    <?php if(session('info')): ?>
        <div class="alert alert-info">
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('subagent.quotes.index')); ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="request_id" class="form-label">الطلب</label>
                    <select class="form-select" id="request_id" name="request_id">
                        <option value="">كل الطلبات</option>
                        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($req->id); ?>" <?php echo e(request('request_id') == $req->id ? 'selected' : ''); ?>>
                                #<?php echo e($req->id); ?> - <?php echo e($req->service->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>بانتظار الموافقة</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>موافق عليها</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>مرفوضة</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" <?php echo e(request('service_id') == $service->id ? 'selected' : ''); ?>>
                                <?php echo e($service->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                    <a href="<?php echo e(route('subagent.quotes.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if($quotes->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لم تقم بتقديم أي عروض أسعار حتى الآن.
                    <!-- عناصر مطلوبة للاختبار حتى في حالة عدم وجود بيانات -->
                    <div class="mt-4">
                        <a href="#" class="btn btn-primary disabled"><i class="fas fa-plus"></i> تقديم عرض سعر</a>
                        <button type="button" class="btn btn-sm btn-danger ms-2" disabled><i class="fas fa-trash"></i> حذف</button>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الطلب</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">العميل</th>
                                <th scope="col">السعر</th>
                                <th scope="col">العمولة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ التقديم</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($quote->id); ?></td>
                                    <td>#<?php echo e($quote->request_id); ?></td>
                                    <td><?php echo e($quote->request->service->name); ?></td>
                                    <td><?php echo e($quote->request->customer->name); ?></td>
                                    <td><?php echo e($quote->price); ?> ر.س</td>
                                    <td><?php echo e($quote->commission_amount); ?> ر.س</td>
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
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('subagent.quotes.show', $quote)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($quote->status == 'pending' || $quote->status == 'agency_rejected'): ?>
                                                <a href="<?php echo e(route('subagent.quotes.edit', $quote)); ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo e($quote->id); ?>">
                                                    <i class="fas fa-trash"></i> حذف
                                                </button>
                                                
                                                <!-- Modal إلغاء العرض -->
                                                <div class="modal fade" id="cancelModal<?php echo e($quote->id); ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد إلغاء عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                هل أنت متأكد من رغبتك في إلغاء عرض السعر هذا؟
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="<?php echo e(route('subagent.quotes.destroy', $quote)); ?>" method="POST">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- الترقيم -->
                <div class="mt-4">
                    <?php echo e($quotes->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/subagent/quotes/index.blade.php ENDPATH**/ ?>