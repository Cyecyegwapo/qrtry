<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AttendanceRepository
{
    protected $model;

    public function __construct(Attendance $model)
    {
        $this->model = $model;
    }

    public function getByEventId($eventId)
    {
        return $this->model->where('event_id', $eventId)->get();
    }

    public function getByUserIdAndEventId($userId, $eventId)
    {
        return $this->model->where('user_id', $userId)->where('event_id', $eventId)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $attendance = $this->model->findOrFail($id);
        $attendance->update($data);
        return $attendance;
    }

     public function delete($id)
    {
        $attendance = $this->model->findOrFail($id);
        $attendance->delete();
    }

    /**
     * Records a user's attendance at an event.
     *
     * @param  int  $userId
     * @param  int  $eventId
     * @return \App\Models\Attendance
     */
    public function recordAttendance(int $userId, int $eventId)
    {
        // Check if attendance already exists
        $attendance = $this->getByUserIdAndEventId($userId, $eventId);

        if ($attendance) {
            //Attendance Already Exists
            return $attendance;
        }
        // Create new attendance record
        return $this->create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'attended_at' => now(),
        ]);
    }

    public function getEventAttendanceCount(int $eventId): int
    {
        return $this->model->where('event_id', $eventId)->count();
    }

    public function getEventAttendees(int $eventId)
    {
        return DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->where('attendances.event_id', $eventId)
            ->select('users.id', 'users.name', 'users.email', 'attendances.attended_at')
            ->get();
    }
}

