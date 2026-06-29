<?php

use App\Models\Employee;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;

function createEmployeeWithRole(string $roleName): array
{
    $role = Role::firstOrCreate(['name' => $roleName], ['description' => ucfirst($roleName)]);
    $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
    $employee = Employee::factory()->create(['user_id' => $user->id]);

    return [$employee, $user];
}

describe('Report routing logic', function () {
    test('receiverPanelId maps receiver role to the correct panel', function () {
        $cases = [
            ['super_admin', 'admin'],
            ['hr_manager', 'hr'],
            ['project_manager', 'pm'],
            ['accountant', 'accountant'],
            ['employee', 'employee'],
        ];

        foreach ($cases as [$roleName, $expectedPanel]) {
            [$receiver, $receiverUser] = createEmployeeWithRole($roleName);
            [$sender, $senderUser] = createEmployeeWithRole('employee');

            $report = Report::factory()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
            ]);

            expect($report->receiverPanelId())->toBe($expectedPanel);
        }
    });

    test('viewUrl builds the correct edit path in the receiver panel', function () {
        [$receiver, $receiverUser] = createEmployeeWithRole('hr_manager');
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        expect($report->viewUrl())->toBe('/hr/reports/' . $report->id . '/edit');
    });

    test('unknown receiver role falls back to employee panel', function () {
        $role = Role::factory()->create(['name' => 'unknown_role']);
        $receiverUser = User::factory()->create(['role_id' => $role->id]);
        $receiver = Employee::factory()->create(['user_id' => $receiverUser->id]);
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        expect($report->receiverPanelId())->toBe('employee');
    });
});

describe('Report notification delivery', function () {
    test('notifyReceiver creates a database notification for the receiver', function () {
        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'title' => 'تقرير الأداء الأسبوعي',
        ]);

        $report->notifyReceiver();

        $receiverUser->refresh();

        expect($receiverUser->notifications)->toHaveCount(1);

        $notification = $receiverUser->notifications->first();
        $data = $notification->data;

        expect($data['title'])->toBe(__('filament.reports.notification_title'))
            ->and($data['body'])->toContain('تقرير الأداء الأسبوعي')
            ->and($data['icon'])->toBe('heroicon-o-document-text');
    });

    test('notification includes a clickable view action pointing to the report', function () {
        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifyReceiver();
        $data = $receiverUser->refresh()->notifications->first()->data;

        expect($data['actions'])->toBeArray()
            ->and($data['actions'][0]['label'])->toBe(__('filament.reports.view_report'))
            ->and($data['actions'][0]['url'])->toBe($report->viewUrl())
            ->and($data['actions'][0]['shouldMarkAsRead'])->toBeTrue();
    });

    test('notification body includes the sender name', function () {
        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $senderUser->update(['name' => 'Khalid Ahmad']);

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'title' => 'Monthly Status',
        ]);

        $report->notifyReceiver();
        $data = $receiverUser->refresh()->notifications->first()->data;

        expect($data['body'])->toContain('Khalid Ahmad');
    });

    test('notification is only delivered to the receiver, never the sender', function () {
        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('hr_manager');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifyReceiver();

        expect($receiverUser->fresh()->notifications)->toHaveCount(1)
            ->and($senderUser->fresh()->notifications)->toHaveCount(0);
    });
});

describe('Notification localization', function () {
    test('notification renders in Arabic', function () {
        app()->setLocale('ar');

        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifyReceiver();
        $data = $receiverUser->refresh()->notifications->first()->data;

        expect($data['title'])->toBe('تقرير جديد')
            ->and($data['actions'][0]['label'])->toBe('عرض التقرير');
    });

    test('notification renders in English', function () {
        app()->setLocale('en');

        [$receiver, $receiverUser] = createEmployeeWithRole('employee');
        [$sender, $senderUser] = createEmployeeWithRole('employee');

        $report = Report::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $report->notifyReceiver();
        $data = $receiverUser->refresh()->notifications->first()->data;

        expect($data['title'])->toBe('New Report')
            ->and($data['actions'][0]['label'])->toBe('View Report');
    });
});
