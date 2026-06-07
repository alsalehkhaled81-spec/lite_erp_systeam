<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Resume;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('Job Application', function () {
    test('authenticated user without employee sees application form', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $response = $this->actingAs($user)->get('/apply');
        $response->assertStatus(200);
    });

    test('authenticated user with active employee is redirected to employee panel', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $this->actingAs($user)
            ->get('/apply')
            ->assertRedirect('/employee');
    });

    test('authenticated user with pending employee sees status page', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        Employee::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $this->actingAs($user)
            ->get('/apply')
            ->assertStatus(200);
    });

    test('guest cannot access apply page', function () {
        $this->get('/apply')->assertRedirect('/login');
    });

    test('user can submit job application', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/apply', [
            'job_title' => 'Software Engineer',
            'expected_salary' => 5000,
            'resume_file' => $file,
        ]);

        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'job_title' => 'Software Engineer',
            'salary' => 5000,
            'status' => 'pending',
        ]);

        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('resumes', [
            'employee_id' => $employee->id,
        ]);
    });

    test('job application validates required fields', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $this->actingAs($user)
            ->post('/apply', [])
            ->assertSessionHasErrors(['job_title', 'expected_salary', 'resume_file']);
    });

    test('job application validates salary is numeric', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post('/apply', [
                'job_title' => 'Developer',
                'expected_salary' => 'not-a-number',
                'resume_file' => $file,
            ])->assertSessionHasErrors(['expected_salary']);
    });

    test('job application validates file type', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $file = UploadedFile::fake()->create('resume.exe', 100);

        $this->actingAs($user)
            ->post('/apply', [
                'job_title' => 'Developer',
                'expected_salary' => 5000,
                'resume_file' => $file,
            ])->assertSessionHasErrors(['resume_file']);
    });
});
