<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
