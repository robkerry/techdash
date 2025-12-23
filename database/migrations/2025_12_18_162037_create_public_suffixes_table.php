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
        Schema::create('public_suffixes', function (Blueprint $table) {
            $table->id();
            $table->string('suffix')->unique();
            $table->string('type')->default('icann'); // icann or private
            $table->timestamps();
            
            $table->index('suffix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_suffixes');
    }
};
