<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Notifications\Notification;

class TaskComment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'comment'];

    protected static function booted(): void
    {
        static::created(function (TaskComment $comment) {
            $task = $comment->task;
            if ($task && $task->employee && $task->employee->user) {
                // Do not notify the person who made the comment
                if ($comment->user_id !== $task->employee->user->id) {
                    Notification::make()
                        ->title('تعليق جديد على مهمة')
                        ->body('تمت إضافة تعليق جديد على المهمة: ' . $task->title)
                        ->info()
                        ->sendToDatabase($task->employee->user);
                }
            }
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
