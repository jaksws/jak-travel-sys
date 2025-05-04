<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO code (USD, SAR, EUR)
            $table->string('name');
            $table->string('symbol');
            $table->string('symbol_position')->default('before'); // تمت إضافة العمود هنا
            $table->boolean('is_default')->default(false);
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // Base rate compared to default currency
            $table->string('status')->default('active'); // Use status instead of is_active
            $table->timestamps();
        });

        // Adding currency field to services and agencies tables
        // Schema::table('services', function (Blueprint $table) {
        //     $table->string('currency_code', 3)->default('SAR')->after('base_price');
        // });

        // Schema::table('agencies', function (Blueprint $table) {
        //     $table->string('default_currency', 3)->default('SAR')->after('name');
        // });

        // Modify quotes table to store currency information
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('SAR')->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
        
        Schema::table('currencies', function (Blueprint $table) {
            if (Schema::hasColumn('currencies', 'symbol_position')) {
                $table->dropColumn('symbol_position');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('currency_code');
        });

        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'default_currency')) {
                $table->dropColumn('default_currency');
            }
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('currency_code');
        });
    }
};
