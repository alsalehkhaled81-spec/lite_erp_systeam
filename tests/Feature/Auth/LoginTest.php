<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

describe('Login', function () {
    test('login page is accessible', function () {
        $this->get('/login')->assertStatus(200);
    });

    test('user can login with valid credentials', function () {
        $role = Role::factory()->create(['name' => 'super_admin']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'is_approved' => true,
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    });

    test('user cannot login with invalid password', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        User::factory()->create([
            'role_id' => $role->id,
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'user@test.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    });

    test('unapproved admin user cannot login', function () {
        $role = Role::factory()->create(['name' => 'hr_manager']);
        User::factory()->create([
            'role_id' => $role->id,
            'email' => 'hr@test.com',
            'password' => Hash::make('password123'),
            'is_approved' => false,
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'hr@test.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    });

    test('login requires email and password', function () {
        Livewire::test(\App\Livewire\Auth\Login::class)
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    });

    test('login requires valid email format', function () {
        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    });

    test('authenticated user cannot access login page', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect();
    });
});
