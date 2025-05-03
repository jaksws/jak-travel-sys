<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index()->after('id');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('contact_email')->nullable()->after('phone');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('status')->default('active'); // Use status instead of is_active
            $table->json('notification_settings')->nullable();
            $table->json('email_settings')->nullable();
            $table->json('commission_settings')->nullable();
            $table->unsignedTinyInteger('price_decimals')->default(2);
            $table->string('price_display_format')->default('symbol_first');
            $table->boolean('auto_convert_prices')->default(true);
            $table->decimal('default_commission_rate', 5, 2)->default(10.00);
            $table->string('default_currency', 10)->default('SAR');
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agencies');
    }
};
