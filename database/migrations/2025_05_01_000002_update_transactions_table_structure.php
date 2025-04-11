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
        // First check if the table exists
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                // Make agency_id nullable as it's not always provided in payment processing
                if (Schema::hasColumn('transactions', 'agency_id')) {
                    $table->foreignId('agency_id')->nullable()->change();
                }
                
                // Add missing columns required for payment processing
                if (!Schema::hasColumn('transactions', 'reference_id')) {
                    $table->string('reference_id')->nullable();
                }
                
                if (!Schema::hasColumn('transactions', 'payment_method')) {
                    $table->string('payment_method')->nullable();
                }
                
                if (!Schema::hasColumn('transactions', 'description')) {
                    $table->string('description')->nullable();
                }
                
                if (!Schema::hasColumn('transactions', 'refunded_at')) {
                    $table->timestamp('refunded_at')->nullable();
                }
                
                if (!Schema::hasColumn('transactions', 'refund_reason')) {
                    $table->string('refund_reason')->nullable();
                }
                
                if (!Schema::hasColumn('transactions', 'refund_reference')) {
                    $table->string('refund_reference')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                // Don't drop columns in down migration to prevent data loss
                // Just revert agency_id to non-nullable if needed
                if (Schema::hasColumn('transactions', 'agency_id')) {
                    $table->foreignId('agency_id')->nullable(false)->change();
                }
            });
        }
    }
};
