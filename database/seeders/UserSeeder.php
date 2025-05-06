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
        // Extra protection: Only allow running this seeder in the testing environment
        if (!app()->environment('testing')) {
            // Show a warning and confirmation message to the user in the terminal
            fwrite(STDOUT, "Warning: Running this seeder will affect user data.\n");
            fwrite(STDOUT, "Are you sure you want to continue? Type 'yes' and press Enter to proceed, or anything else to skip: ");
            $handle = fopen ("php://stdin","r");
            sleep(3);
            $line = trim(fgets($handle));
            fclose($handle);
            if ($line !== env('DEFAULT_YES_RESPONSE', 'yes')) {
                fwrite(STDOUT, "UserSeeder skipped.\n");
                return; // Skip only this seeder
            }
        }

        $isTesting = app()->environment('testing');
        $faker = \Faker\Factory::create();

        // Fetch previously created agencies
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        $gulfAgency = Agency::where('email', 'info@gulf-travel.com')->first();
        
        if ($yemenAgency) {
            // Create agency admin for Yemen agency
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
            
            // Create subagents for Yemen agency
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
            
            // Create customers for Yemen agency
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
            // Create agency admin for Gulf agency
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
            
            // Create subagent for Gulf agency
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
            
            // Create customer for Gulf agency
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
        
        // Create a premium user for quick testing
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
                'is_admin' => 1,
                'status' => 'active',
                'locale' => 'en',
                'theme' => 'light',
                'email_notifications' => true,
            ]
        );

        // In testing environment: Ensure a fixed admin user exists for Dusk tests
        if ($isTesting) {
            $admin = User::updateOrCreate(
                ['email' => 'admin@dusk-test.com'],
                [
                    'name' => 'Dusk Admin',
                    'password' => Hash::make('duskpassword'),
                    'role' => 'admin',
                    'is_admin' => 1,
                    'status' => 'active',
                    'locale' => 'ar',
                    'theme' => 'light',
                    'email_notifications' => true,
                ]
            );
        }
        // Timer logic removed as seeders should not require user interaction
        fwrite(STDOUT, "Timer completed.\n");

        // Check to ensure the user seeder is working correctly
        $userCount = User::count();
        if ($userCount > 0) {
            fwrite(STDOUT, "UserSeeder completed successfully. Total users: $userCount\n");
        } else {
            fwrite(STDOUT, "UserSeeder failed. No users were created.\n");
        }

        // Add default admin user
        User::firstOrCreate(
            ['email' => 'admin@jaksws.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin@1211'),
                'role' => 'admin',
                'status' => 'active',
                'locale' => 'en',
                'theme' => 'light',
                'email_notifications' => true,
            ]
        );
    }
}
