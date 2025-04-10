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
        
        // تحديد عمود الدور المستخدم في النظام
        $roleColumn = null;
        if (Schema::hasColumn('users', 'role')) {
            $roleColumn = 'role';
        } elseif (Schema::hasColumn('users', 'user_type')) {
            $roleColumn = 'user_type';
        } elseif (Schema::hasColumn('users', 'type')) {
            $roleColumn = 'type';
        } else {
            $this->error('لا يوجد عمود لتحديد دور المستخدم في جدول المستخدمين!');
            $this->line('يرجى التأكد من تنفيذ جميع ملفات الهجرة الخاصة بالمستخدمين.');
            return 1;
        }
        
        // تحديد القيم المسموحة للعمود
        $validUserTypes = $this->getValidUserTypes($roleColumn);
        
        // اختيار القيمة المناسبة للمسؤول
        $adminType = $this->determineAdminType($validUserTypes);

        // توضيح دور المسؤول في النظام
        $this->info('=== صلاحيات المسؤول (Admin) ===');
        $this->info('المسؤول هو المستخدم الذي يدير جميع الوكالات ولا ينتمي إلى وكالة معينة.');
        $this->line('الصلاحيات الرئيسية للمسؤول:');
        $this->line('- إدارة جميع الوكالات والسبوكلاء');
        $this->line('- الوصول الكامل لجميع الخدمات والطلبات');
        $this->line('- إدارة إعدادات النظام العامة');
        $this->line('- مراقبة أنشطة النظام والتقارير');
        $this->line('');

        if ($adminType !== 'admin') {
            $this->warn("تم استخدام دور '{$adminType}' بدلاً من 'admin' نظراً لقيود قاعدة البيانات.");
            $this->line("القيم المسموحة في حقل {$roleColumn} هي: " . implode(', ', $validUserTypes));
        }

        if (!$adminType) {
            $this->error('لا يوجد قيمة مناسبة لدور المسؤول في النظام!');
            return 1;
        }
        
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
                $roleColumn => $adminType, // استخدام نوع المستخدم المناسب
            ];

            // إضافة القيم الافتراضية للأعمدة الموجودة
            if (in_array('is_active', $columns)) {
                $userData['is_active'] = true;
            }
            
            if (in_array('phone', $columns)) {
                $userData['phone'] = $this->ask('رقم الهاتف (اختياري)') ?: null;
            }
            
            if (in_array('locale', $columns)) {
                $userData['locale'] = 'ar'; // افتراضي: اللغة العربية
            }
            
            if (in_array('theme_preference', $columns)) {
                $userData['theme_preference'] = 'light';
            }
            
            // المسؤول لا ينتمي إلى أي وكالة، بل يدير جميع الوكالات
            if (in_array('agency_id', $columns)) {
                $userData['agency_id'] = null;
                $this->info('المسؤول لا ينتمي لأي وكالة محددة ولديه صلاحية على جميع الوكالات.');
            }
            
            if (in_array('parent_id', $columns)) {
                $userData['parent_id'] = null;
            }
            
            // حذف البيانات غير الضرورية لتجنب أخطاء CHECK constraint
            if (array_key_exists('agency_id', $userData) && $userData['agency_id'] === 'لا يوجد') {
                $userData['agency_id'] = null;
            }

            // معالجة حقول التفضيلات
            $preferencesColumns = ['preferences', 'user_preferences', 'notification_preferences'];
            foreach ($preferencesColumns as $column) {
                if (in_array($column, $columns)) {
                    $userData[$column] = json_encode([]);
                }
            }
            
            // إنشاء أو تحديث المستخدم المسؤول
            $user = User::updateOrCreate(
                ['email' => $email],
                $userData
            );
            
            // إضافة وسم خاص للمستخدم للإشارة إلى أنه مسؤول
            if (in_array('is_superadmin', $columns)) {
                $user->is_superadmin = true;
                $user->save();
                $hasSuperAdminFlag = true;
            } else {
                $hasSuperAdminFlag = false;
            }
            
            // تعيين حقل is_admin إذا كان موجوداً
            if (in_array('is_admin', $columns)) {
                $user->is_admin = true;
                $user->save();
                $hasAdminFlag = true;
            } else {
                $hasAdminFlag = false;
            }
            
            $this->info('تم إنشاء حساب المسؤول بنجاح!');
            $this->line('');
            $this->line('تفاصيل الحساب:');
            $this->line("الاسم: {$user->name}");
            $this->line("البريد الإلكتروني: {$user->email}");
            $this->line("الدور: {$user->$roleColumn} (مسؤول)");
            $this->line("الصلاحية: إدارة جميع الوكالات");
            
            // التحقق من الصلاحيات الإضافية
            if ($hasSuperAdminFlag) {
                $this->line("وسم المسؤول العام: مفعّل (is_superadmin = true)");
            }
            
            if ($hasAdminFlag) {
                $this->line("وسم المسؤول: مفعّل (is_admin = true)");
            }
            
            if (!$hasSuperAdminFlag && !$hasAdminFlag && $adminType !== 'admin') {
                $this->warn("ملاحظة: لم يتم العثور على وسم إضافي للمسؤول (is_admin/is_superadmin).");
                $this->warn("الصلاحيات معتمدة فقط على حقل {$roleColumn} = '{$adminType}'");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("حدث خطأ أثناء إنشاء المستخدم: " . $e->getMessage());
            
            // محاولة معالجة حالة خطأ CHECK constraint
            if (strpos($e->getMessage(), 'CHECK constraint') !== false) {
                $this->line('');
                $this->warn("يبدو أن هناك قيودًا على حقل {$roleColumn} في قاعدة البيانات.");
                $this->line("القيم المقبولة لحقل {$roleColumn} هي: " . implode(', ', $validUserTypes));
                
                $adminType = $this->choice(
                    'يرجى اختيار قيمة صالحة لدور المستخدم المسؤول:',
                    $validUserTypes
                );
                
                try {
                    // إعادة المحاولة باستخدام القيمة التي اختارها المستخدم
                    $userData[$roleColumn] = $adminType;
                    
                    // حذف البيانات غير الضرورية لتجنب أخطاء CHECK constraint
                    if (isset($userData['agency_id']) && $userData['agency_id'] === null) {
                        unset($userData['agency_id']);
                    }
                    
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        $userData
                    );
                    
                    $this->info('تم إنشاء حساب المسؤول بنجاح!');
                    $this->line('');
                    $this->line('تفاصيل الحساب:');
                    $this->line("الاسم: {$user->name}");
                    $this->line("البريد الإلكتروني: {$user->email}");
                    $this->line("الدور: {$user->$roleColumn} (مسؤول)");
                    
                    return 0;
                } catch (\Exception $e2) {
                    $this->error("فشلت المحاولة الثانية لإنشاء المستخدم: " . $e2->getMessage());
                }
            }
            
            if ($this->confirm('هل تريد محاولة إنشاء المستخدم باستخدام استعلام SQL مباشر؟', true)) {
                try {
                    // استخدام نوع المستخدم الذي تم اختياره
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
                    
                    // عرض معلومات إضافية للمساعدة في التشخيص
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
        // محاولة ضبط ترميز الطرفية
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
        
        if (function_exists('mb_http_output')) {
            mb_http_output('UTF-8');
        }
        
        // محاولة ضبط محلية النظام (locale)
        if (function_exists('setlocale')) {
            setlocale(LC_ALL, 'ar_SA.UTF-8', 'ar_SA', 'ar');
        }
        
        // تعيين ترميز خرج الـ PHP
        if (function_exists('ini_set')) {
            ini_set('default_charset', 'UTF-8');
        }
    }
    
    /**
     * الحصول على القيم الصالحة للعمود المحدد في جدول المستخدمين
     */
    private function getValidUserTypes(string $roleColumn): array
    {
        try {
            // محاولة جلب القيم الصالحة من الجدول
            $columnType = DB::select("PRAGMA table_info(users)");
            foreach ($columnType as $column) {
                if ($column->name === $roleColumn) {
                    // للتعامل مع enum في SQLite
                    if (strpos($column->type, 'enum') !== false) {
                        preg_match("/enum\s*\(\s*'(.+?)'\s*\)/i", $column->type, $matches);
                        if (isset($matches[1])) {
                            return explode("','", $matches[1]);
                        }
                    }
                }
            }
            
            // إذا لم نجد القيم من الجدول، حاول استنتاجها من أحد المستخدمين الموجودين
            $values = DB::table('users')->select($roleColumn)->distinct()->pluck($roleColumn)->filter()->toArray();
            if (!empty($values)) {
                return $values;
            }
            
            // إذا لم يكن هناك مستخدمين، استخدم القيم المتوقعة
            return ['admin', 'agency', 'subagent', 'customer'];
        } catch (\Exception $e) {
            // استخدم القيم الافتراضية كخطة بديلة
            return ['agency', 'subagent', 'customer'];
        }
    }
    
    /**
     * تحديد نوع المستخدم المناسب للمسؤول
     */
    private function determineAdminType(array $validUserTypes): string
    {
        // البحث عن أنسب دور للمستخدم المسؤول بترتيب الأفضلية
        if (in_array('admin', $validUserTypes)) {
            return 'admin';
        } elseif (in_array('superadmin', $validUserTypes)) {
            return 'superadmin';
        } elseif (in_array('administrator', $validUserTypes)) {
            return 'administrator';
        } elseif (in_array('admin_user', $validUserTypes)) {
            return 'admin_user';
        } elseif (in_array('agency', $validUserTypes)) {
            return 'agency'; // وكالة كأقرب دور للمسؤول
        }
        
        // إذا لم يتم العثور على قيمة مناسبة، استخدم القيمة الأولى
        return reset($validUserTypes) ?: '';
    }
    
    /**
     * التحقق من وجود مستخدمين بدور مسؤول
     */
    private function checkForExistingAdmin(string $roleColumn, string $adminType): bool
    {
        // التحقق مما إذا كان المستخدم من نوع 'admin' و 'agency' موجودًا
        $query = User::where(function ($q) use ($roleColumn, $adminType) {
            $q->where($roleColumn, $adminType)
              ->orWhere($roleColumn, 'admin')
              ->orWhere($roleColumn, 'Admin')
              ->orWhere($roleColumn, 'superadmin');
        });
        
        // إذا كان هناك عمود is_superadmin، تحقق منه أيضًا
        if (Schema::hasColumn('users', 'is_superadmin')) {
            $query->orWhere('is_superadmin', true);
        }
        
        return $query->exists();
    }
}