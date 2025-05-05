<?php $__env->startSection('title', 'إدارة الطلبات'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الطلبات</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة الطلبات</h1>
        <a href="<?php echo e(route('admin.requests.store')); ?>" class="btn btn-primary d-none" dusk="add-request-button">إضافة طلب جديد</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
            <span><i class="fas fa-clipboard-list me-2"></i> قائمة الطلبات</span>
            <form method="GET" class="d-flex gap-2 align-items-center" action="">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث بالعنوان..." value="<?php echo e(request('search')); ?>">
                <select name="status" class="form-select form-select-sm">
                    <option value="">كل الحالات</option>
                    <option value="pending" <?php echo e(request('status')=='pending'?'selected':''); ?>>قيد الانتظار</option>
                    <option value="in_progress" <?php echo e(request('status')=='in_progress'?'selected':''); ?>>قيد التنفيذ</option>
                    <option value="completed" <?php echo e(request('status')=='completed'?'selected':''); ?>>مكتمل</option>
                    <option value="cancelled" <?php echo e(request('status')=='cancelled'?'selected':''); ?>>ملغي</option>
                </select>
                <button class="btn btn-sm btn-light" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>العميل</th>
                            <th>الخدمة</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($request->id); ?></td>
                            <td><?php echo e($request->title ?? '-'); ?></td>
                            <td><?php echo e($request->user->name ?? 'غير محدد'); ?></td>
                            <td><?php echo e($request->service->name ?? 'غير محدد'); ?></td>
                            <td>
                                <?php switch($request->status):
                                    case ('pending'): ?>
                                        <span class="badge bg-secondary">قيد الانتظار</span>
                                        <?php break; ?>
                                    <?php case ('in_progress'): ?>
                                        <span class="badge bg-info">قيد التنفيذ</span>
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        <span class="badge bg-success">مكتمل</span>
                                        <?php break; ?>
                                    <?php case ('cancelled'): ?>
                                        <span class="badge bg-danger">ملغي</span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge bg-light"><?php echo e($request->status); ?></span>
                                <?php endswitch; ?>
                            </td>
                            <td><?php echo e($request->created_at ? $request->created_at->format('Y-m-d') : '-'); ?></td>
                            <td> 
                                <a href="<?php echo e(route('admin.requests.show', $request->id)); ?>" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.requests.edit', $request->id)); ?>" class="btn btn-sm btn-warning" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.requests.destroy', $request->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطلب؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center">لا توجد طلبات</td> 
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($requests->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/admin/requests/index.blade.php ENDPATH**/ ?>