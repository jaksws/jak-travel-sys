<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CheckFeaturesDatabaseIntegrity extends Command
{
    protected $signature = 'app:check-features-db-integrity';
    protected $description = 'التحقق من تكامل ميزات التطبيق مع هيكل قاعدة البيانات';

    public function handle()
    {
        $this->info('جاري التحقق من تكامل ميزات التطبيق مع هيكل قاعدة البيانات...');
        
        $features = Config::get('v1_features');
        $issues = [];
        
        // التحقق من ميزات إدارة المستخدمين
        if (!empty($features['user_management'])) {
            if (!Schema::hasTable('users')) {
                $issues[] = 'ميزة إدارة المستخدمين نشطة لكن جدول المستخدمين غير موجود';
            } else {
                // التحقق من دعم أدوار المستخدمين خاصة دور المسؤول
                if (!Schema::hasColumn('users', 'role') && !Schema::hasColumn('users', 'type') && !Schema::hasColumn('users', 'user_type')) {
                    $issues[] = 'جدول المستخدمين لا يحتوي على عمود لتحديد الدور أو النوع (role/type/user_type)';
                } else {
                    // تحديد عمود الدور المستخدم في النظام
                    $roleColumn = null;
                    if (Schema::hasColumn('users', 'role')) {
                        $roleColumn = 'role';
                    } elseif (Schema::hasColumn('users', 'user_type')) {
                        $roleColumn = 'user_type';
                    } elseif (Schema::hasColumn('users', 'type')) {
                        $roleColumn = 'type';
                    }
                    
                    // التحقق من وجود مستخدمين بدور مسؤول
                    try {
                        $adminExists = DB::table('users')
                            ->where(function($query) use ($roleColumn) {
                                $query->where($roleColumn, 'admin')
                                      ->orWhere($roleColumn, 'Admin')
                                      ->orWhere($roleColumn, '4'); // افتراض أن 4 يمثل دور المسؤول
                            })
                            ->exists();
                            
                        if (!$adminExists) {
                            $issues[] = 'لا يوجد مستخدمين بدور مسؤول (admin) في النظام';
                        } else {
                            // التحقق من صلاحيات المسؤول
                            $this->info('✓ تم العثور على مستخدم بدور مسؤول (admin)');
                            $adminCount = DB::table('users')
                                ->where(function($query) use ($roleColumn) {
                                    $query->where($roleColumn, 'admin')
                                          ->orWhere($roleColumn, 'Admin')
                                          ->orWhere($roleColumn, '4');
                                })
                                ->count();
                            $this->line("  عدد المسؤولين في النظام: {$adminCount}");
                            
                            // فحص إضافي لحالة الحساب
                            if (Schema::hasColumn('users', 'is_active')) {
                                $activeAdmins = DB::table('users')
                                    ->where(function($query) use ($roleColumn) {
                                        $query->where($roleColumn, 'admin')
                                              ->orWhere($roleColumn, 'Admin')
                                              ->orWhere($roleColumn, '4');
                                    })
                                    ->where('is_active', true)
                                    ->count();
                                
                                if ($activeAdmins == 0) {
                                    $issues[] = 'لا يوجد مسؤولين نشطين في النظام، يرجى تفعيل حساب مسؤول واحد على الأقل';
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $issues[] = 'خطأ عند التحقق من وجود مستخدمين بدور مسؤول: ' . $e->getMessage();
                    }
                }
            }
        }
        
        // التحقق من ميزات إدارة الوكالات
        if (!empty($features['agency_management'])) {
            if (!Schema::hasTable('agencies')) {
                $issues[] = 'ميزة إدارة الوكالات نشطة لكن جدول الوكالات غير موجود';
            }
        }
        
        // التحقق من ميزات الطلبات والعروض
        if (!empty($features['request_management'])) {
            $requestsTable = Schema::hasTable('travel_requests') ? 'travel_requests' : 'requests';
            if (!Schema::hasTable($requestsTable)) {
                $issues[] = 'ميزة إدارة الطلبات نشطة لكن جدول الطلبات غير موجود';
            }
            
            if (!Schema::hasTable('quotes')) {
                $issues[] = 'ميزة إدارة العروض نشطة لكن جدول العروض غير موجود';
            }
        }
        
        // التحقق من ميزات المدفوعات
        if (!empty($features['payment_processing'])) {
            if (!Schema::hasTable('payments')) {
                $issues[] = 'ميزة معالجة المدفوعات نشطة لكن جدول المدفوعات غير موجود';
            }
            
            if (!Schema::hasTable('currencies')) {
                $issues[] = 'ميزة معالجة المدفوعات نشطة لكن جدول العملات غير موجود';
            }
        }
        
        // التحقق من صلاحيات المسؤول للوصول للميزات المهمة
        if (!empty($features['admin_dashboard']) || !empty($features['user_management'])) {
            try {
                $this->info('جاري التحقق من صلاحيات المسؤول...');
                
                // التحقق من نظام الصلاحيات
                if (Schema::hasTable('permissions') || Schema::hasTable('roles')) {
                    $this->line('✓ يوجد نظام للصلاحيات');
                    
                    // فحص وجود صلاحيات للمسؤول إذا كان النظام يستخدم جدول الصلاحيات
                    if (Schema::hasTable('permissions')) {
                        $adminPermissionsCount = DB::table('permissions')
                            ->where('name', 'like', '%admin%')
                            ->orWhere('name', 'like', '%manage%')
                            ->count();
                        
                        $this->line("  عدد صلاحيات المسؤول المكتشفة: {$adminPermissionsCount}");
                        
                        if ($adminPermissionsCount == 0) {
                            $this->warn('⚠️ لم يتم اكتشاف أي صلاحيات للمسؤول في جدول الصلاحيات');
                        }
                    }
                } else {
                    $this->line('⚠️ نظام الصلاحيات غير موجود، يرجى التأكد من تكوين صلاحيات المسؤول بشكل صحيح');
                }
                
                // فحص وجود واجهات خاصة بالمسؤول
                $viewsPath = resource_path('views/admin');
                if (file_exists($viewsPath)) {
                    $this->line('✓ توجد واجهات مستخدم للمسؤول');
                } else {
                    $this->warn('⚠️ مجلد واجهات المسؤول غير موجود في المسار: ' . $viewsPath);
                }
                
                // فحص وجود وثائق ومستندات للمسؤول
                $adminGuidePath = resource_path('docs/admin-guide.md');
                if (file_exists($adminGuidePath)) {
                    $this->line('✓ يوجد دليل استخدام للمسؤول');
                } else {
                    $this->warn('⚠️ دليل المسؤول غير موجود في المسار: ' . $adminGuidePath);
                }
            } catch (\Exception $e) {
                // تجاهل هذا الخطأ إذا لم يكن نظام الصلاحيات مستخدماً
                $this->warn('⚠️ حدث خطأ أثناء التحقق من صلاحيات المسؤول: ' . $e->getMessage());
            }
        }
        
        // التحقق من إعدادات النظام الخاصة بالمسؤول
        if (!empty($features['system_settings']) || !empty($features['admin_dashboard'])) {
            if (!Schema::hasTable('settings')) {
                $this->warn('⚠️ جدول إعدادات النظام غير موجود، المسؤول قد لا يتمكن من تغيير إعدادات النظام');
            } else {
                $this->line('✓ جدول إعدادات النظام موجود');
                
                try {
                    $settingsCount = DB::table('settings')->count();
                    $this->line("  عدد الإعدادات المتاحة: {$settingsCount}");
                } catch (\Exception $e) {
                    $this->warn('⚠️ خطأ في الوصول إلى جدول الإعدادات: ' . $e->getMessage());
                }
            }
            
            // التحقق من وجود ميزة التعدد اللغوي (الضرورية للمسؤول)
            if (!empty($features['multilingual']['enabled'])) {
                $this->line('✓ ميزة تعدد اللغات مفعّلة للمسؤول');
            } else {
                $this->line('⚠️ ميزة تعدد اللغات غير مفعّلة، قد يؤثر على تجربة المسؤول');
            }
        }
        
        if (empty($issues)) {
            $this->info('✅ جميع ميزات التطبيق متوافقة مع هيكل قاعدة البيانات وتدعم دور المسؤول');
        } else {
            $this->error('❌ تم اكتشاف مشاكل في تكامل الميزات مع قاعدة البيانات:');
            foreach ($issues as $issue) {
                $this->warn("- {$issue}");
            }
            $this->line('');
            $this->line('لإصلاح هذه المشاكل، يرجى اتباع الإرشادات التالية:');
            $this->line('1. تأكد من وجود مستخدم واحد على الأقل بدور "admin"');
            $this->line('2. تأكد من أن المستخدم بدور "admin" مفعّل في النظام');
            $this->line('3. تحقق من تكوين صلاحيات المسؤول بشكل صحيح');
        }
        
        return empty($issues) ? 0 : 1;
    }
}
