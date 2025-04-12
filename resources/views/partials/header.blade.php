<header class="navbar navbar-expand-md navbar-dark fixed-top bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            {{ config('app.name', 'تطبيق وكالات السفر') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="تبديل التنقل">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <!-- القائمة العلوية على اليمين -->
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                </li>
                @auth
                    @if(auth()->user()->isAgency())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('agency.*') ? 'active' : '' }}" href="{{ route('agency.dashboard') }}">لوحة التحكم</a>
                        </li>
                    @elseif(auth()->user()->isSubagent())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subagent.*') ? 'active' : '' }}" href="{{ route('subagent.dashboard') }}">لوحة التحكم</a>
                        </li>
                    @elseif(auth()->user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">لوحة التحكم</a>
                        </li>
                    @endif
                @endauth
            </ul>
            
            <!-- القائمة العلوية على اليسار -->
            <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">تسجيل الدخول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">التسجيل</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle me-1"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('v2.profile_settings') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.preferences') }}">{{ __('v2.appearance_settings') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('v2.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
                <!-- إضافة زر تبديل الوضع الليلي -->
                @if(config('v1_features.dark_mode.enabled', false))
                <li class="nav-item ms-2">
                    <button id="dark-mode-toggle" class="btn btn-outline-secondary btn-sm" title="{{ __('v2.toggle_dark_mode') }}">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>
                </li>
                @endif
                <!-- إضافة مفتاح تغيير اللغة -->
                @if(config('v1_features.multilingual.enabled', false))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        @foreach(config('v1_features.multilingual.available_locales', ['ar']) as $locale)
                            <li>
                                <form action="{{ route('user.preferences.save') }}" method="POST" class="locale-form">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $locale }}">
                                    <button type="submit" class="dropdown-item {{ app()->getLocale() == $locale ? 'active' : '' }}">
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
</script>
@endpush
