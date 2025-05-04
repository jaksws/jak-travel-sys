<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->text('description')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->foreignId('subagent_id')->nullable()->constrained('users');
            $table->decimal('price', 10, 2);
            $table->string('currency_code', 3)->default('SAR');
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->text('details')->nullable();
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'expired',
                'paid',
                'unpaid',
                'canceled',
                'agency_approved',
                'agency_rejected',
                'customer_approved',
                'customer_rejected',
            ])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
};
