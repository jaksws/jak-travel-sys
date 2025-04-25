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
        // Add locale column if it does not exist
        if (!Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('locale', 10)->default('ar')->after('remember_token');
            });
        }
        // Add theme_preference column if it does not exist
        if (!Schema::hasColumn('users', 'theme_preference')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('theme_preference', 20)->default('light')->after('locale');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['locale', 'theme_preference']);
        });
    }
};
