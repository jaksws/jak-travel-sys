<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<div class="row welcome-banner rounded-lg mb-5 shadow">
    <div class="col-md-8 offset-md-2 text-center py-5">
        <h1 class="mb-3">مرحباً بك في نظام وكالات السفر</h1>
        <p class="lead mb-4">منصة متكاملة لإدارة وكالات السفر والسبوكلاء والعملاء بطريقة سهلة وفعالة</p>
        <span style="display:none">Laravel</span>
        <?php if(auth()->guard()->guest()): ?>
        <div>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-light btn-lg me-2 mb-2 mb-sm-0">
                <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
            </a>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-light btn-lg mb-2 mb-sm-0">
                <i class="fas fa-user-plus me-1"></i> التسجيل
            </a>
        </div>
        <?php else: ?>
        <div>
            <?php if(auth()->user()->isAgency()): ?>
                <a href="<?php echo e(route('agency.dashboard')); ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            <?php elseif(auth()->user()->isSubagent()): ?>
                <a href="<?php echo e(route('subagent.dashboard')); ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            <?php elseif(auth()->user()->isCustomer()): ?>
                <a href="<?php echo e(route('customer.dashboard')); ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-12 text-center mb-4">
        <h2>مميزات النظام</h2>
        <p class="lead text-muted">كل ما تحتاجه لإدارة أعمالك بكفاءة عالية</p>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-users feature-icon"></i>
                <h4>إدارة العملاء والسبوكلاء</h4>
                <p class="text-muted">إدارة فعالة لبيانات العملاء والسبوكلاء مع إمكانية تتبع النشاطات والطلبات.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-cogs feature-icon"></i>
                <h4>خدمات متنوعة</h4>
                <p class="text-muted">إدارة مجموعة واسعة من الخدمات مثل الموافقات الأمنية، النقل البري، الحج والعمرة وغيرها.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-file-alt feature-icon"></i>
                <h4>إدارة الطلبات</h4>
                <p class="text-muted">تقديم ومتابعة الطلبات وعروض الأسعار بطريقة سهلة ومنظمة.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-chart-bar feature-icon"></i>
                <h4>تقارير وإحصائيات</h4>
                <p class="text-muted">تقارير مفصلة وإحصائيات دقيقة لمساعدتك في اتخاذ القرارات الصحيحة.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-money-bill-wave feature-icon"></i>
                <h4>إدارة المعاملات المالية</h4>
                <p class="text-muted">متابعة العمولات والمدفوعات والمستحقات بين الوكالة والسبوكلاء والعملاء.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
                <i class="fas fa-globe feature-icon"></i>
                <h4>دعم تعدد العملات</h4>
                <p class="text-muted">دعم للعديد من العملات العالمية مثل الريال السعودي والدولار واليورو وغيرها للتعامل مع عملاء من مختلف الدول.</p>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<?php if(isset($services) && !$services->isEmpty()): ?>
<div class="row mb-5">
    <div class="col-12 text-center mb-4">
        <h2>خدماتنا المتاحة</h2>
        <p class="lead text-muted">استعرض مجموعة الخدمات التي نقدمها</p>
    </div>
    <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $typeServices): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-12 mb-4">
            <h4 class="mb-3">
                <?php if($type == 'security_approval'): ?>
                    <i class="fas fa-shield-alt me-1"></i> خدمات الموافقات الأمنية
                <?php elseif($type == 'transportation'): ?>
                    <i class="fas fa-bus me-1"></i> خدمات النقل البري
                <?php elseif($type == 'hajj_umrah'): ?>
                    <i class="fas fa-kaaba me-1"></i> خدمات الحج والعمرة
                <?php elseif($type == 'flight'): ?>
                    <i class="fas fa-plane me-1"></i> خدمات تذاكر الطيران
                <?php elseif($type == 'passport'): ?>
                    <i class="fas fa-passport me-1"></i> خدمات الجوازات
                <?php else: ?>
                    <i class="fas fa-cog me-1"></i> خدمات أخرى
                <?php endif; ?>
            </h4>
            <div class="row">
                <?php $__currentLoopData = $typeServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 feature-card">
                            <?php if($service->image_path): ?>
                                <img src="<?php echo e(asset('storage/' . $service->image_path)); ?>" class="card-img-top" alt="<?php echo e($service->name); ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light text-center py-5">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo e($service->name); ?></h5>
                                <p class="card-text text-muted"><?php echo e(Str::limit($service->description, 100)); ?></p>
                                <div class="text-end">
                                    <span class="badge bg-primary"><?php echo e($service->base_price); ?> ر.س</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<!-- Call to Action -->
<div class="row">
    <div class="col-md-8 offset-md-2 text-center py-5">
        <h2 class="mb-3">ابدأ في استخدام النظام الآن</h2>
        <p class="lead mb-4">انضم إلى مئات الوكالات التي تستخدم نظامنا بنجاح لإدارة أعمالها</p>
        <?php if(auth()->guard()->guest()): ?>
        <div>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-lg me-2 mb-2 mb-sm-0">
                <i class="fas fa-user-plus me-1"></i> تسجيل حساب جديد
            </a>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary btn-lg mb-2 mb-sm-0">
                <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
            </a>
        </div>
        <?php else: ?>
        <div>
            <?php if(auth()->user()->isAgency()): ?>
                <a href="<?php echo e(route('agency.dashboard')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            <?php elseif(auth()->user()->isSubagent()): ?>
                <a href="<?php echo e(route('subagent.dashboard')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            <?php elseif(auth()->user()->isCustomer()): ?>
                <a href="<?php echo e(route('customer.dashboard')); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-tachometer-alt me-1"></i> الذهاب إلى لوحة التحكم
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/welcome.blade.php ENDPATH**/ ?>