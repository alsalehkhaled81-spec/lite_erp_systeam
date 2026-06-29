<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Vacancy;
use App\Models\User;

class ResumeParsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_parse_resume_upon_application()
    {
        Storage::fake('public');

        $vacancy = Vacancy::create([
            'title' => 'Software Engineer',
            'status' => 'open',
            'salary_min' => 1000,
            'salary_max' => 5000,
        ]);

        $user = User::factory()->create();

        // Create a fake PDF file
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('job.store'), [
            'vacancy_id' => $vacancy->id,
            'expected_salary' => 2000,
            'resume_file' => $file,
        ]);

        $response->assertRedirect(route('job.apply'));
        
        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'pending',
        ]);

        $employee = \App\Models\Employee::where('user_id', $user->id)->first();
        
        // Since we are uploading a fake PDF without actual text data, 
        // the parser will return empty string and fallback to the default text
        $this->assertDatabaseHas('resumes', [
            'employee_id' => $employee->id,
            'resume_text' => 'تعذر استخراج النص تلقائياً من الملف المرفق. قد يكون الملف عبارة عن صور ممسوحة ضوئياً (Scanned).',
        ]);
        
        Storage::disk('public')->assertExists('resumes/' . $file->hashName());
    }
}
