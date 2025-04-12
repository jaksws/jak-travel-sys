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
        // First, backup any existing notifications
        if (Schema::hasTable('notifications')) {
            // Create a backup table
            Schema::create('notifications_backup_'.time(), function (Blueprint $table) {
                $table->id();
                $table->json('data')->nullable();
                $table->timestamps();
            });

            // Copy data to backup
            $backupTable = 'notifications_backup_'.time();
            DB::statement("INSERT INTO {$backupTable} (id, data, created_at, updated_at) 
                          SELECT id, 
                                 JSON_OBJECT(
                                     'title', COALESCE(title, ''),
                                     'message', COALESCE(message, ''),
                                     'type', COALESCE(type, 'general'),
                                     'user_id', user_id,
                                     'is_read', COALESCE(is_read, false),
                                     'data', COALESCE(data, '{}'),
                                     'link', link
                                 ),
                                 created_at,
                                 updated_at
                          FROM notifications");

            // Drop the old table
            Schema::dropIfExists('notifications');
        }
        
        // Create the standard Laravel notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};