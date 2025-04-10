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
                
                // التحقق من وجود دور المسؤول
                if (in_array(strtolower($role), ['admin', '4'])) {
                    $this->info('✓ دور المسؤول موجود');
                    
                    // إحصاء عدد المسؤولين
                    $adminCount = DB::table('users')
                        ->where($roleColumn, $role)
                        ->count();
                        
                    $this->info("  عدد المسؤولين: {$adminCount}");
                }
            }
            
            // التحقق من وجود دور المسؤول إذا لم يتم العثور عليه سابقاً
            if (!in_array('admin', array_map('strtolower', $roleTypes)) && !in_array('4', $roleTypes)) {
                $this->warn('⚠️ لم يتم العثور على دور المسؤول في النظام');
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
