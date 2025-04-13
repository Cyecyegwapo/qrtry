<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'time',
    ];

     /**
     * The users that belong to the event.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendances')->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
