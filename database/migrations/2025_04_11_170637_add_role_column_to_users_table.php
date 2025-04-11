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
        Schema::table('users', function (Blueprint $table) {
            // إضافة عمود الدور إذا لم يكن موجودًا
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('client')->after('password');
            }
            
            // إضافة عمود معرف الوكالة إذا لم يكن موجودًا
            if (!Schema::hasColumn('users', 'agency_id')) {
                $table->foreignId('agency_id')->nullable()->after('role')->constrained()->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // لا نقوم بحذف عمود الدور في حالة التراجع عن الهجرة
            // لتجنب فقدان البيانات
        });
    }
};
