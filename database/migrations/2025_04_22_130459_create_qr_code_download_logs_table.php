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
        Schema::create('qr_code_download_logs', function (Blueprint $table) {
            $table->id(); // Log entry ID
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Which event's QR was downloaded
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who downloaded it (nullable if guest/unknown allowed)
            $table->ipAddress('ip_address')->nullable(); // Optional: IP address of downloader
            $table->timestamp('downloaded_at'); // When it was downloaded
            // No need for updated_at usually for logs
            // $table->timestamps(); // Use this if you prefer created_at over downloaded_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_download_logs');
    }
};