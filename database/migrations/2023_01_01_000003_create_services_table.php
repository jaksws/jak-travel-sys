<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('services');
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->string('name');
            $table->enum('type', [
                'security_approval',
                'transport',
                'transportation',
                'hajj',
                'umrah',
                'hajj_umrah',
                'flight',
                'flight_ticket',
                'visa',
                'passport',
            ]);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
