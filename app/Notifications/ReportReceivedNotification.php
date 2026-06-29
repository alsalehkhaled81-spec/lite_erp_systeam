<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Report;

class ReportReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $senderName = $this->report->sender?->user?->name ?? __('filament.reports.unknown_sender');

        return [
            'title' => __('filament.reports.notification_title'),
            'body' => __('filament.reports.notification_body', [
                'sender' => $senderName,
                'title' => $this->report->title,
            ]),
            'icon' => 'heroicon-o-document-text',
            'iconColor' => 'info',
            'actions' => [
                [
                    'label' => __('filament.reports.view_report'),
                    'url' => $this->report->viewUrl(),
                    'shouldMarkAsRead' => true,
                ],
            ],
        ];
    }
}
