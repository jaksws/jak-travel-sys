<?php $__env->startSection('title', 'تعديل بيانات المستخدم'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">إدارة المستخدمين</a></li>
    <li class="breadcrumb-item active">تعديل <?php echo e($user->name); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل بيانات المستخدم</h1>
        <div>
            <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-info">
                <i class="fas fa-eye me-1"></i> عرض التفاصيل
            </a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold">بيانات المستخدم</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST" dusk="edit-user-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password" name="password" placeholder="اتركها فارغة للاحتفاظ بكلمة المرور الحالية">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">اترك هذا الحقل فارغًا إذا كنت لا تريد تغيير كلمة المرور</small>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="role" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="role" name="role" required>
                            <option value="admin" <?php echo e(old('role', $user->role) == 'admin' ? 'selected' : ''); ?>>مسؤول</option>
                            <option value="agency" <?php echo e(old('role', $user->role) == 'agency' ? 'selected' : ''); ?>>وكالة</option>
                            <option value="subagent" <?php echo e(old('role', $user->role) == 'subagent' ? 'selected' : ''); ?>>سبوكيل</option>
                            <option value="customer" <?php echo e(old('role', $user->role) == 'customer' ? 'selected' : ''); ?>>عميل</option>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status" required>
                            <option value="active" <?php echo e(old('status', $user->status) == 'active' ? 'selected' : ''); ?>>نشط</option>
                            <option value="inactive" <?php echo e(old('status', $user->status) == 'inactive' ? 'selected' : ''); ?>>معطل</option>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="col-md-6" id="subagent-agency-select" style="display: none;">
                        <label for="agency_id" class="form-label">اختر الوكالة التابعة <span class="text-danger">*</span></label>
                        <select class="form-select" id="agency_id" name="agency_id">
                            <option value="">-- اختر وكالة --</option>
                            <?php $__currentLoopData = \App\Models\Agency::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($agency->id); ?>" <?php echo e(old('agency_id', $user->agency_id) == $agency->id ? 'selected' : ''); ?>><?php echo e($agency->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row g-3" id="agency-details" <?php echo e($user->role === 'agency' ? '' : 'style=display:none;'); ?>>
                    <div class="col-12">
                        <h4>معلومات الوكالة</h4>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_name" class="form-label">اسم الوكالة</label>
                        <input type="text" class="form-control" id="agency_name" name="agency_name" value="<?php echo e($user->agency->name ?? old('agency_name')); ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_address" class="form-label">العنوان</label>
                        <input type="text" class="form-control" id="agency_address" name="agency_address" value="<?php echo e($user->agency->address ?? old('agency_address')); ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="agency_phone" name="agency_phone" value="<?php echo e($user->agency->phone ?? old('agency_phone')); ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="agency_license_number" class="form-label">رقم الترخيص</label>
                        <input type="text" class="form-control" id="agency_license_number" name="agency_license_number" value="<?php echo e($user->agency->license_number ?? old('agency_license_number')); ?>">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" dusk="delete-user-button">
                        <i class="fas fa-trash me-1"></i> حذف المستخدم
                    </button>
                    
                    <button type="submit" class="btn btn-primary" dusk="update-user-submit">
                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نموذج تأكيد الحذف -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true" dusk="delete-user-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">تأكيد الحذف</h5>
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

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // عرض حقول الوكالة عند اختيار نوع المستخدم كوكالة
        const roleSelect = document.getElementById('role');
        const agencyDetails = document.getElementById('agency-details');
        
        roleSelect.addEventListener('change', function() {
            if (this.value === 'agency') {
                agencyDetails.style.display = 'flex';
                document.getElementById('subagent-agency-select').style.display = 'none';
            } else if (this.value === 'subagent') {
                agencyDetails.style.display = 'none';
                document.getElementById('subagent-agency-select').style.display = 'block';
            } else {
                agencyDetails.style.display = 'none';
                document.getElementById('subagent-agency-select').style.display = 'none';
            }
        });

        // عند تحميل الصفحة: إظهار اختيار الوكالة إذا كان الدور الحالي سبوكيل
        if (roleSelect.value === 'subagent') {
            document.getElementById('subagent-agency-select').style.display = 'block';
        }
        
        // تبديل إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type');
            passwordInput.setAttribute('type', type === 'password' ? 'text' : 'password');
            
            // تغيير الأيقونة
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-eye')) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>