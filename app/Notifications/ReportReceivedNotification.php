<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportReceivedNotification extends Notification
{
    use Queueable;

    protected $reportTitle;
    protected $senderName;

    public function __construct(string $reportTitle, string $senderName)
    {
        $this->reportTitle = $reportTitle;
        $this->senderName = $senderName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تقرير جديد',
            'body' => "استلمت تقريراً جديداً من {$this->senderName}: {$this->reportTitle}",
            'icon' => 'heroicon-o-document-text',
        ];
    }
}
