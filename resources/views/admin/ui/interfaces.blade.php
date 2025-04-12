@extends('layouts.app')

@section('title', 'إدارة واجهات التطبيق')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة واجهات التطبيق</li>
@endsection

@section('styles')
<style>
    .card-header-tabs .nav-link {
        border-radius: 0;
        margin-right: 5px;
        color: #555;
    }
    .card-header-tabs .nav-link.active {
        font-weight: bold;
        border-bottom: 2px solid #3b82f6;
    }
    .add-new-item {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        border: 1px dashed #ddd;
        margin-top: 20px;
    }
    .banner-item, .alert-item {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
    }
    .banner-preview {
        max-height: 100px;
        border: 1px solid #eee;
        padding: 5px;
        margin: 10px 0;
    }
    .page-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    .nav-item-row {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 10px 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .footer-section {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
    }
    .social-item, .link-item {
        background: #ffffff;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #eee;
    }
    .delete-item {
        color: #dc3545;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-xl font-bold">إدارة واجهات التطبيق</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <div class="card-header bg-light p-0">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#navigation-tab" 
                            type="button" role="tab" aria-selected="true">التنقل (Navigation)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pages-tab" 
                            type="button" role="tab" aria-selected="false">الصفحات</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#banners-tab" 
                            type="button" role="tab" aria-selected="false">البانرات</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alerts-tab" 
                            type="button" role="tab" aria-selected="false">التنبيهات</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#footer-tab" 
                            type="button" role="tab" aria-selected="false">التذييل (Footer)</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ui.interfaces.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="tab-content">
                    <!-- قسم التنقل -->
                    <div class="tab-pane fade show active" id="navigation-tab" role="tabpanel">
                        <h4 class="mb-3">إدارة قائمة التنقل</h4>
                        <p class="text-muted mb-4">قم بتخصيص القوائم الرئيسية للموقع وتنظيمها</p>
                        
                        <div id="nav-items-container">
                            @foreach($navigation ?? [] as $index => $navItem)
                            <div class="nav-item-row">
                                <div class="row mb-2">
                                    <div class="col-lg-4 mb-2">
                                        <label class="form-label">العنوان</label>
                                        <input type="text" name="navigation[{{ $index }}][title]" 
                                               class="form-control" value="{{ $navItem['title'] ?? '' }}" required>
                                    </div>
                                    <div class="col-lg-4 mb-2">
                                        <label class="form-label">الرابط</label>
                                        <input type="text" name="navigation[{{ $index }}][url]" 
                                               class="form-control" value="{{ $navItem['url'] ?? '' }}" required>
                                    </div>
                                    <div class="col-lg-2 mb-2">
                                        <label class="form-label">الأيقونة</label>
                                        <input type="text" name="navigation[{{ $index }}][icon]" 
                                               class="form-control" value="{{ $navItem['icon'] ?? '' }}" 
                                               placeholder="fa-home">
                                    </div>
                                    <div class="col-lg-2 mb-2 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="navigation[{{ $index }}][active]" 
                                                   id="navItem{{ $index }}" 
                                                   {{ ($navItem['active'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="navItem{{ $index }}">تفعيل</label>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-nav-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- القائمة الفرعية -->
                                <div class="submenu-container ms-4 mt-2">
                                    <label class="form-label">القائمة الفرعية</label>
                                    <div class="submenu-items">
                                        @if(isset($navItem['submenu']) && is_array($navItem['submenu']))
                                            @foreach($navItem['submenu'] as $subIndex => $subItem)
                                            <div class="row mb-2 submenu-item">
                                                <div class="col-lg-5">
                                                    <input type="text" name="navigation[{{ $index }}][submenu][{{ $subIndex }}][title]" 
                                                           class="form-control" value="{{ $subItem['title'] ?? '' }}" 
                                                           placeholder="العنوان" required>
                                                </div>
                                                <div class="col-lg-5">
                                                    <input type="text" name="navigation[{{ $index }}][submenu][{{ $subIndex }}][url]" 
                                                           class="form-control" value="{{ $subItem['url'] ?? '' }}" 
                                                           placeholder="الرابط" required>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-submenu-item">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary add-submenu-item mt-2">
                                        <i class="fas fa-plus"></i> إضافة عنصر فرعي
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary mt-3" id="add-nav-item">
                            <i class="fas fa-plus"></i> إضافة عنصر قائمة جديد
                        </button>
                    </div>
                    
                    <!-- قسم الصفحات -->
                    <div class="tab-pane fade" id="pages-tab" role="tabpanel">
                        <h4 class="mb-3">إدارة الصفحات</h4>
                        <p class="text-muted mb-4">قم بتخصيص محتوى الصفحات الساكنة في الموقع</p>
                        
                        <!-- الصفحات الحالية -->
                        <div class="existing-pages mb-4">
                            <h5 class="mb-3">الصفحات الحالية</h5>
                            @foreach($pages ?? [] as $slug => $page)
                            <div class="page-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">عنوان الصفحة</label>
                                            <input type="text" name="page_updates[{{ $slug }}][title]" 
                                                   class="form-control" value="{{ $page['title'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">مُعرّف الصفحة (Slug)</label>
                                            <input type="text" class="form-control" value="{{ $slug }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="page_updates[{{ $slug }}][active]" 
                                                   id="page{{ $slug }}" 
                                                   {{ ($page['active'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="page{{ $slug }}">تفعيل</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">محتوى الصفحة</label>
                                    <textarea name="page_updates[{{ $slug }}][content]" 
                                              class="form-control editor" rows="4">{{ $page['content'] ?? '' }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- إضافة صفحة جديدة -->
                        <div class="add-new-item">
                            <h5 class="mb-3">إضافة صفحة جديدة</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">عنوان الصفحة</label>
                                        <input type="text" name="new_page_title" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">مُعرّف الصفحة (Slug)</label>
                                        <input type="text" name="new_page_slug" class="form-control" 
                                               placeholder="about-us">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">محتوى الصفحة</label>
                                <textarea name="new_page_content" class="form-control editor" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- قسم البانرات -->
                    <div class="tab-pane fade" id="banners-tab" role="tabpanel">
                        <h4 class="mb-3">إدارة البانرات</h4>
                        <p class="text-muted mb-4">قم بتعديل البانرات التي تظهر في جميع أنحاء التطبيق</p>
                        
                        <div id="banners-container">
                            @foreach($banners ?? [] as $index => $banner)
                            <div class="banner-item">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">العنوان</label>
                                            <input type="text" name="banner_titles[]" 
                                                   class="form-control" value="{{ $banner['title'] ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="banner_active[]" 
                                                   id="banner{{ $index }}" 
                                                   {{ ($banner['active'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="banner{{ $index }}">تفعيل</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">المحتوى</label>
                                    <textarea name="banner_contents[]" class="form-control" rows="2">{{ $banner['content'] ?? '' }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">الصورة</label>
                                    <input type="file" name="banner_images[]" class="form-control" accept="image/*">
                                    @if(!empty($banner['image']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $banner['image']) }}" 
                                                 alt="{{ $banner['title'] ?? 'Banner' }}" class="banner-preview">
                                            <input type="hidden" name="banner_existing_images[]" value="{{ $banner['image'] }}">
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-banner">
                                        <i class="fas fa-trash"></i> حذف البانر
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary mt-3" id="add-banner">
                            <i class="fas fa-plus"></i> إضافة بانر جديد
                        </button>
                    </div>
                    
                    <!-- قسم التنبيهات -->
                    <div class="tab-pane fade" id="alerts-tab" role="tabpanel">
                        <h4 class="mb-3">إدارة التنبيهات</h4>
                        <p class="text-muted mb-4">قم بإنشاء وتعديل التنبيهات التي تظهر للمستخدمين</p>
                        
                        <div id="alerts-container">
                            @foreach($alerts ?? [] as $index => $alert)
                            <div class="alert-item">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">نص التنبيه</label>
                                            <textarea name="alert_messages[]" class="form-control" rows="2" required>{{ $alert['message'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">نوع التنبيه</label>
                                            <select name="alert_types[]" class="form-select">
                                                <option value="info" {{ ($alert['type'] ?? '') == 'info' ? 'selected' : '' }}>معلومات</option>
                                                <option value="success" {{ ($alert['type'] ?? '') == 'success' ? 'selected' : '' }}>نجاح</option>
                                                <option value="warning" {{ ($alert['type'] ?? '') == 'warning' ? 'selected' : '' }}>تحذير</option>
                                                <option value="danger" {{ ($alert['type'] ?? '') == 'danger' ? 'selected' : '' }}>خطر</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">تاريخ انتهاء التنبيه (اختياري)</label>
                                            <input type="date" name="alert_expiry[]" class="form-control" 
                                                   value="{{ isset($alert['expiry']) ? date('Y-m-d', strtotime($alert['expiry'])) : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="alert_active[]" 
                                                   id="alert{{ $index }}" 
                                                   {{ ($alert['active'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="alert{{ $index }}">تفعيل</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-alert">
                                        <i class="fas fa-trash"></i> حذف التنبيه
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary mt-3" id="add-alert">
                            <i class="fas fa-plus"></i> إضافة تنبيه جديد
                        </button>
                    </div>
                    
                    <!-- قسم التذييل -->
                    <div class="tab-pane fade" id="footer-tab" role="tabpanel">
                        <h4 class="mb-3">إدارة التذييل (Footer)</h4>
                        <p class="text-muted mb-4">قم بتخصيص معلومات وروابط تذييل الموقع</p>
                        
                        <div class="footer-section mb-4">
                            <h5 class="mb-3">النص الرئيسي</h5>
                            <div class="mb-3">
                                <textarea name="footer_text" class="form-control" rows="3">{{ $footer['text'] ?? '' }}</textarea>
                                <small class="text-muted">يمكنك استخدام وسوم HTML الأساسية</small>
                            </div>
                        </div>
                        
                        <div class="footer-section mb-4">
                            <h5 class="mb-3">روابط مفيدة</h5>
                            <div id="footer-links-container">
                                @foreach($footer['links'] ?? [] as $index => $link)
                                <div class="link-item">
                                    <div class="row">
                                        <div class="col-md-5 mb-2">
                                            <label class="form-label">عنوان الرابط</label>
                                            <input type="text" name="footer_link_texts[]" 
                                                   class="form-control" value="{{ $link['text'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">الرابط</label>
                                            <input type="text" name="footer_link_urls[]" 
                                                   class="form-control" value="{{ $link['url'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-1 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-link">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="add-footer-link">
                                <i class="fas fa-plus"></i> إضافة رابط
                            </button>
                        </div>
                        
                        <div class="footer-section">
                            <h5 class="mb-3">روابط التواصل الاجتماعي</h5>
                            <div id="social-links-container">
                                @foreach($footer['social'] ?? [] as $index => $social)
                                <div class="social-item">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">اسم المنصة</label>
                                            <input type="text" name="footer_social_names[]" 
                                                   class="form-control" value="{{ $social['name'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <label class="form-label">الرابط</label>
                                            <input type="text" name="footer_social_urls[]" 
                                                   class="form-control" value="{{ $social['url'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">الأيقونة</label>
                                            <input type="text" name="footer_social_icons[]" 
                                                   class="form-control" value="{{ $social['icon'] ?? '' }}" 
                                                   placeholder="twitter">
                                            <small class="text-muted">اسم أيقونة من Font Awesome</small>
                                        </div>
                                        <div class="col-md-1 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-social">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="add-social-link">
                                <i class="fas fa-plus"></i> إضافة منصة تواصل اجتماعي
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.2.0/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة محرر النصوص المتقدم
    tinymce.init({
        selector: '.editor',
        directionality: 'rtl',
        plugins: 'anchor autolink charmap image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | removeformat',
        height: 300
    });
    
    // إضافة عنصر قائمة جديد
    document.getElementById('add-nav-item').addEventListener('click', function() {
        const container = document.getElementById('nav-items-container');
        const index = container.children.length;
        
        const navItemTemplate = `
            <div class="nav-item-row">
                <div class="row mb-2">
                    <div class="col-lg-4 mb-2">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="navigation[${index}][title]" class="form-control" required>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <label class="form-label">الرابط</label>
                        <input type="text" name="navigation[${index}][url]" class="form-control" required>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <label class="form-label">الأيقونة</label>
                        <input type="text" name="navigation[${index}][icon]" class="form-control" placeholder="fa-home">
                    </div>
                    <div class="col-lg-2 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="navigation[${index}][active]" id="navItem${index}" checked>
                            <label class="form-check-label" for="navItem${index}">تفعيل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-nav-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="submenu-container ms-4 mt-2">
                    <label class="form-label">القائمة الفرعية</label>
                    <div class="submenu-items"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary add-submenu-item mt-2">
                        <i class="fas fa-plus"></i> إضافة عنصر فرعي
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', navItemTemplate);
        setupEventListeners();
    });
    
    // إضافة بانر جديد
    document.getElementById('add-banner').addEventListener('click', function() {
        const container = document.getElementById('banners-container');
        const index = container.children.length;
        
        const bannerTemplate = `
            <div class="banner-item">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="banner_titles[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="banner_active[]" id="banner${index}" checked>
                            <label class="form-check-label" for="banner${index}">تفعيل</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">المحتوى</label>
                    <textarea name="banner_contents[]" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="banner_images[]" class="form-control" accept="image/*">
                </div>
                
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-banner">
                        <i class="fas fa-trash"></i> حذف البانر
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', bannerTemplate);
        setupEventListeners();
    });
    
    // إضافة تنبيه جديد
    document.getElementById('add-alert').addEventListener('click', function() {
        const container = document.getElementById('alerts-container');
        const index = container.children.length;
        
        const alertTemplate = `
            <div class="alert-item">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">نص التنبيه</label>
                            <textarea name="alert_messages[]" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">نوع التنبيه</label>
                            <select name="alert_types[]" class="form-select">
                                <option value="info">معلومات</option>
                                <option value="success">نجاح</option>
                                <option value="warning">تحذير</option>
                                <option value="danger">خطر</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">تاريخ انتهاء التنبيه (اختياري)</label>
                            <input type="date" name="alert_expiry[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="alert_active[]" id="alert${index}" checked>
                            <label class="form-check-label" for="alert${index}">تفعيل</label>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-alert">
                        <i class="fas fa-trash"></i> حذف التنبيه
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', alertTemplate);
        setupEventListeners();
    });
    
    // إضافة رابط في التذييل
    document.getElementById('add-footer-link').addEventListener('click', function() {
        const container = document.getElementById('footer-links-container');
        
        const linkTemplate = `
            <div class="link-item">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="form-label">عنوان الرابط</label>
                        <input type="text" name="footer_link_texts[]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">الرابط</label>
                        <input type="text" name="footer_link_urls[]" class="form-control" required>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-link">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', linkTemplate);
        setupEventListeners();
    });
    
    // إضافة منصة تواصل اجتماعي
    document.getElementById('add-social-link').addEventListener('click', function() {
        const container = document.getElementById('social-links-container');
        
        const socialTemplate = `
            <div class="social-item">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">اسم المنصة</label>
                        <input type="text" name="footer_social_names[]" class="form-control" required>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="form-label">الرابط</label>
                        <input type="text" name="footer_social_urls[]" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">الأيقونة</label>
                        <input type="text" name="footer_social_icons[]" class="form-control" placeholder="twitter">
                        <small class="text-muted">اسم أيقونة من Font Awesome</small>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-social">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', socialTemplate);
        setupEventListeners();
    });
    
    // إعداد أحداث إضافة وحذف العناصر الفرعية
    function setupEventListeners() {
        // أحداث إضافة عناصر فرعية للقوائم
        document.querySelectorAll('.add-submenu-item').forEach(button => {
            button.addEventListener('click', function(e) {
                const submenuContainer = this.previousElementSibling;
                const parentNavItem = this.closest('.nav-item-row');
                const navIndex = Array.from(document.querySelectorAll('.nav-item-row')).indexOf(parentNavItem);
                const subIndex = submenuContainer.children.length;
                
                const submenuItemTemplate = `
                    <div class="row mb-2 submenu-item">
                        <div class="col-lg-5">
                            <input type="text" name="navigation[${navIndex}][submenu][${subIndex}][title]" 
                                   class="form-control" placeholder="العنوان" required>
                        </div>
                        <div class="col-lg-5">
                            <input type="text" name="navigation[${navIndex}][submenu][${subIndex}][url]" 
                                   class="form-control" placeholder="الرابط" required>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-submenu-item">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                submenuContainer.insertAdjacentHTML('beforeend', submenuItemTemplate);
                setupEventListeners();
            });
        });
        
        // أحداث حذف العناصر
        document.querySelectorAll('.remove-nav-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.nav-item-row').remove();
            });
        });
        
        document.querySelectorAll('.remove-submenu-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.submenu-item').remove();
            });
        });
        
        document.querySelectorAll('.remove-banner').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.banner-item').remove();
            });
        });
        
        document.querySelectorAll('.remove-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.alert-item').remove();
            });
        });
        
        document.querySelectorAll('.remove-link').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.link-item').remove();
            });
        });
        
        document.querySelectorAll('.remove-social').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.social-item').remove();
            });
        });
    }
    
    // تهيئة أحداث الحذف والإضافة
    setupEventListeners();
});
</script>
@endpush