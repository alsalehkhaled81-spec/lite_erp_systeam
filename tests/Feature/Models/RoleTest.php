<?php

use App\Models\Role;
use App\Models\User;

describe('Role Model', function () {
    test('role has correct fillable attributes', function () {
        $role = new Role();
        expect($role->getFillable())->toContain('name', 'description');
    });

    test('role has many users', function () {
        $role = Role::factory()->create();
        User::factory()->count(3)->create(['role_id' => $role->id]);
        expect($role->users)->toHaveCount(3);
    });
});
