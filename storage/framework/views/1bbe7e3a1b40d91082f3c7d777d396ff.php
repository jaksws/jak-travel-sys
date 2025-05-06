<?php $__env->startSection('title', 'عرض بيانات المستخدم'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">إدارة المستخدمين</a></li>
    <li class="breadcrumb-item active"><?php echo e($user->name); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المستخدم</h1>
        <div>
            <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> تعديل
            </a>
            <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </form>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>

    <!-- معلومات المستخدم الأساسية -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold">المعلومات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto">
                            <span class="avatar-text"><?php echo e(substr($user->name, 0, 1)); ?></span>
                        </div>
                        <h4 class="mt-3"><?php echo e($user->name); ?></h4>
                        <?php switch($user->role):
                            case ('admin'): ?>
                                <span class="badge bg-primary fs-6">مسؤول</span>
                                <?php break; ?>
                            <?php case ('agency'): ?>
                                <span class="badge bg-success fs-6">وكالة</span>
                                <?php break; ?>
                            <?php case ('subagent'): ?>
                                <span class="badge bg-info fs-6">سبوكيل</span>
                                <?php break; ?>
                            <?php default: ?>
                                <span class="badge bg-warning fs-6">عميل</span>
                        <?php endswitch; ?>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope me-2 text-muted"></i> البريد الإلكتروني</span>
                            <span><?php echo e($user->email); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-toggle-on me-2 text-muted"></i> الحالة</span>
                            <?php if($user->status === 'active'): ?>
                                <span class="badge bg-success">نشط</span>
                            <?php else: ?>
                                <span class="badge bg-danger">معطل</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-alt me-2 text-muted"></i> تاريخ التسجيل</span>
                            <span><?php echo e($user->created_at->format('Y-m-d')); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clock me-2 text-muted"></i> آخر تحديث</span>
                            <span><?php echo e($user->updated_at->format('Y-m-d')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-<?php echo e($user->status === 'active' ? 'danger' : 'success'); ?>" 
                                onclick="document.getElementById('toggle-form').submit();">
                            <?php if($user->status === 'active'): ?>
                                <i class="fas fa-ban me-1"></i> تعطيل الحساب
                            <?php else: ?>
                                <i class="fas fa-check me-1"></i> تفعيل الحساب
                            <?php endif; ?>
                        </button>
                        <form id="toggle-form" action="<?php echo e(route('admin.users.toggle-status', $user->id)); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- الطلبات -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">آخر الطلبات</h6>
                    <a href="#" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>الخدمة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($user->requests && $user->requests->count() > 0): ?>
                                    <?php $__currentLoopData = $user->requests->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($request->title); ?></td>
                                        <td><?php echo e($request->service->name ?? 'غير متوفر'); ?></td>
                                        <td>
                                            <?php switch($request->status):
                                                case ('pending'): ?>
                                                    <span class="badge bg-secondary">قيد الانتظار</span>
                                                    <?php break; ?>
                                                <?php case ('in_progress'): ?>
                                                    <span class="badge bg-primary">قيد التنفيذ</span>
                                                    <?php break; ?>
                                                <?php case ('completed'): ?>
                                                    <span class="badge bg-success">مكتملة</span>
                                                    <?php break; ?>
                                                <?php case ('cancelled'): ?>
                                                    <span class="badge bg-danger">ملغاة</span>
                                                    <?php break; ?>
                                                <?php default: ?>
                                                    <span class="badge bg-secondary"><?php echo e($request->status); ?></span>
                                            <?php endswitch; ?>
                                        </td>
                                        <td><?php echo e($request->created_at->format('Y-m-d')); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">لا يوجد طلبات لهذا المستخدم</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- المعاملات المالية -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">آخر المعاملات المالية</h6>
                    <a href="#" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>رقم المعاملة</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($user->transactions && $user->transactions->count() > 0): ?>
                                    <?php $__currentLoopData = $user->transactions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($transaction->reference_number); ?></td>
                                        <td><?php echo e($transaction->amount); ?> <?php echo e($transaction->currency); ?></td>
                                        <td><?php echo e($transaction->payment_method); ?></td>
                                        <td>
                                            <?php switch($transaction->status):
                                                case ('completed'): ?>
                                                    <span class="badge bg-success">مكتملة</span>
                                                    <?php break; ?>
                                                <?php case ('pending'): ?>
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                    <?php break; ?>
                                                <?php case ('failed'): ?>
                                                    <span class="badge bg-danger">فشلت</span>
                                                    <?php break; ?>
                                                <?php default: ?>
                                                    <span class="badge bg-secondary"><?php echo e($transaction->status); ?></span>
                                            <?php endswitch; ?>
                                        </td>
                                        <td><?php echo e($transaction->created_at->format('Y-m-d')); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">لا يوجد معاملات مالية لهذا المستخدم</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-text {
        font-size: 48px;
        color: #fff;
        font-weight: bold;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/admin/users/show.blade.php ENDPATH**/ ?>