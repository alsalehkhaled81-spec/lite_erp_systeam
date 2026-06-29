<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

use Filament\Notifications\Notification;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = ['employee_id', 'type', 'start_date', 'end_date', 'reason', 'status'];

    protected static function booted(): void
    {
        static::created(function (Leave $leave) {
            // Notify managers or HR
            $hrUsers = User::whereHas('role', function($q) {
                $q->where('name', 'hr_manager')->orWhere('name', 'super_admin');
            })->get();

            foreach ($hrUsers as $user) {
                Notification::make()
                    ->title('طلب إجازة جديد')
                    ->body('قدم ' . ($leave->employee->user->name ?? 'موظف') . ' طلب إجازة جديد.')
                    ->info()
                    ->sendToDatabase($user);
            }
        });

        static::updated(function (Leave $leave) {
            if ($leave->isDirty('status') && $leave->employee && $leave->employee->user) {
                Notification::make()
                    ->title('تحديث حالة الإجازة')
                    ->body('تم تغيير حالة طلب الإجازة إلى: ' . __("filament.status.{$leave->status}"))
                    ->success()
                    ->sendToDatabase($leave->employee->user);
            }
        });
    }

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
