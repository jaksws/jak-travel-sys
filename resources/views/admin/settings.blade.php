@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إعدادات النظام</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">إعدادات النظام</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                
                <!-- إعدادات تعدد اللغات -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إعدادات تعدد اللغات</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="multilingual_enabled" 
                                           name="multilingual[enabled]" value="1" 
                                           {{ $settings['multilingual']['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="multilingual_enabled">تفعيل دعم تعدد اللغات</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">اللغة الافتراضية</label>
                            <div class="col-md-9">
                                <select name="multilingual[default_locale]" class="form-control">
                                    <option value="ar" {{ $settings['multilingual']['default_locale'] == 'ar' ? 'selected' : '' }}>العربية</option>
                                    <option value="en" {{ $settings['multilingual']['default_locale'] == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ $settings['multilingual']['default_locale'] == 'fr' ? 'selected' : '' }}>Français</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">اللغات المتاحة</label>
                            <div class="col-md-9">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="locale_ar" 
                                           name="multilingual[available_locales][]" value="ar"
                                           {{ in_array('ar', $settings['multilingual']['available_locales'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="locale_ar">العربية</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="locale_en" 
                                           name="multilingual[available_locales][]" value="en" 
                                           {{ in_array('en', $settings['multilingual']['available_locales'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="locale_en">English</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="locale_fr" 
                                           name="multilingual[available_locales][]" value="fr" 
                                           {{ in_array('fr', $settings['multilingual']['available_locales'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="locale_fr">Français</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الوضع المظلم -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إعدادات الوضع المظلم</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="dark_mode_enabled" 
                                           name="dark_mode[enabled]" value="1" 
                                           {{ $settings['dark_mode']['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="dark_mode_enabled">تفعيل الوضع المظلم</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">الوضع الافتراضي</label>
                            <div class="col-md-9">
                                <select name="dark_mode[default]" class="form-control">
                                    <option value="light" {{ $settings['dark_mode']['default'] == 'light' ? 'selected' : '' }}>الوضع الفاتح</option>
                                    <option value="dark" {{ $settings['dark_mode']['default'] == 'dark' ? 'selected' : '' }}>الوضع المظلم</option>
                                    <option value="auto" {{ $settings['dark_mode']['default'] == 'auto' ? 'selected' : '' }}>تلقائي (حسب إعدادات النظام)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات نظام الدفع -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إعدادات نظام الدفع</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="payment_system_enabled" 
                                           name="payment_system[enabled]" value="1" 
                                           {{ $settings['payment_system']['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="payment_system_enabled">تفعيل نظام الدفع</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">بوابات الدفع المتاحة</label>
                            <div class="col-md-9">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="payment_stripe" 
                                           name="payment_system[providers][]" value="stripe" 
                                           {{ in_array('stripe', $settings['payment_system']['providers'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="payment_stripe">Stripe</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="payment_paypal" 
                                           name="payment_system[providers][]" value="paypal" 
                                           {{ in_array('paypal', $settings['payment_system']['providers'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="payment_paypal">PayPal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="payment_bank" 
                                           name="payment_system[providers][]" value="bank_transfer" 
                                           {{ in_array('bank_transfer', $settings['payment_system']['providers'] ?? []) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="payment_bank">التحويل البنكي</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات واجهة المستخدم المحسنة -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إعدادات واجهة المستخدم المحسنة</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="enhanced_ui_enabled" 
                                           name="enhanced_ui[enabled]" value="1" 
                                           {{ $settings['enhanced_ui']['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="enhanced_ui_enabled">تفعيل واجهة المستخدم المحسنة</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات ميزات الذكاء الاصطناعي -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">إعدادات ميزات الذكاء الاصطناعي</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="ai_features_enabled" 
                                           name="ai_features[enabled]" value="1" 
                                           {{ $settings['ai_features']['enabled'] ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ai_features_enabled">تفعيل ميزات الذكاء الاصطناعي</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i> حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection