<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Repositories\EventRepository;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $eventRepository;
    protected $attendanceRepository;

    public function __construct(EventRepository $eventRepository, AttendanceRepository $attendanceRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * Display a listing of the events.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = $this->eventRepository->getAll();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('events.create'); // Ensure it returns the correct view
    }
    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $event = $this->eventRepository->create($request->validated());
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = $this->eventRepository->getById($id);
        $attendanceCount = $this->attendanceRepository->getEventAttendanceCount($id);
        return view('events.show', compact('event', 'attendanceCount'));
    }

    /**
     * Show the form for editing the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = $this->eventRepository->getById($id);
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {
        $this->eventRepository->update($id, $request->validated());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->eventRepository->delete($id);
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Generate QR Code for the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateQrCode($id)
    {
        $event = $this->eventRepository->getById($id);
        $qrCode = QrCode::size(200)->generate(route('events.attendance.record', ['id' => $id])); // Generate QR code
        return view('events.qrcode', compact('event', 'qrCode'));
    }

    /**
     * Record attendance using QR Code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recordAttendance(Request $request, $id)
    {
        $user = Auth::user(); // Get the authenticated user.

        $event = $this->eventRepository->getById($id);

        // Record the attendance
        $attendance = $this->attendanceRepository->recordAttendance($user->id, $id);

        if($attendance->attended_at){
             return redirect()->route('events.show', $id)->with('success', 'Attendance recorded successfully.');
        }
        else{
            return redirect()->route('events.show', $id)->with('error', 'Attendance already recorded.');
        }


    }

     public function showAttendance($id)
    {
        $event = $this->eventRepository->getById($id);
        $attendees = $this->attendanceRepository->getEventAttendees($id);
        return view('events.attendance', compact('event', 'attendees'));
    }
}

