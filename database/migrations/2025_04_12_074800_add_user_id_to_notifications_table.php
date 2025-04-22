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
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                // حذف أي كود يضيف user_id من migration 2025_04_12_074800_add_user_id_to_notifications_table.php
            });
        } else {
            // إنشاء الجدول إذا لم يكن موجودًا
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message')->nullable();
                $table->string('type')->default('general');
                $table->string('link')->nullable();
                $table->boolean('is_read')->default(false);
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // حذف أي كود يضيف user_id من migration 2025_04_12_074800_add_user_id_to_notifications_table.php
        });
    }
};
