<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SetupAdminUser extends Command
{
    protected $signature = 'app:setup-admin-user';
    protected $description = 'إنشاء أو تحديث حساب مستخدم من نوع مسؤول (admin)';

    public function handle()
    {
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
        
        // التحقق من وجود مستخدمين بدور مسؤول
        $adminExists = User::where($roleColumn, 'admin')
            ->orWhere($roleColumn, 'Admin')
            ->orWhere($roleColumn, '4')
            ->exists();
            
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
        
        // إنشاء أو تحديث المستخدم المسؤول
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                $roleColumn => 'admin',
                'is_active' => true,
            ]
        );
        
        $this->info('تم إنشاء حساب المسؤول بنجاح!');
        $this->line('');
        $this->line('تفاصيل الحساب:');
        $this->line("الاسم: {$user->name}");
        $this->line("البريد الإلكتروني: {$user->email}");
        $this->line("الدور: {$user->$roleColumn}");
        
        return 0;
    }
}
