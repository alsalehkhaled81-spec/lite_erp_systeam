<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Resume;
use App\Models\Vacancy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('Job Application', function () {
    test('authenticated user without employee sees application form', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        Vacancy::factory()->create(['status' => 'open']);

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

    test('user can submit job application linked to a vacancy', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        $vacancy = Vacancy::factory()->create(['status' => 'open', 'title' => 'Senior PHP Developer']);

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post('/apply', [
            'vacancy_id' => $vacancy->id,
            'expected_salary' => 5000,
            'resume_text' => str_repeat('Experienced PHP developer with Laravel skills. ', 5),
            'resume_file' => $file,
        ]);

        $response->assertRedirect(route('job.apply'));

        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'job_title' => 'Senior PHP Developer',
            'salary' => 5000,
            'status' => 'pending',
        ]);

        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('resumes', [
            'employee_id' => $employee->id,
        ]);
        $resume = Resume::where('employee_id', $employee->id)->first();
        expect($resume->resume_text)->not->toBeNull();
    });

    test('job application validates required fields', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $this->actingAs($user)
            ->post('/apply', [])
            ->assertSessionHasErrors(['vacancy_id', 'expected_salary', 'resume_file']);
    });

    test('job application validates salary is numeric', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        $vacancy = Vacancy::factory()->create(['status' => 'open']);

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post('/apply', [
                'vacancy_id' => $vacancy->id,
                'expected_salary' => 'not-a-number',
                'resume_text' => str_repeat('Some resume text here. ', 5),
                'resume_file' => $file,
            ])->assertSessionHasErrors(['expected_salary']);
    });

    test('job application validates file type', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        $vacancy = Vacancy::factory()->create(['status' => 'open']);

        $file = UploadedFile::fake()->create('resume.exe', 100);

        $this->actingAs($user)
            ->post('/apply', [
                'vacancy_id' => $vacancy->id,
                'expected_salary' => 5000,
                'resume_text' => str_repeat('Some resume text here. ', 5),
                'resume_file' => $file,
            ])->assertSessionHasErrors(['resume_file']);
    });

    test('cannot apply to a closed vacancy', function () {
        Storage::fake('public');
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);
        $vacancy = Vacancy::factory()->create(['status' => 'closed']);

        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post('/apply', [
                'vacancy_id' => $vacancy->id,
                'expected_salary' => 5000,
                'resume_text' => str_repeat('Some resume text here. ', 5),
                'resume_file' => $file,
            ])->assertSessionHasErrors(['vacancy_id']);
    });
});
