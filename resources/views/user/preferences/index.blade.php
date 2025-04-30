@extends('layouts.app')

@section('title', __('v2.profile_settings'))

@section('content')
<div class="container-fluid px-0 px-md-3 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow rounded-lg border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-cog fa-lg me-2"></i>
                    <h4 class="mb-0">{{ __('v2.profile_settings') }}</h4>
                </div>
                <div class="card-body p-4">
                    <form id="preferences-form" action="{{ route('user.preferences.save') }}" method="POST">
                        @csrf
                        <ul class="nav nav-tabs mb-4" id="settings-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab" aria-controls="appearance" aria-selected="true">
                                    <i class="fas fa-palette me-1"></i> {{ __('v2.appearance_settings') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                    <i class="fas fa-bell me-1"></i> {{ __('v2.notification_settings') }}
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="settings-content">
                            <div class="tab-pane fade show active" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                                <div class="mb-4">
                                    <label for="locale" class="form-label fw-bold">
                                        <i class="fas fa-language me-1"></i> {{ __('v2.language') }}
                                    </label>
                                    <select id="locale" name="locale" class="form-select">
                                        @foreach((array) (config('v1_features.multilingual.available_locales') ?? ['ar','en']) as $locale)
                                            <option value="{{ $locale }}" {{ $currentLocale == $locale ? 'selected' : '' }}>
                                                @switch($locale)
                                                    @case('ar') العربية @break
                                                    @case('en') English @break
                                                    @case('fr') Français @break
                                                    @case('tr') Türkçe @break
                                                    @case('es') Español @break
                                                    @case('id') Bahasa Indonesia @break
                                                    @case('ur') اردو @break
                                                    @default {{ $locale }}
                                                @endswitch
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="theme" class="form-label fw-bold">
                                        <i class="fas fa-moon me-1"></i> {{ __('v2.theme') }}
                                    </label>
                                    <select id="theme-selector" name="theme" class="form-select">
                                        <option value="light" {{ ($currentTheme ?? 'system') == 'light' ? 'selected' : '' }}>{{ __('v2.light_mode') }}</option>
                                        <option value="dark" {{ ($currentTheme ?? 'system') == 'dark' ? 'selected' : '' }}>{{ __('v2.dark_mode') }}</option>
                                        <option value="system" {{ ($currentTheme ?? 'system') == 'system' ? 'selected' : '' }}>{{ __('v2.system_mode') }}</option>
                                    </select>
                                </div>
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="dark-mode-toggle" {{ ($currentTheme ?? 'system') == 'dark' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dark-mode-toggle">
                                        <i class="fas fa-adjust me-1"></i> {{ __('v2.toggle_dark_mode') }}
                                    </label>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input id="email_notifications" name="email_notifications" type="checkbox" class="form-check-input" {{ isset($preferences['email_notifications']) && $preferences['email_notifications'] ? 'checked' : '' }}>
                                        <label for="email_notifications" class="form-check-label fw-bold">
                                            <i class="fas fa-envelope me-1"></i> {{ __('Email Notifications') }}
                                        </label>
                                        <div class="form-text">{{ __('Receive notifications via email') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> {{ __('v2.save') }}
                            </button>
                        </div>
                        <div id="preferences-success" class="alert alert-success mt-3 d-none" role="alert">
                            <i class="fas fa-check-circle me-1"></i> {{ __('v2.preferences_updated_successfully', ['default' => 'تم حفظ التفضيلات بنجاح']) }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
                    const currentLocale = '{{ $currentLocale }}';
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
@endpush
@endsection