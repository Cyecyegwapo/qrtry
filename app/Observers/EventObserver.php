<?php

namespace App\Observers;

use App\Models\Event; // Your main event model
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Route; // For checking routes
use SimpleSoftwareIO\QrCode\Facades\QrCode; // For generating QR codes

// Listens for events on the Event model (like 'created')
class EventObserver
{
    /**
     * Handle the Event "created" event.
     * Automatically runs after a new Event record is saved to the database.
     * Generates the QR code and saves it to the related event_qrcodes table.
     * Includes detailed logging for debugging.
     *
     * @param  \App\Models\Event  $event The Event model instance that was just created.
     * @return void
     */
    public function created(Event $event): void
    {
        // Log that the observer method has started for this specific event
        Log::info("==================== EventObserver::created START for Event ID: {$event->id} ====================");

        $routeName = 'events.attendance.record'; // The named route for the QR code URL

        // Check if the required route exists before proceeding
        if (!Route::has($routeName)) {
            Log::warning("EventObserver: Route [{$routeName}] not found. Skipping QR generation for Event ID: {$event->id}");
            Log::info("==================== EventObserver::created END (Route Missing) for Event ID: {$event->id} ====================");
            return; // Exit the method if the route doesn't exist
        }

        // Try to generate and save the QR code
        try {
            // Check if the event has an ID (it always should in the 'created' event)
            if ($event->id) {
                Log::info("EventObserver: Generating QR for Event ID: {$event->id}");
                // Create the URL the QR code should point to
                $qrCodeUrl = route($routeName, $event->id);
                // Generate the SVG data for the QR code
                $qrCodeSvg = QrCode::size(200)->format('svg')->generate($qrCodeUrl);

                Log::info("EventObserver: Attempting to create related EventQrcode record for Event ID: {$event->id}");

                // Use the 'qrcode' relationship (defined in Event model) to create
                // a new record in the 'event_qrcodes' table, automatically setting the event_id.
                $newQrCodeRecord = $event->qrcode()->create([
                    'svg_data' => $qrCodeSvg, // The column name in event_qrcodes table
                    'event_title' => $event->title, // <-- The added part
                ]);

                // Check if the record creation was successful
                if ($newQrCodeRecord) {
                     Log::info("EventObserver: Successfully created EventQrcode record (ID: {$newQrCodeRecord->id}) for Event ID {$event->id}");
                } else {
                     // This might happen if ->create() fails for some reason (e.g., DB error, validation on model)
                     Log::error("EventObserver: Failed to create EventQrcode record for Event ID {$event->id}. The create() method returned null/false.");
                }

            } else {
                 // This case should theoretically not happen in the 'created' event
                 Log::warning("EventObserver: Event ID missing in 'created' method for some reason.");
            }
        // Catch any exceptions during the process
        } catch (\Exception $e) {
            Log::error("EventObserver: EXCEPTION occurred for event ID {$event->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString() // Log stack trace for debugging
            ]);
        }
        // Log that the observer method has finished for this specific event
        Log::info("==================== EventObserver::created END for Event ID: {$event->id} ====================");
    }

    // You might add other observer methods like updated(), deleted() if needed
}