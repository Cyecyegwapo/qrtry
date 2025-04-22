<?php

namespace App\Http\Controllers;

// Essential imports
use App\Http\Requests\EventRequest;
use App\Repositories\EventRepository;
use App\Repositories\AttendanceRepository;
use App\Models\Event;
use App\Models\QrCodeDownloadLog; // <-- Import the new Log model
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // <-- Import Request (needed for IP address)
use Illuminate\Support\Facades\Log; // System Log facade
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; // <-- Import Auth facade (needed for user ID)
use Illuminate\Support\Str; // Needed for Str::slug()
use Symfony\Component\HttpFoundation\StreamedResponse; // Needed for download response

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
        // Example: Apply middleware
        // $this->middleware(['auth', 'admin'])->only(['create', 'store', 'edit', 'update', 'destroy', 'generateQrCode', 'showAttendance', 'downloadQrCode']);
        // $this->middleware('auth')->only(['index', 'show', 'recordAttendance']); // Example for authenticated users
    }

    /**
     * Display a listing of the events.
     */
    public function index(): View
    {
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
     */
    public function show(Event $event): View
    {
        $attendanceCount = $this->attendanceRepository->getEventAttendanceCount($event->id);
        return view('events.show', compact('event', 'attendanceCount'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->eventRepository->update($event->id, $request->validated());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->eventRepository->delete($event->id); // Assumes cascade delete handles related qrcode/logs
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Display the QR Code page for the specified event.
     */
    public function generateQrCode(Event $event): View|RedirectResponse
    {
        $qrCodeSvg = $event->qrcode?->svg_data; // Fetch via relationship
        if (empty($qrCodeSvg)) {
            Log::warning("[generateQrCode] EventQrcode record or svg_data missing for event ID {$event->id}.");
            return redirect()->route('events.show', $event->id)->with('error', 'QR code is not available for this event yet.');
        }
        return view('events.qrcode', compact('event', 'qrCodeSvg'));
    }

    /**
     * Handle request to download the event's QR code as an SVG file.
     * Logs the download action before streaming.
     *
     * @param  \Illuminate\Http\Request $request // <-- Inject Request to get IP
     * @param  \App\Models\Event  $event
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadQrCode(Request $request, Event $event): StreamedResponse|RedirectResponse // <-- Added Request $request
    {
        $qrCodeSvg = $event->qrcode?->svg_data;

        if (empty($qrCodeSvg)) {
            Log::error("Download QR attempt failed: SVG data missing for event ID {$event->id}.");
            return redirect()->route('events.show', $event->id)
                             ->with('error', 'QR code data is not available for download.');
        }

      // --- Log the download action ---
      try {
        // This is where the save should happen
        QrCodeDownloadLog::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),        // Get ID of logged-in user
            'ip_address' => $request->ip(), // Get downloader's IP
            'downloaded_at' => now()        // Current timestamp
        ]);
        // If successful, this log message should appear
        Log::info("QR Code download logged for Event ID: {$event->id} by User ID: " . (Auth::id() ?? 'Guest/Unknown'));
    } catch (\Exception $e) {
        // If create() fails, this error should appear in the logs
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
     */
    public function recordAttendance(Request $request, Event $event): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
             return redirect()->route('login')->with('error', 'Please login to record attendance.');
        }
        // Assumes repository handles duplicate check before saving
        $attendance = $this->attendanceRepository->recordAttendance($user->id, $event->id);
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
      public function showAttendance(Event $event): View
      {
          $attendees = $this->attendanceRepository->getEventAttendees($event->id);
          return view('events.attendance', compact('event', 'attendees'));
      }
}