<?php $__env->startSection('title', 'إدارة المستخدمين'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة المستخدمين</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal" dusk="add-user-button">
            <i class="fas fa-plus"></i> إضافة مستخدم جديد
        </a>
    </div>

    <!-- بطاقة البحث والتصفية -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">بحث وتصفية</h6>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-sm btn-light">
                <i class="fas fa-redo"></i> إعادة تعيين
            </a>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.users.index')); ?>" method="GET" class="mb-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="ابحث بالاسم أو البريد الإلكتروني" value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>مسؤول</option>
                            <option value="agency" <?php echo e(request('role') == 'agency' ? 'selected' : ''); ?>>وكالة</option>
                            <option value="subagent" <?php echo e(request('role') == 'subagent' ? 'selected' : ''); ?>>سبوكيل</option>
                            <option value="customer" <?php echo e(request('role') == 'customer' ? 'selected' : ''); ?>>عميل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="order_by" class="form-select">
                            <option value="created_at" <?php echo e(request('order_by', 'created_at') == 'created_at' ? 'selected' : ''); ?>>تاريخ التسجيل</option>
                            <option value="name" <?php echo e(request('order_by') == 'name' ? 'selected' : ''); ?>>الاسم</option>
                            <option value="email" <?php echo e(request('order_by') == 'email' ? 'selected' : ''); ?>>البريد الإلكتروني</option>
                            <option value="role" <?php echo e(request('order_by') == 'role' ? 'selected' : ''); ?>>نوع المستخدم</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المستخدمين -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">قائمة المستخدمين (<?php echo e($users->total()); ?>)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>نوع المستخدم</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr dusk="user-row-<?php echo e($user->id); ?>">
                                <td><?php echo e($users->firstItem() + $index); ?></td>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php switch($user->role):
                                        case ('admin'): ?>
                                            <span class="badge bg-primary">مسؤول</span>
                                            <?php break; ?>
                                        <?php case ('agency'): ?>
                                            <span class="badge bg-success">وكالة</span>
                                            <?php break; ?>
                                        <?php case ('subagent'): ?>
                                            <span class="badge bg-info">سبوكيل</span>
                                            <?php break; ?>
                                        <?php case ('customer'): ?>
                                            <span class="badge bg-warning">عميل</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-secondary"><?php echo e($user->role); ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php if($user->status === 'active'): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-sm btn-info" title="عرض" dusk="view-user-<?php echo e($user->id); ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-sm btn-warning" title="تعديل" dusk="edit-user-<?php echo e($user->id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-<?php echo e($user->status === 'active' ? 'danger' : 'success'); ?>" 
                                                title="<?php echo e($user->status === 'active' ? 'تعطيل' : 'تفعيل'); ?>"
                                                onclick="document.getElementById('toggle-form-<?php echo e($user->id); ?>').submit();" dusk="toggle-status-<?php echo e($user->id); ?>">
                                            <i class="fas fa-<?php echo e($user->status === 'active' ? 'ban' : 'check'); ?>"></i>
                                        </button>
                                        <form id="toggle-form-<?php echo e($user->id); ?>" action="<?php echo e(route('admin.users.toggle-status', $user->id)); ?>" method="POST" style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal-<?php echo e($user->id); ?>" dusk="delete-user-button-<?php echo e($user->id); ?>">حذف</button>
                                    </div>
                                </td>
                            </tr>
                            <!-- حذف مودال لكل مستخدم -->
                            <div class="modal fade" id="deleteUserModal-<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel-<?php echo e($user->id); ?>" aria-hidden="true" dusk="delete-user-modal-<?php echo e($user->id); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteUserModalLabel-<?php echo e($user->id); ?>">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger mb-0">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                هل أنت متأكد من رغبتك في حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه وسيؤدي إلى حذف جميع البيانات المرتبطة به.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger" dusk="confirm-delete-button">نعم، قم بالحذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">لم يتم العثور على أي مستخدمين</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <?php echo e($users->withQueryString()->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- نموذج إضافة مستخدم جديد -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true" dusk="create-user-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" dusk="create-user-form">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name" required dusk="create-user-name">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required dusk="create-user-email">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required dusk="create-user-password">
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required dusk="create-user-password-confirm">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">نوع المستخدم</label>
                            <select class="form-select" id="role" name="role" required dusk="create-user-role">
                                <option value="admin">مسؤول</option>
                                <option value="agency">وكالة</option>
                                <option value="subagent">سبوكيل</option>
                                <option value="customer" selected>عميل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status" required dusk="create-user-status">
                                <option value="active" selected>نشط</option>
                                <option value="inactive">معطل</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" dusk="create-user-submit">إضافة المستخدم</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/admin/users/index.blade.php ENDPATH**/ ?>