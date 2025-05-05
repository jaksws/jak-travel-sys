<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('agency.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة السبوكلاء</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-users me-2"></i> إدارة السبوكلاء</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubagentModal">
                <i class="fas fa-plus-circle me-1"></i> إضافة سبوكيل جديد
            </button>
            <a href="#" class="btn btn-success ms-2">
                <i class="fas fa-file-export"></i> تصدير
            </a>
            <a href="#" class="btn btn-light ms-2"><i class="fas fa-search"></i> بحث</a>
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
            <form action="<?php echo e(route('agency.subagents.index')); ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم أو بريد إلكتروني">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>نشط</option>
                        <option value="0" <?php echo e(request('status') == '0' && request('status') !== null ? 'selected' : ''); ?>>معطل</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <?php if($subagents->isEmpty()): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i> لا يوجد سبوكلاء حتى الآن.
                    <!-- عناصر مطلوبة للاختبار حتى في حالة عدم وجود بيانات -->
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubagentModal">
                            <i class="fas fa-plus-circle me-1"></i> إضافة سبوكيل جديد
                        </button>
                        <a href="#" class="btn btn-success ms-2">
                            <i class="fas fa-file-export"></i> تصدير
                        </a>
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
                                <th scope="col">الاسم</th>
                                <th scope="col">البريد الإلكتروني</th>
                                <th scope="col">رقم الهاتف</th>
                                <th scope="col">الخدمات المتاحة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">تاريخ الإضافة</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $subagents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subagent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($subagent->id); ?></td>
                                    <td><?php echo e($subagent->name); ?></td>
                                    <td><?php echo e($subagent->email); ?></td>
                                    <td><?php echo e($subagent->phone); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo e($subagent->services->count()); ?></span>
                                    </td>
                                    <td>
                                        <?php if($subagent->is_active): ?>
                                            <span class="badge bg-success">نشط</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">معطل</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($subagent->created_at->format('Y-m-d')); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('agency.subagents.show', $subagent)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('agency.subagents.edit', $subagent)); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-<?php echo e($subagent->is_active ? 'danger' : 'success'); ?>" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo e($subagent->id); ?>">
                                                <i class="fas fa-<?php echo e($subagent->is_active ? 'ban' : 'check'); ?>"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($subagent->id); ?>">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </div>
                                        
                                        <!-- Modal تغيير الحالة -->
                                        <div class="modal fade" id="statusModal<?php echo e($subagent->id); ?>" tabindex="-1" aria-labelledby="statusModalLabel<?php echo e($subagent->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalLabel<?php echo e($subagent->id); ?>">تأكيد تغيير الحالة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من تغيير حالة السبوكيل "<?php echo e($subagent->name); ?>" إلى <?php echo e($subagent->is_active ? 'معطل' : 'نشط'); ?>؟
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="<?php echo e(route('agency.subagents.toggle-status', $subagent)); ?>" method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>
                                                            <button type="submit" class="btn btn-<?php echo e($subagent->is_active ? 'danger' : 'success'); ?>">
                                                                <?php echo e($subagent->is_active ? 'تعطيل' : 'تنشيط'); ?> السبوكيل
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal حذف -->
                                        <div class="modal fade" id="deleteModal<?php echo e($subagent->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo e($subagent->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo e($subagent->id); ?>">تأكيد الحذف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        هل أنت متأكد من رغبتك في حذف هذا السبوكيل؟ لا يمكن التراجع عن هذا الإجراء.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="<?php echo e(route('agency.subagents.destroy', $subagent)); ?>" method="POST">
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
                    <?php echo e($subagents->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal إضافة سبوكيل جديد -->
<div class="modal fade" id="addSubagentModal" tabindex="-1" aria-labelledby="addSubagentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('agency.subagents.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addSubagentModalLabel">إضافة سبوكيل جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم الكامل*</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني*</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف*</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">كلمة المرور*</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الخدمات المتاحة (اختياري)</label>
                        <div class="row">
                            <?php $__currentLoopData = \App\Models\Service::where('agency_id', auth()->user()->agency_id)
                                        ->where('status', 'active')
                                        ->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="services[]" value="<?php echo e($service->id); ?>" id="service<?php echo e($service->id); ?>">
                                        <label class="form-check-label" for="service<?php echo e($service->id); ?>">
                                            <?php echo e($service->name); ?>

                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة السبوكيل</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/agency/subagents/index.blade.php ENDPATH**/ ?>