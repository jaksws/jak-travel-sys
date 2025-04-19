@extends('layouts.app')

@section('title', __('v2.profile_settings'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">{{ __('v2.profile_settings') }}</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <form id="preferences-form" action="{{ route('user.preferences.save') }}" method="POST">
            @csrf
            
            <!-- Tabs for different setting sections -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex -mb-px" id="settings-tabs" role="tablist">
                    <li class="mr-2">
                        <button type="button" class="inline-block py-4 px-4 text-sm font-medium text-center border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 active" 
                            id="appearance-tab" data-tabs-target="#appearance" role="tab" aria-selected="true">
                            {{ __('v2.appearance_settings') }}
                        </button>
                    </li>
                    <li class="mr-2">
                        <button type="button" class="inline-block py-4 px-4 text-sm font-medium text-center border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                            id="notifications-tab" data-tabs-target="#notifications" role="tab" aria-selected="false">
                            {{ __('v2.notification_settings') }}
                        </button>
                    </li>
                </ul>
            </div>
            
            <!-- Tab content -->
            <div id="settings-content">
                <!-- Appearance Settings Tab -->
                <div class="block" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">{{ __('v2.language_settings') }}</h2>
                        <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('v2.language') }}
                        </label>
                        <select id="locale" name="locale" class="input">
                            @foreach(config('v1_features.multilingual.available_locales') as $locale)
                                <option value="{{ $locale }}" {{ $currentLocale == $locale ? 'selected' : '' }}>
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
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">{{ __('v2.appearance_settings') }}</h2>
                        <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('v2.theme') }}
                        </label>
                        <select id="theme-selector" name="theme" class="input">
                            <option value="light" {{ ($currentTheme ?? 'system') == 'light' ? 'selected' : '' }}>
                                {{ __('v2.light_mode') }}
                            </option>
                            <option value="dark" {{ ($currentTheme ?? 'system') == 'dark' ? 'selected' : '' }}>
                                {{ __('v2.dark_mode') }}
                            </option>
                            <option value="system" {{ ($currentTheme ?? 'system') == 'system' ? 'selected' : '' }}>
                                {{ __('v2.system_mode') }}
                            </option>
                        </select>
                        
                        <div class="mt-4 flex items-center">
                            <button type="button" id="dark-mode-toggle" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                                <span id="theme-icon" class="{{ ($currentTheme ?? 'system') == 'dark' ? 'fas fa-sun' : 'fas fa-moon' }}"></span>
                                <span class="sr-only">{{ __('v2.toggle_dark_mode') }}</span>
                            </button>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('v2.toggle_dark_mode') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications Settings Tab -->
                <div class="hidden" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-4">{{ __('v2.notification_settings') }}</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="email_notifications" name="email_notifications" type="checkbox" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700" 
                                        {{ isset($preferences['email_notifications']) && $preferences['email_notifications'] ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="email_notifications" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Email Notifications') }}</label>
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('Receive notifications via email') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('v2.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching logic
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('[role="tabpanel"]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document.querySelector(tab.dataset.tabsTarget);
                
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                tabs.forEach(t => {
                    t.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent');
                    t.setAttribute('aria-selected', 'false');
                });
                
                tab.classList.add('active', 'border-blue-600', 'text-blue-600');
                tab.classList.remove('border-transparent');
                tab.setAttribute('aria-selected', 'true');
                
                target.classList.remove('hidden');
                target.classList.add('block');
            });
        });
        
        // Handle form submission via AJAX
        const preferencesForm = document.getElementById('preferences-form');
        
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
                    // Show success message
                    alert(data.message || '{{ __("v2.success") }}');
                    
                    // Reload page if locale was changed
                    const newLocale = formData.get('locale');
                    const currentLocale = '{{ $currentLocale }}';
                    
                    if (newLocale !== currentLocale) {
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("v2.error") }}');
            });
        });
    });
</script>
@endpush
@endsection