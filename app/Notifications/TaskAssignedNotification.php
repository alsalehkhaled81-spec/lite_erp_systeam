<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    protected $taskTitle;
    protected $projectName;

    public function __construct(string $taskTitle, string $projectName)
    {
        $this->taskTitle = $taskTitle;
        $this->projectName = $projectName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'مهمة جديدة',
            'body' => "تم تعيين مهمة جديدة لك: {$this->taskTitle} في مشروع {$this->projectName}",
            'icon' => 'heroicon-o-clipboard-document-check',
        ];
    }
}
