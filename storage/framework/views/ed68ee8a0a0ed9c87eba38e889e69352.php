<?php $__env->startSection('title', 'إعدادات النظام'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إعدادات النظام</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" dusk="settings-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إعدادات النظام</h1>
    </div>

    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show my-3" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="alert alert-danger my-3">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> <?php echo e(__('v2.general_settings')); ?></h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" class="mb-0">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h5 class="mb-3">واجهة المستخدم</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="multilingual" name="multilingual" value="1" <?php echo e(($settings['multilingual'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="multilingual">
                                دعم تعدد اللغات
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل دعم اللغات المتعددة في النظام"></i>
                                <small class="d-block text-muted">تفعيل دعم اللغات المتعددة في النظام</small>
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" value="1" <?php echo e(($settings['dark_mode'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="dark_mode">
                                الوضع الداكن
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل الوضع الليلي لجميع المستخدمين"></i>
                                <small class="d-block text-muted">السماح للمستخدمين باستخدام الوضع الداكن</small>
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enhanced_ui" name="enhanced_ui" value="1" <?php echo e(($settings['enhanced_ui'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="enhanced_ui">
                                واجهة مستخدم محسنة
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل الرسوم المتحركة والمؤثرات البصرية المتقدمة"></i>
                                <small class="d-block text-muted">تفعيل الرسوم المتحركة والمؤثرات البصرية المتقدمة</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="mb-3">المدفوعات والميزات المتقدمة</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="payment_system" name="payment_system" value="1" <?php echo e(($settings['payment_system'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="payment_system">
                                نظام الدفع
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل معالجة المدفوعات داخل النظام"></i>
                                <small class="d-block text-muted">تفعيل معالجة المدفوعات داخل النظام</small>
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="ai_features" name="ai_features" value="1" <?php echo e(($settings['ai_features'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="ai_features">
                                ميزات الذكاء الاصطناعي
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل الذكاء الاصطناعي لتحسين تجربة المستخدم"></i>
                                <small class="d-block text-muted">تفعيل الذكاء الاصطناعي لتحسين تجربة المستخدم</small>
                            </label>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                
                <div class="card mb-4 border-info shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-shoe-prints me-2"></i> إعدادات الفوتر</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="footer_text" class="form-label fw-bold">
                                        نص الفوتر
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="النص الرئيسي الذي يظهر في أسفل الموقع"></i>
                                    </label>
                                    <input type="text" class="form-control" id="footer_text" name="footer_text" value="<?php echo e(config('ui.footer.text', '')); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        روابط الفوتر
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="روابط سريعة تظهر في الفوتر"></i>
                                    </label>
                                    <div id="footer-links-list">
                                        <?php $footerLinks = config('ui.footer.links', []); ?>
                                        <?php $__currentLoopData = $footerLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="input-group mb-2 footer-link-row">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                            <input type="text" name="footer_link_texts[]" class="form-control" placeholder="النص" value="<?php echo e($link['text']); ?>">
                                            <input type="text" name="footer_link_urls[]" class="form-control" placeholder="الرابط" value="<?php echo e($link['url']); ?>">
                                            <button type="button" class="btn btn-outline-danger remove-footer-link" title="حذف الرابط"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info mt-2" id="add-footer-link">
                                        <i class="fas fa-plus"></i> إضافة رابط
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        روابط الخدمات بالفوتر
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="روابط الخدمات السريعة في الفوتر"></i>
                                    </label>
                                    <div id="footer-service-links-list">
                                        <?php $footerServiceLinks = config('ui.footer.services', []); ?>
                                        <?php $__currentLoopData = $footerServiceLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="input-group mb-2 footer-service-link-row">
                                            <span class="input-group-text"><i class="fas fa-concierge-bell"></i></span>
                                            <input type="text" name="footer_service_link_texts[]" class="form-control" placeholder="النص" value="<?php echo e($link['text']); ?>">
                                            <input type="text" name="footer_service_link_urls[]" class="form-control" placeholder="الرابط" value="<?php echo e($link['url']); ?>">
                                            <button type="button" class="btn btn-outline-danger remove-footer-service-link" title="حذف الرابط"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info mt-2" id="add-footer-service-link">
                                        <i class="fas fa-plus"></i> إضافة رابط خدمة
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        روابط التواصل الاجتماعي
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="روابط حسابات التواصل الاجتماعي"></i>
                                    </label>
                                    <div id="footer-social-list">
                                        <?php $footerSocial = config('ui.footer.social', []); ?>
                                        <?php $__currentLoopData = $footerSocial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="input-group mb-2 footer-social-row">
                                            <span class="input-group-text"><i class="fab fa-<?php echo e($social['icon'] ?? 'globe'); ?>"></i></span>
                                            <input type="text" name="footer_social_names[]" class="form-control" placeholder="اسم الشبكة" value="<?php echo e($social['name']); ?>">
                                            <input type="text" name="footer_social_urls[]" class="form-control" placeholder="الرابط" value="<?php echo e($social['url']); ?>">
                                            <input type="text" name="footer_social_icons[]" class="form-control" placeholder="الأيقونة (مثال: facebook)" value="<?php echo e($social['icon']); ?>">
                                            <button type="button" class="btn btn-outline-danger remove-footer-social" title="حذف الشبكة"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info mt-2" id="add-footer-social">
                                        <i class="fas fa-plus"></i> إضافة شبكة
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label fw-bold">
                                        رقم الهاتف
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="رقم الهاتف الظاهر في الفوتر"></i>
                                    </label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo e(config('ui.footer.contact.phone', '')); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label fw-bold">
                                        البريد الإلكتروني
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="البريد الإلكتروني الظاهر في الفوتر"></i>
                                    </label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo e(config('ui.footer.contact.email', '')); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="contact_address" class="form-label fw-bold">
                                        العنوان الفعلي
                                        <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="العنوان الفعلي للوكالة أو الشركة"></i>
                                    </label>
                                    <input type="text" class="form-control" id="contact_address" name="contact_address" value="<?php echo e(config('ui.footer.contact.address', '')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('add-footer-link').onclick = function() {
                        const row = document.createElement('div');
                        row.className = 'input-group mb-2 footer-link-row';
                        row.innerHTML = `<span class="input-group-text"><i class="fas fa-link"></i></span><input type="text" name="footer_link_texts[]" class="form-control" placeholder="النص"><input type="text" name="footer_link_urls[]" class="form-control" placeholder="الرابط"><button type="button" class="btn btn-outline-danger remove-footer-link" title="حذف الرابط"><i class="fas fa-trash"></i></button>`;
                        document.getElementById('footer-links-list').appendChild(row);
                    };
                    document.getElementById('footer-links-list').addEventListener('click', function(e) {
                        if(e.target.classList.contains('remove-footer-link')) {
                            e.target.parentElement.remove();
                        }
                    });
                    document.getElementById('add-footer-service-link').onclick = function() {
                        const row = document.createElement('div');
                        row.className = 'input-group mb-2 footer-service-link-row';
                        row.innerHTML = `<span class="input-group-text"><i class="fas fa-concierge-bell"></i></span><input type="text" name="footer_service_link_texts[]" class="form-control" placeholder="النص"><input type="text" name="footer_service_link_urls[]" class="form-control" placeholder="الرابط"><button type="button" class="btn btn-outline-danger remove-footer-service-link" title="حذف الرابط"><i class="fas fa-trash"></i></button>`;
                        document.getElementById('footer-service-links-list').appendChild(row);
                    };
                    document.getElementById('footer-service-links-list').addEventListener('click', function(e) {
                        if(e.target.classList.contains('remove-footer-service-link')) {
                            e.target.parentElement.remove();
                        }
                    });
                    document.getElementById('add-footer-social').onclick = function() {
                        const row = document.createElement('div');
                        row.className = 'input-group mb-2 footer-social-row';
                        row.innerHTML = `<span class="input-group-text"><i class="fab fa-globe"></i></span><input type="text" name="footer_social_names[]" class="form-control" placeholder="اسم الشبكة"><input type="text" name="footer_social_urls[]" class="form-control" placeholder="الرابط"><input type="text" name="footer_social_icons[]" class="form-control" placeholder="الأيقونة (مثال: facebook)" title="أدخل اسم الأيقونة مثل: facebook"><button type="button" class="btn btn-outline-danger remove-footer-social" title="حذف الشبكة"><i class="fas fa-trash"></i></button>`;
                        document.getElementById('footer-social-list').appendChild(row);
                    };
                    document.getElementById('footer-social-list').addEventListener('click', function(e) {
                        if(e.target.classList.contains('remove-footer-social')) {
                            e.target.parentElement.remove();
                        }
                    });
                });
                </script>
                
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ الإعدادات
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i> الإعدادات المتقدمة</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.settings.updateAdvancedSettings')); ?>" method="POST" class="mb-0">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h5 class="mb-3">إعدادات حسب الدور</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="role_based_settings" name="role_based_settings" value="1" <?php echo e(($settings['role_based_settings'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="role_based_settings">
                                إعدادات حسب الدور
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="تفعيل إعدادات مخصصة حسب صلاحية المستخدم"></i>
                                <small class="d-block text-muted">تفعيل إعدادات مختلفة حسب دور المستخدم</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="mb-3">سجلات التدقيق</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="audit_logs" name="audit_logs" value="1" <?php echo e(($settings['audit_logs'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="audit_logs">
                                سجلات التدقيق
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="عرض التغييرات التي تم إجراؤها على الإعدادات"></i>
                                <small class="d-block text-muted">عرض التغييرات التي تم إجراؤها على الإعدادات</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h5 class="mb-3">تخصيص الثيمات</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="customizable_themes" name="customizable_themes" value="1" <?php echo e(($settings['customizable_themes'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="customizable_themes">
                                تخصيص الثيمات
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="السماح للمسؤولين بتخصيص الثيمات"></i>
                                <small class="d-block text-muted">السماح للمسؤولين بتخصيص الثيمات</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ الإعدادات
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-shapes me-2"></i> ميزات الفوتر</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.settings.updateFooterFeatures')); ?>" method="POST" class="mb-0">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h5 class="mb-3">معاينة الفوتر</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="footer_preview" name="footer_preview" value="1" <?php echo e(($settings['footer_preview'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="footer_preview">
                                معاينة الفوتر
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="عرض معاينة حية للفوتر أثناء التعديل"></i>
                                <small class="d-block text-muted">عرض معاينة حية للفوتر أثناء التعديل</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="mb-3">سحب وإفلات الروابط</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="drag_and_drop_links" name="drag_and_drop_links" value="1" <?php echo e(($settings['drag_and_drop_links'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="drag_and_drop_links">
                                سحب وإفلات الروابط
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="إعادة ترتيب الروابط باستخدام السحب والإفلات"></i>
                                <small class="d-block text-muted">إعادة ترتيب الروابط باستخدام السحب والإفلات</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h5 class="mb-3">طرق تواصل إضافية</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="additional_contact_methods" name="additional_contact_methods" value="1" <?php echo e(($settings['additional_contact_methods'] ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="additional_contact_methods">
                                طرق تواصل إضافية
                                <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" title="إضافة طرق تواصل مثل واتساب وتيليجرام"></i>
                                <small class="d-block text-muted">إضافة طرق تواصل مثل واتساب وتيليجرام</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ الإعدادات
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> إعادة تعيين
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">معلومات النظام</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>إصدار النظام:</th>
                                <td>1.0</td>
                            </tr>
                            <tr>
                                <th>إصدار PHP:</th>
                                <td><?php echo e(phpversion()); ?></td>
                            </tr>
                            <tr>
                                <th>إصدار Laravel:</th>
                                <td><?php echo e(app()->version()); ?></td>
                            </tr>
                            <tr>
                                <th>نوع قاعدة البيانات:</th>
                                <td><?php echo e(config('database.default')); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-lg-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>مساحة التخزين المستخدمة:</th>
                                <td><?php echo e(round(disk_total_space(storage_path()) / 1024 / 1024, 2)); ?> MB</td>
                            </tr>
                            <tr>
                                <th>مساحة التخزين الحرة:</th>
                                <td><?php echo e(round(disk_free_space(storage_path()) / 1024 / 1024, 2)); ?> MB</td>
                            </tr>
                            <tr>
                                <th>الذاكرة المستخدمة:</th>
                                <td><?php echo e(round(memory_get_usage() / 1024 / 1024, 2)); ?> MB</td>
                            </tr>
                            <tr>
                                <th>حالة السيرفر:</th>
                                <td><span class="badge bg-success">يعمل</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">أدوات النظام</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle mx-auto mb-3 bg-primary">
                                <i class="fas fa-database text-white"></i>
                            </div>
                            <h5 class="card-title">نسخ احتياطي</h5>
                            <p class="card-text small">إنشاء نسخة احتياطية من قاعدة البيانات</p>
                            <button class="btn btn-sm btn-primary">إنشاء نسخة</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle mx-auto mb-3 bg-success">
                                <i class="fas fa-broom text-white"></i>
                            </div>
                            <h5 class="card-title">تنظيف الذاكرة</h5>
                            <p class="card-text small">حذف الملفات المؤقتة وتنظيف الذاكرة المخبأة</p>
                            <button class="btn btn-sm btn-success">تنظيف الذاكرة</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle mx-auto mb-3 bg-warning">
                                <i class="fas fa-tasks text-white"></i>
                            </div>
                            <h5 class="card-title">مراقبة الأداء</h5>
                            <p class="card-text small">عرض تقرير مفصل عن أداء النظام</p>
                            <button class="btn btn-sm btn-warning">عرض التقرير</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle mx-auto mb-3 bg-danger">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                            <h5 class="card-title">فحص أمني</h5>
                            <p class="card-text small">البحث عن الثغرات الأمنية المحتملة</p>
                            <button class="btn btn-sm btn-danger">بدء الفحص</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // تفعيل تلميحات Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/jak-travel-sys/resources/views/admin/settings.blade.php ENDPATH**/ ?>