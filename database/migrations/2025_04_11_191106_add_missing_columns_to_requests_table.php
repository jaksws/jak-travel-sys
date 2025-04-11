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
        // We'll skip this migration as we've incorporated all these columns
        // in the more recent create_requests_table migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to revert as we've skipped the up method
    }
};
