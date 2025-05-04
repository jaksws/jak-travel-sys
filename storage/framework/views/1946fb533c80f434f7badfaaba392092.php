<?php $__env->startSection('title', 'لوحة تحكم المسؤول'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">لوحة التحكم</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" dusk="requests-page">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2 text-xl font-bold"><i class="fas fa-tachometer-alt me-2"></i> لوحة تحكم المسؤول</h1>
            <p class="mb-0 text-muted">مرحباً، <?php echo e(auth()->user()->name); ?></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal" dusk="add-user-button"><i class="fas fa-plus"></i> إضافة مستخدم</a>
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRequestModal" dusk="add-request-button-dashboard"><i class="fas fa-plus"></i> إضافة طلب</a>
            <a href="#" class="btn btn-info" id="export-requests-btn"><i class="fas fa-file-export"></i> تصدير</a>
            <a href="#" class="btn btn-light"><i class="fas fa-search"></i> بحث</a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary" dusk="manage-users-link">إدارة المستخدمين</a>
            <a href="<?php echo e(route('admin.requests.index')); ?>" class="btn btn-outline-secondary" dusk="manage-requests-link">إدارة الطلبات</a>
            <a href="/admin/settings" class="btn btn-outline-dark" dusk="settings-link">الإعدادات</a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
            <a href="#" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" dusk="logout-link-main">تسجيل الخروج</a>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-primary text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h5 class="card-title">المستخدمين</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['users']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-success text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                    <h5 class="card-title">الوكالات</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['agencies']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-info text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-concierge-bell fa-lg"></i>
                    </div>
                    <h5 class="card-title">الخدمات</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['services']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-warning text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-clipboard-list fa-lg"></i>
                    </div>
                    <h5 class="card-title">الطلبات</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['requests']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-danger text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <h5 class="card-title">عروض الأسعار</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['quotes']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card h-100 shadow border-0">
                <div class="card-body text-center">
                    <div class="icon-circle mx-auto mb-2 bg-secondary text-white" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                    <h5 class="card-title">المعاملات</h5>
                    <h3 class="mb-0 font-weight-bold"><?php echo e($stats['transactions']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- بيانات المستخدمين -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">إحصائيات المستخدمين</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:200px;">
                        <canvas id="userStatsChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>الأدمن</span>
                            <span class="badge bg-primary"><?php echo e($userStats['admins']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>وكالات</span>
                            <span class="badge bg-success"><?php echo e($userStats['agencies']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>سبوكلاء</span>
                            <span class="badge bg-info"><?php echo e($userStats['subagents']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>عملاء</span>
                            <span class="badge bg-warning"><?php echo e($userStats['customers']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حالة الطلبات -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">حالة الطلبات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:200px;">
                        <canvas id="requestStatusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>قيد الانتظار</span>
                            <span class="badge bg-secondary"><?php echo e($requestStats['pending']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>قيد التنفيذ</span>
                            <span class="badge bg-primary"><?php echo e($requestStats['in_progress']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>مكتملة</span>
                            <span class="badge bg-success"><?php echo e($requestStats['completed']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>ملغاة</span>
                            <span class="badge bg-danger"><?php echo e($requestStats['cancelled']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإيرادات -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">الإيرادات (الستة أشهر الأخيرة)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث المستخدمين والطلبات -->
    <div class="row">
        <!-- أحدث المستخدمين -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between bg-light">
                    <h5 class="mb-0 font-weight-bold">أحدث المستخدمين</h5>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>النوع</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $latestUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
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
                                            <?php default: ?>
                                                <span class="badge bg-warning">عميل</span>
                                        <?php endswitch; ?>
                                    </td>
                                    <td><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">لا يوجد مستخدمين حديثين</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between bg-light">
                    <h5 class="mb-0 font-weight-bold">أحدث الطلبات</h5>
                    <a href="<?php echo e(route('admin.requests.index')); ?>" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $latestRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($request->title); ?></td>
                                    <td><?php echo e($request->user ? $request->user->name : 'غير محدد'); ?></td>
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
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">لا يوجد طلبات حديثة</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 font-weight-bold">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between" dusk="manage-users-link">
                                <span><i class="fas fa-users me-2"></i> إدارة المستخدمين</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?php echo e(route('admin.requests.index')); ?>" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between" dusk="manage-requests-link">
                                <span><i class="fas fa-clipboard-list me-2"></i> إدارة الطلبات</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?php echo e(route('admin.system.logs')); ?>" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between" dusk="quick-link-system-logs">
                                <span><i class="fas fa-file-alt me-2"></i> سجلات النظام</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="/admin/settings" class="btn btn-light btn-block py-3 d-flex align-items-center justify-content-between" dusk="settings-link">
                                <span><i class="fas fa-cog me-2"></i> إعدادات النظام</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- سجلات النظام -->
    <div class="container-fluid" dusk="system-logs-page">
        <!-- محتوى سجلات النظام -->
    </div>
</div>

<!-- Modal: إضافة مستخدم جديد -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true" dusk="create-user-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="createUserModalLabel">إضافة مستخدم جديد</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="user-name" class="form-label">الاسم</label>
            <input type="text" class="form-control" id="user-name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="user-email" class="form-label">البريد الإلكتروني</label>
            <input type="email" class="form-control" id="user-email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="user-password" class="form-label">كلمة المرور</label>
            <input type="password" class="form-control" id="user-password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="user-password-confirm" class="form-label">تأكيد كلمة المرور</label>
            <input type="password" class="form-control" id="user-password-confirm" name="password_confirmation" required>
          </div>
          <div class="mb-3">
            <label for="user-role" class="form-label">الدور</label>
            <select class="form-select" id="user-role" name="role" required>
              <option value="admin">مسؤول</option>
              <option value="agency">وكالة</option>
              <option value="subagent">سبوكيل</option>
              <option value="customer">عميل</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="user-status" class="form-label">الحالة</label>
            <select class="form-select" id="user-status" name="status" required>
              <option value="active">نشط</option>
              <option value="inactive">معطل</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: إضافة طلب جديد -->
<div class="modal fade" id="createRequestModal" tabindex="-1" aria-labelledby="createRequestModalLabel" aria-hidden="true" dusk="create-request-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?php echo e(route('admin.requests.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="createRequestModalLabel">إضافة طلب جديد</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="request-title" class="form-label">عنوان الطلب</label>
            <input type="text" class="form-control" id="request-title" name="title" required>
          </div>
          <div class="mb-3">
            <label for="request-user" class="form-label">العميل</label>
            <select class="form-select" id="request-user" name="user_id" required>
              <option value="">اختر عميلاً</option>
              <?php $__currentLoopData = App\Models\User::where('role', 'customer')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?> (<?php echo e($customer->email); ?>)</option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="request-service" class="form-label">الخدمة</label>
            <select class="form-select" id="request-service" name="service_id" required>
              <option value="">اختر خدمة</option>
              <?php $__currentLoopData = App\Models\Service::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="request-date" class="form-label">تاريخ التنفيذ</label>
            <input type="date" class="form-control" id="request-date" name="required_date">
          </div>
          <div class="mb-3">
            <label for="request-status" class="form-label">الحالة</label>
            <select class="form-select" id="request-status" name="status">
              <option value="pending">قيد الانتظار</option>
              <option value="in_progress">قيد التنفيذ</option>
              <option value="completed">مكتملة</option>
              <option value="cancelled">ملغاة</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="request-notes" class="form-label">ملاحظات</label>
            <textarea class="form-control" id="request-notes" name="notes"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني لإحصائيات المستخدمين
    const userChart = new Chart(document.getElementById('userStatsChart'), {
        type: 'doughnut',
        data: {
            labels: ['الأدمن', 'وكالات', 'سبوكلاء', 'عملاء'],
            datasets: [{
                data: [
                    <?php echo e($userStats['admins']); ?>, 
                    <?php echo e($userStats['agencies']); ?>, 
                    <?php echo e($userStats['subagents']); ?>, 
                    <?php echo e($userStats['customers']); ?>

                ],
                backgroundColor: ['#3b82f6', '#10b981', '#3b82f6', '#f59e0b'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // رسم بياني لحالة الطلبات
    const requestChart = new Chart(document.getElementById('requestStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['قيد الانتظار', 'قيد التنفيذ', 'مكتملة', 'ملغاة'],
            datasets: [{
                data: [
                    <?php echo e($requestStats['pending']); ?>, 
                    <?php echo e($requestStats['in_progress']); ?>, 
                    <?php echo e($requestStats['completed']); ?>, 
                    <?php echo e($requestStats['cancelled']); ?>

                ],
                backgroundColor: ['#9ca3af', '#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // رسم بياني للإيرادات
    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($revenueData['months']); ?>,
            datasets: [{
                label: 'الإيرادات',
                data: <?php echo json_encode($revenueData['revenue']); ?>,
                backgroundColor: '#3b82f6',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // زر تصدير الطلبات
    $(document).ready(function() {
        $('#export-requests-btn').on('click', function(e) {
            e.preventDefault();
            window.location.href = "<?php echo e(route('admin.requests.index', ['export' => 'csv'])); ?>";
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>