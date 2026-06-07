<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'name', 'description', 'budget', 'start_date', 'end_date', 'status'];

    protected $appends = ['completion_percentage'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->tasks()->where('status', 'done')->count();
        return round(($completed / $total) * 100, 1);
    }

    public function getTotalTrackedHoursAttribute(): float
    {
        return $this->tasks->sum(fn ($task) => $task->timeEntries->sum('hours'));
    }
}
