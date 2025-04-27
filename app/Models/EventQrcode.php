<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // Import Carbon for type hinting if needed

class EventQrcode extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * (Optional if table name is standard plural 'event_qrcodes')
     * @var string
     */
    // protected $table = 'event_qrcodes';

    /**
     * The attributes that are mass assignable.
     * Correctly includes all necessary fields.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',    // From original code
        'svg_data',    // From original code & needed for QR
        'event_title', // From original code (Ensure column exists in DB)
        'active_from', // Needed for validity
        'active_until',// Needed for validity
        'is_active',   // Needed for validity
    ];

    /**
     * The attributes that should be cast.
     * Correctly defines types for validity fields.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active_from' => 'datetime', // Correct cast
        'active_until' => 'datetime', // Correct cast
        'is_active' => 'boolean',    // Correct cast
    ];

    /**
     * Get the event that owns the QR code.
     * Correct relationship definition.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if the QR code is currently valid (considers time and admin status).
     * Correct helper method logic.
     *
     * @return bool
     */
    public function isValidNow(): bool
    {
        // 1. Check admin status first
        if (!$this->is_active) {
            return false; // Correct: Force stopped
        }

        $now = now(); // Get the current time (Carbon instance)

        // 2. Check if it has become active yet (if active_from is set)
        if ($this->active_from !== null && $now->lt($this->active_from)) {
           return false; // Correct: Not active yet
        }

        // 3. Check if it has expired (if active_until is set)
        if ($this->active_until !== null && $now->gt($this->active_until)) {
            return false; // Correct: Expired
        }

        // If none of the above conditions returned false, the QR code is valid
        return true; // Correct: All checks passed
    }
}