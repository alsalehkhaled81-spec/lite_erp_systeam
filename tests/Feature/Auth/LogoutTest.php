<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Password;

describe('Logout', function () {
    test('user can logout', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    });

    test('logout invalidates session', function () {
        $role = Role::factory()->create(['name' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    });
});
