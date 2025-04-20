<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds event_title column to the event_qrcodes table.
     */
    public function up(): void
    {
        Schema::table('event_qrcodes', function (Blueprint $table) {
            // Add column after event_id
            // Making it nullable just in case, though event should always have title
            $table->string('event_title')->nullable()->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     * Removes the event_title column.
     */
    public function down(): void
    {
        Schema::table('event_qrcodes', function (Blueprint $table) {
            // Make sure the column exists before trying to drop if needed
            if (Schema::hasColumn('event_qrcodes', 'event_title')) {
                $table->dropColumn('event_title');
            }
        });
    }
};