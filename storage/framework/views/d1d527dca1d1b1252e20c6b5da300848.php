<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" 
      dir="<?php echo e(Session::get('textDirection', 'rtl')); ?>"
      class="<?php echo e(Session::get('theme') === 'dark' ? 'dark dark-theme' : ''); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', config('app.name', 'تطبيق وكالات السفر')); ?></title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- بوتستراب RTL/LTR CSS -->
    <?php if(Session::get('textDirection', 'rtl') === 'rtl'): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php else: ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php endif; ?>
    
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- الستايلات المخصصة -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    
    <!-- السكريبتات -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-card: #ffffff;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --primary-color: #4a90e2;
            --secondary-color: #6c757d;
        }
        
        .dark {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-card: #2d2d2d;
            --text-primary: #e0e0e0;
            --text-secondary: #aaaaaa;
            --border-color: #424242;
            --primary-color: #4b9fff;
            --secondary-color: #757575;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .navbar, .navbar-light {
            background-color: var(--bg-card) !important;
            border-bottom: 1px solid var(--border-color);
        }

        .dark .navbar-light {
            background-color: var(--bg-secondary) !important;
        }
        
        .dark .navbar-light .navbar-nav .nav-link {
            color: var(--text-primary);
        }
        
        .dropdown-menu {
            text-align: <?php echo e(Session::get('textDirection', 'rtl') === 'rtl' ? 'right' : 'left'); ?>;
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }
        
        .dropdown-item {
            color: var(--text-primary);
        }
        
        .dropdown-item:hover {
            background-color: var(--bg-secondary);
            color: var(--primary-color);
        }
        
        .main-content {
            min-height: calc(100vh - 160px);
        }
        
        .breadcrumb {
            background-color: var(--bg-secondary);
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            color: var(--text-secondary);
        }
        
        .card {
            background-color: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-primary);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-<?php echo e(Session::get('textDirection', 'rtl') === 'rtl' ? 'left' : 'right'); ?>: 10px;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #4a90e2, #825ee4);
            color: white;
            padding: 3rem 0;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .feature-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
            background-color: var(--bg-card);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .sidebar {
            min-height: calc(100vh - 72px);
            background-color: var(--bg-secondary);
            position: sticky;
            top: 72px;
            padding-top: 20px;
            transition: right 0.3s ease; /* P2e61 */
        }
        
        .sidebar .nav-link {
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--bg-primary);
            color: var(--primary-color);
        }
        
        .footer {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            border-top: 1px solid var(--border-color);
        }
        
        .dark .footer {
            background-color: #1a1a1a;
        }
        
        .dark .footer a {
            color: #4b9fff;
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                right: -250px; /* P492e */
                width: 250px; /* P2ab4 */
                height: 100%;
                z-index: 1040;
                transition: right 0.3s ease; /* P554a */
            }
            
            .sidebar.show {
                right: 0; /* P492e */
            }
            
            .sidebar .nav-link {
                display: block; /* P68cd */
                padding: 10px 15px; /* P68cd */
                margin: 5px 0; /* P68cd */
            }
            
            .sidebar .nav-link i {
                margin-right: 10px; /* P4dfd */
            }
            
            .sidebar .nav-link span {
                display: inline-block; /* P4dfd */
                vertical-align: middle; /* P4dfd */
            }
            
            .sidebar .nav-link.active {
                background-color: var(--primary-color); /* P614a */
                color: #fff; /* P614a */
            }
            
            .sidebar .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1); /* P7847 */
                color: #fff; /* P7847 */
            }
            
            .main-content {
                margin-right: 0;
                width: 100%;
            }
            
            .navbar-toggler {
                display: block;
            }
            
            .dashboard-stats .stat-card {
                margin-bottom: 15px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
        
        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div id="app">
        <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="py-4">
            <?php if(
                request()->routeIs('agency.*') ||
                request()->routeIs('subagent.*') ||
                request()->routeIs('customer.*') ||
                request()->routeIs('admin.*') ||
                request()->routeIs('profile.*') ||
                request()->routeIs('user.preferences') ||
                request()->routeIs('notifications.*')
            ): ?>
                <div class="container-fluid">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-md-3 col-lg-2 sidebar collapsible"> <!-- P674c -->
                            <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                        <!-- Main content -->
                        <div class="col-md-9 col-lg-10 px-md-4">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb" class="mb-4">
                                <ol class="breadcrumb">
                                    <?php echo $__env->yieldContent('breadcrumb'); ?>
                                </ol>
                            </nav>
                            <!-- Content -->
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="container">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            <?php endif; ?>
        </main>
        
        <footer class="footer py-5 mt-4">
            <div class="container">
                <!-- القسم العلوي من التذييل -->
                <div class="row mb-4">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="footer-brand d-flex align-items-center mb-3">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="<?php echo e(config('app.name')); ?>" width="40" height="40" class="me-2" onerror="this.style.display='none'">
                            <h5 class="m-0"><?php echo e(config('app.name', 'وكالات السفر')); ?></h5>
                        </div>
                        <p class="text-secondary"><?php echo e(config('ui.footer.text', 'نظام متكامل لإدارة وكالات السفر والسبوكلاء والعملاء، يوفر حلولاً شاملة لعمليات الحجز وإدارة الخدمات السياحية.')); ?></p>
                        <div class="social-icons">
                            <?php $__currentLoopData = config('ui.footer.social', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($social['url']); ?>" class="me-2 social-icon" target="_blank">
                                    <i class="fab fa-<?php echo e($social['icon'] ?? 'globe'); ?>"></i>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0">
                        <h6 class="fw-bold mb-3">روابط سريعة</h6>
                        <ul class="list-unstyled footer-links">
                            <?php $__currentLoopData = config('ui.footer.links', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="mb-2"><a href="<?php echo e($link['url']); ?>"><i class="fas fa-chevron-left me-1 small"></i> <?php echo e($link['text']); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <div class="col-sm-6 col-lg-2 mb-4 mb-lg-0">
                        <h6 class="fw-bold mb-3">خدماتنا</h6>
                        <ul class="list-unstyled footer-links">
                            <?php $__currentLoopData = config('ui.footer.services', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="mb-2"><a href="<?php echo e($link['url']); ?>"><i class="fas fa-chevron-left me-1 small"></i> <?php echo e($link['text']); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h6 class="fw-bold mb-3"><?php echo e(__('Contact Us')); ?></h6>
                        <ul class="list-unstyled footer-contact">
                            <li class="mb-3 d-flex">
                                <span class="icon-circle bg-primary-soft text-primary me-3">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <div>
                                    <p class="mb-0 text-secondary">البريد الإلكتروني</p>
                                    <a href="mailto:<?php echo e(config('ui.footer.contact.email', 'info@travelagency.com')); ?>"><?php echo e(config('ui.footer.contact.email', 'info@travelagency.com')); ?></a>
                                </div>
                            </li>
                            <li class="mb-3 d-flex">
                                <span class="icon-circle bg-primary-soft text-primary me-3">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div>
                                    <p class="mb-0 text-secondary">رقم الهاتف</p>
                                    <a href="tel:<?php echo e(config('ui.footer.contact.phone', '+966551234567')); ?>"><?php echo e(config('ui.footer.contact.phone', '+966 55 123 4567')); ?></a>
                                </div>
                            </li>
                            <li class="d-flex">
                                <span class="icon-circle bg-primary-soft text-primary me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <div>
                                    <p class="mb-0 text-secondary">العنوان</p>
                                    <address class="mb-0"><?php echo e(config('ui.footer.contact.address', 'الرياض، المملكة العربية السعودية')); ?></address>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- القسم السفلي من التذييل -->
                <hr>
                <div class="row align-items-center py-3">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0">&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'وكالات السفر')); ?>. <?php echo e(config('ui.footer.text', __('All rights reserved'))); ?></p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-meta d-flex flex-wrap justify-content-center justify-content-md-end">
                            <?php $__currentLoopData = config('ui.footer.links', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="mx-2"><a href="<?php echo e($link['url']); ?>"><?php echo e($link['text']); ?></a></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <span class="version ms-3 text-muted">
                                <i class="fas fa-code-branch me-1 small"></i> v<?php echo e(config('app.version', '1.1')); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    
    <!-- تضمين سكريبت الوضع المظلم -->
    <?php if(config('v1_features.dark_mode.enabled', false)): ?>
        <script>
            window.darkModeSettings = {
                enabled: <?php echo e(config('v1_features.dark_mode.enabled') ? 'true' : 'false'); ?>,
                default: '<?php echo e(config('v1_features.dark_mode.default', 'system')); ?>'
            };
            window.userId = <?php echo e(auth()->id() ?: 'null'); ?>;
        </script>
        <script src="<?php echo e(asset('js/dark-mode.js')); ?>"></script>
    <?php endif; ?>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/mohammed/web/sys.jaksws.com/public_html/resources/views/layouts/app.blade.php ENDPATH**/ ?>