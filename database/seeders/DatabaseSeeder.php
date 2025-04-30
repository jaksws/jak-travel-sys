<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

// Reviewed on 2023-10-01 by John Doe

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Check if the 'service_requests' table exists before seeding
        if (!Schema::hasTable('service_requests')) {
            $this->command->error("The 'service_requests' table does not exist. Please run the migrations first.");
            return;
        }

        // ترتيب تنفيذ البذور مهم لمراعاة العلاقات بين الجداول
        $this->call([
            // البيانات الأساسية أولاً
            AgencySeeder::class,
            CurrencySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            
            // البيانات التي تعتمد على الجداول السابقة
            RequestSeeder::class,
            QuoteSeeder::class,
        ]);
    }
}
