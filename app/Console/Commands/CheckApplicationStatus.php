<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckApplicationStatus extends Command
{
    protected $signature = 'app:check-status';
    protected $description = 'التحقق من حالة التطبيق وإصلاح المشاكل الشائعة';

    public function handle()
    {
        $this->info('جاري التحقق من حالة التطبيق...');

        // التحقق من اتصال قاعدة البيانات
        $this->info('التحقق من اتصال قاعدة البيانات...');
        try {
            \DB::connection()->getPdo();
            $this->info('✓ الاتصال بقاعدة البيانات ناجح: ' . \DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error('✗ خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage());
            $this->line('- تأكد من تكوين ملف .env بشكل صحيح');
            $this->line('- تأكد من تشغيل خدمة قاعدة البيانات');
        }

        // التحقق من التصاريح
        $this->info('التحقق من تصاريح المجلدات...');
        $paths = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            public_path('storage'),
        ];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                $this->error("✗ المجلد غير موجود: {$path}");
                continue;
            }

            if (!is_writable($path)) {
                $this->error("✗ المجلد غير قابل للكتابة: {$path}");
                $this->line("- قم بتنفيذ: chmod -R 775 {$path}");
            } else {
                $this->info("✓ تصاريح المجلد صحيحة: {$path}");
            }
        }

        // التحقق من وجود الترجمات العربية
        $this->info('التحقق من ملفات الترجمة العربية...');
        if (!file_exists(resource_path('lang/ar'))) {
            $this->error('✗ ملفات الترجمة العربية غير موجودة');
            $this->line('- قم بتنفيذ: php artisan lang:publish');
            $this->line('- ثم قم بإنشاء مجلد ar مع ملفات الترجمة');
        } else {
            $this->info('✓ ملفات الترجمة العربية موجودة');
        }

        // تنظيف الكاش
        $this->info('تنظيف الكاش...');
        try {
            // التحقق من وجود جدول 'cache' قبل تنفيذ الكاش
            if (Schema::hasTable('cache')) {
                $this->callSilent('cache:clear');
            } else {
                $this->warn('✗ جدول cache غير موجود، تم تخطي cache:clear');
            }
        } catch (\Exception $e) {
            $this->warn('✗ حدث خطأ أثناء تنفيذ cache:clear: ' . $e->getMessage());
        }

        $this->callSilent('config:clear');
        $this->callSilent('route:clear');
        $this->callSilent('view:clear');

        // إذا كانت المجلدات غير موجودة، ننشئ الرابط
        $this->info('التحقق من وجود الرابط العام...');
        if (!file_exists(public_path('storage'))) {
            $this->info('✗ المجلد public/storage غير موجود، سيتم إنشاء الرابط');
            $this->call('storage:link');
        } else {
            $this->info('✓ الرابط العام موجود');
        }

        // التحقق من أخطاء سير عمل GitHub
        $this->info('التحقق من أخطاء سير عمل GitHub...');
        $this->checkGitHubWorkflowErrors();

        // إرجاع حالة التطبيق
        $this->info('تم الانتهاء من فحص حالة التطبيق!');
    }

    private function checkGitHubWorkflowErrors()
    {
        $this->info('التحقق من أخطاء سير عمل GitHub...');
        // Add logic to check for GitHub workflow errors
        // This is a placeholder for the actual implementation
    }
}
