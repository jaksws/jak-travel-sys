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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite does not support dropping foreign keys directly, so we need to handle it differently
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('documents', function (Blueprint $table) {
                // Drop the foreign key constraint first if it exists
                try {
                    $table->dropForeign(['uploaded_by']);
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
                $table->dropColumn('uploaded_by');
            });
        } else {
            // For SQLite, drop the morph index if it exists before renaming
            try {
                \DB::statement('DROP INDEX IF EXISTS documents_documentable_type_documentable_id_index');
            } catch (\Throwable $e) {
                // Ignore if index does not exist
            }
            // 1. Rename the old table
            Schema::rename('documents', 'documents_old');
            // 2. Recreate the table without 'uploaded_by'
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->morphs('documentable');
                $table->enum('visibility', ['public', 'private', 'agency'])->default('public');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            // 3. Copy data from old table to new table (excluding uploaded_by)
            \DB::statement('INSERT INTO documents (id, name, file_path, file_type, size, documentable_type, documentable_id, visibility, notes, created_at, updated_at) SELECT id, name, file_path, file_type, size, documentable_type, documentable_id, visibility, notes, created_at, updated_at FROM documents_old');
            // 4. Drop the old table
            Schema::drop('documents_old');
        }
    }
};
