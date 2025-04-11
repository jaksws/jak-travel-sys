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
        if (Schema::hasTable('notifications')) {
            // First check if we need to add the message column
            if (!Schema::hasColumn('notifications', 'message')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->text('message')->nullable();
                });
            }
            
            // Add notifiable_type and notifiable_id if needed
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('notifiable_type')->nullable();
                    $table->unsignedBigInteger('notifiable_id')->nullable();
                });
            }
            
            // Fix any NOT NULL constraints by making sure message has a default value
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('UPDATE notifications SET message = "System notification" WHERE message IS NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for this fix
    }
};
