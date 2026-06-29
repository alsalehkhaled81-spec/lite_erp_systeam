<?php

use App\Models\Resume;
use App\Models\Employee;
use App\Models\Vacancy;
use App\Models\User;
use App\Models\Role;

describe('Resume AI Persistence', function () {
    test('resume has AI fillable attributes', function () {
        $resume = new Resume();
        expect($resume->getFillable())->toContain(
            'ai_score', 'ai_summary', 'ai_report', 'ai_recommendation', 'analyzed_at'
        );
    });

    test('resume can store and retrieve AI analysis results', function () {
        $resume = Resume::factory()->create();

        $resume->update([
            'ai_score' => 85,
            'ai_summary' => 'Strong candidate with excellent PHP skills.',
            'ai_report' => 'Detailed analysis report here.',
            'ai_recommendation' => 'مقبول',
            'analyzed_at' => now(),
        ]);

        $resume->refresh();
        expect($resume->ai_score)->toBe(85)
            ->and($resume->ai_summary)->toBe('Strong candidate with excellent PHP skills.')
            ->and($resume->ai_recommendation)->toBe('مقبول')
            ->and($resume->analyzed_at)->not->toBeNull();
    });

    test('resume ai fields are nullable by default', function () {
        $resume = Resume::factory()->create();
        expect($resume->ai_score)->toBeNull()
            ->and($resume->analyzed_at)->toBeNull();
    });
});

describe('Resume Vacancy Linkage', function () {
    test('resume linked to employee can access vacancy keywords', function () {
        $vacancy = Vacancy::factory()->create([
            'title' => 'Backend Developer',
            'requirements' => 'PHP, Laravel, MySQL, Redis',
        ]);
        $employee = Employee::factory()->create(['vacancy_id' => $vacancy->id]);
        $resume = Resume::factory()->create(['employee_id' => $employee->id]);

        expect($resume->employee->vacancy)->not->toBeNull()
            ->and($resume->employee->vacancy->title)->toBe('Backend Developer')
            ->and($resume->employee->vacancy->requirements)->toBe('PHP, Laravel, MySQL, Redis');
    });

    test('application creates resume with both file and text', function () {
        $vacancy = Vacancy::factory()->create(['status' => 'open', 'title' => 'Full Stack Dev']);
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $this->actingAs($user)->post('/apply', [
            'vacancy_id' => $vacancy->id,
            'expected_salary' => 6000,
            'resume_text' => str_repeat('Full stack developer with React and Node.js experience. ', 5),
            'resume_file' => $file,
        ])->assertRedirect(route('job.apply'));

        $resume = Resume::whereHas('employee', fn ($q) => $q->where('user_id', $user->id))->first();

        expect($resume)->not->toBeNull()
            ->and($resume->file_path)->not->toBeNull()
            ->and($resume->resume_text)->toContain('Full stack developer');
    });
});

describe('Resume Default Filtering', function () {
    test('only pending applications are returned when filtering by default', function () {
        $vacancy = Vacancy::factory()->create();

        $pendingEmployee = Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);
        $activeEmployee = Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'active']);
        $rejectedEmployee = Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'rejected']);

        Resume::factory()->create(['employee_id' => $pendingEmployee->id]);
        Resume::factory()->create(['employee_id' => $activeEmployee->id]);
        Resume::factory()->create(['employee_id' => $rejectedEmployee->id]);

        $pendingResumes = Resume::whereHas('employee', fn ($q) => $q->where('status', 'pending'))->get();

        expect($pendingResumes)->toHaveCount(1)
            ->and($pendingResumes->first()->employee->status)->toBe('pending');
    });
});
