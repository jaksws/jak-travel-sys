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
        Schema::table('agencies', function (Blueprint $table) {
            // Add website column if it doesn't exist
            if (!Schema::hasColumn('agencies', 'website')) {
                $table->string('website')->nullable();
            }
            
            // Add description and status columns if they don't exist (also used in tests)
            if (!Schema::hasColumn('agencies', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('agencies', 'license_number')) {
                $table->string('license_number')->nullable();
            }
            
            if (!Schema::hasColumn('agencies', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['website', 'description', 'status']);
            
            // Only drop license_number if we added it
            if (Schema::hasColumn('agencies', 'license_number')) {
                $table->dropColumn('license_number');
            }
        });
    }
};
