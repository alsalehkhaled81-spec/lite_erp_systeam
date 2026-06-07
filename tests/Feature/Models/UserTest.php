<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Expense;
use Filament\Panel;

describe('User Model', function () {
    test('user has correct fillable attributes', function () {
        $user = new User();
        expect($user->getFillable())->toContain(
            'role_id', 'name', 'email', 'password', 'profile_photo_path', 'is_approved'
        );
    });

    test('user hides password and remember token', function () {
        $user = new User();
        expect($user->getHidden())->toContain('password', 'remember_token');
    });

    test('user belongs to role', function () {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);
        expect($user->role)->not->toBeNull()->and($user->role->id)->toBe($role->id);
    });

    test('user has one employee', function () {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        expect($user->employee)->not->toBeNull()->and($user->employee->id)->toBe($employee->id);
    });

    test('user has many expenses', function () {
        $user = User::factory()->create();
        Expense::factory()->count(3)->create(['user_id' => $user->id]);
        expect($user->expenses)->toHaveCount(3);
    });

    test('canAccessPanel returns false when user has no role', function () {
        $user = new User();
        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('admin');
        expect($user->canAccessPanel($panel))->toBeFalse();
    });

    test('canAccessPanel routes super_admin to admin panel', function () {
        $role = Role::factory()->create(['name' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('admin');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    test('canAccessPanel routes hr_manager to hr panel', function () {
        $role = Role::factory()->create(['name' => 'hr_manager']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('hr');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    test('canAccessPanel routes project_manager to pm panel', function () {
        $role = Role::factory()->create(['name' => 'project_manager']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('pm');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    test('canAccessPanel routes accountant to accountant panel', function () {
        $role = Role::factory()->create(['name' => 'accountant']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('accountant');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    test('canAccessPanel routes employee to employee panel', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('employee');
        expect($user->canAccessPanel($panel))->toBeTrue();
    });

    test('canAccessPanel denies cross-panel access', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('admin');
        expect($user->canAccessPanel($panel))->toBeFalse();
    });

    test('canAccessPanel denies unknown panel', function () {
        $role = Role::factory()->create(['name' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $panel = Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('unknown_panel');
        expect($user->canAccessPanel($panel))->toBeFalse();
    });
});
