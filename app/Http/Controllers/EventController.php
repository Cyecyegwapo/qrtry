<?php

namespace App\Http\Controllers;

// Essential imports (add others as needed for your controller)
use App\Http\Requests\EventRequest; // Your form request for validation
use App\Repositories\EventRepository; // Your repository for events
use App\Repositories\AttendanceRepository; // Your repository for attendance
use App\Models\Event; // Your Event model
use Illuminate\Http\RedirectResponse; // For redirects
use Illuminate\Http\Request; // Standard request object
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\View\View; // For returning views
use Illuminate\Support\Facades\Auth; // For authentication

// Manages actions related to events
class EventController extends Controller
{
    // Repository properties injected via constructor
    protected EventRepository $eventRepository;
    protected AttendanceRepository $attendanceRepository;

    /**
     * Constructor to inject dependencies (repositories).
     */
    public function __construct(EventRepository $eventRepository, AttendanceRepository $attendanceRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->attendanceRepository = $attendanceRepository;
        // $this->middleware('admin')->only([...]); // Apply middleware if needed
    }

    /**
     * Display a listing of the events.
     */
    public function index(): View
    {
        $events = $this->eventRepository->getAll();
        return view('events.index', compact('events')); // Ensure view exists
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('events.create'); // Ensure view exists
    }

    /**
     * Store a newly created event in storage.
     * This method now includes detailed logging for debugging observer issues.
     */
    public function store(EventRequest $request): RedirectResponse
    {
        // --- Start Debug Logging ---
        Log::info('==================== EventController::store START ====================');
        $validatedData = $request->validated();
        Log::info('[Store] Event data validated.', $validatedData);

        try {
            Log::info('[Store] Attempting event creation via eventRepository->create()...');
            // Create the event record using the repository
            $event = $this->eventRepository->create($validatedData);

            // Log details about the creation attempt
            if ($event) {
                Log::info('[Store] EventRepository::create executed successfully.');
                Log::info('[Store] Created object class: ' . get_class($event));
                Log::info('[Store] Created Event ID: ' . $event->id);

                // Check if it's the correct model type (needed for observer)
                if ($event instanceof \App\Models\Event) {
                     Log::info('[Store] CONFIRMED: Created object IS an instance of App\Models\Event.');
                } else {
                     Log::error('[Store] CRITICAL: Created object is NOT an instance of App\Models\Event! It is: ' . get_class($event));
                }

                // Check if the 'created' event should fire (wasRecentlyCreated flag)
                if ($event->wasRecentlyCreated) {
                    Log::info('[Store] Event model wasRecentlyCreated is TRUE.');
                } else {
                     Log::warning('[Store] Event model wasRecentlyCreated is FALSE.');
                }

            } else {
                Log::error('[Store] EventRepository::create returned null or false. Event creation failed.');
            }
            // --- End Debug Logging ---

            // Handle failure case where event wasn't created
            if (!$event) {
                 Log::error('[Store] Redirecting back with error because $event is null/false.');
                 return redirect()->back()->with('error', 'Failed to create event in repository.')->withInput();
            }

            // If successful, redirect to the index page
            Log::info('[Store] Redirecting to events.index...');
            Log::info('==================== EventController::store END ======================');
            return redirect()->route('events.index')->with('success', 'Event created successfully.');

        // Catch any exceptions during the process
        } catch (\Exception $e) {
            Log::error('[Store] EXCEPTION caught during event creation process: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            Log::info('==================== EventController::store END WITH EXCEPTION ============');
            return redirect()->back()->with('error', 'An unexpected error occurred while creating the event.')->withInput();
        }
    }

    /**
     * Display the specified event.
     */
     // Recommended: Use Route Model Binding for $event
    public function show(Event $event): View // Changed int $id to Event $event
    {
        // $event = $this->eventRepository->getById($id); // No longer needed with RMB
        $attendanceCount = $this->attendanceRepository->getEventAttendanceCount($event->id); // Use $event->id
        return view('events.show', compact('event', 'attendanceCount')); // Ensure view exists
    }

    /**
     * Show the form for editing the specified event.
     */
     // Recommended: Use Route Model Binding
    public function edit(Event $event): View // Changed int $id to Event $event
    {
        // $event = $this->eventRepository->getById($id); // No longer needed with RMB
        return view('events.edit', compact('event')); // Ensure view exists
    }

    /**
     * Update the specified event in storage.
     */
     // Recommended: Use Route Model Binding
    public function update(EventRequest $request, Event $event): RedirectResponse // Changed int $id to Event $event
    {
        $this->eventRepository->update($event->id, $request->validated()); // Use $event->id
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
     // Recommended: Use Route Model Binding
    public function destroy(Event $event): RedirectResponse // Changed int $id to Event $event
    {
        $this->eventRepository->delete($event->id); // Use $event->id
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Display the QR Code page for the specified event.
     * Fetches QR code from the related event_qrcodes table.
     */
     // Recommended: Use Route Model Binding
    public function generateQrCode(Event $event): View|RedirectResponse
    {
        // Load the related QR code data using the 'qrcode' relationship
        // The '?->' null-safe operator prevents errors if the qrcode record doesn't exist yet
        $qrCodeSvg = $event->qrcode?->svg_data;

        // Check if the QR code exists in the related table
        if (empty($qrCodeSvg)) {
            Log::warning("[generateQrCode] EventQrcode record or svg_data missing for event ID {$event->id}. Check Observer logs for creation details.");
            // Redirect back to the event page with an error message
            return redirect()->route('events.show', $event->id)->with('error', 'QR code is not available for this event yet.');
        }

        // Pass the event and the SVG string to the view
        return view('events.qrcode', compact('event', 'qrCodeSvg')); // Ensure view exists
    }

    /**
     * Record attendance using QR Code scan.
     */
     // Recommended: Use Route Model Binding
    public function recordAttendance(Request $request, Event $event): RedirectResponse // Changed int $id to Event $event
    {
        $user = Auth::user();
        if (!$user) {
             return redirect()->route('login')->with('error', 'Please login to record attendance.');
        }

        // Record the attendance using the repository
        $attendance = $this->attendanceRepository->recordAttendance($user->id, $event->id); // Use $event->id

        // Check attendance status and redirect
        if ($attendance && $attendance->wasRecentlyCreated) {
             return redirect()->route('events.show', $event->id)->with('success', 'Attendance recorded successfully.');
        } elseif ($attendance) {
             return redirect()->route('events.show', $event->id)->with('info', 'Attendance already recorded previously.');
        } else {
             return redirect()->route('events.show', $event->id)->with('error', 'Failed to record attendance. Please try again.');
        }
    }

    /**
     * Display the attendance list for a specific event.
     */
     // Recommended: Use Route Model Binding
      public function showAttendance(Event $event): View // Changed int $id to Event $event
      {
          $attendees = $this->attendanceRepository->getEventAttendees($event->id); // Use $event->id
          return view('events.attendance', compact('event', 'attendees')); // Ensure view exists
      }
}