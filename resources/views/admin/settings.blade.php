@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إعدادات النظام</li>
@endsection

@section('content')
<div class="container-fluid" dusk="settings-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إعدادات النظام</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">تعديل الإعدادات العامة</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="mb-0">
                @csrf
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <h5 class="mb-3">واجهة المستخدم</h5>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="multilingual" name="multilingual" value="1" {{ $settings['multilingual'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="multilingual">
                                دعم تعدد اللغات
                                <small class="d-block text-muted">تفعيل دعم اللغات المتعددة في النظام</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" value="1" {{ $settings['dark_mode'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="dark_mode">
                                الوضع الداكن
                                <small class="d-block text-muted">السماح للمستخدمين باستخدام الوضع الداكن</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="enhanced_ui" name="enhanced_ui" value="1" {{ $settings['enhanced_ui'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="enhanced_ui">
                                واجهة مستخدم محسنة
                                <small class="d-block text-muted">تفعيل الرسوم المتحركة والمؤثرات البصرية المتقدمة</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <h5 class="mb-3">المدفوعات والميزات المتقدمة</h5>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="payment_system" name="payment_system" value="1" {{ $settings['payment_system'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_system">
                                نظام الدفع
                                <small class="d-block text-muted">تفعيل معالجة المدفوعات داخل النظام</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="ai_features" name="ai_features" value="1" {{ $settings['ai_features'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="ai_features">
                                ميزات الذكاء الاصطناعي
                                <small class="d-block text-muted">تفعيل الذكاء الاصطناعي لتحسين تجربة المستخدم</small>
                            </label>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3">إعدادات الفوتر</h5>
                <div class="mb-3">
                    <label for="footer_text" class="form-label">نص الفوتر</label>
                    <input type="text" class="form-control" id="footer_text" name="footer_text" value="{{ config('ui.footer.text', '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">روابط الفوتر</label>
                    <div id="footer-links-list">
                        @php $footerLinks = config('ui.footer.links', []); @endphp
                        @foreach($footerLinks as $i => $link)
                        <div class="input-group mb-2 footer-link-row">
                            <input type="text" name="footer_link_texts[]" class="form-control" placeholder="النص" value="{{ $link['text'] }}">
                            <input type="text" name="footer_link_urls[]" class="form-control" placeholder="الرابط" value="{{ $link['url'] }}">
                            <button type="button" class="btn btn-danger remove-footer-link">-</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-footer-link">إضافة رابط</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">روابط الخدمات بالفوتر</label>
                    <div id="footer-service-links-list">
                        @php $footerServiceLinks = config('ui.footer.services', []); @endphp
                        @foreach($footerServiceLinks as $i => $link)
                        <div class="input-group mb-2 footer-service-link-row">
                            <input type="text" name="footer_service_link_texts[]" class="form-control" placeholder="النص" value="{{ $link['text'] }}">
                            <input type="text" name="footer_service_link_urls[]" class="form-control" placeholder="الرابط" value="{{ $link['url'] }}">
                            <button type="button" class="btn btn-danger remove-footer-service-link">-</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-footer-service-link">إضافة رابط خدمة</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">روابط التواصل الاجتماعي</label>
                    <div id="footer-social-list">
                        @php $footerSocial = config('ui.footer.social', []); @endphp
                        @foreach($footerSocial as $i => $social)
                        <div class="input-group mb-2 footer-social-row">
                            <input type="text" name="footer_social_names[]" class="form-control" placeholder="اسم الشبكة" value="{{ $social['name'] }}">
                            <input type="text" name="footer_social_urls[]" class="form-control" placeholder="الرابط" value="{{ $social['url'] }}">
                            <input type="text" name="footer_social_icons[]" class="form-control" placeholder="الأيقونة (مثال: facebook)" value="{{ $social['icon'] }}">
                            <button type="button" class="btn btn-danger remove-footer-social">-</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-footer-social">إضافة شبكة</button>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('add-footer-link').onclick = function() {
                        const row = document.createElement('div');
                        row.className = 'input-group mb-2 footer-link-row';
                        row.innerHTML = `<input type="text" name="footer_link_texts[]" class="form-control" placeholder="النص"><input type="text" name="footer_link_urls[]" class="form-control" placeholder="الرابط"><button type="button" class="btn btn-danger remove-footer-link">-</button>`;
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
                        row.innerHTML = `<input type="text" name="footer_service_link_texts[]" class="form-control" placeholder="النص"><input type="text" name="footer_service_link_urls[]" class="form-control" placeholder="الرابط"><button type="button" class="btn btn-danger remove-footer-service-link">-</button>`;
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
                        row.innerHTML = `<input type="text" name="footer_social_names[]" class="form-control" placeholder="اسم الشبكة"><input type="text" name="footer_social_urls[]" class="form-control" placeholder="الرابط"><input type="text" name="footer_social_icons[]" class="form-control" placeholder="الأيقونة (مثال: facebook)"><button type="button" class="btn btn-danger remove-footer-social">-</button>`;
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
                                <td>{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <th>إصدار Laravel:</th>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <th>نوع قاعدة البيانات:</th>
                                <td>{{ config('database.default') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-lg-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>مساحة التخزين المستخدمة:</th>
                                <td>{{ round(disk_total_space(storage_path()) / 1024 / 1024, 2) }} MB</td>
                            </tr>
                            <tr>
                                <th>مساحة التخزين الحرة:</th>
                                <td>{{ round(disk_free_space(storage_path()) / 1024 / 1024, 2) }} MB</td>
                            </tr>
                            <tr>
                                <th>الذاكرة المستخدمة:</th>
                                <td>{{ round(memory_get_usage() / 1024 / 1024, 2) }} MB</td>
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
@endsection