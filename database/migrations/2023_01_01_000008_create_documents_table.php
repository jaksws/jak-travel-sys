<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->unsignedBigInteger('size')->nullable(); // rename file_size to size
                $table->morphs('documentable'); // Polymorphic relation
                $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // uploader reference
                $table->enum('visibility', ['private', 'agency', 'customer', 'public'])->default('private');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
