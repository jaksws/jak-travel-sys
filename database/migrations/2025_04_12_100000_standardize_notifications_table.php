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
        // هذا الميجريشن يجب أن يعدل فقط الأعمدة إذا لزم الأمر، ولا ينشئ الجدول إذا كان موجودًا
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        } else {
            // إذا كان الجدول موجودًا، تأكد فقط من الأعمدة القياسية (بدون أي أعمدة مثل user_id أو title أو is_read أو غيرها)
            Schema::table('notifications', function (Blueprint $table) {
                // لا تضف أي أعمدة غير قياسية هنا
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