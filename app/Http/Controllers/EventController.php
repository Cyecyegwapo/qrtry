<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Repositories\EventRepository;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use App\Models\Event; // Although not directly used here, often needed
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Ensure this is imported
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Although not directly used here, often useful with dates/times
use Illuminate\View\View; // Import View facade or use helper
use Illuminate\Http\RedirectResponse; // Import RedirectResponse
use Illuminate\Support\Facades\Log; // <-- Add this line to import the Log facade

class EventController extends Controller
{
    // Repository properties
    protected EventRepository $eventRepository;
    protected AttendanceRepository $attendanceRepository;

    /**
     * Constructor to inject repositories.
     *
     * @param EventRepository $eventRepository
     * @param AttendanceRepository $attendanceRepository
     */
    public function __construct(EventRepository $eventRepository, AttendanceRepository $attendanceRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->attendanceRepository = $attendanceRepository;

        // Apply admin middleware to specific actions within the controller
        // This is an alternative to applying it in the routes file
        // $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'generateQrCode', 'showAttendance']);
    }

    /**
     * Display a listing of the events.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $events = $this->eventRepository->getAll();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     * Requires admin privileges (handled by route middleware).
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     * Requires admin privileges (handled by route middleware).
     * Uses EventRequest for validation and authorization.
     *
     * @param  EventRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EventRequest $request): RedirectResponse
    {
        // Validation and authorization handled by EventRequest
        $event = $this->eventRepository->create($request->validated());
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     *
     * @param  int  $id The ID of the event.
     * @return \Illuminate\View\View
     */
    public function show(int $id): View // Use route model binding or type hint ID
    {
        // Consider using Route Model Binding for cleaner code: public function show(Event $event)
        $event = $this->eventRepository->getById($id);
        $attendanceCount = $this->attendanceRepository->getEventAttendanceCount($id);
        return view('events.show', compact('event', 'attendanceCount'));
    }

    /**
     * Show the form for editing the specified event.
     * Requires admin privileges (handled by route middleware).
     *
     * @param  int  $id The ID of the event.
     * @return \Illuminate\View\View
     */
    public function edit(int $id): View // Use route model binding or type hint ID
    {
        // Consider using Route Model Binding: public function edit(Event $event)
        $event = $this->eventRepository->getById($id);
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     * Requires admin privileges (handled by route middleware).
     * Uses EventRequest for validation and authorization.
     *
     * @param  EventRequest  $request
     * @param  int  $id The ID of the event being updated.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EventRequest $request, int $id): RedirectResponse // Use route model binding or type hint ID
    {
        // Validation and authorization handled by EventRequest
        // Consider using Route Model Binding: public function update(EventRequest $request, Event $event)
        $this->eventRepository->update($id, $request->validated());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     * Requires admin privileges (handled by route middleware).
     *
     * @param  int  $id The ID of the event to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse // Use route model binding or type hint ID
    {
        // Consider using Route Model Binding: public function destroy(Event $event)
        $this->eventRepository->delete($id);
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Generate QR Code for the specified event.
     * Requires admin privileges (handled by route middleware).
     *
     * @param  int  $id The ID of the event.
     * @return \Illuminate\View\View
     */
    public function generateQrCode(Event $event): View|RedirectResponse
    {
        $qrCode = $event->qr_code_svg; // Retrieve stored SVG
    
        // Fallback if it's empty for some reason
        if (empty($qrCode)) {
            Log::warning("QR code SVG missing for event ID {$event->id}. Generating on the fly for display only.");
            try {
                $qrCodeUrl = route('events.attendance.record', $event->id);
                $qrCode = QrCode::size(200)->format('svg')->generate($qrCodeUrl);
            } catch (\Exception $e) {
                 Log::error("Failed fallback QR for event ID {$event->id}: " . $e->getMessage());
                 return redirect()->route('events.show', $event->id)->with('error', 'Could not generate or retrieve QR code.');
            }
        }
        // Pass the SVG string to the view
        return view('events.qrcode', compact('event', 'qrCode'));
    }
    /**
     * Record attendance using QR Code scan.
     * Accessible by any authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id The ID of the event from the URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recordAttendance(Request $request, int $id): RedirectResponse // Use route model binding or type hint ID
    {
        // Consider using Route Model Binding: public function recordAttendance(Request $request, Event $event)
        $user = Auth::user(); // Get the authenticated user.

        // Optional: Check if event exists (Route model binding handles this)
        // $event = $this->eventRepository->getById($id);

        // Record the attendance using the repository
        $attendance = $this->attendanceRepository->recordAttendance($user->id, $id);

        // Check if attendance was newly recorded or already existed
        // The exact check depends on how recordAttendance signals success/failure/already_exists
        // Assuming it returns the model and attended_at is set on new records:
        if ($attendance && $attendance->wasRecentlyCreated) { // Check if the record was just created
             return redirect()->route('events.show', $id)->with('success', 'Attendance recorded successfully.');
        } elseif ($attendance) { // Record already existed
            return redirect()->route('events.show', $id)->with('info', 'Attendance already recorded previously.'); // Use 'info' or 'warning'
        } else {
             // Handle potential errors if attendance couldn't be recorded for some reason
             return redirect()->route('events.show', $id)->with('error', 'Failed to record attendance.');
        }
    }

    /**
     * Display the attendance list for a specific event.
     * Requires admin privileges (handled by route middleware).
     *
     * @param  int  $id The ID of the event.
     * @return \Illuminate\View\View
     */
     public function showAttendance(int $id): View // Use route model binding or type hint ID
     {
        // Consider using Route Model Binding: public function showAttendance(Event $event)
        $event = $this->eventRepository->getById($id);
        $attendees = $this->attendanceRepository->getEventAttendees($id);
        return view('events.attendance', compact('event', 'attendees'));
     }
}
