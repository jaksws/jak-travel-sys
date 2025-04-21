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
        // Add user_id column if it doesn't exist
        if (!Schema::hasColumn('requests', 'user_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }

        // Add customer_id column if it doesn't exist
        if (!Schema::hasColumn('requests', 'customer_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
            });
        }

        // Add agency_id column if it doesn't exist
        if (!Schema::hasColumn('requests', 'agency_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('cascade');
            });
        }

        // Add service_id column if it doesn't exist
        if (!Schema::hasColumn('requests', 'service_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            });
        }

        // Add title column if it doesn't exist
        if (!Schema::hasColumn('requests', 'title')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->string('title')->nullable();
            });
        }

        // Add description column if it doesn't exist
        if (!Schema::hasColumn('requests', 'description')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }

        // Add details column if it doesn't exist
        if (!Schema::hasColumn('requests', 'details')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->text('details')->nullable();
            });
        }

        // Add status column if it doesn't exist
        if (!Schema::hasColumn('requests', 'status')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        }

        // Add required_date column if it doesn't exist
        if (!Schema::hasColumn('requests', 'required_date')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->date('required_date')->nullable();
            });
        }

        // Add requested_date column if it doesn't exist
        if (!Schema::hasColumn('requests', 'requested_date')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->date('requested_date')->nullable();
            });
        }

        // Add priority column if it doesn't exist
        if (!Schema::hasColumn('requests', 'priority')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->string('priority')->nullable();
            });
        }

        // Add notes column if it doesn't exist
        if (!Schema::hasColumn('requests', 'notes')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->text('notes')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop user_id column if it exists
        if (Schema::hasColumn('requests', 'user_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        // Drop customer_id column if it exists
        if (Schema::hasColumn('requests', 'customer_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }

        // Drop agency_id column if it exists
        if (Schema::hasColumn('requests', 'agency_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['agency_id']);
                $table->dropColumn('agency_id');
            });
        }

        // Drop service_id column if it exists
        if (Schema::hasColumn('requests', 'service_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            });
        }

        // Drop title column if it exists
        if (Schema::hasColumn('requests', 'title')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }

        // Drop description column if it exists
        if (Schema::hasColumn('requests', 'description')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Drop details column if it exists
        if (Schema::hasColumn('requests', 'details')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('details');
            });
        }

        // Drop status column if it exists
        if (Schema::hasColumn('requests', 'status')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        // Drop required_date column if it exists
        if (Schema::hasColumn('requests', 'required_date')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('required_date');
            });
        }

        // Drop requested_date column if it exists
        if (Schema::hasColumn('requests', 'requested_date')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('requested_date');
            });
        }

        // Drop priority column if it exists
        if (Schema::hasColumn('requests', 'priority')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }

        // Drop notes column if it exists
        if (Schema::hasColumn('requests', 'notes')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }
};
