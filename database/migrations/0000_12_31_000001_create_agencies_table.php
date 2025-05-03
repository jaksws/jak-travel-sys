<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('agencies');
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('license_number')->unique();
            $table->decimal('default_commission_rate', 5, 2)->default(10.00);
            $table->string('default_currency', 10)->default('SAR');
            $table->unsignedTinyInteger('price_decimals')->default(2);
            $table->string('price_display_format')->default('symbol_first');
            $table->json('notification_settings')->nullable();
            $table->json('email_settings')->nullable();
            $table->json('commission_settings')->nullable();
            $table->string('theme_color')->nullable();
            $table->string('agency_language')->nullable();
            $table->string('social_media_instagram')->nullable();
            $table->string('social_media_twitter')->nullable();
            $table->string('social_media_facebook')->nullable();
            $table->string('social_media_linkedin')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agencies');
    }
};
