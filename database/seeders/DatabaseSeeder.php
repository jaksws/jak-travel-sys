<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Check if the 'requests' table exists before seeding
        if (!Schema::hasTable('requests')) {
            $this->command->error("The 'requests' table does not exist. Please run the migrations first.");
            return;
        }

        // ترتيب تنفيذ البذور مهم لمراعاة العلاقات بين الجداول
        $this->call([
            // البيانات الأساسية أولاً
            UserSeeder::class,
            AgencySeeder::class,
            CurrencySeeder::class,
            ServiceSeeder::class,
            
            // البيانات التي تعتمد على الجداول السابقة
            RequestSeeder::class,
            QuoteSeeder::class,
        ]);
    }
}
