<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckFullApplicationIntegrity extends Command
{
    protected $signature = 'app:check-all';
    protected $description = 'فحص شامل لحالة التطبيق وتكامل الميزات والأدوار';

    public function handle()
    {
        $this->info('بدء الفحص الشامل للتطبيق...');

        // 1. تحقق من حالة التطبيق
        $this->info('1. التحقق من حالة التطبيق...');
        $statusCode = $this->call('app:check-status');

        // 2. تحقق من تكامل الميزات مع قاعدة البيانات
        $this->info('2. التحقق من تكامل الميزات مع قاعدة البيانات...');
        $featuresCode = $this->call('app:check-features-db-integrity');

        // 3. تحقق من تكامل أدوار المستخدمين
        $this->info('3. التحقق من تكامل أدوار المستخدمين...');
        $rolesCode = $this->call('app:check-user-roles');

        // تقييم النتائج
        if (in_array(1, [$statusCode, $featuresCode, $rolesCode], true)) {
            $this->error('❌ الفحص الشامل أظهر بعض الأخطاء.');
            return 1;
        }

        $this->info('✅ الفحص الشامل اكتمل بنجاح بدون أخطاء.');
        return 0;
    }
}
