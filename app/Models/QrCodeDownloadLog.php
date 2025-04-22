<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCodeDownloadLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Set to false because we manually handle 'downloaded_at'
     * and the table doesn't have 'created_at'/'updated_at'.
     *
     * @var bool
     */
    public $timestamps = false; // <-- ADD THIS LINE

    // Define the table name if not standard plural 'qr_code_download_logs'
    // protected $table = 'qr_code_download_logs';

    // Make sure these match your columns (except id and timestamps if false)
    protected $fillable = [
        'event_id',
        'user_id',
        'ip_address',
        'downloaded_at',
    ];

    // Define relationships if needed
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function event() {
        return $this->belongsTo(Event::class);
    }
}