<?php

namespace App\Observers;

use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import the QrCode facade
use Illuminate\Support\Facades\Log; // Optional: for logging errors

class EventObserver
{
    /**
     * Handle the Event "created" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function created(Event $event): void
    {
         // Check if route exists (important for console commands like seeding)
         if (!Route::has('events.attendance.record')) {
             Log::warning("EventObserver: Route [events.attendance.record] not found. Skipping QR generation for Event ID: {$event->id}");
             return;
         }
        try {
            if ($event->id) {
                $qrCodeUrl = route('events.attendance.record', $event->id);
                $qrCodeSvg = QrCode::size(200)->format('svg')->generate($qrCodeUrl);
                // Save the generated SVG to the database column
                $event->updateQuietly(['qr_code_svg' => $qrCodeSvg]);
            }
        } catch (\Exception $e) {
            Log::error("EventObserver: Failed QR for event ID {$event->id}: " . $e->getMessage());
        }
    }
    // You can also add 'updated' method if QR needs regeneration on updates,
    // but it's likely not needed if the URL only depends on the ID.
    // public function updated(Event $event): void { ... }

    // Define other event methods if needed (deleted, restored, etc.)
}
