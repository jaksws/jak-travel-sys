<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('details')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('pending');
            $table->date('requested_date')->nullable();
            $table->date('required_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
