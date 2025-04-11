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
        if (!Schema::hasTable('requests')) {
            Schema::create('requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->foreignId('agency_id')->nullable()->constrained('agencies')->cascadeOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->text('details')->nullable();
                $table->string('status')->default('pending');
                $table->date('required_date')->nullable();
                $table->date('requested_date')->nullable();
                $table->string('priority')->nullable();
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
        Schema::dropIfExists('requests');
    }
};
