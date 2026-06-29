<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Filament\Notifications\Notification;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'employee_id', 'title', 'description', 'start_date', 'due_date', 'status', 'priority', 'sort_order'];

    protected static function booted(): void
    {
        static::created(function (Task $task) {
            if ($task->employee && $task->employee->user) {
                Notification::make()
                    ->title('مهمة جديدة: ' . $task->title)
                    ->body('تم تعيين مهمة جديدة لك في مشروع: ' . ($task->project->name ?? 'بدون مشروع'))
                    ->success()
                    ->sendToDatabase($task->employee->user);
            }
        });

        static::updated(function (Task $task) {
            if ($task->isDirty('status') && $task->employee && $task->employee->user) {
                Notification::make()
                    ->title('تحديث حالة المهمة: ' . $task->title)
                    ->body('تم تغيير حالة المهمة إلى: ' . __("filament.status.{$task->status}"))
                    ->info()
                    ->sendToDatabase($task->employee->user);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->timeEntries->sum('hours');
    }
}
