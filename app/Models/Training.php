<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    protected $fillable = ['title', 'description', 'trainer', 'start_date', 'end_date', 'status', 'location', 'max_participants'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
            ->withPivot(['status', 'certificate_url', 'completion_date'])
            ->withTimestamps();
    }
}
