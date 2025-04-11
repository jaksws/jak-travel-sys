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
        // Only create the table if it doesn't already exist
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('subagent_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->decimal('price', 10, 2);
                $table->foreignId('currency_id')->nullable()->constrained('currencies');
                $table->text('description')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('valid_until')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in the down migration to avoid losing data
        // Schema::dropIfExists('quotes');
    }
};
