<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'type', 'start_date', 'end_date', 'reason', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
