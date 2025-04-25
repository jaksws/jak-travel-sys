<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\Hash;

// Reviewed on 2023-10-01 by John Doe

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب الوكالات المنشأة سابقاً
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        $gulfAgency = Agency::where('email', 'info@gulf-travel.com')->first();
        
        if ($yemenAgency) {
            // إنشاء مستخدم وكيل لوكالة اليمن
            $agencyAdmin = User::firstOrCreate(
                ['email' => 'admin@yemen-travel.com'],
                [
                    'name' => 'مدير وكالة اليمن',
                    'password' => Hash::make('password123'),
                    'role' => 'agent',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء سبوكلاء لوكالة اليمن
            $subagent1 = User::firstOrCreate(
                ['email' => 'ahmed@yemen-travel.com'],
                [
                    'name' => 'أحمد محمد',
                    'password' => Hash::make('password123'),
                    'role' => 'subagent',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            $subagent2 = User::firstOrCreate(
                ['email' => 'mohammed@yemen-travel.com'],
                [
                    'name' => 'محمد علي',
                    'password' => Hash::make('password123'),
                    'role' => 'subagent',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء عملاء لوكالة اليمن
            User::firstOrCreate(
                ['email' => 'salem@example.com'],
                [
                    'name' => 'سالم علي',
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            User::firstOrCreate(
                ['email' => 'fatima@example.com'],
                [
                    'name' => 'فاطمة أحمد',
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
        }
        
        if ($gulfAgency) {
            // إنشاء مستخدم وكيل لوكالة الخليج
            $gulfAdmin = User::firstOrCreate(
                ['email' => 'admin@gulf-travel.com'],
                [
                    'name' => 'مدير وكالة الخليج',
                    'password' => Hash::make('password123'),
                    'role' => 'agent',
                    'agency_id' => $gulfAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء سبوكيل لوكالة الخليج
            User::firstOrCreate(
                ['email' => 'khaled@gulf-travel.com'],
                [
                    'name' => 'خالد حسن',
                    'password' => Hash::make('password123'),
                    'role' => 'subagent',
                    'agency_id' => $gulfAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء عميل لوكالة الخليج
            User::firstOrCreate(
                ['email' => 'abdullah@example.com'],
                [
                    'name' => 'عبد الله محمد',
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'agency_id' => $gulfAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
        }
        
        // إنشاء مستخدم متميز للاختبار السريع
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم اختباري',
                'password' => Hash::make('123456'),
                'role' => 'agent',
                'agency_id' => $yemenAgency ? $yemenAgency->id : ($gulfAgency ? $gulfAgency->id : null),
                'status' => 'active',
                'locale' => 'en',
                'theme' => 'dark',
                'email_notifications' => false,
            ]
        );

        // Add Admin User
        User::firstOrCreate(
            ['email' => 'admin@jak.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'locale' => 'en',
                'theme' => 'light',
                'email_notifications' => true,
            ]
        );
    }
}
