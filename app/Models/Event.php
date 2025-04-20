<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- Import HasOne relationship type

class Event extends Model
{
    use HasFactory; // Assuming you use factories

    /**
     * The table associated with the model.
     * (Usually not needed if table name is 'events')
     * @var string
     */
    // protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     * Make sure all fields you save via $request->validated() or similar are listed here.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        // 'qr_code_svg', // You can comment out or remove this later if you drop the column from the 'events' table
        // Add any other event fields that need to be fillable
    ];

    /**
     * The attributes that should be cast.
     * (Optional: Define casting for date/time fields, etc.)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date', // Example: Cast 'date' column to a Date object
        // 'time' => 'datetime:H:i', // Example: Cast time - adjust format as needed
    ];


    // --- Add this relationship method ---

    /**
     * Get the QR code record associated with the event.
     * This links to the event_qrcodes table.
     */
    public function qrcode(): HasOne
    {
        // Defines a one-to-one relationship with EventQrcode model
        // Assumes the foreign key in event_qrcodes is 'event_id'
        return $this->hasOne(EventQrcode::class);
    }

    // --- End of added relationship method ---


    // Add any other custom methods or relationships for your Event model below

}