<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration ensures AUTO_INCREMENT values are set correctly.
     * The values are also set in the table creation migrations,
     * but this serves as a safety net if migrations are run out of order.
     */
    public function up(): void
    {
        $prefix = Schema::getConnection()->getTablePrefix();

        // Set User ID auto increment to start at 100126697 (only if table exists)
        if (Schema::hasTable('users')) {
            $tableName = $prefix.'users';
            DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 100126697");
        }

        // Set Team ID auto increment to start at 30021993 (only if table exists)
        if (Schema::hasTable('teams')) {
            $tableName = $prefix.'teams';
            DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 30021993");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = Schema::getConnection()->getTablePrefix();

        // Reset to default auto increment (1) - only if tables exist
        if (Schema::hasTable('users')) {
            $tableName = $prefix.'users';
            DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");
        }

        if (Schema::hasTable('teams')) {
            $tableName = $prefix.'teams';
            DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");
        }
    }
};
