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
        Schema::table('notifications', function (Blueprint $table) {
            // إضافة الأعمدة المطلوبة إذا لم تكن موجودة بالفعل
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'link')) {
                $table->string('link')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'message', 'type', 'link']);
        });
    }
};
