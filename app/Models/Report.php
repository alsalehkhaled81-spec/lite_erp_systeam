<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class Report extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id', 'receiver_id', 'title', 'content', 'feedback', 'status'];

    protected static function booted(): void
    {
        static::creating(function (Report $report) {
            if ($report->sender_id) {
                $sender = Employee::with(['department.head.user'])->find($report->sender_id);
                if ($sender) {
                    $jobTitle = $sender->job_title ?? 'غير محدد';
                    $departmentName = $sender->department?->name ?? 'غير محدد';
                    $headName = $sender->department?->head?->user?->name ?? 'غير محدد';
                    
                    $header = "--- معلومات المرسل ---\n";
                    $header .= "الوظيفة: {$jobTitle}\n";
                    $header .= "القسم: {$departmentName}\n";
                    $header .= "مدير القسم: {$headName}\n";
                    $header .= "------------------------\n\n";
                    
                    $report->content = $header . $report->content;
                }
            }
        });

    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'receiver_id');
    }

    public function receiverPanelId(): string
    {
        return $this->roleToPanelId($this->receiver?->user?->role?->name);
    }

    public function senderPanelId(): string
    {
        return $this->roleToPanelId($this->sender?->user?->role?->name);
    }

    protected function roleToPanelId(?string $roleName): string
    {
        return match ($roleName) {
            'super_admin' => 'admin',
            'hr_manager' => 'hr',
            'project_manager' => 'pm',
            'accountant' => 'accountant',
            'employee' => 'employee',
            default => 'employee',
        };
    }

    public function viewUrl(): string
    {
        return '/' . $this->receiverPanelId() . '/reports/' . $this->id . '/edit';
    }

    public function senderViewUrl(): string
    {
        return '/' . $this->senderPanelId() . '/reports/' . $this->id . '/edit';
    }

    public function notifyReceiver(): void
    {
        $receiverUser = $this->receiver?->user;
        if (!$receiverUser) {
            return;
        }

        $senderName = $this->sender?->user?->name ?? __('filament.reports.unknown_sender');

        Notification::make()
            ->title(__('filament.reports.notification_title'))
            ->body(__('filament.reports.notification_body', [
                'sender' => $senderName,
                'title' => $this->title,
            ]))
            ->icon('heroicon-o-document-text')
            ->iconColor('info')
            ->actions([
                Action::make('view_report')
                    ->button()
                    ->label(__('filament.reports.view_report'))
                    ->url($this->viewUrl())
                    ->markAsRead(),
            ])
            ->sendToDatabase($receiverUser);
    }

    public function notifySender(): void
    {
        $senderUser = $this->sender?->user;
        if (!$senderUser) {
            return;
        }

        $receiverName = $this->receiver?->user?->name ?? __('filament.reports.unknown_receiver');

        Notification::make()
            ->title(__('filament.reports.sender_notification_title'))
            ->body(__('filament.reports.sender_notification_body', [
                'receiver' => $receiverName,
                'title' => $this->title,
            ]))
            ->icon('heroicon-o-paper-airplane')
            ->iconColor('success')
            ->actions([
                Action::make('view_report')
                    ->button()
                    ->label(__('filament.reports.view_report'))
                    ->url($this->senderViewUrl())
                    ->markAsRead(),
            ])
            ->sendToDatabase($senderUser);
    }

    public function notifyReplied(): void
    {
        $senderUser = $this->sender?->user;
        if (!$senderUser) {
            return;
        }

        $receiverName = $this->receiver?->user?->name ?? __('filament.reports.unknown_receiver');

        Notification::make()
            ->title(__('filament.reports.reply_notification_title'))
            ->body(__('filament.reports.reply_notification_body', [
                'receiver' => $receiverName,
                'title' => $this->title,
            ]))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->iconColor('warning')
            ->actions([
                Action::make('view_reply')
                    ->button()
                    ->label(__('filament.reports.view_reply'))
                    ->url($this->senderViewUrl())
                    ->markAsRead(),
            ])
            ->sendToDatabase($senderUser);
    }
}
