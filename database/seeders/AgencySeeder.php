<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;

// Reviewed on 2023-10-01 by John Doe

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تأكد من وجود الوكالات الأساسية دائماً في بيئة الاختبار
        if (app()->environment('testing')) {
            \App\Models\Agency::firstOrCreate(
                ['email' => 'info@yemen-travel.com'],
                [
                    'name' => 'وكالة اليمن للسفر والسياحة',
                    'phone' => '777123456',
                    'address' => 'صنعاء - شارع جمال عبد الناصر',
                    'status' => 'active',
                    'license_number' => 'AG10001'
                ]
            );
            \App\Models\Agency::firstOrCreate(
                ['email' => 'info@gulf-travel.com'],
                [
                    'name' => 'وكالة الخليج للسفريات',
                    'phone' => '777654321',
                    'address' => 'عدن - المنصورة',
                    'status' => 'active',
                    'license_number' => 'AG10002'
                ]
            );
        }

        // Use firstOrCreate to avoid duplicate entries
        Agency::firstOrCreate(
            ['email' => 'info@yemen-travel.com'],
            [
                'name' => 'وكالة اليمن للسفر والسياحة',
                'phone' => '777123456',
                'address' => 'صنعاء - شارع جمال عبد الناصر',
                'status' => 'active',
                'license_number' => 'AG10001'
            ]
        );

        Agency::firstOrCreate(
            ['email' => 'info@gulf-travel.com'],
            [
                'name' => 'وكالة الخليج للسفريات',
                'phone' => '777654321',
                'address' => 'عدن - المنصورة',
                'status' => 'active',
                'license_number' => 'AG10002'
            ]
        );

        Agency::firstOrCreate(
            ['email' => 'info@east-travel.com'],
            [
                'name' => 'وكالة الشرق للسفر',
                'phone' => '777111222',
                'address' => 'حضرموت - المكلا',
                'status' => 'active',
                'license_number' => 'AG10003'
            ]
        );
    }
}
