<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the event_qrcodes table.
     */
    public function up(): void
    {
        Schema::create('event_qrcodes', function (Blueprint $table) {
            // Primary Key for this table (the 'qr_id' you mentioned)
            $table->id();

            // Foreign key linking to the events table
            $table->foreignId('event_id')          // Creates unsigned big integer column named event_id
                  ->unique()                       // Ensure one QR code per event (one-to-one)
                  ->constrained('events')          // Adds foreign key constraint to events.id
                  ->onDelete('cascade');           // If an event is deleted, delete its QR code too

            // Column to store the actual SVG data
            $table->text('svg_data');

            // Standard created_at and updated_at timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Drops the event_qrcodes table.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_qrcodes');
    }
};