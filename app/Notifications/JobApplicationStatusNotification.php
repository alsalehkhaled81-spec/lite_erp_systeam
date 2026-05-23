<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApplicationStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $applicantName;

    public function __construct(string $status, string $applicantName)
    {
        $this->status = $status;
        $this->applicantName = $applicantName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = $this->status === 'active' ? 'تم قبول' : 'تم رفض';

        return [
            'title' => 'تحديث طلب التوظيف',
            'body' => "{$statusText} طلب التوظيف للمتقدم {$this->applicantName}",
            'icon' => 'heroicon-o-user-plus',
        ];
    }
}
