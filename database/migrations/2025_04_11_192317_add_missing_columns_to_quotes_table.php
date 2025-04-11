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
        Schema::table('quotes', function (Blueprint $table) {
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('quotes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            
            // Add currency_id column if it doesn't exist
            if (!Schema::hasColumn('quotes', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->constrained('currencies');
            }
            
            // Add description column if it doesn't exist
            if (!Schema::hasColumn('quotes', 'description')) {
                $table->text('description')->nullable();
            }
            
            // Add valid_until column if it doesn't exist
            if (!Schema::hasColumn('quotes', 'valid_until')) {
                $table->timestamp('valid_until')->nullable();
            }
            
            // Add notes column if it doesn't exist
            if (!Schema::hasColumn('quotes', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $columns = ['user_id', 'currency_id', 'description', 'valid_until', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('quotes', $column)) {
                    if (in_array($column, ['user_id', 'currency_id'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
