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
        // لا يمكن تعديل الـ enum في SQLite مباشرة، لذا نستخدم حلاً بديلاً
        // عند استخدام SQLite، يتم إضافة وسم خاص بالمسؤول في جدول المستخدمين        
        if (Schema::hasColumn('users', 'is_admin')) {
            return; // الحقل موجود بالفعل
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('is_active');
        });
        
        // تحديث أي مستخدمين تم إنشاؤهم كمسؤولين في السابق
        $roleColumn = $this->getRoleColumn();
        if ($roleColumn) {
            DB::table('users')
                ->where($roleColumn, 'admin')
                ->where(function($query) {
                    $query->where('email', 'like', '%admin%')
                          ->orWhere('name', 'like', '%admin%');
                })
                ->update(['is_admin' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
        });
    }
    
    /**
     * تحديد عمود الدور المستخدم في النظام
     */
    private function getRoleColumn(): ?string
    {
        if (Schema::hasColumn('users', 'role')) {
            return 'role';
        } elseif (Schema::hasColumn('users', 'user_type')) {
            return 'user_type';
        } elseif (Schema::hasColumn('users', 'type')) {
            return 'type';
        }
        
        return null;
    }
};
