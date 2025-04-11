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
        Schema::table('transactions', function (Blueprint $table) {
            // Make type column nullable or provide a default value
            if (Schema::hasColumn('transactions', 'type')) {
                $table->string('type')->nullable()->change();
            } else {
                // If the column doesn't exist, create it as nullable
                $table->string('type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert changes if needed
            if (Schema::hasColumn('transactions', 'type')) {
                $table->string('type')->nullable(false)->change();
            }
        });
    }
};
