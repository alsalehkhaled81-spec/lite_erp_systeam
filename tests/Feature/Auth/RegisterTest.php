<?php

use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;

describe('Register', function () {
    test('register page is accessible', function () {
        Role::factory()->create(['name' => 'employee']);
        $this->get('/register')->assertStatus(200);
    });

    test('guest can register as employee and gets auto-logged in', function () {
        $role = Role::factory()->create(['name' => 'employee']);

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role_id', $role->id)
            ->call('register')
            ->assertRedirect(route('job.apply'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'is_approved' => false,
        ]);
    });

    test('admin role registration sets is_approved to false', function () {
        $role = Role::factory()->create(['name' => 'hr_manager']);

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'HR User')
            ->set('email', 'hr@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role_id', $role->id)
            ->call('register')
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'hr@example.com',
            'is_approved' => false,
        ]);
    });

    test('registration validates required fields', function () {
        Livewire::test(\App\Livewire\Auth\Register::class)
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password', 'role_id']);
    });

    test('registration validates email uniqueness', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        User::factory()->create(['email' => 'taken@example.com', 'role_id' => $role->id]);

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Test')
            ->set('email', 'taken@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role_id', $role->id)
            ->call('register')
            ->assertHasErrors(['email']);
    });

    test('registration validates password confirmation', function () {
        $role = Role::factory()->create(['name' => 'employee']);

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Test')
            ->set('email', 'test2@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->set('role_id', $role->id)
            ->call('register')
            ->assertHasErrors(['password']);
    });

    test('registration validates password minimum length', function () {
        $role = Role::factory()->create(['name' => 'employee']);

        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Test')
            ->set('email', 'test3@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->set('role_id', $role->id)
            ->call('register')
            ->assertHasErrors(['password']);
    });

    test('registration validates role_id exists', function () {
        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Test')
            ->set('email', 'test4@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role_id', 9999)
            ->call('register')
            ->assertHasErrors(['role_id']);
    });
});
