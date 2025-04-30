<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

return new class extends Migration
{
    public function up()
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Ensure the column doesn't already exist before adding
            if (!Schema::hasColumn('agencies', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index()->after('id');
                // Add foreign key constraint if needed, assuming 'users' table and 'id' column exist
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Check if the column exists before attempting to drop
            if (Schema::hasColumn('agencies', 'user_id')) {
                // Check if using SQLite
                $isSqlite = DB::connection()->getDriverName() === 'sqlite';

                if ($isSqlite) {
                    // SQLite requires dropping the index explicitly before the column
                    // The default index name Laravel creates is usually table_column_index
                    try {
                        // Attempt to drop the index first
                        $table->dropIndex('agencies_user_id_index');
                    } catch (\Exception $e) {
                        // Log or ignore if index doesn't exist or another issue occurs
                        // Log::warning("Could not drop index 'agencies_user_id_index': " . $e->getMessage());
                    } finally {
                        // Always attempt to drop the column
                        $table->dropColumn('user_id');
                    }

                } else {
                    // For other databases like MySQL, dropping the foreign key first (if exists) then the column is usually sufficient
                    // Assuming a foreign key constraint was named 'agencies_user_id_foreign'
                    // if (collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('agencies'))->pluck('name')->contains('agencies_user_id_foreign')) {
                    //     $table->dropForeign(['user_id']);
                    // }
                    $table->dropColumn('user_id');
                }
            }
        });
    }
};
