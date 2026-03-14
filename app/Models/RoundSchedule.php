<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RoundSchedule extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'duration_minutes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'duration_minutes' => 'integer',
        'sort_order'       => 'integer',
    ];

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('start_time');
    }

    /**
     * Get the calculated end time (as a time string HH:MM:SS) for this schedule.
     */
    public function getEndTimeAttribute(): string
    {
        return Carbon::createFromFormat('H:i:s', $this->start_time)
            ->addMinutes($this->duration_minutes)
            ->format('H:i:s');
    }

    /**
     * Check if this schedule overlaps with a given time window.
     * @param string $startTime HH:MM:SS
     * @param string $endTime   HH:MM:SS
     */
    public function overlapsWith(string $startTime, string $endTime): bool
    {
        $thisStart = Carbon::createFromFormat('H:i:s', $this->start_time);
        $thisEnd   = Carbon::createFromFormat('H:i:s', $this->end_time);
        $newStart  = Carbon::createFromFormat('H:i:s', $startTime);
        $newEnd    = Carbon::createFromFormat('H:i:s', $endTime);

        return $thisStart->lt($newEnd) && $thisEnd->gt($newStart);
    }

    /**
     * Check if a round for TODAY has already been created from this schedule.
     */
    public function hasTodaysRound(): bool
    {
        return $this->rounds()->whereDate('start_time', today())->exists();
    }
}
