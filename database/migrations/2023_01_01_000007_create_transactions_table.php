<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained(); // subagent or customer
            $table->foreignId('quote_id')->nullable()->constrained();
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->string('reference_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('refund_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['payment', 'commission', 'refund']);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
