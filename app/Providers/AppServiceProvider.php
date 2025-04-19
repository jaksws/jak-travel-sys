<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Helpers\CurrencyHelper;
use App\Providers\MultilingualServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use JavaScript;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register V1 service providers if features are enabled
        if (config('V1_features.multilingual.enabled')) {
            $this->app->register(MultilingualServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add workaround for missing intl extension
        $this->handleIntlExtensionMissing();
        
        // إضافة دالة تنسيق السعر كمساعد Blade
        Blade::directive('formatPrice', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatPrice($expression); ?>";
        });

        // Share V1 feature settings with all views
        View::share('V1Features', config('V1_features'));

        // Pass dark mode settings to JavaScript
        if (config('V1_features.dark_mode.enabled')) {
            View::composer('*', function ($view) {
                JavaScript::put([
                    'darkModeSettings' => config('V1_features.dark_mode'),
                    'userId' => Auth::id()
                ]);
            });
        }
    }
    
    /**
     * Provides a workaround for environments without the intl extension
     */
    private function handleIntlExtensionMissing(): void
    {
        if (!extension_loaded('intl')) {
            // Monkey patch the Number::format method to avoid the intl extension requirement error
            Number::macro('formatWithoutIntl', function ($number, $locale = null, $currency = null, $options = []) {
                // Simple fallback formatting without intl extension
                if (is_null($currency)) {
                    return number_format($number, $options['decimals'] ?? 2, '.', ',');
                } else {
                    // Very basic currency formatting
                    $symbol = $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : $currency);
                    return $symbol . ' ' . number_format($number, 2, '.', ',');
                }
            });
            
            // Override the format method to use our fallback
            Number::macro('format', function ($number, $locale = null, $options = []) {
                return Number::formatWithoutIntl($number, $locale, null, $options);
            });
            
            // Override the currency method to use our fallback
            Number::macro('currency', function ($number, $currency = 'USD', $locale = null) {
                return Number::formatWithoutIntl($number, $locale, $currency);
            });
        }
    }
}
