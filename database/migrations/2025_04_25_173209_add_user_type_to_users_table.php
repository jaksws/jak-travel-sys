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
        // Add is_admin column if missing
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('role');
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
        // Drop is_admin if exist
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }
};
