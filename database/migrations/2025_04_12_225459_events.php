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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->time('time'); // Storing time as TIME type
            $table->string('location');
            // ADDED: Column to store the QR code SVG data
            // TEXT is suitable for SVG, use LONGTEXT if SVGs might be very large
            // Make it nullable in case generation fails or for older records
            $table->text('qr_code_svg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
