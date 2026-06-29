<?php

use App\Models\Employee;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function ceoUser(): User
{
    $role = Role::firstOrCreate(['name' => 'super_admin'], ['description' => 'Super Admin']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'is_approved' => true,
    ]);
    Employee::factory()->create(['user_id' => $user->id]);

    return $user;
}

function accountantUser(): User
{
    $role = Role::firstOrCreate(['name' => 'accountant'], ['description' => 'Accountant']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'is_approved' => true,
    ]);
    Employee::factory()->create(['user_id' => $user->id]);

    return $user;
}

describe('Report notification appears in receiver panel', function () {
    test('accountant sending a report creates a notification the CEO can read', function () {
        [$ceo, $ceoEmployee] = [ceoUser(), null];
        $ceoEmployee = Employee::where('user_id', $ceo->id)->first();

        $accountant = accountantUser();
        $accountantEmployee = Employee::where('user_id', $accountant->id)->first();

        // Accountant creates a report addressed to the CEO
        $report = Report::create([
            'sender_id' => $accountantEmployee->id,
            'receiver_id' => $ceoEmployee->id,
            'title' => 'تقرير الميزانية',
            'content' => 'تفاصيل الميزانية',
            'status' => 'unread',
        ]);
        $report->notifyReceiver();

        // CEO has an unread database notification
        $ceo->refresh();
        expect($ceo->unreadNotifications)->toHaveCount(1);

        $data = $ceo->unreadNotifications->first()->data;
        expect($data['title'])->toBe(__('filament.reports.notification_title'))
            ->and($data['body'])->toContain('تقرير الميزانية')
            ->and($data['actions'][0]['url'])->toContain('/admin/reports/' . $report->id);
    });

    test('CEO admin dashboard renders the database notifications bell', function () {
        $ceo = ceoUser();
        $ceoEmployee = Employee::where('user_id', $ceo->id)->first();

        $sender = accountantUser();
        $senderEmployee = Employee::where('user_id', $sender->id)->first();

        Report::create([
            'sender_id' => $senderEmployee->id,
            'receiver_id' => $ceoEmployee->id,
            'title' => 'Monthly Finance',
            'content' => '...',
            'status' => 'unread',
        ])->notifyReceiver();

        $ceo->refresh();

        $response = $this->actingAs($ceo)->get('/admin');

        $response->assertStatus(200);

        $html = $response->content();

        expect($html)
            ->toContain('database-notifications')
            ->and($ceo->fresh()->unreadNotifications)->toHaveCount(1);
    });

    test('every panel has database notifications enabled', function () {
        foreach (['admin', 'hr', 'pm', 'accountant', 'employee'] as $panelId) {
            $panel = \Filament\Facades\Filament::getPanel($panelId);

            expect($panel)->not->toBeNull()
                ->and($panel->hasDatabaseNotifications())->toBeTrue("Panel [{$panelId}] should have database notifications enabled");
        }
    });
});
