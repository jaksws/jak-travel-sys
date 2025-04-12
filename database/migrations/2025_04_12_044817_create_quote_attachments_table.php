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
        // إنشاء جدول جديد فقط إذا لم يكن موجودًا
        if (!Schema::hasTable('quote_attachments')) {
            Schema::create('quote_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quote_id')->constrained()->onDelete('cascade');
                $table->string('file_name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_type')->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->text('description')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        } else {
            // إذا كان الجدول موجودًا، تأكد من وجود الأعمدة المطلوبة
            Schema::table('quote_attachments', function (Blueprint $table) {
                if (!Schema::hasColumn('quote_attachments', 'file_name')) {
                    $table->string('file_name')->nullable();
                }
                if (!Schema::hasColumn('quote_attachments', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
                if (!Schema::hasColumn('quote_attachments', 'file_type')) {
                    $table->string('file_type')->nullable();
                }
                if (!Schema::hasColumn('quote_attachments', 'file_size')) {
                    $table->unsignedBigInteger('file_size')->default(0);
                }
                if (!Schema::hasColumn('quote_attachments', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('quote_attachments', 'uploaded_by')) {
                    $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_attachments');
    }
};
