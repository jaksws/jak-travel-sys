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
            // Add columns needed for payment processing
            $table->string('reference_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('pending')->change();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('refund_reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'reference_id',
                'payment_method',
                'description',
                'refunded_at',
                'refund_reason',
                'refund_reference'
            ]);
            // Note: We can't easily revert the 'status' column change in a down migration
        });
    }
};
