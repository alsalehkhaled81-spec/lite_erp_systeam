<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $leaveType;

    public function __construct(string $status, string $leaveType)
    {
        $this->status = $status;
        $this->leaveType = $leaveType;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = match ($this->status) {
            'approved_by_head' => 'تمت موافقة رئيس القسم على',
            'approved_by_hr' => 'تمت الموافقة النهائية على',
            'rejected' => 'تم رفض',
            default => $this->status,
        };

        return [
            'title' => 'تحديث الإجازة',
            'body' => "{$statusText} طلب الإجازة ({$this->leaveType})",
            'icon' => 'heroicon-o-calendar-days',
        ];
    }
}
