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
        // Add user_type column if missing
        if (!Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_type')->default('client')->after('role');
            });
        }
        // Backfill user_type from role
        DB::table('users')->update(['user_type' => DB::raw('role')]);
        // Add is_admin column if missing
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('user_type');
            });
        }
        // Backfill is_admin flag for admins
        DB::table('users')->update(['is_admin' => DB::raw("CASE WHEN role='admin' THEN 1 ELSE 0 END")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop is_admin and user_type if exist
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
        if (Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }
    }
};
