<?php

namespace App\Observers;

use App\Models\Event; // Your main event model
use App\Models\EventQrcode; // Import the EventQrcode model
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
     * Includes setting initial QR code validity fields.
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

        // Initialize variable
        $qrCodeSvg = null;

        // Try to generate and save the QR code
        try {
            // Check if the event has an ID (it always should in the 'created' event)
            if ($event->id) {
                Log::info("EventObserver: Generating QR for Event ID: {$event->id}");
                // Create the URL the QR code should point to
                $qrCodeUrl = route($routeName, ['event' => $event->id]); // Pass event parameter correctly
                Log::info("EventObserver: Generated URL for QR code: {$qrCodeUrl}");

                // Generate the SVG data for the QR code - Wrapped generation in its own try/catch
                try {
                    $qrCodeSvg = QrCode::size(200)->format('svg')->generate($qrCodeUrl);

                    // *** ADDED DEBUG LOGGING START ***
                    // Log whether the generated SVG is empty or not
                    Log::debug("EventObserver: SVG Data generated for Event ID {$event->id}. Is empty? " . (empty($qrCodeSvg) ? 'YES' : 'NO'));
                    // Log the beginning of the SVG to verify structure (optional, can be verbose)
                    // Log::debug("EventObserver: SVG Start for Event ID {$event->id}: " . substr($qrCodeSvg ?? '', 0, 150));

                    // Explicitly log an error if the generation results in empty data
                    if (empty($qrCodeSvg)) {
                        Log::error("EventObserver: CRITICAL - QR Code generation returned EMPTY svg_data for Event ID {$event->id}! URL used: {$qrCodeUrl}");
                        // Consider throwing exception if empty SVG is unacceptable
                        // throw new \Exception("QR Code generation returned empty data.");
                    }
                    // *** ADDED DEBUG LOGGING END ***

                } catch (\Exception $genException) {
                    // Catch errors specifically from the QrCode::generate call
                    Log::error("EventObserver: EXCEPTION during QR Code *generation* for Event ID {$event->id}: " . $genException->getMessage(), [
                        'url_used' => $qrCodeUrl,
                        'exception' => $genException
                    ]);
                    // Ensure $qrCodeSvg remains null if generation failed
                    $qrCodeSvg = null;
                }


                // Proceed only if SVG generation didn't throw fatal error / wasn't critically empty (if exception thrown above)
                Log::info("EventObserver: Attempting to create related EventQrcode record for Event ID: {$event->id}");

                // Prepare data for the new EventQrcode record
                $qrCodeData = [
                    // Use the potentially null $qrCodeSvg from above
                    'svg_data' => $qrCodeSvg,
                    'event_title' => $event->title,

                    // Initial QR Code Validity Settings
                    'active_from' => null, // Default: No start limit
                    'active_until' => null, // Default: No end limit
                    // 'is_active' uses database default (TRUE)

                    // Example using event times (adjust property names if needed):
                    // 'active_from' => $event->start_time ?? null,
                    // 'active_until' => $event->end_time ?? null,
                ];

                // Use the 'qrcode' relationship to create the record
                $newQrCodeRecord = $event->qrcode()->create($qrCodeData);

                // MODIFIED CHECK: Check if record was created AND if svg_data is NULL
                if ($newQrCodeRecord && is_null($newQrCodeRecord->svg_data)) {
                    // Log if record created but SVG is null (indicates generation failure or empty SVG passed)
                    Log::warning("EventObserver: EventQrcode record created for Event ID {$event->id} (Record ID: {$newQrCodeRecord->id}), BUT svg_data was saved as NULL.", $qrCodeData);
                } elseif ($newQrCodeRecord) {
                    // Log success only if record created AND svg_data seems present
                    Log::info("EventObserver: Successfully created EventQrcode record (ID: {$newQrCodeRecord->id}) with svg_data for Event ID {$event->id}.", ['record_id' => $newQrCodeRecord->id]);
                } else {
                    // Log if the create() method itself failed
                    Log::error("EventObserver: Failed to create EventQrcode record for Event ID {$event->id}. The create() method returned null/false.", $qrCodeData);
                }

            } else {
                Log::warning("EventObserver: Event ID missing in 'created' method.");
            }
        // Catch any broader exceptions during the process
        } catch (\Exception $e) {
            // Avoid logging the specific generation exception again if caught above
            if (!isset($genException) || $e !== $genException) {
                 Log::error("EventObserver: General EXCEPTION occurred for event ID {$event->id}: " . $e->getMessage(), [
                     'exception' => $e
                 ]);
            }
        }
        // Log that the observer method has finished for this specific event
        Log::info("==================== EventObserver::created END for Event ID: {$event->id} ====================");
    }

    // You might add other observer methods like updated(), deleted() if needed
}