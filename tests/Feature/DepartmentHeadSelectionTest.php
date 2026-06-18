<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;

describe('Department Head Selection', function () {

    test('eligibleDepartmentHead excludes super_admin role users', function () {
        $superAdminRole = Role::factory()->create(['name' => 'super_admin']);
        $employeeRole = Role::factory()->create(['name' => 'employee']);

        $superAdminUser = User::factory()->create(['role_id' => $superAdminRole->id]);
        $normalUser = User::factory()->create(['role_id' => $employeeRole->id]);

        $ceoEmployee = Employee::factory()->create(['user_id' => $superAdminUser->id, 'job_title' => 'CEO']);
        Employee::factory()->create(['user_id' => $normalUser->id, 'job_title' => 'Developer']);

        $eligible = Employee::eligibleDepartmentHead()->get();

        expect($eligible->pluck('id'))->not->toContain($ceoEmployee->id)
            ->and($eligible->count())->toBeGreaterThanOrEqual(1);
    });

    test('eligibleDepartmentHead excludes employees who are already department heads', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user1 = User::factory()->create(['role_id' => $role->id]);
        $user2 = User::factory()->create(['role_id' => $role->id]);

        $headEmployee = Employee::factory()->create(['user_id' => $user1->id, 'job_title' => 'Manager']);
        $normalEmployee = Employee::factory()->create(['user_id' => $user2->id, 'job_title' => 'Developer']);

        Department::factory()->create(['head_id' => $headEmployee->id]);

        $eligible = Employee::eligibleDepartmentHead()->get();

        expect($eligible->pluck('id'))->not->toContain($headEmployee->id)
            ->and($eligible->pluck('id'))->toContain($normalEmployee->id);
    });

    test('eligibleDepartmentHead includes current department head when editing', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $head = Employee::factory()->create(['user_id' => $user->id, 'job_title' => 'Manager']);

        $dept = Department::factory()->create(['head_id' => $head->id]);

        $eligible = Employee::eligibleDepartmentHead($dept->head_id)->get();

        expect($eligible->pluck('id'))->toContain($head->id);
    });

    test('eligibleDepartmentHead includes both exclusions together', function () {
        $superAdminRole = Role::factory()->create(['name' => 'super_admin']);
        $employeeRole = Role::factory()->create(['name' => 'employee']);

        $superAdminUser = User::factory()->create(['role_id' => $superAdminRole->id]);
        $normalUser1 = User::factory()->create(['role_id' => $employeeRole->id]);
        $normalUser2 = User::factory()->create(['role_id' => $employeeRole->id]);

        $ceoEmployee = Employee::factory()->create(['user_id' => $superAdminUser->id, 'job_title' => 'CEO']);
        $existingHead = Employee::factory()->create(['user_id' => $normalUser1->id, 'job_title' => 'Manager']);
        $normalEmployee = Employee::factory()->create(['user_id' => $normalUser2->id, 'job_title' => 'Developer']);

        Department::factory()->create(['head_id' => $existingHead->id]);

        $eligible = Employee::eligibleDepartmentHead()->get();

        expect($eligible->pluck('id'))
            ->not->toContain($ceoEmployee->id)
            ->not->toContain($existingHead->id)
            ->toContain($normalEmployee->id);
    });
});
