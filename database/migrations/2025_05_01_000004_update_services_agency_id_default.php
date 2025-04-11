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
        Schema::table('services', function (Blueprint $table) {
            // Make agency_id nullable to fix the issue in tests
            if (Schema::hasColumn('services', 'agency_id')) {
                // Use a default value if possible or make it nullable
                $table->foreignId('agency_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'agency_id')) {
                $table->foreignId('agency_id')->nullable(false)->change();
            }
        });
    }
};
