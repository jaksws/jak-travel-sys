<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckUserRolesIntegrity extends Command
{
    protected $signature = 'app:check-user-roles';
    protected $description = 'التحقق من تكامل أدوار المستخدمين مع ميزات النظام';

    public function handle()
    {
        $this->info('جاري التحقق من تكامل أدوار المستخدمين...');
        
        // تحقق ذكي من وجود قاعدة البيانات والجداول الأساسية قبل أي فحص
        $dbConnection = config('database.default');
        $dbName = config('database.connections.' . $dbConnection . '.database');
        if (!$dbName || !file_exists($dbName)) {
            $this->error('❌ قاعدة البيانات غير موجودة أو لم يتم تحديدها بشكل صحيح: ' . $dbName);
            return 1;
        }
        $requiredTables = [
            'users', 'agencies', 'services', 'requests', 'quotes', 'notifications',
            'transactions', 'documents', 'currencies', 'quote_attachments', 'payments'
        ];
        $missingTables = [];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        if (!empty($missingTables)) {
            $this->error('❌ الجداول التالية مفقودة في قاعدة البيانات: ' . implode(', ', $missingTables));
            return 1;
        }

        $issues = [];
        
        // التحقق من وجود جدول المستخدمين
        if (!Schema::hasTable('users')) {
            $this->error('❌ جدول المستخدمين غير موجود');
            return 1;
        }
        
        // تحديد عمود الدور
        $roleColumn = null;
        if (Schema::hasColumn('users', 'role')) {
            $roleColumn = 'role';
        } elseif (Schema::hasColumn('users', 'type')) {
            $roleColumn = 'type';
        }
        
        if (!$roleColumn) {
            $this->error('❌ لا يوجد عمود لتحديد دور المستخدم في جدول المستخدمين');
            return 1;
        }
        
        // استخراج أنواع المستخدمين الموجودة
        try {
            $roleTypes = DB::table('users')
                ->select($roleColumn)
                ->distinct()
                ->pluck($roleColumn)
                ->toArray();

            $this->info('أدوار المستخدمين الموجودة في النظام:');
            foreach ($roleTypes as $role) {
                $this->line("- {$role}");
            }

            // التحقق من وجود مسؤول فعلي عبر is_admin
            $adminUser = DB::table('users')->where('is_admin', 1)->first();
            if ($adminUser) {
                $this->info('✓ تم العثور على مستخدم مسؤول (is_admin=1) في النظام.');
                // لا تضف أي مشكلة إذا وُجد مستخدم is_admin=1
                $issues = []; // تأكد من إفراغ قائمة المشاكل
            } elseif (in_array('admin', array_map('strtolower', $roleTypes)) || in_array('4', $roleTypes)) {
                $this->info('✓ دور المسؤول موجود عبر role=admin أو role=4.');
                // لا تضف أي مشكلة إذا وُجد role=admin
                $issues = [];
            } else {
                $this->warn('⚠️ لم يتم العثور على دور المسؤول في النظام (لا يوجد مستخدم is_admin=1 ولا role=admin)');
                $issues[] = 'دور المسؤول (admin) غير موجود';
            }

            // التحقق من جداول الصلاحيات إذا كانت موجودة
            if (Schema::hasTable('permissions') || Schema::hasTable('role_permissions')) {
                $this->info('يوجد نظام للصلاحيات - تحقق من صلاحيات المسؤول');
                
                // هنا يمكن إضافة منطق للتحقق من صلاحيات المسؤولين حسب هيكل النظام
            }
            
        } catch (\Exception $e) {
            $this->error('❌ حدث خطأ أثناء التحقق من أدوار المستخدمين: ' . $e->getMessage());
            return 1;
        }
        
        if (empty($issues)) {
            $this->info('✅ أدوار المستخدمين متكاملة مع النظام');
            return 0;
        }
        
        $this->warn('قائمة المشاكل المكتشفة:');
        foreach ($issues as $issue) {
            $this->warn("- {$issue}");
        }
        
        return 1;
    }
}
