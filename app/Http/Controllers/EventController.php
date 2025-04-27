<?php

namespace App\Http\Controllers;

// Essential imports
use App\Http\Requests\EventRequest;
use App\Repositories\EventRepository;
use App\Repositories\AttendanceRepository;
use App\Models\Event;
use App\Models\EventQrcode; // ADDED: Import EventQrcode model
use App\Models\QrCodeDownloadLog; // Existing: Import the new Log model
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Existing: Import Request (needed for IP address, and update method)
use Illuminate\Support\Facades\Log; // Existing: System Log facade
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // Existing: Import Auth facade (needed for user ID)
use Illuminate\Support\Str; // Existing: Needed for Str::slug()
use Symfony\Component\HttpFoundation\StreamedResponse; // Existing: Needed for download response

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
        // Example: Apply middleware - Consider adding 'updateQrCodeSettings' to admin list
        // $this->middleware(['auth', 'admin'])->only(['create', 'store', 'edit', 'update', 'destroy', 'generateQrCode', 'showAttendance', 'downloadQrCode', 'updateQrCodeSettings']);
        // $this->middleware('auth')->only(['index', 'show', 'recordAttendance']);
    }

    /**
     * Display a listing of the events.
     */
    public function index(): View
    {
        // OPTIONAL: Eager load qrcodes if displaying status on index page
        // $events = $this->eventRepository->getAllWithQrcodes();
        $events = $this->eventRepository->getAll();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     * Includes detailed logging for debugging observer issues.
     * (No changes needed here for QR validity)
     */
    public function store(EventRequest $request): RedirectResponse
    {
        Log::info('==================== EventController::store START ====================');
        $validatedData = $request->validated();
        Log::info('[Store] Event data validated.', $validatedData);
        try {
            Log::info('[Store] Attempting event creation via eventRepository->create()...');
            $event = $this->eventRepository->create($validatedData);
            // --- Logging Event Creation Details ---
            if ($event) {
                Log::info('[Store] EventRepository::create executed successfully.');
                Log::info('[Store] Created object class: ' . get_class($event));
                Log::info('[Store] Created Event ID: ' . $event->id);
                if ($event instanceof \App\Models\Event) { Log::info('[Store] CONFIRMED: Is App\Models\Event.'); }
                else { Log::error('[Store] CRITICAL: NOT App\Models\Event! Is: ' . get_class($event)); }
                if ($event->wasRecentlyCreated) { Log::info('[Store] wasRecentlyCreated is TRUE.'); }
                else { Log::warning('[Store] wasRecentlyCreated is FALSE.'); }
            } else { Log::error('[Store] EventRepository::create returned null/false.'); }
            // --- End Logging ---
            if (!$event) {
                Log::error('[Store] Redirecting back with error because $event is null/false.');
                return redirect()->back()->with('error', 'Failed to create event in repository.')->withInput();
            }
            Log::info('[Store] Redirecting to events.index...');
            Log::info('==================== EventController::store END ======================');
            return redirect()->route('events.index')->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            Log::error('[Store] EXCEPTION: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Log::info('==================== EventController::store END WITH EXCEPTION ============');
            return redirect()->back()->with('error', 'An unexpected error occurred.')->withInput();
        }
    }

    /**
     * Display the specified event.
     * MODIFIED: Eager load qrcode if showing status on show page
     */
    public function show(Event $event): View
    {
        $attendanceCount = $this->attendanceRepository->getEventAttendanceCount($event->id);
        // ADDED: Eager load the qrcode relationship if needed for the view
        $event->load('qrcode');
        return view('events.show', compact('event', 'attendanceCount'));
    }

    /**
     * Show the form for editing the specified event.
     * MODIFIED: Eager load qrcode for the settings form
     */
    public function edit(Event $event): View
    {
        // ADDED: Eager load the qrcode relationship to populate the QR settings form
        $event->load('qrcode');
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     * (No changes needed here unless combining event/QR updates)
     */
    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->eventRepository->update($event->id, $request->validated());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     * (No changes needed here)
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->eventRepository->delete($event->id); // Assumes cascade delete handles related qrcode/logs
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Display the QR Code page for the specified event.
     * **UPDATED**: Now includes validity check and passes status to view.
     */
    public function generateQrCode(Event $event): View|RedirectResponse
    {
        $eventQrcode = $event->qrcode; // Fetch the related EventQrcode model

        if (!$eventQrcode || empty($eventQrcode->svg_data)) {
            Log::warning("[generateQrCode] EventQrcode record or svg_data missing for event ID {$event->id}.");
            return redirect()->route('events.show', $event->id)->with('error', 'QR code is not available for this event yet.');
        }

        // Check validity using the model method
        $isValid = $eventQrcode->isValidNow();
        $validityMessage = '';
        if (!$isValid) {
            // Determine specific reason for message
            if (!$eventQrcode->is_active) {
                $validityMessage = 'QR Code is currently disabled by an administrator.';
            } elseif ($eventQrcode->active_from && now()->lt($eventQrcode->active_from)) {
                $validityMessage = 'QR Code is not active yet. Active from: ' . $eventQrcode->active_from->format('Y-m-d H:i');
            } elseif ($eventQrcode->active_until && now()->gt($eventQrcode->active_until)) {
                $validityMessage = 'QR Code has expired. Was valid until: ' . $eventQrcode->active_until->format('Y-m-d H:i');
            }
        }

        $qrCodeSvg = $eventQrcode->svg_data;

        // Pass event, svg, validity status, and message to the view
        return view('events.qrcode', compact('event', 'qrCodeSvg', 'isValid', 'validityMessage'));
    }

    /**
     * Handle request to download the event's QR code as an SVG file.
     * Logs the download action before streaming.
     * **UPDATED**: Added validity check primarily for logging.
     */
    public function downloadQrCode(Request $request, Event $event): StreamedResponse|RedirectResponse
    {
        $eventQrcode = $event->qrcode; // Fetch the related EventQrcode model

        if (!$eventQrcode || empty($eventQrcode->svg_data)) {
            Log::error("Download QR attempt failed: SVG data missing for event ID {$event->id}.");
            return redirect()->route('events.show', $event->id)
                ->with('error', 'QR code data is not available for download.');
        }

        $qrCodeSvg = $eventQrcode->svg_data;

        // Check validity primarily for logging/awareness
        $isValid = $eventQrcode->isValidNow();
        $validityStatus = $isValid ? 'Valid' : 'Invalid/Disabled';

        // --- Log the download action ---
        try {
            QrCodeDownloadLog::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),      // Get ID of logged-in user
                'ip_address' => $request->ip(), // Get downloader's IP
                'downloaded_at' => now()        // Current timestamp
            ]);
            // Log includes validity status now
            Log::info("QR Code download logged (Status: {$validityStatus}) for Event ID: {$event->id} by User ID: " . (Auth::id() ?? 'Guest/Unknown'));
        } catch (\Exception $e) {
            Log::error("Failed to log QR download for Event ID {$event->id}: " . $e->getMessage());
        }
        // --- End logging ---

        // --- Prepare and stream the SVG download ---
        $fileName = 'event_' . $event->id . '_' . Str::slug($event->title) . '_qrcode.svg';
        $headers = [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function () use ($qrCodeSvg) {
            echo $qrCodeSvg;
        }, $fileName, $headers);
    }

    /**
     * Record attendance using QR Code scan.
     * **UPDATED**: Now checks QR code validity before recording.
     */
    public function recordAttendance(Request $request, Event $event): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to record attendance.');
        }

        // **ADDED**: Check QR Code Validity
        $eventQrcode = $event->qrcode; // Get the related QR code model

        if (!$eventQrcode || !$eventQrcode->isValidNow()) {
             // Determine specific reason if needed for better user feedback
            $reason = 'Attendance recording failed: QR Code is not valid or available.';
             if (!$eventQrcode) {
                 $reason = 'Attendance recording failed: QR Code data not found.';
             } elseif (!$eventQrcode->is_active) {
                $reason = 'Attendance recording failed: QR Code has been disabled by an administrator.';
            } elseif ($eventQrcode->active_from && now()->lt($eventQrcode->active_from)) {
                $reason = 'Attendance recording failed: QR Code is not active yet.';
            } elseif ($eventQrcode->active_until && now()->gt($eventQrcode->active_until)) {
                $reason = 'Attendance recording failed: QR Code has expired.';
            }
            Log::warning("[recordAttendance] Blocked for Event ID {$event->id}, User ID {$user->id}. Reason: {$reason}");
            return redirect()->route('events.show', $event->id)->with('error', $reason);
        }
        // --- End QR Code Validity Check ---


        // If QR code is valid, proceed to record attendance
        Log::info("[recordAttendance] QR Code valid for Event ID {$event->id}. Attempting to record attendance for User ID {$user->id}.");
        // Assumes repository handles duplicate check before saving
        $attendance = $this->attendanceRepository->recordAttendance($user->id, $event->id);

        if ($attendance && $attendance->wasRecentlyCreated) {
            return redirect()->route('events.show', $event->id)->with('success', 'Attendance recorded successfully.');
        } elseif ($attendance) {
            return redirect()->route('events.show', $event->id)->with('info', 'Attendance already recorded previously.');
        } else {
             Log::error("[recordAttendance] Failed to record attendance via repository for Event ID {$event->id}, User ID {$user->id}.");
            return redirect()->route('events.show', $event->id)->with('error', 'Failed to record attendance. Please try again.');
        }
    }

    /**
     * Display the attendance list for a specific event.
     * (No changes needed here)
     */
      public function showAttendance(Event $event): View
      {
          $attendees = $this->attendanceRepository->getEventAttendees($event->id);
          return view('events.attendance', compact('event', 'attendees'));
      }


    /**
     * **NEW METHOD**: Update QR Code specific settings (validity period, active status).
     * Handles submission from the QR settings form in edit.blade.php
     */
    public function updateQrCodeSettings(Request $request, Event $event): RedirectResponse
    {
        // Find the associated QR code record
        $eventQrcode = $event->qrcode;

        if (!$eventQrcode) {
             Log::error("[updateQrCodeSettings] Attempted update but EventQrcode record not found for Event ID: {$event->id}");
             return redirect()->back()->with('error', 'QR Code record not found for this event.');
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'active_from' => 'nullable|date',
            'active_until' => 'nullable|date|after_or_equal:active_from',
            // Use 'present' to ensure the field is submitted (even if unchecked, hidden field sends '0')
            // Use 'boolean' to convert '1', '0', 'true', 'false', 'on', 'off' to boolean
            'is_active' => 'present|boolean',
        ]);

         // Prepare data for update (handle boolean conversion explicitly)
         $updateData = [
             'active_from' => $validated['active_from'] ?? null,
             'active_until' => $validated['active_until'] ?? null,
             // Convert validated input (which might be 1/0) strictly to boolean
             'is_active' => filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN),
         ];

        // Update the EventQrcode record
        try {
            $eventQrcode->update($updateData);
             Log::info("[updateQrCodeSettings] Updated QR code settings for Event ID: {$event->id}", $updateData);
            return redirect()->route('events.edit', $event->id)->with('success', 'QR Code settings updated successfully.');
            // Consider redirecting back to show page or staying on edit page
        } catch (\Exception $e) {
             Log::error("[updateQrCodeSettings] Failed to update QR settings for Event ID {$event->id}: " . $e->getMessage());
             return redirect()->back()->with('error', 'Failed to update QR Code settings.')->withInput();
        }
    } // **END OF NEW METHOD**
}