<?php $__env->startSection('title', __('v2.profile_settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0 px-md-3 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow rounded-lg border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-cog fa-lg me-2"></i>
                    <h4 class="mb-0"><?php echo e(__('v2.profile_settings')); ?></h4>
                </div>
                <div class="card-body p-4">
                    <form id="preferences-form" action="<?php echo e(route('user.preferences.save')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <ul class="nav nav-tabs mb-4" id="settings-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab" aria-controls="appearance" aria-selected="true">
                                    <i class="fas fa-palette me-1"></i> <?php echo e(__('v2.appearance_settings')); ?>

                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                    <i class="fas fa-bell me-1"></i> <?php echo e(__('v2.notification_settings')); ?>

                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="settings-content">
                            <div class="tab-pane fade show active" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                                <div class="mb-4">
                                    <label for="locale" class="form-label fw-bold">
                                        <i class="fas fa-language me-1"></i> <?php echo e(__('v2.language')); ?>

                                    </label>
                                    <select id="locale" name="locale" class="form-select">
                                        <?php $__currentLoopData = (array) (config('v1_features.multilingual.available_locales') ?? ['ar','en']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($locale); ?>" <?php echo e($currentLocale == $locale ? 'selected' : ''); ?>>
                                                <?php switch($locale):
                                                    case ('ar'): ?> العربية <?php break; ?>
                                                    <?php case ('en'): ?> English <?php break; ?>
                                                    <?php case ('fr'): ?> Français <?php break; ?>
                                                    <?php case ('tr'): ?> Türkçe <?php break; ?>
                                                    <?php case ('es'): ?> Español <?php break; ?>
                                                    <?php case ('id'): ?> Bahasa Indonesia <?php break; ?>
                                                    <?php case ('ur'): ?> اردو <?php break; ?>
                                                    <?php default: ?> <?php echo e($locale); ?>

                                                <?php endswitch; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="theme" class="form-label fw-bold">
                                        <i class="fas fa-moon me-1"></i> <?php echo e(__('v2.theme')); ?>

                                    </label>
                                    <select id="theme-selector" name="theme" class="form-select">
                                        <option value="light" <?php echo e(($currentTheme ?? 'system') == 'light' ? 'selected' : ''); ?>><?php echo e(__('v2.light_mode')); ?></option>
                                        <option value="dark" <?php echo e(($currentTheme ?? 'system') == 'dark' ? 'selected' : ''); ?>><?php echo e(__('v2.dark_mode')); ?></option>
                                        <option value="system" <?php echo e(($currentTheme ?? 'system') == 'system' ? 'selected' : ''); ?>><?php echo e(__('v2.system_mode')); ?></option>
                                    </select>
                                </div>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="dark-mode-toggle" <?php echo e(($currentTheme ?? 'system') == 'dark' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="dark-mode-toggle">
                                        <i class="fas fa-adjust me-1"></i> <?php echo e(__('v2.toggle_dark_mode')); ?>

                                    </label>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input id="email_notifications" name="email_notifications" type="checkbox" class="form-check-input" <?php echo e(isset($preferences['email_notifications']) && $preferences['email_notifications'] ? 'checked' : ''); ?>>
                                        <label for="email_notifications" class="form-check-label fw-bold">
                                            <i class="fas fa-envelope me-1"></i> <?php echo e(__('Email Notifications')); ?>

                                        </label>
                                        <div class="form-text"><?php echo e(__('Receive notifications via email')); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> <?php echo e(__('v2.save')); ?>

                            </button>
                        </div>
                        <div id="preferences-success" class="alert alert-success mt-3 d-none" role="alert">
                            <i class="fas fa-check-circle me-1"></i> <?php echo e(__('v2.preferences_updated_successfully', ['default' => 'تم حفظ التفضيلات بنجاح'])); ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap tab switching
        var triggerTabList = [].slice.call(document.querySelectorAll('#settings-tabs button'));
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });

        // تفعيل زر التبديل للوضع الليلي
        const themeSelector = document.getElementById('theme-selector');
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const preferencesForm = document.getElementById('preferences-form');

        if (darkModeToggle && themeSelector) {
            darkModeToggle.addEventListener('change', function() {
                console.log('Toggle changed:', darkModeToggle.checked);
                if (darkModeToggle.checked) {
                    themeSelector.value = 'dark';
                } else {
                    themeSelector.value = 'light';
                }
                // Trigger change event for select to sync UI
                themeSelector.dispatchEvent(new Event('change'));
                // Submit the form after a short delay to ensure value is set
                setTimeout(function() {
                    preferencesForm.requestSubmit();
                }, 100);
            });
            themeSelector.addEventListener('change', function() {
                console.log('Theme select changed:', themeSelector.value);
                if (themeSelector.value === 'dark') {
                    darkModeToggle.checked = true;
                } else {
                    darkModeToggle.checked = false;
                }
            });
        }

        // AJAX form submission
        preferencesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(preferencesForm);
            fetch(preferencesForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preferences-success').classList.remove('d-none');
                    setTimeout(() => {
                        document.getElementById('preferences-success').classList.add('d-none');
                    }, 2500);
                    // Reload page if locale was changed
                    const newLocale = formData.get('locale');
                    const currentLocale = '<?php echo e($currentLocale); ?>';
                    if (newLocale !== currentLocale) {
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                alert('حدث خطأ أثناء الحفظ');
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/user/preferences/index.blade.php ENDPATH**/ ?>