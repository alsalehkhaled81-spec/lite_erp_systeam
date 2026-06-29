<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Notifications\Notification;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'project_id', 'title', 'category', 'amount', 'expense_date', 'receipt_url', 'status', 'approved_by'];

    protected static function booted(): void
    {
        static::created(function (Expense $expense) {
            $financeUsers = User::whereHas('role', function($q) {
                $q->where('name', 'accountant')->orWhere('name', 'super_admin');
            })->get();

            foreach ($financeUsers as $user) {
                Notification::make()
                    ->title('طلب مصروف جديد')
                    ->body('قدم ' . ($expense->user->name ?? 'مستخدم') . ' طلب مصروف جديد بقيمة ' . $expense->amount)
                    ->info()
                    ->sendToDatabase($user);
            }
        });

        static::updated(function (Expense $expense) {
            if ($expense->isDirty('status') && $expense->user) {
                Notification::make()
                    ->title('تحديث حالة المصروف')
                    ->body('تم تغيير حالة طلب المصروف (' . $expense->title . ') إلى: ' . __("filament.status.{$expense->status}"))
                    ->success()
                    ->sendToDatabase($expense->user);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
