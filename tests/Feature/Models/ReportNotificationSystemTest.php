<?php

use App\Models\Employee;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;

function createReportUser(string $roleName): array
{
    $role = Role::firstOrCreate(['name' => $roleName], ['description' => ucfirst($roleName)]);
    $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
    $employee = Employee::factory()->create(['user_id' => $user->id]);

    return [$employee, $user];
}

describe('Report Sender Notifications', function () {
    test('notifySender creates a confirmation notification for the sender', function () {
        [$sender, $senderUser] = createReportUser('employee');
        [$receiver, $receiverUser] = createReportUser('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'title' => 'تقرير الشهر',
        ]);

        $report->notifySender();

        $senderUser->refresh();
        expect($senderUser->notifications)->toHaveCount(1);

        $data = $senderUser->notifications->first()->data;
        expect($data['title'])->toBe(__('filament.reports.sender_notification_title'))
            ->and($data['body'])->toContain('تقرير الشهر')
            ->and($data['icon'])->toBe('heroicon-o-paper-airplane');
    });

    test('sender notification action links to sender panel', function () {
        [$sender, $senderUser] = createReportUser('employee');
        [$receiver, $receiverUser] = createReportUser('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifySender();
        $data = $senderUser->refresh()->notifications->first()->data;

        expect($data['actions'][0]['url'])->toBe($report->senderViewUrl())
            ->and($data['actions'][0]['shouldMarkAsRead'])->toBeTrue();
    });

    test('sender notification is only delivered to the sender, never the receiver', function () {
        [$sender, $senderUser] = createReportUser('employee');
        [$receiver, $receiverUser] = createReportUser('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifySender();

        expect($senderUser->fresh()->notifications)->toHaveCount(1)
            ->and($receiverUser->fresh()->notifications)->toHaveCount(0);
    });
});

describe('Report Reply Notifications', function () {
    test('notifyReplied creates a notification for the original sender', function () {
        [$sender, $senderUser] = createReportUser('employee');
        [$receiver, $receiverUser] = createReportUser('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'title' => 'تقرير الأداء',
            'feedback' => 'عمل ممتاز، تابع التقدم',
        ]);

        $report->notifyReplied();

        $senderUser->refresh();
        expect($senderUser->notifications)->toHaveCount(1);

        $data = $senderUser->notifications->first()->data;
        expect($data['title'])->toBe(__('filament.reports.reply_notification_title'))
            ->and($data['body'])->toContain('تقرير الأداء')
            ->and($data['icon'])->toBe('heroicon-o-chat-bubble-left-right')
            ->and($data['iconColor'])->toBe('warning');
    });

    test('reply notification action links to sender panel with view_reply label', function () {
        [$sender, $senderUser] = createReportUser('employee');
        [$receiver, $receiverUser] = createReportUser('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifyReplied();
        $data = $senderUser->refresh()->notifications->first()->data;

        expect($data['actions'][0]['label'])->toBe(__('filament.reports.view_reply'))
            ->and($data['actions'][0]['url'])->toBe($report->senderViewUrl());
    });
});

describe('Report Notification Panel Polling', function () {
    test('all 5 panels have database notifications with polling enabled', function () {
        $panels = ['admin', 'hr', 'pm', 'accountant', 'employee'];

        foreach ($panels as $panelId) {
            $panel = \Filament\Facades\Filament::getPanel($panelId);
            expect($panel)->not->toBeNull()
                ->and($panel->hasDatabaseNotifications())->toBeTrue();
        }
    });
});

describe('Report Sender Panel ID', function () {
    test('senderPanelId maps sender role to the correct panel', function () {
        $cases = [
            ['super_admin', 'admin'],
            ['hr_manager', 'hr'],
            ['project_manager', 'pm'],
            ['accountant', 'accountant'],
            ['employee', 'employee'],
        ];

        foreach ($cases as [$roleName, $expectedPanel]) {
            [$sender, $senderUser] = createReportUser($roleName);
            [$receiver, $receiverUser] = createReportUser('employee');

            $report = Report::factory()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
            ]);

            expect($report->senderPanelId())->toBe($expectedPanel);
        }
    });

    test('senderViewUrl builds the correct edit path in the sender panel', function () {
        [$sender, $senderUser] = createReportUser('hr_manager');
        [$receiver, $receiverUser] = createReportUser('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        expect($report->senderViewUrl())->toBe('/hr/reports/' . $report->id . '/edit');
    });
});
