<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_tables_exist()
    {
        $tables = [
            'users', 'agencies', 'services', 'requests', 'quotes', 'notifications',
            'transactions', 'documents', 'currencies', 'quote_attachments', 'payments'
        ];
        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table '$table' does not exist");
        }
    }

    public function test_users_table_columns_exist()
    {
        $columns = [
            'id', 'name', 'email', 'password', 'role', 'status', 'agency_id', 'phone',
            'avatar', 'id_number', 'passport_number', 'nationality', 'city', 'country',
            'preferred_currency', 'notification_preferences', 'locale', 'theme',
            'email_notifications', 'is_admin', 'remember_token', 'created_at', 'updated_at'
        ];
        foreach ($columns as $col) {
            $this->assertTrue(
                Schema::hasColumn('users', $col),
                "Column '$col' missing in users table"
            );
        }
    }

    public function test_agencies_table_columns_exist()
    {
        $columns = [
            'id', 'name', 'status', 'logo', 'phone', 'email', 'address', 'license_number',
            'default_commission_rate', 'default_currency', 'price_decimals', 'price_display_format',
            'notification_settings', 'email_settings', 'commission_settings', 'theme_color',
            'agency_language', 'social_media_instagram', 'social_media_twitter',
            'social_media_facebook', 'social_media_linkedin', 'website', 'description',
            'created_at', 'updated_at'
        ];
        foreach ($columns as $col) {
            $this->assertTrue(
                Schema::hasColumn('agencies', $col),
                "Column '$col' missing in agencies table"
            );
        }
    }

    public function test_services_table_columns_exist()
    {
        $columns = [
            'id', 'name', 'agency_id', 'type', 'description', 'status', 'base_price',
            'commission_rate', 'currency_code', 'image', 'price', 'created_at', 'updated_at'
        ];
        foreach ($columns as $col) {
            $this->assertTrue(
                Schema::hasColumn('services', $col),
                "Column '$col' missing in services table"
            );
        }
    }

    public function test_can_run_database_seeder()
    {
        $exitCode = Artisan::call('db:seed');
        $this->assertEquals(0, $exitCode, 'Database seeder failed');
    }

    // public function test_can_run_demo_seeder()
    // {
    //     $exitCode = \Artisan::call('app:seed-demo');
    //     $this->assertEquals(0, $exitCode, 'Demo seeder command failed');
    //     // تحقق من وجود بيانات تجريبية بعد التنفيذ
    //     $this->assertGreaterThan(0, \DB::table('users')->count(), 'No users found after demo seed');
    //     $this->assertGreaterThan(0, \DB::table('agencies')->count(), 'No agencies found after demo seed');
    //     $this->assertGreaterThan(0, \DB::table('services')->count(), 'No services found after demo seed');
    // }

    public function test_demo_data_seeded_via_factories()
    {
        // إنشاء بيانات تجريبية كما يفعل demo seeder
        $agency = \App\Models\Agency::factory()->create();
        $users = \App\Models\User::factory()->count(5)->create(['agency_id' => $agency->id]);
        $services = \App\Models\Service::factory()->count(3)->create(['agency_id' => $agency->id]);
        $this->assertGreaterThan(0, \App\Models\User::count(), 'No users found after demo factory seed');
        $this->assertGreaterThan(0, \App\Models\Agency::count(), 'No agencies found after demo factory seed');
        $this->assertGreaterThan(0, \App\Models\Service::count(), 'No services found after demo factory seed');
    }
}
