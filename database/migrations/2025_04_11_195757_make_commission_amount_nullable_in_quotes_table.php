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
        // For SQLite, we need a different approach
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create a temporary table with the desired schema
            if (!Schema::hasTable('quotes_temp')) {
                Schema::create('quotes_temp', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->foreignId('subagent_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->decimal('price', 10, 2);
                    $table->decimal('commission_amount', 10, 2)->nullable(); // Make nullable
                    $table->foreignId('currency_id')->nullable()->constrained('currencies');
                    $table->text('description')->nullable();
                    $table->text('details')->nullable();
                    $table->string('status')->default('pending');
                    $table->string('currency_code')->nullable();
                    $table->text('rejection_reason')->nullable();
                    $table->timestamp('valid_until')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            
                // Copy data from the original table to the temporary table
                DB::statement('INSERT INTO quotes_temp 
                    SELECT id, request_id, user_id, subagent_id, price, commission_amount, 
                           currency_id, description, details, status, currency_code, rejection_reason, 
                           valid_until, notes, created_at, updated_at 
                    FROM quotes');
                
                // Drop the original table
                Schema::dropIfExists('quotes');
                
                // Rename the temporary table to the original table name
                Schema::rename('quotes_temp', 'quotes');
            }
        } else {
            // For MySQL/PostgreSQL
            Schema::table('quotes', function (Blueprint $table) {
                $table->decimal('commission_amount', 10, 2)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For non-SQLite databases
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('quotes', function (Blueprint $table) {
                $table->decimal('commission_amount', 10, 2)->nullable(false)->change();
            });
        }
        // For SQLite, we can't easily revert this change without the same process in reverse
    }
};
