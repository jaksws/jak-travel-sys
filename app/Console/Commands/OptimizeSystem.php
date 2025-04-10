<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeSystem extends Command
{
    /**
     * اسم وتوصيف الأمر.
     *
     * @var string
     */
    protected $signature = 'system:optimize {--full : تشغيل التحسين الكامل}';

    /**
     * وصف الأمر.
     *
     * @var string
     */
    protected $description = 'تحسين أداء النظام من خلال تنظيف الذاكرة المؤقتة وتجميع الموارد';

    /**
     * تنفيذ الأمر.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('بدء عملية تحسين النظام...');

        // تنظيف الذاكرة المؤقتة
        $this->info('تنظيف الذاكرة المؤقتة للتطبيق...');
        Artisan::call('cache:clear');
        $this->info($this->formatOutput(Artisan::output()));

        // تنظيف إعدادات التكوين
        $this->info('تنظيف ذاكرة التكوين المؤقتة...');
        Artisan::call('config:clear');
        $this->info($this->formatOutput(Artisan::output()));

        // تنظيف ذاكرة المسارات
        $this->info('تنظيف ذاكرة المسارات المؤقتة...');
        Artisan::call('route:clear');
        $this->info($this->formatOutput(Artisan::output()));

        // تنظيف ذاكرة العرض
        $this->info('تنظيف ذاكرة العرض المؤقتة...');
        Artisan::call('view:clear');
        $this->info($this->formatOutput(Artisan::output()));

        // إذا كان مطلوباً التحسين الكامل
        if ($this->option('full')) {
            // تنفيذ تحسينات إضافية
            $this->info('تنفيذ تحسينات إضافية...');

            $this->info('تجميع إعدادات التكوين...');
            Artisan::call('config:cache');
            $this->info($this->formatOutput(Artisan::output()));

            $this->info('تجميع المسارات...');
            Artisan::call('route:cache');
            $this->info($this->formatOutput(Artisan::output()));
            
            $this->info('تحسين تحميل الفئات...');
            Artisan::call('optimize');
            $this->info($this->formatOutput(Artisan::output()));
        }

        $this->info('اكتملت عملية تحسين النظام!');

        return 0;
    }

    /**
     * تنسيق مخرجات الأمر
     */
    private function formatOutput($output)
    {
        return trim($output) ?: 'تم التنفيذ بنجاح.';
    }
}
