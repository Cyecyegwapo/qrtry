<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; // Existing relationship type
use Illuminate\Database\Eloquent\Relations\HasMany; // ADDED: For HasMany relationships
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ADDED: For Many-to-Many relationships
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ADDED: For BelongsTo relationships
use Illuminate\Support\Carbon; // Import Carbon for date/time manipulation

class Event extends Model
{
    use HasFactory; // Assuming you use factories

    /**
     * The table associated with the model.
     */
    // protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'year_level',
        'department',
        'svg_data', // Kept uncommented as requested
        // Add any other event fields that need to be fillable
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date', // Cast 'date' column to a Carbon Date object
        // 'time' => 'datetime:H:i',
    ];


    // --- Existing Relationship ---

    /**
     * Get the QR code record associated with the event.
     */
    public function qrcode(): HasOne
    {
        // Defines a one-to-one relationship with EventQrcode model
        return $this->hasOne(EventQrcode::class); // Assumes EventQrcode model exists
    }

    // --- ADDED: Relationships ---

    /**
     * Get all attendance records for the event.
     */
    public function attendances(): HasMany
    {
        // Assumes Attendance model exists and 'attendances' table has 'event_id'
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all users who attended the event.
     * This goes through the 'attendances' table.
     */
    public function attendees(): BelongsToMany
    {
        // Assumes User model exists
        // Assumes 'attendances' is the pivot table name
        // Assumes foreign keys in pivot table are 'event_id' and 'user_id'
        return $this->belongsToMany(User::class, 'attendances', 'event_id', 'user_id')
                    ->withTimestamps(); // Optionally load pivot timestamps (created_at/updated_at from attendances table)
    }

    /**
     * Get all QR code download log entries for this event.
     */
    public function qrCodeDownloadLogs(): HasMany
    {
        // Assumes QrCodeDownloadLog model exists and 'qr_code_download_logs' table has 'event_id'
        return $this->hasMany(QrCodeDownloadLog::class);
    }

    /**
     * Get the user who created the event (Optional).
     */
    // public function creator(): BelongsTo
    // {
        // Assumes User model exists
        // Assumes you have a 'creator_id' or 'user_id' column on your 'events' table
        // return $this->belongsTo(User::class, 'creator_id'); // Adjust 'creator_id' if needed
    // }

    // --- End of Added Relationships ---


    // --- Existing Accessor ---

    /**
     * Get the event's start date and time as a Carbon instance.
     * Access via: $event->start_time
     */
    public function getStartTimeAttribute(): ?Carbon
    {
        if ($this->date && $this->time) {
            try {
                // Combine date (already Carbon object due to cast) and time string
                return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    // --- ADDED: Example Helper Method ---

    /**
     * Check if the event's start time is in the past.
     *
     * @return bool
     */
    public function isPast(): bool
    {
        $startTime = $this->start_time; // Uses the getStartTimeAttribute accessor
        return $startTime !== null && $startTime->isPast();
    }

    // --- End of Added Helper Method ---


    // Add any other custom methods for your Event model below

}