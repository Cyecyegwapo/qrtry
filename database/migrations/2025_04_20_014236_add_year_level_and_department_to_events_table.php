<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds year_level and department columns to the events table.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add columns after 'location' for example
            // Allow NULL if events might be open to all or type doesn't apply
            $table->string('year_level')->nullable()->after('location');
            $table->string('department')->nullable()->after('year_level');
        });
    }

    /**
     * Reverse the migrations.
     * Removes the added columns.
     */
    public function down(): void
    {
        // Use try/catch or Schema::hasColumn if needed for robustness
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['year_level', 'department']);
        });
    }
};