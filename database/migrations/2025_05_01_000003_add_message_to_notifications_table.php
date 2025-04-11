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
        // Add message column to notifications table if it doesn't exist
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'message')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->text('message')->nullable();
            });
        }

        // Add notifiable_type and notifiable_id columns if they don't exist
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'notifiable_type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('notifiable_type')->nullable();
                $table->unsignedBigInteger('notifiable_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'message')) {
                    $table->dropColumn('message');
                }
                if (Schema::hasColumn('notifications', 'notifiable_type')) {
                    $table->dropColumn(['notifiable_type', 'notifiable_id']);
                }
            });
        }
    }
};
