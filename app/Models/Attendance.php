<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

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
        $checkIn = $checkIn instanceof \Carbon\Carbon ? $checkIn : \Carbon\Carbon::parse($checkIn);
        $checkOut = $checkOut instanceof \Carbon\Carbon ? $checkOut : \Carbon\Carbon::parse($checkOut);

        return round($checkIn->diffInMinutes($checkOut) / 60, 2);
    }

    public static function combineDateTime($date, $time): ?\Carbon\Carbon
    {
        if (!$date || !$time) {
            return null;
        }

        $dateStr = $date instanceof \Carbon\Carbon ? $date->toDateString() : \Carbon\Carbon::parse($date)->toDateString();

        return \Carbon\Carbon::parse($dateStr . ' ' . $time);
    }

    public static function calculateHoursFromTimes($checkInTime, $checkOutTime): float
    {
        if (!$checkInTime || !$checkOutTime) {
            return 0;
        }

        $checkInTs = strtotime(is_string($checkInTime) ? $checkInTime : $checkInTime->format('H:i:s'));
        $checkOutTs = strtotime(is_string($checkOutTime) ? $checkOutTime : $checkOutTime->format('H:i:s'));

        if ($checkOutTs <= $checkInTs) {
            return 0;
        }

        return round(($checkOutTs - $checkInTs) / 3600, 2);
    }

    public function getIsLateAttribute(): bool
    {
        if (!$this->check_in) return false;
        return $this->check_in->format('H:i:s') > '09:15:00';
    }
}
