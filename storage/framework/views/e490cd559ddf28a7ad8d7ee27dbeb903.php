<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('agency.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الطلبات</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-file-alt me-2"></i> إدارة الطلبات</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?php echo e(route('agency.requests.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> إضافة طلب جديد
            </a>
        </div>
    </div>

    <!-- رسائل النجاح والخطأ -->
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

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-1"></i> بحث وتصفية</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('agency.requests.index')); ?>" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="رقم الطلب أو التفاصيل">
                </div>
                <div class="col-md-3">
                    <label for="service" class="form-label">الخدمة</label>
                    <select class="form-select" id="service" name="service">
                        <option value="">كل الخدمات</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->id); ?>" <?php echo e(request('service') == $service->id ? 'selected' : ''); ?>><?php echo e($service->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>قيد الانتظار</option>
                        <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">الأولوية</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">كل الأولويات</option>
                        <option value="normal" <?php echo e(request('priority') == 'normal' ? 'selected' : ''); ?>>عادي</option>
                        <option value="urgent" <?php echo e(request('priority') == 'urgent' ? 'selected' : ''); ?>>مستعجل</option>
                        <option value="emergency" <?php echo e(request('priority') == 'emergency' ? 'selected' : ''); ?>>طارئ</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <?php if($requests->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا توجد طلبات حتى الآن.
                    <!-- عناصر مطلوبة للاختبار حتى في حالة عدم وجود بيانات -->
                    <div class="mt-4">
                        <a href="<?php echo e(route('agency.requests.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> إضافة طلب جديد
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">العميل</th>
                                <th scope="col">الخدمة</th>
                                <th scope="col">الأولوية</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ الطلب</th>
                                <th scope="col">عروض الأسعار</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($request->id); ?></td>
                                    <td><?php echo e($request->customer->name); ?></td>
                                    <td><?php echo e($request->service->name); ?></td>
                                    <td>
                                        <?php if($request->priority == 'normal'): ?>
                                            <span class="badge bg-info">عادي</span>
                                        <?php elseif($request->priority == 'urgent'): ?>
                                            <span class="badge bg-warning">مستعجل</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">طارئ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($request->status == 'pending'): ?>
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        <?php elseif($request->status == 'in_progress'): ?>
                                            <span class="badge bg-info">قيد التنفيذ</span>
                                        <?php elseif($request->status == 'completed'): ?>
                                            <span class="badge bg-success">مكتمل</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">ملغي</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($request->created_at->format('Y-m-d')); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo e($request->quotes->count()); ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('agency.requests.show', $request)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('agency.requests.edit', $request)); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($request->id); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal حذف -->
                                        <div class="modal fade" id="deleteModal<?php echo e($request->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo e($request->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo e($request->id); ?>">تأكيد الحذف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من رغبتك في حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="<?php echo e(route('agency.requests.destroy', $request)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- الترقيم -->
                <div class="mt-4">
                    <?php echo e($requests->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/agency/requests/index.blade.php ENDPATH**/ ?>