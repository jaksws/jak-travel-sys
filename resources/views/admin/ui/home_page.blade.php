@extends('layouts.app')

@section('title', 'إدارة الصفحة الرئيسية')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
    <li class="breadcrumb-item active">إدارة الصفحة الرئيسية</li>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" rel="stylesheet" />
<style>
    .section-item {
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 10px;
        padding: 15px;
        cursor: move;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }
    .section-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .section-item.inactive {
        opacity: 0.6;
        background: #f1f1f1;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .color-picker-wrapper {
        position: relative;
        height: 40px;
    }
    .color-picker {
        width: 100%;
        height: 40px;
        cursor: pointer;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .font-preview {
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .logo-preview {
        max-height: 100px;
        max-width: 100%;
        display: block;
        margin: 10px 0;
        border: 1px solid #eee;
        padding: 5px;
    }
    .sortable-ghost {
        opacity: 0.4;
        background: #f0f8ff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-xl font-bold">إدارة الصفحة الرئيسية</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 font-weight-bold">التحكم بالواجهة الرئيسية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ui.home.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- أقسام الصفحة الرئيسية -->
                <div class="mb-4">
                    <h5 class="mb-3">أقسام الصفحة الرئيسية</h5>
                    <p class="text-muted">اسحب الأقسام لإعادة ترتيبها. يمكنك أيضاً تفعيل أو تعطيل أي قسم.</p>
                    
                    <input type="hidden" name="section_order" id="sectionOrder">
                    
                    <div id="sectionsList" class="mb-3">
                        @foreach($homePageSections as $id => $section)
                            <div class="section-item {{ $section['active'] ? '' : 'inactive' }}" data-id="{{ $id }}">
                                <div class="section-header">
                                    <h6 class="mb-0">{{ $section['title'] }}</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               name="sections[{{ $id }}][active]" 
                                               id="section{{ $id }}" 
                                               {{ $section['active'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="section{{ $id }}">تفعيل</label>
                                    </div>
                                </div>
                                <p class="text-muted mb-0 small">{{ $section['description'] ?? '' }}</p>
                                
                                @if(isset($section['settings']) && count($section['settings']) > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        @foreach($section['settings'] as $settingKey => $setting)
                                            <div class="mb-2">
                                                <label class="form-label">{{ $setting['title'] }}</label>
                                                @if($setting['type'] == 'text')
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="sections[{{ $id }}][settings][{{ $settingKey }}]"
                                                           value="{{ $setting['value'] ?? '' }}">
                                                @elseif($setting['type'] == 'textarea')
                                                    <textarea class="form-control" 
                                                              name="sections[{{ $id }}][settings][{{ $settingKey }}]"
                                                              rows="3">{{ $setting['value'] ?? '' }}</textarea>
                                                @elseif($setting['type'] == 'select')
                                                    <select class="form-select" 
                                                            name="sections[{{ $id }}][settings][{{ $settingKey }}]">
                                                        @foreach($setting['options'] as $optValue => $optLabel)
                                                            <option value="{{ $optValue }}" 
                                                                    {{ $setting['value'] == $optValue ? 'selected' : '' }}>
                                                                {{ $optLabel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row mb-4">
                    <!-- الألوان -->
                    <div class="col-md-6 mb-4">
                        <h5 class="mb-3">تخصيص الألوان</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">اللون الرئيسي</label>
                            <div class="color-picker-wrapper">
                                <div id="primaryColorPicker" class="color-picker" 
                                     style="background-color: {{ $colors['primary'] ?? '#3b82f6' }}"></div>
                                <input type="hidden" name="primary_color" 
                                       id="primaryColor" value="{{ $colors['primary'] ?? '#3b82f6' }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">اللون الثانوي</label>
                            <div class="color-picker-wrapper">
                                <div id="secondaryColorPicker" class="color-picker" 
                                     style="background-color: {{ $colors['secondary'] ?? '#64748b' }}"></div>
                                <input type="hidden" name="secondary_color" 
                                       id="secondaryColor" value="{{ $colors['secondary'] ?? '#64748b' }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">لون التمييز (Accent)</label>
                            <div class="color-picker-wrapper">
                                <div id="accentColorPicker" class="color-picker" 
                                     style="background-color: {{ $colors['accent'] ?? '#10b981' }}"></div>
                                <input type="hidden" name="accent_color" 
                                       id="accentColor" value="{{ $colors['accent'] ?? '#10b981' }}">
                            </div>
                        </div>
                        
                        <div class="font-preview p-3 mt-3" id="colorPreview">
                            <h4 style="color: {{ $colors['primary'] ?? '#3b82f6' }}">معاينة اللون الرئيسي</h4>
                            <p style="color: {{ $colors['secondary'] ?? '#64748b' }}">هذا النص بلون ثانوي للمعاينة</p>
                            <button class="btn btn-sm" style="background-color: {{ $colors['accent'] ?? '#10b981' }}; color: white;">
                                زر بلون التمييز
                            </button>
                        </div>
                    </div>
                    
                    <!-- الخطوط -->
                    <div class="col-md-6 mb-4">
                        <h5 class="mb-3">تخصيص الخطوط</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">الخط الرئيسي</label>
                            <select class="form-select" name="font_primary" id="fontPrimary">
                                <option value="Cairo" {{ ($fonts['primary'] ?? '') == 'Cairo' ? 'selected' : '' }}>Cairo</option>
                                <option value="Tajawal" {{ ($fonts['primary'] ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal</option>
                                <option value="Almarai" {{ ($fonts['primary'] ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai</option>
                                <option value="Changa" {{ ($fonts['primary'] ?? '') == 'Changa' ? 'selected' : '' }}>Changa</option>
                                <option value="IBM Plex Sans Arabic" {{ ($fonts['primary'] ?? '') == 'IBM Plex Sans Arabic' ? 'selected' : '' }}>IBM Plex Sans Arabic</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">الخط الثانوي</label>
                            <select class="form-select" name="font_secondary" id="fontSecondary">
                                <option value="Cairo" {{ ($fonts['secondary'] ?? '') == 'Cairo' ? 'selected' : '' }}>Cairo</option>
                                <option value="Tajawal" {{ ($fonts['secondary'] ?? '') == 'Tajawal' ? 'selected' : '' }}>Tajawal</option>
                                <option value="Almarai" {{ ($fonts['secondary'] ?? '') == 'Almarai' ? 'selected' : '' }}>Almarai</option>
                                <option value="Changa" {{ ($fonts['secondary'] ?? '') == 'Changa' ? 'selected' : '' }}>Changa</option>
                                <option value="IBM Plex Sans Arabic" {{ ($fonts['secondary'] ?? '') == 'IBM Plex Sans Arabic' ? 'selected' : '' }}>IBM Plex Sans Arabic</option>
                            </select>
                        </div>
                        
                        <div class="font-preview mt-3" id="fontPreview">
                            <h4>معاينة الخط الرئيسي</h4>
                            <p>هذا نص للمعاينة بالخط الثانوي.</p>
                            <p>تطبيق جاك للسفريات - هذا النص للمعاينة فقط 123456789</p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- الشعارات -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">الشعارات</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الشعار الرئيسي</label>
                                <input type="file" name="main_logo" class="form-control" accept="image/*">
                                @if(!empty($logoSettings['main']))
                                    <img src="{{ asset('storage/' . $logoSettings['main']) }}" 
                                         alt="الشعار الرئيسي" class="logo-preview">
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الشعار المصغر</label>
                                <input type="file" name="small_logo" class="form-control" accept="image/*">
                                @if(!empty($logoSettings['small']))
                                    <img src="{{ asset('storage/' . $logoSettings['small']) }}" 
                                         alt="الشعار المصغر" class="logo-preview">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة السحب والإفلات للأقسام
    const sectionsList = document.getElementById('sectionsList');
    const sortable = new Sortable(sectionsList, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onSort: function() {
            updateSectionOrder();
        }
    });
    
    // تحديث ترتيب الأقسام
    function updateSectionOrder() {
        const sections = document.querySelectorAll('.section-item');
        const order = Array.from(sections).map(section => section.dataset.id);
        document.getElementById('sectionOrder').value = order.join(',');
    }
    
    // تنفيذ التحديث الأولي للترتيب
    updateSectionOrder();
    
    // إعداد منتقي الألوان
    const createColorPicker = function(element, input, previewElement, property) {
        return new Pickr({
            el: element,
            theme: 'classic',
            default: input.value,
            components: {
                preview: true,
                opacity: true,
                hue: true,
                interaction: {
                    input: true,
                    save: true
                }
            }
        }).on('save', (color) => {
            const hex = color.toHEXA().toString();
            input.value = hex;
            element.style.backgroundColor = hex;
            
            if (previewElement && property) {
                previewElement.style[property] = hex;
            }
        });
    };
    
    // تهيئة منتقيات الألوان
    const primaryColorPicker = createColorPicker(
        document.getElementById('primaryColorPicker'),
        document.getElementById('primaryColor'),
        document.querySelector('#colorPreview h4'),
        'color'
    );
    
    const secondaryColorPicker = createColorPicker(
        document.getElementById('secondaryColorPicker'),
        document.getElementById('secondaryColor'),
        document.querySelector('#colorPreview p'),
        'color'
    );
    
    const accentColorPicker = createColorPicker(
        document.getElementById('accentColorPicker'),
        document.getElementById('accentColor'),
        document.querySelector('#colorPreview button'),
        'backgroundColor'
    );
    
    // تحديث معاينة الخطوط
    const updateFontPreview = function() {
        const primaryFont = document.getElementById('fontPrimary').value;
        const secondaryFont = document.getElementById('fontSecondary').value;
        
        document.querySelector('#fontPreview h4').style.fontFamily = primaryFont;
        document.querySelector('#fontPreview p').style.fontFamily = secondaryFont;
    };
    
    document.getElementById('fontPrimary').addEventListener('change', updateFontPreview);
    document.getElementById('fontSecondary').addEventListener('change', updateFontPreview);
    
    // تحديث معاينة الخطوط عند التحميل
    updateFontPreview();
});
</script>
@endpush