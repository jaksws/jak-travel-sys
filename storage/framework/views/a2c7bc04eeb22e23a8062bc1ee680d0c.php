<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('agency.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">عروض الأسعار</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-file-invoice-dollar me-2"></i> إدارة عروض الأسعار</h2>
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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('agency.quotes.index')); ?>" method="GET" class="row">
                <div class="col-md-3">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>بانتظار الموافقة</option>
                        <option value="agency_approved" <?php echo e(request('status') == 'agency_approved' ? 'selected' : ''); ?>>معتمد من الوكالة</option>
                        <option value="customer_approved" <?php echo e(request('status') == 'customer_approved' ? 'selected' : ''); ?>>مقبول من العميل</option>
                        <option value="agency_rejected" <?php echo e(request('status') == 'agency_rejected' ? 'selected' : ''); ?>>مرفوض من الوكالة</option>
                        <option value="customer_rejected" <?php echo e(request('status') == 'customer_rejected' ? 'selected' : ''); ?>>مرفوض من العميل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="subagent_id" class="form-label">السبوكيل</label>
                    <select class="form-select" id="subagent_id" name="subagent_id">
                        <option value="">كل السبوكلاء</option>
                        <?php $__currentLoopData = $subagents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subagent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subagent->id); ?>" <?php echo e(request('subagent_id') == $subagent->id ? 'selected' : ''); ?>><?php echo e($subagent->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="service_id" class="form-label">الخدمة</label>
                    <select class="form-select" id="service_id" name="service_id">
                        <option value="">كل الخدمات</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" <?php echo e(request('service_id') == $service->id ? 'selected' : ''); ?>><?php echo e($service->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if($quotes->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد عروض أسعار متطابقة مع معايير البحث.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الطلب</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">السبوكيل</th>
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
                                    <td><a href="<?php echo e(route('agency.requests.show', $quote->request_id)); ?>">#<?php echo e($quote->request_id); ?></a></td>
                                    <td><?php echo e($quote->request->service->name); ?></td>
                                    <td><?php echo e($quote->subagent->name); ?></td>
                                    <td><?php echo e($quote->request->customer->name); ?></td>
                                    <td><?php echo e(number_format($quote->price, 2)); ?> <?php echo e($quote->currency_code); ?></td>
                                    <td><?php echo e(number_format($quote->commission_amount, 2)); ?> <?php echo e($quote->currency_code); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($quote->status_badge); ?>"><?php echo e($quote->status_text); ?></span>
                                    </td>
                                    <td><?php echo e($quote->created_at->format('Y-m-d')); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('agency.quotes.show', $quote)); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($quote->status == 'pending'): ?>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo e($quote->id); ?>">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($quote->id); ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                
                                                <!-- Modal تأكيد الموافقة -->
                                                <div class="modal fade" id="approveModal<?php echo e($quote->id); ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تأكيد الموافقة على عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>هل أنت متأكد من الموافقة على عرض السعر هذا؟</p>
                                                                <p>سيتم عرض هذا العرض للعميل للموافقة عليه.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                <form action="<?php echo e(route('agency.quotes.approve', $quote)); ?>" method="POST">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="btn btn-success">تأكيد الموافقة</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal تأكيد الرفض -->
                                                <div class="modal fade" id="rejectModal<?php echo e($quote->id); ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">رفض عرض السعر</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="<?php echo e(route('agency.quotes.reject', $quote)); ?>" method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="rejection_reason<?php echo e($quote->id); ?>" class="form-label">سبب الرفض</label>
                                                                        <textarea class="form-control" id="rejection_reason<?php echo e($quote->id); ?>" name="rejection_reason" rows="3" required></textarea>
                                                                        <small class="form-text text-muted">سيتم إرسال هذا السبب إلى السبوكيل</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                                                                </div>
                                                            </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/agency/quotes/index.blade.php ENDPATH**/ ?>