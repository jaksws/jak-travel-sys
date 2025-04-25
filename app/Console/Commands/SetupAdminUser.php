<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SetupAdminUser extends Command
{
    protected $signature = 'app:setup-admin-user';
    protected $description = 'إنشاء أو تحديث حساب مستخدم من نوع مسؤول (admin) - يدير جميع الوكالات';

    public function handle()
    {
        // تعيين اتجاه النص للعرض في الطرفية
        $this->setTerminalEncoding();
        
        $this->info('=== إعداد حساب المسؤول ===');
        
        // التحقق من وجود جدول المستخدمين
        if (!Schema::hasTable('users')) {
            $this->error('جدول المستخدمين غير موجود! يرجى تنفيذ أوامر الهجرة أولاً:');
            $this->line('php artisan migrate');
            return 1;
        }
        
        // تحديد عمود الدور المستخدم في النظام - Simplified
        $roleColumn = 'role'; // Directly use 'role'
        if (!Schema::hasColumn('users', $roleColumn)) {
            $this->error("Column 'role' does not exist in the users table!");
            $this->line('Please ensure migrations are run correctly.');
            return 1;
        }
        
        // تحديد القيم المسموحة للعمود - Simplified
        $validUserTypes = ['admin', 'agent', 'subagent', 'client']; 
        
        // اختيار القيمة المناسبة للمسؤول
        $adminType = 'admin'; // Directly use 'admin'

        // توضيح دور المسؤول في النظام
        $this->info('=== صلاحيات المسؤول (Admin) ===');
        $this->info('المسؤول هو المستخدم الذي يدير جميع الوكالات ولا ينتمي إلى وكالة معينة.');
        $this->line('الصلاحيات الرئيسية للمسؤول:');
        $this->line('- إدارة جميع الوكالات والسبوكلاء');
        $this->line('- الوصول الكامل لجميع الخدمات والطلبات');
        $this->line('- إدارة إعدادات النظام العامة');
        $this->line('- مراقبة أنشطة النظام والتقارير');
        $this->line('');

        // التحقق من وجود مستخدمين بدور مسؤول
        $adminExists = $this->checkForExistingAdmin($roleColumn, $adminType);
        
        if ($adminExists) {
            if (!$this->confirm('تم اكتشاف مستخدم بدور مسؤول. هل ترغب في إنشاء مستخدم مسؤول جديد؟', true)) {
                $this->info('تم إلغاء العملية.');
                return 0;
            }
        }
        
        // جمع معلومات المستخدم الجديد
        $name = $this->ask('اسم المستخدم المسؤول');
        $email = $this->ask('البريد الإلكتروني للمستخدم المسؤول');
        $password = $this->secret('كلمة المرور للمستخدم المسؤول (الحد الأدنى 8 أحرف)');
        $passwordConfirmation = $this->secret('تأكيد كلمة المرور');
        
        // التحقق من صحة البيانات
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        if ($validator->fails()) {
            $this->error('فشل التحقق من البيانات:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("- {$error}");
            }
            return 1;
        }

        try {
            // الحصول على أعمدة جدول المستخدمين
            $columns = Schema::getColumnListing('users');
            
            // إعداد البيانات الأساسية
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                $roleColumn => $adminType, // Use role column
            ];

            // إضافة القيم الافتراضية للأعمدة الموجودة
            if (in_array('status', $columns)) {
                $userData['status'] = 'active'; // Default to active
            }
            
            if (in_array('locale', $columns)) {
                $userData['locale'] = 'en'; // Default admin locale to 'en'
            }
            
            if (in_array('theme', $columns)) {
                $userData['theme'] = 'light'; // Default admin theme
            }

            if (in_array('email_notifications', $columns)) {
                $userData['email_notifications'] = true; // Default admin email notifications
            }
            
            // المسؤول لا ينتمي إلى أي وكالة، بل يدير جميع الوكالات
            if (in_array('agency_id', $columns)) {
                $userData['agency_id'] = null; // Admin has no agency
            }
            
            // معالجة حقول التفضيلات (assuming notification_preferences)
            if (in_array('notification_preferences', $columns)) {
                 $userData['notification_preferences'] = json_encode(['system' => true, 'email' => true]); // Example default
            }
            
            // إنشاء أو تحديث المستخدم المسؤول
            $user = User::updateOrCreate(
                ['email' => $email], // Find by email
                $userData
            );
            
            $this->info('تم إنشاء حساب المسؤول بنجاح!');
            $this->line('');
            $this->line('تفاصيل الحساب:');
            $this->line("الاسم: {$user->name}");
            $this->line("البريد الإلكتروني: {$user->email}");
            $this->line("الدور: {$user->$roleColumn} (مسؤول)");
            $this->line("الصلاحية: إدارة جميع الوكالات");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("حدث خطأ أثناء إنشاء المستخدم: " . $e->getMessage());
            
            if ($this->confirm('هل تريد محاولة إنشاء المستخدم باستخدام استعلام SQL مباشر؟', true)) {
                try {
                    DB::insert(
                        "INSERT INTO users (name, email, password, {$roleColumn}, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?)",
                        [
                            $name,
                            $email,
                            Hash::make($password),
                            $adminType,
                            now(),
                            now()
                        ]
                    );
                    
                    $this->info('تم إنشاء حساب المسؤول بنجاح باستخدام طريقة بديلة!');
                    $this->line("الاسم: {$name}");
                    $this->line("البريد الإلكتروني: {$email}");
                    $this->line("الدور: {$adminType} (مسؤول)");
                    $this->line("الصلاحية: إدارة جميع الوكالات");
                    return 0;
                } catch (\Exception $sqlEx) {
                    $this->error("فشل إنشاء المستخدم: " . $sqlEx->getMessage());
                    $this->line("يرجى التحقق من هيكل قاعدة البيانات وإعادة المحاولة.");
                    
                    $this->line('');
                    $this->line('معلومات تشخيصية:');
                    $this->line('1. أعمدة جدول المستخدمين: ' . implode(', ', $columns));
                    $this->line("2. القيم المسموحة لحقل {$roleColumn}: " . implode(', ', $validUserTypes));
                    
                    return 1;
                }
            }
            
            return 1;
        }
    }
    
    /**
     * ضبط إعدادات الترميز للطرفية لدعم اللغة العربية بشكل أفضل
     */
    private function setTerminalEncoding()
    {
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
        
        if (function_exists('mb_http_output')) {
            mb_http_output('UTF-8');
        }
        
        if (function_exists('setlocale')) {
            setlocale(LC_ALL, 'ar_SA.UTF-8', 'ar_SA', 'ar');
        }
        
        if (function_exists('ini_set')) {
            ini_set('default_charset', 'UTF-8');
        }
    }
    
    /**
     * التحقق من وجود مستخدمين بدور مسؤول
     */
    private function checkForExistingAdmin(string $roleColumn, string $adminType): bool
    {
        $query = User::where($roleColumn, $adminType);
        return $query->exists();
    }
}