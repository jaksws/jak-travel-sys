<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // نهج أكثر دقة وتوافق مع بيئة الاختبار
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title')->default('إشعار جديد');
                $table->text('message')->nullable();
                $table->string('type')->default('general');
                // Commenting out the addition of the 'is_read' column to avoid duplication
                // $table->boolean('is_read')->default(false);
                $table->json('data')->nullable();
                $table->string('link')->nullable();
                $table->timestamps();
            });
            return;
        }

        // إضافة كل عمود مطلوب بشكل منفصل إذا كان الجدول موجودًا بالفعل
        if (Schema::hasTable('notifications')) {
            if (!Schema::hasColumn('notifications', 'title')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('title')->default('إشعار جديد')->after('id');
                });
            }
            
            if (!Schema::hasColumn('notifications', 'message')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->text('message')->nullable()->after('title');
                });
            }
            
            if (!Schema::hasColumn('notifications', 'type')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('type')->default('general')->after('message');
                });
            }
            
            if (!Schema::hasColumn('notifications', 'link')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('link')->nullable()->after('data');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا داعي لتنفيذ عملية عكسية لأن هذه هجرة إصلاحية
    }
};
