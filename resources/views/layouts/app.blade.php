<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ Session::get('textDirection', 'rtl') }}"
      class="{{ Session::get('theme') === 'dark' ? 'dark dark-theme' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'تطبيق وكالات السفر'))</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- بوتستراب RTL/LTR CSS -->
    @if(Session::get('textDirection', 'rtl') === 'rtl')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @endif
    
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- الستايلات المخصصة -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- السكريبتات -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
            text-align: {{ Session::get('textDirection', 'rtl') === 'rtl' ? 'right' : 'left' }};
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
            margin-{{ Session::get('textDirection', 'rtl') === 'rtl' ? 'left' : 'right' }}: 10px;
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
                position: static;
                margin-bottom: 20px;
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
        @include('partials.header')

        <main class="py-4">
            @if(request()->routeIs('agency.*') || request()->routeIs('subagent.*') || request()->routeIs('customer.*'))
                <div class="container-fluid">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-md-3 col-lg-2 sidebar">
                            @include('partials.sidebar')
                        </div>
                        
                        <!-- Main content -->
                        <div class="col-md-9 col-lg-10 px-md-4">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb" class="mb-4">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('v2.dashboard') }}</a></li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                            
                            <!-- Content -->
                            @yield('content')
                        </div>
                    </div>
                </div>
            @else
                <div class="container">
                    @yield('content')
                </div>
            @endif
        </main>
        
        <footer class="footer py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h5>{{ config('app.name', 'وكالات السفر') }}</h5>
                        <p>نظام متكامل لإدارة وكالات السفر والسبوكلاء والعملاء</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>روابط سريعة</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ url('/') }}">{{ __('v2.dashboard') }}</a></li>
                            <li><a href="#">{{ __('About Us') }}</a></li>
                            <li><a href="#">{{ __('Contact') }}</a></li>
                            <li><a href="#">{{ __('Terms & Conditions') }}</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Contact Us') }}</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope me-2"></i> info@travelagency.com</li>
                            <li><i class="fas fa-phone me-2"></i> +966 55 123 4567</li>
                            <li><i class="fas fa-map-marker-alt me-2"></i> الرياض، المملكة العربية السعودية</li>
                        </ul>
                        <div class="mt-3">
                            <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'وكالات السفر') }}. {{ __('All rights reserved') }}</p>
                    <div class="version">
                        Version: {{ config('app.version', '1.1') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- تضمين سكريبت الوضع المظلم -->
    @if(config('v1_features.dark_mode.enabled', false))
        <script>
            window.darkModeSettings = {
                enabled: {{ config('v1_features.dark_mode.enabled') ? 'true' : 'false' }},
                default: '{{ config('v1_features.dark_mode.default', 'system') }}'
            };
            window.userId = {{ auth()->id() ?: 'null' }};
        </script>
        <script src="{{ asset('js/dark-mode.js') }}"></script>
    @endif
    
    @stack('scripts')
</body>
</html>
