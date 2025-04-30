<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'user_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'user_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id');
            });
        }
    }
};
