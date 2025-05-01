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
        // حماية إضافية: لا تسمح بتشغيل هذا السييدر إلا في بيئة الاختبار
        if (!app()->environment('testing')) {
            throw new \Exception('UserSeeder can only be run in the testing environment!');
        }

        $isTesting = app()->environment('testing');
        $faker = \Faker\Factory::create();

        // جلب الوكالات المنشأة سابقاً
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        $gulfAgency = Agency::where('email', 'info@gulf-travel.com')->first();
        
        if ($yemenAgency) {
            // إنشاء مستخدم وكيل لوكالة اليمن
            $agencyAdminEmail = $isTesting ? $faker->unique()->safeEmail() : 'admin@yemen-travel.com';
            $agencyAdmin = User::firstOrCreate(
                ['email' => $agencyAdminEmail],
                [
                    'name' => 'مدير وكالة اليمن',
                    'password' => Hash::make('password123'),
                    'role' => 'agency',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء سبوكلاء لوكالة اليمن
            $subagent1Email = $isTesting ? $faker->unique()->safeEmail() : 'ahmed@yemen-travel.com';
            $subagent1 = User::firstOrCreate(
                ['email' => $subagent1Email],
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
            
            $subagent2Email = $isTesting ? $faker->unique()->safeEmail() : 'mohammed@yemen-travel.com';
            $subagent2 = User::firstOrCreate(
                ['email' => $subagent2Email],
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
            $customer1Email = $isTesting ? $faker->unique()->safeEmail() : 'salem@example.com';
            User::firstOrCreate(
                ['email' => $customer1Email],
                [
                    'name' => 'سالم علي',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'agency_id' => $yemenAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            $customer2Email = $isTesting ? $faker->unique()->safeEmail() : 'fatima@example.com';
            User::firstOrCreate(
                ['email' => $customer2Email],
                [
                    'name' => 'فاطمة أحمد',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
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
            $gulfAdminEmail = $isTesting ? $faker->unique()->safeEmail() : 'admin@gulf-travel.com';
            $gulfAdmin = User::firstOrCreate(
                ['email' => $gulfAdminEmail],
                [
                    'name' => 'مدير وكالة الخليج',
                    'password' => Hash::make('password123'),
                    'role' => 'agency',
                    'agency_id' => $gulfAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
            
            // إنشاء سبوكيل لوكالة الخليج
            $subagentEmail = $isTesting ? $faker->unique()->safeEmail() : 'khaled@gulf-travel.com';
            User::firstOrCreate(
                ['email' => $subagentEmail],
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
            $customerEmail = $isTesting ? $faker->unique()->safeEmail() : 'abdullah@example.com';
            User::firstOrCreate(
                ['email' => $customerEmail],
                [
                    'name' => 'عبد الله محمد',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'agency_id' => $gulfAgency->id,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
        }
        
        // إنشاء مستخدم متميز للاختبار السريع
        $testUserEmail = $isTesting ? $faker->unique()->safeEmail() : 'test@example.com';
        User::firstOrCreate(
            ['email' => $testUserEmail],
            [
                'name' => 'مستخدم اختباري',
                'password' => Hash::make('123456'),
                'role' => 'agency',
                'agency_id' => $yemenAgency ? $yemenAgency->id : ($gulfAgency ? $gulfAgency->id : null),
                'status' => 'active',
                'locale' => 'en',
                'theme' => 'dark',
                'email_notifications' => false,
            ]
        );

        // Add Admin User
        $adminUserEmail = $isTesting ? $faker->unique()->safeEmail() : 'admin@jak.com';
        User::firstOrCreate(
            ['email' => $adminUserEmail],
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

        // في بيئة الاختبار: تأكد من وجود مستخدم أدمن ثابت يمكن استخدامه في اختبارات Dusk
        if ($isTesting) {
            User::firstOrCreate(
                ['email' => 'admin@dusk-test.com'],
                [
                    'name' => 'Dusk Admin',
                    'password' => Hash::make('duskpassword'),
                    'role' => 'admin',
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
        }
    }
}
