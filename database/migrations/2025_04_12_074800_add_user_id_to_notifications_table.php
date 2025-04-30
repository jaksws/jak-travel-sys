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
        if (!Schema::hasTable('notifications')) {
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
        Schema::dropIfExists('notifications');
    }
};
