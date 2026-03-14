<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $fillable = [
        'round_serial',
        'name',
        'round_schedule_id',
        'start_time',
        'end_time',
        'result_number',
        'status',
        'total_pool',
        'commission_amount',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function schedule()
    {
        return $this->belongsTo(RoundSchedule::class, 'round_schedule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now());
    }
}
