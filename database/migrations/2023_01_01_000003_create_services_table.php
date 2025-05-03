<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['security_approval', 'transportation', 'hajj_umrah', 'flight', 'passport']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
