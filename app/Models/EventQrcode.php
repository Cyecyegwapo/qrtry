<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id', // Allow event_id to be filled
        'svg_data', // Allow svg_data to be filled
    ];

    /**
     * Get the event that owns the QR code.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}