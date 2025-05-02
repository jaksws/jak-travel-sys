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
        // إضافة العمود license_number إلى جدول agencies
        if (!Schema::hasColumn('agencies', 'license_number')) {
            Schema::table('agencies', function (Blueprint $table) {
                $table->string('license_number')->after('name')->nullable();
            });
        }

        // إضافة العمود price إلى جدول services
        if (!Schema::hasColumn('services', 'price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->after('description')->nullable();
            });
        }

        // إضافة عمود title إلى جدول notifications إذا كان مطلوباً
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'title')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('title')->nullable();
            });
        }
        
        // تعديل عمود title في جدول notifications ليكون nullable
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'title')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('title')->nullable()->change();
            });
        }
        
        // إضافة عمود symbol_position إلى جدول currencies إذا لم يكن موجوداً
        if (Schema::hasTable('currencies') && !Schema::hasColumn('currencies', 'symbol_position')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->string('symbol_position')->default('before')->after('symbol');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف العمود license_number من جدول agencies
        if (Schema::hasColumn('agencies', 'license_number')) {
            Schema::table('agencies', function (Blueprint $table) {
                $table->dropColumn('license_number');
            });
        }

        // حذف العمود price من جدول services
        if (Schema::hasColumn('services', 'price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
        
        // حذف عمود title من جدول notifications إذا كان موجوداً
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'title')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        
        // حذف عمود symbol_position من جدول currencies إذا كان موجوداً
        if (Schema::hasTable('currencies') && Schema::hasColumn('symbol_position')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->dropColumn('symbol_position');
            });
        }
    }
};
