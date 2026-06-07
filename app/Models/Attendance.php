<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'hours_worked', 'status', 'notes'];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function calculateHoursWorked($checkIn, $checkOut): float
    {
        if (!$checkIn || !$checkOut) {
            return 0;
        }
        return round($checkIn->diffInMinutes($checkOut) / 60, 2);
    }

    public function getIsLateAttribute(): bool
    {
        if (!$this->check_in) return false;
        return $this->check_in->format('H:i:s') > '09:15:00';
    }
}
