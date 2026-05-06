<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Resume;

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

        // إذا لم يكن لديه طلب، نعرض له نموذج التقديم
        return view('application.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'expected_salary' => 'required|numeric|min:0',
            'resume_file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = auth()->user();

        // 1. إنشاء طلب التوظيف (سجل الموظف بحالة pending)
        $employee = Employee::create([
            'user_id' => $user->id,
            'job_title' => $request->job_title,
            'salary' => $request->expected_salary,
            'status' => 'pending', // قيد المراجعة
        ]);

        // 2. رفع السيرة الذاتية
        $path = $request->file('resume_file')->store('resumes', 'public');

        Resume::create([
            'employee_id' => $employee->id,
            'file_path' => $path,
            // هنا يمكن لاحقاً استدعاء API الذكاء الاصطناعي لاستخراج النص وحفظه في resume_text
        ]);

        return redirect()->route('job.apply')->with('success', 'تم تقديم طلبك بنجاح! يرجى انتظار رد الموارد البشرية.');
    }
}
