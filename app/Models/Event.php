<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date', // Cast the 'date' column to a Carbon date object
        // 'time' => 'time', // REMOVED: Laravel doesn't have a 'time' cast. It will be retrieved as a string.
        'password' => 'hashed', // Added default password cast if needed, remove if not applicable
    ];

    /**
     * The users that belong to the event (attendees).
     * Defines the many-to-many relationship through the 'attendances' table.
     */
    public function users(): BelongsToMany
    {
        // An event belongs to many users through the 'attendances' pivot table.
        // withTimestamps() automatically updates created_at/updated_at on the pivot table.
        return $this->belongsToMany(User::class, 'attendances')->withTimestamps();
    }

    /**
     * Get the attendance records associated with the event.
     * Defines a one-to-many relationship.
     */
    public function attendances()
    {
        // An event has many attendance records.
        return $this->hasMany(Attendance::class);
    }
}
