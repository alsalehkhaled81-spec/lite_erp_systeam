<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Resume;
use App\Models\Vacancy;

class JobApplicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        // إذا كان الموظف مقبولاً ونشطاً، نوجهه للوحة الموظفين الخاصة به
        if ($employee && $employee->status === 'active') {
            return redirect('/employee');
        }

        // إذا كان لديه طلب مسبق (قيد المراجعة أو مرفوض)، نعرض له حالة الطلب
        if ($employee) {
            return view('application.status', compact('employee'));
        }

        // إذا لم يكن لديه طلب، نعرض له نموذج التقديم مع الوظائف الشاغرة
        $vacancies = Vacancy::where('status', 'open')->orderBy('created_at', 'desc')->get();

        return view('application.form', compact('vacancies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'expected_salary' => 'required|numeric|min:0',
            'resume_file' => 'required|mimes:pdf,doc,docx|max:2048',
            'resume_text' => 'nullable|string',
        ]);

        $vacancy = Vacancy::findOrFail($request->vacancy_id);

        if ($vacancy->status !== 'open') {
            return back()->withErrors(['vacancy_id' => 'هذه الوظيفة لم تعد متاحة.'])->withInput();
        }

        $user = auth()->user();

        // 1. إنشاء طلب التوظيف (سجل الموظف بحالة pending) مرتبط بالوظيفة الشاغرة
        $employee = Employee::create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'job_title' => $vacancy->title,
            'salary' => $request->expected_salary,
            'status' => 'pending', // قيد المراجعة
        ]);

        // 2. رفع السيرة الذاتية وحفظ النص والملف
        $file = $request->file('resume_file');
        $path = $file->store('resumes', 'public');
        
        $parser = new \App\Services\ResumeParserService();
        $fullPath = storage_path('app/public/' . $path);
        $extractedText = $parser->parse($fullPath, $file->getClientMimeType());

        $resumeText = trim($request->input('resume_text', ''));
        if (empty($resumeText)) {
            $resumeText = trim($extractedText);
        }
        if (empty($resumeText)) {
            $resumeText = 'تعذر استخراج النص تلقائياً من الملف المرفق. قد يكون الملف عبارة عن صور ممسوحة ضوئياً (Scanned).';
        }

        Resume::create([
            'employee_id' => $employee->id,
            'file_path' => $path,
            'resume_text' => $resumeText,
        ]);

        return redirect()->route('job.apply')->with('success', 'تم تقديم طلبك بنجاح! يرجى انتظار رد الموارد البشرية.');
    }
}
