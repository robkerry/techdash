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
        Schema::table('websites', function (Blueprint $table) {
            $table->text('gsc_property')->nullable()->after('domain');
            $table->text('gsc_access_token')->nullable()->after('gsc_property');
            $table->text('gsc_refresh_token')->nullable()->after('gsc_access_token');
            $table->timestamp('gsc_last_refreshed_at')->nullable()->after('gsc_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn([
                'gsc_property',
                'gsc_access_token',
                'gsc_refresh_token',
                'gsc_last_refreshed_at',
            ]);
        });
    }
};
