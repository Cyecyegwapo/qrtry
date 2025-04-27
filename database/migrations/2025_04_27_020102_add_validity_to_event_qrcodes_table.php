<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Make sure the class name matches the filename Laravel generates
// (e.g., AddValidityToEventQrcodesTable)
return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds the columns needed for QR code time frame and active status.
     */
    public function up(): void
    {
        // Use Schema::table since the 'event_qrcodes' table should already exist
        Schema::table('event_qrcodes', function (Blueprint $table) {

            // --- Add these columns ---

            // Column to store the start date/time for QR code validity
            // ->after('svg_data') places it after the svg_data column. Adjust if your column order differs.
            // If 'svg_data' doesn't exist yet, you might place it after 'event_title' or 'event_id'.
            $table->dateTime('active_from')->nullable()->after('svg_data'); // CHANGE 'svg_data' if needed

            // Column to store the end date/time for QR code validity
            $table->dateTime('active_until')->nullable()->after('active_from');

            // Column for admin enable/disable toggle (force stop)
            // Defaults to true (enabled) when a new record is created
            $table->boolean('is_active')->default(true)->after('active_until');

        });
    }

    /**
     * Reverse the migrations.
     * Defines how to remove the columns if you roll back this migration.
     */
    public function down(): void
    {
        Schema::table('event_qrcodes', function (Blueprint $table) {
            // Drop the columns if they exist (important for rollback safety)
             if (Schema::hasColumn('event_qrcodes', 'active_from')) {
                 $table->dropColumn('active_from');
             }
             if (Schema::hasColumn('event_qrcodes', 'active_until')) {
                 $table->dropColumn('active_until');
             }
             if (Schema::hasColumn('event_qrcodes', 'is_active')) {
                 $table->dropColumn('is_active');
             }
             // Alternatively, a simpler drop if you're sure they exist:
             // $table->dropColumn(['active_from', 'active_until', 'is_active']);
        });
    }
};
