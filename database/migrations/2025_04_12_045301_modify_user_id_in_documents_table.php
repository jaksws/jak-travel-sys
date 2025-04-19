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
        // نهج أبسط للتعامل مع الأعمدة في SQLite
        // نتحقق من وجود الجدول والعمود قبل محاولة تعديله
        if (Schema::hasTable('documents')) {
            // إذا كان الجدول موجودًا، نتحقق من وجود قيد NOT NULL للعمود user_id
            // وبما أنه من الصعب تغيير الخصائص في SQLite، نضيف عمود uploaded_by بدلًا منه
            // وهذا العمود سيكون موجودًا من الهجرة السابقة

            // هنا نتأكد من أن عمود user_id موجود ويمكن أن يكون NULL
            if (Schema::hasColumn('documents', 'user_id')) {
                // حل بديل: نسخ البيانات من user_id إلى uploaded_by إذا كان فارغًا
                DB::statement('UPDATE documents SET uploaded_by = user_id WHERE uploaded_by IS NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة للتنفيذ في حالة التراجع
    }
};
