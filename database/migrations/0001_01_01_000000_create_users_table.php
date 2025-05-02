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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('customer'); // Consolidated role
            $table->string('status')->default('active'); // Consolidated status
            $table->string('avatar')->nullable();
            $table->string('id_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('nationality')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('preferred_currency')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->string('locale', 10)->default('ar');
            $table->string('theme')->nullable(); // Consolidated theme preference
            $table->boolean('email_notifications')->default(true); // Defaulting to true, adjust if needed
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
