<div class="sidebar-container h-100">
    <!-- معلومات المستخدم المختصرة -->
    <div class="text-center mb-4 p-4">
        <div class="avatar-circle mx-auto mb-3 position-relative shadow-sm" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), #825ee4);">
            @if(auth()->user()->profile_photo_path)
                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="rounded-circle w-100 h-100">
            @else
                <div class="d-flex align-items-center justify-content-center w-100 h-100">
                    <span class="text-white fw-bold fs-3">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            @endif
            <div class="online-indicator"></div>
        </div>
        <h6 class="fw-bold mb-1">{{ auth()->user()->name }}</h6>
        <span class="badge rounded-pill px-3 py-2 mb-3" 
            style="background: {{ auth()->user()->isAgency() ? 'var(--success-color)' : (auth()->user()->isSubagent() ? 'var(--info-color)' : 'var(--warning-color)') }}">
            @if(auth()->user()->isAgency())
                <i class="fas fa-building me-1"></i> وكيل رئيسي
            @elseif(auth()->user()->isSubagent())
                <i class="fas fa-handshake me-1"></i> سبوكيل
            @elseif(auth()->user()->isCustomer())
                <i class="fas fa-user me-1"></i> عميل
            @endif
        </span>
    </div>
    
    <div class="px-3">
        <div class="sidebar-search mb-3">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="fas fa-search text-muted small"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-start-0 form-control-sm" 
                    id="sidebarSearch" placeholder="بحث سريع...">
            </div>
        </div>
        
        <!-- قائمة حسب نوع المستخدم -->
        <div class="sidebar-menu">
            @if(auth()->user()->isAdmin())
                <!-- قائمة المسؤول -->
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">نظرة عامة</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tachometer-alt fa-fw"></i>
                            </div>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">إدارة النظام</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-users fa-fw"></i>
                            </div>
                            <span>إدارة المستخدمين</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}" href="{{ route('admin.requests.index') }}" dusk="manage-requests-link">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-clipboard-list fa-fw"></i>
                            </div>
                            <span>إدارة الطلبات</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">النظام</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->is('admin/settings*') ? 'active' : '' }}" href="/admin/settings" dusk="settings-link">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-cog fa-fw"></i>
                            </div>
                            <span>الإعدادات</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.system.logs') ? 'active' : '' }}" href="{{ route('admin.system.logs') }}" dusk="quick-link-system-logs">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-file-alt fa-fw"></i>
                            </div>
                            <span>سجلات النظام</span>
                        </a>
                    </li>
                </ul>
            @elseif(auth()->user()->isAgency())
                <!-- قائمة الوكيل الأساسي -->
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">نظرة عامة</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.dashboard') ? 'active' : '' }}" href="{{ route('agency.dashboard') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tachometer-alt fa-fw"></i>
                            </div>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.reports.*') ? 'active' : '' }}" href="{{ route('agency.reports.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-chart-bar fa-fw"></i>
                            </div>
                            <span>التقارير</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">إدارة العملاء</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.subagents.*') ? 'active' : '' }}" href="{{ route('agency.subagents.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-users fa-fw"></i>
                            </div>
                            <span>السبوكلاء</span>
                            @php $subagentsCount = rand(5, 20); @endphp <!-- سنستبدل هذا بعدد فعلي فيما بعد -->
                            <span class="badge ms-auto rounded-pill bg-secondary">{{ $subagentsCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.customers.*') ? 'active' : '' }}" href="{{ route('agency.customers.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-user-friends fa-fw"></i>
                            </div>
                            <span>العملاء</span>
                            @php $customersCount = rand(10, 40); @endphp <!-- سنستبدل هذا بعدد فعلي فيما بعد -->
                            <span class="badge ms-auto rounded-pill bg-secondary">{{ $customersCount }}</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">الخدمات والعروض</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.services.*') ? 'active' : '' }}" href="{{ route('agency.services.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-cogs fa-fw"></i>
                            </div>
                            <span>الخدمات</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.requests.*') ? 'active' : '' }}" href="{{ route('agency.requests.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-file-alt fa-fw"></i>
                            </div>
                            <span>الطلبات</span>
                            @if(rand(0, 1) > 0) <!-- سنستبدل هذا بعدد فعلي فيما بعد -->
                            <span class="badge pulse ms-auto rounded-pill bg-danger">{{ rand(1, 5) }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.quotes.*') ? 'active' : '' }}" href="{{ route('agency.quotes.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tag fa-fw"></i>
                            </div>
                            <span>عروض الأسعار</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">المالية</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.transactions.*') ? 'active' : '' }}" href="{{ route('agency.transactions.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-money-bill fa-fw"></i>
                            </div>
                            <span>المعاملات المالية</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">الإعدادات</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.settings.*') ? 'active' : '' }}" href="{{ route('agency.settings.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-cog fa-fw"></i>
                            </div>
                            <span>الإعدادات العامة</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('agency.settings.currencies') ? 'active' : '' }}" href="{{ route('agency.settings.currencies') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-dollar-sign fa-fw"></i>
                            </div>
                            <span>إعدادات العملات</span>
                        </a>
                    </li>
                </ul>
            @elseif(auth()->user()->isSubagent())
                <!-- قائمة السبوكيل -->
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">نظرة عامة</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('subagent.dashboard') ? 'active' : '' }}" href="{{ route('subagent.dashboard') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tachometer-alt fa-fw"></i>
                            </div>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">الخدمات والطلبات</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('subagent.services.*') ? 'active' : '' }}" href="{{ route('subagent.services.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-cogs fa-fw"></i>
                            </div>
                            <span>الخدمات المتاحة</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('subagent.requests.*') ? 'active' : '' }}" href="{{ route('subagent.requests.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-file-alt fa-fw"></i>
                            </div>
                            <span>طلبات عروض الأسعار</span>
                            @if(rand(0, 1) > 0) <!-- سنستبدل هذا بعدد فعلي فيما بعد -->
                            <span class="badge pulse ms-auto rounded-pill bg-danger">{{ rand(1, 3) }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('subagent.quotes.*') ? 'active' : '' }}" href="{{ route('subagent.quotes.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tag fa-fw"></i>
                            </div>
                            <span>عروض الأسعار المقدمة</span>
                        </a>
                    </li>
                </ul>
            @elseif(auth()->user()->isCustomer())
                <!-- قائمة العميل -->
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">نظرة عامة</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tachometer-alt fa-fw"></i>
                            </div>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">الخدمات والطلبات</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.services.*') ? 'active' : '' }}" href="{{ route('customer.services.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-concierge-bell fa-fw"></i>
                            </div>
                            <span>الخدمات المتاحة</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.requests.create') ? 'active' : '' }}" href="{{ route('customer.requests.create') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-plus-circle fa-fw"></i>
                            </div>
                            <span>طلب جديد</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.requests.*') && !request()->routeIs('customer.requests.create') ? 'active' : '' }}" href="{{ route('customer.requests.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-file-alt fa-fw"></i>
                            </div>
                            <span>طلباتي</span>
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.quotes.*') ? 'active' : '' }}" href="{{ route('customer.quotes.index') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-tag fa-fw"></i>
                            </div>
                            <span>عروض الأسعار</span>
                            @if(rand(0, 1) > 0) <!-- سنستبدل هذا بعدد فعلي فيما بعد -->
                            <span class="badge pulse ms-auto rounded-pill bg-success">{{ rand(1, 3) }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-heading">
                    <small class="text-uppercase opacity-75 fw-bold fs-8">الدعم</small>
                </div>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item mb-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('customer.support') ? 'active' : '' }}" href="{{ route('customer.support') }}">
                            <div class="icon-circle flex-shrink-0 me-2">
                                <i class="fas fa-question-circle fa-fw"></i>
                            </div>
                            <span>الدعم الفني</span>
                        </a>
                    </li>
                </ul>
            @endif
        </div>
        
        <hr>
        
        <!-- روابط الملف الشخصي والخروج -->
        <ul class="nav flex-column mb-2">
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <div class="icon-circle flex-shrink-0 me-2">
                        <i class="fas fa-user-edit fa-fw"></i>
                    </div>
                    <span>الملف الشخصي</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('user.preferences') ? 'active' : '' }}" href="{{ route('user.preferences') }}">
                    <div class="icon-circle flex-shrink-0 me-2">
                        <i class="fas fa-palette fa-fw"></i>
                    </div>
                    <span>الإعدادات</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <div class="icon-circle flex-shrink-0 me-2">
                        <i class="fas fa-bell fa-fw"></i>
                    </div>
                    <span>الإشعارات</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge ms-auto rounded-pill bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();" dusk="logout-link">
                    <div class="icon-circle flex-shrink-0 me-2">
                        <i class="fas fa-sign-out-alt fa-fw"></i>
                    </div>
                    <span>تسجيل الخروج</span>
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .sidebar-container {
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
        background: var(--bg-secondary);
        border-left: 1px solid var(--border-color);
    }
    
    .sidebar-container::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar-container::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .sidebar-container::-webkit-scrollbar-thumb {
        background-color: var(--border-color);
        border-radius: 6px;
    }
    
    .sidebar-heading {
        padding: 0.75rem 1rem 0.5rem;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        margin-top: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .sidebar-search .form-control {
        background-color: var(--bg-secondary);
        border-color: var(--border-color);
    }
    
    .sidebar-search .input-group-text {
        border-color: var(--border-color);
    }
    
    .sidebar .nav-link {
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 4px;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        position: relative;
        padding: 0.65rem 1rem;
        color: var(--text-primary);
        background: transparent;
    }
    
    .sidebar .nav-link .icon-circle {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: var(--bg-card);
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        font-size: 1.15rem;
        margin-left: 0.75rem;
        margin-right: 0;
        box-shadow: 0 1px 4px rgba(59,130,246,0.04);
    }
    
    .sidebar .nav-link.active,
    .sidebar .nav-link:focus,
    .sidebar .nav-link:hover {
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: #fff !important;
        box-shadow: 0 2px 8px rgba(59,130,246,0.07);
    }
    
    .sidebar .nav-link.active .icon-circle,
    .sidebar .nav-link:focus .icon-circle,
    .sidebar .nav-link:hover .icon-circle {
        background: #fff;
        color: var(--primary-color);
        box-shadow: 0 2px 8px rgba(59,130,246,0.10);
    }
    
    .sidebar .badge {
        font-size: 0.8rem;
        padding: 0.25em 0.7em;
        border-radius: 12px;
        font-weight: 600;
        margin-right: 0.5em;
        background: var(--primary-soft, #e3f0ff);
        color: var(--primary-color);
        box-shadow: 0 1px 3px rgba(59,130,246,0.07);
    }
    
    .sidebar .badge.pulse {
        animation: pulse 1.5s infinite;
        background: var(--danger-color, #dc3545);
        color: #fff;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220,53,69,0.5);}
        70% { box-shadow: 0 0 0 10px rgba(220,53,69,0);}
        100% { box-shadow: 0 0 0 0 rgba(220,53,69,0);}
    }
    
    .sidebar .online-indicator {
        position: absolute;
        bottom: 2px;
        right: 0px;
        width: 12px;
        height: 12px;
        background-color: #10b981;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px var(--bg-secondary);
    }
    
    .avatar-circle {
        transition: transform 0.3s ease;
    }
    
    .avatar-circle:hover {
        transform: scale(1.05);
    }
    
    /* الوضع الليلي */
    .dark .sidebar-container {
        background: #181a1b;
        border-left: 1px solid #23272b;
    }
    .dark .sidebar .nav-link {
        color: #e0e0e0;
        background: transparent;
    }
    .dark .sidebar .nav-link.active,
    .dark .sidebar .nav-link:focus,
    .dark .sidebar .nav-link:hover {
        background: linear-gradient(90deg, #4b9fff 0%, #757575 100%);
        color: #fff !important;
    }
    .dark .sidebar .nav-link .icon-circle {
        background: #23272b;
        color: #aaa;
    }
    .dark .sidebar .nav-link.active .icon-circle,
    .dark .sidebar .nav-link:focus .icon-circle,
    .dark .sidebar .nav-link:hover .icon-circle {
        background: #fff;
        color: #4b9fff;
    }
    .dark .sidebar-heading {
        color: #4b9fff;
    }
    
    /* تحسين التباعد في الأجهزة الصغيرة */
    @media (max-width: 768px) {
        .sidebar-container {
            padding-bottom: 60px;
        }
        .sidebar .nav-link {
            font-size: 0.97rem;
            padding: 0.6rem 0.8rem;
        }
        .sidebar-heading {
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    }
</style>

<script>
    // تفعيل البحث في القائمة الجانبية
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const navLinks = document.querySelectorAll('.sidebar-menu .nav-link');
                
                navLinks.forEach(link => {
                    const text = link.textContent.toLowerCase();
                    const parent = link.closest('.nav-item');
                    
                    if (text.includes(searchTerm)) {
                        parent.style.display = '';
                    } else {
                        parent.style.display = 'none';
                    }
                });
                
                // إظهار/إخفاء العناوين حسب توفر العناصر
                const sections = document.querySelectorAll('.sidebar-menu .sidebar-heading');
                sections.forEach(section => {
                    const nextUl = section.nextElementSibling;
                    const hasVisibleItems = nextUl && 
                        Array.from(nextUl.querySelectorAll('.nav-item'))
                            .some(item => item.style.display !== 'none');
                    
                    section.style.display = hasVisibleItems ? '' : 'none';
                });
            });
        }
    });
</script>
