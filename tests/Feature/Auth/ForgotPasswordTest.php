<?php

use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;

describe('Forgot Password', function () {
    test('forgot password page is accessible', function () {
        $this->get('/forgot-password')->assertStatus(200);
    });

    test('forgot password requires valid email', function () {
        Livewire::test(\App\Livewire\Auth\ForgotPassword::class)
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    });

    test('forgot password requires existing email', function () {
        Livewire::test(\App\Livewire\Auth\ForgotPassword::class)
            ->set('email', 'nonexistent@example.com')
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    });

    test('authenticated user cannot access forgot password page', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->get('/forgot-password')
            ->assertRedirect();
    });
});
