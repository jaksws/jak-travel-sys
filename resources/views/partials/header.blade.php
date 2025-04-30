<header class="navbar navbar-expand-md shadow-sm sticky-top transition-all duration-300" style="background-color: var(--bg-card);">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" width="32" height="32" class="me-2" onerror="this.style.display='none'">
            <span>{{ config('app.name', 'تطبيق وكالات السفر') }}</span>
        </a>
        <button class="navbar-toggler border-0 focus:outline-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="تبديل التنقل">
            <i class="fas fa-bars text-primary"></i>
        </button>
        <button class="btn btn-primary d-md-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <!-- القائمة العلوية على اليمين -->
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }} hover:text-primary transition-colors duration-200" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> الرئيسية
                    </a>
                </li>
                @auth
                    @if(auth()->user()->isAgency())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('agency.*') ? 'active fw-bold' : '' }} hover:text-primary transition-colors duration-200" href="{{ route('agency.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                            </a>
                        </li>
                    @elseif(auth()->user()->isSubagent())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subagent.*') ? 'active fw-bold' : '' }} hover:text-primary transition-colors duration-200" href="{{ route('subagent.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                            </a>
                        </li>
                    @elseif(auth()->user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.*') ? 'active fw-bold' : '' }} hover:text-primary transition-colors duration-200" href="{{ route('customer.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
            
            <!-- القائمة العلوية على اليسار -->
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 d-flex align-items-center">
                @guest
                    <li class="nav-item mx-1">
                        <a class="nav-link btn btn-outline-primary rounded-pill px-3 py-1 {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link btn btn-primary text-white rounded-pill px-3 py-1 {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> التسجيل
                        </a>
                    </li>
                @else
                    <!-- إضافة زر الإشعارات -->
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" dusk="notifications-dropdown-toggle">
                            <i class="fas fa-bell fs-5"></i>
                            <!-- يمكن تعديل هذا الجزء ليعرض عدد الإشعارات الفعلية من قاعدة البيانات -->
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                    <span class="visually-hidden">إشعارات جديدة</span>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 280px; max-height: 400px; overflow-y: auto;" aria-labelledby="notificationsDropdown" dusk="notifications-dropdown-menu">
                            <h6 class="dropdown-header bg-light fw-bold">الإشعارات</h6>
                            @if(auth()->user()->notifications->count() > 0)
                                @foreach(auth()->user()->notifications->take(5) as $notification)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 {{ $notification->read_at ? '' : 'bg-light' }}" href="#">
                                            <div class="icon-circle bg-primary text-white me-3">
                                                <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                            </div>
                                            <div>
                                                <span class="small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                                                <p class="mb-0 small">{{ $notification->data['message'] ?? 'إشعار جديد' }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}">عرض كل الإشعارات</a></li>
                            @else
                                <li><a class="dropdown-item text-center py-3" href="#">لا توجد إشعارات</a></li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" dusk="user-dropdown-toggle">
                            <!-- صورة المستخدم أو الأحرف الأولى من اسمه -->
                            <div class="avatar-circle bg-primary text-white me-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:50%;">
                                @if(auth()->user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="rounded-circle w-100 h-100">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown" dusk="user-dropdown-menu">
                            <li>
                                <div class="dropdown-header bg-primary bg-gradient text-white p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-white text-primary me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;border-radius:50%;">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ auth()->user()->name }}</h6>
                                            <small>{{ auth()->user()->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}" dusk="user-dropdown-profile-link"><i class="fas fa-user-edit me-2"></i> {{ __('v2.profile_settings') }}</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('user.preferences') }}"><i class="fas fa-palette me-2"></i> {{ __('v2.appearance_settings') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger" dusk="user-dropdown-logout-btn"><i class="fas fa-sign-out-alt me-2"></i> {{ __('v2.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
                <!-- إضافة زر تبديل الوضع الليلي -->
                @if(config('v1_features.dark_mode.enabled', false))
                <li class="nav-item ms-2">
                    <button id="dark-mode-toggle" class="btn btn-sm rounded-circle" style="width: 38px; height: 38px; background-color: var(--bg-secondary);" title="{{ __('v2.toggle_dark_mode') }}">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>
                </li>
                @endif
                <!-- إضافة مفتاح تغيير اللغة -->
                @if(config('v1_features.multilingual.enabled', false))
                <li class="nav-item dropdown ms-2">
                    <a class="nav-link p-0 btn btn-sm rounded-circle" style="width: 38px; height: 38px; background-color: var(--bg-secondary);" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="languageDropdown">
                        <h6 class="dropdown-header">{{ __('v2.select_language') }}</h6>
                        @foreach(config('v1_features.multilingual.available_locales', ['ar']) as $locale)
                            <li>
                                <form action="{{ route('user.preferences.save') }}" method="POST" class="locale-form">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $locale }}">
                                    <button type="submit" class="dropdown-item d-flex align-items-center {{ app()->getLocale() == $locale ? 'active bg-light' : '' }}">
                                        <!-- تحديد أعلام/رموز اللغات -->
                                        <span class="me-2">
                                            @switch($locale)
                                                @case('ar')
                                                    🇸🇦
                                                    @break
                                                @case('en')
                                                    🇬🇧
                                                    @break
                                                @case('fr')
                                                    🇫🇷
                                                    @break
                                                @case('tr')
                                                    🇹🇷
                                                    @break
                                                @case('es')
                                                    🇪🇸
                                                    @break
                                                @case('id')
                                                    🇮🇩
                                                    @break
                                                @case('ur')
                                                    🇵🇰
                                                    @break
                                                @default
                                                    🌐
                                            @endswitch
                                        </span>
                                        @switch($locale)
                                            @case('ar')
                                                العربية
                                                @break
                                            @case('en')
                                                English
                                                @break
                                            @case('fr')
                                                Français
                                                @break
                                            @case('tr')
                                                Türkçe
                                                @break
                                            @case('es')
                                                Español
                                                @break
                                            @case('id')
                                                Bahasa Indonesia
                                                @break
                                            @case('ur')
                                                اردو
                                                @break
                                            @default
                                                {{ $locale }}
                                        @endswitch
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</header>

@push('scripts')
<script>
    // تنفيذ نماذج تغيير اللغة بواسطة AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const localeForms = document.querySelectorAll('.locale-form');
        
        localeForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                fetch(form.action, {
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
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });

    // Toggle sidebar visibility
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
    });
</script>
@endpush
