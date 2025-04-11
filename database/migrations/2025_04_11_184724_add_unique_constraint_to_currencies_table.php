<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration as the index already exists
        // This avoids the error when running migrations
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here since we're skipping the up migration
    }
};
