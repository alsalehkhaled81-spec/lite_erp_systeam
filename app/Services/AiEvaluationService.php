<?php

namespace App\Services;

use App\Models\Employee;
use OpenAI\Laravel\Facades\OpenAI;

class AiEvaluationService
{
    public function evaluate(Employee $employee): string
    {
        $data = $this->gatherEmployeeData($employee);
        $prompt = $this->buildPrompt($data);

        try {
            $aiService = app(\App\Services\AiService::class);
            
            $response = $aiService->chat([
                ['role' => 'system', 'content' => 'أنت مدير موارد بشرية خبير. قم بتقييم أداء الموظف بناءً على البيانات المقدمة. أجب باللغة العربية. كن موضوعياً وقدم توصيات عملية.'],
                ['role' => 'user', 'content' => $prompt],
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                return $response['choices'][0]['message']['content'];
            }

            return 'لم يتم تلقي استجابة صحيحة من الذكاء الاصطناعي.';
        } catch (\Exception $e) {
            return 'خطأ في الاتصال بخدمة الذكاء الاصطناعي: ' . $e->getMessage();
        }
    }

    protected function gatherEmployeeData(Employee $employee): array
    {
        $employee->load(['tasks', 'sentReports', 'receivedReports', 'leaves', 'user']);

        $tasks = $employee->tasks;
        $totalTasks = $tasks->count();
        $doneTasks = $tasks->where('status', 'done')->count();
        $overdueTasks = $tasks->filter(fn ($t) => $t->due_date && $t->due_date < now() && $t->status !== 'done')->count();
        $inProgressTasks = $tasks->where('status', 'in_progress')->count();

        $totalLeaves = $employee->leaves->count();
        $approvedLeaves = $employee->leaves->where('status', 'approved_by_hr')->count();

        $sentReports = $employee->sentReports->count();
        $repliedReports = $employee->sentReports->where('status', 'replied')->count();

        return [
            'name' => $employee->user?->name,
            'job_title' => $employee->job_title,
            'department' => $employee->department?->name,
            'status' => $employee->status,
            'salary' => $employee->salary,
            'hire_date' => $employee->hire_date?->format('Y-m-d'),
            'total_tasks' => $totalTasks,
            'done_tasks' => $doneTasks,
            'overdue_tasks' => $overdueTasks,
            'in_progress_tasks' => $inProgressTasks,
            'task_completion_rate' => $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100, 1) : 0,
            'total_leaves' => $totalLeaves,
            'approved_leaves' => $approvedLeaves,
            'sent_reports' => $sentReports,
            'replied_reports' => $repliedReports,
        ];
    }

    protected function buildPrompt(array $data): string
    {
        return "قم بتقييم أداء الموظف التالي:\n\n"
            . "الاسم: {$data['name']}\n"
            . "المسمى الوظيفي: {$data['job_title']}\n"
            . "القسم: " . ($data['department'] ?? 'غير محدد') . "\n"
            . "الحالة: {$data['status']}\n"
            . "تاريخ التعيين: {$data['hire_date']}\n\n"
            . "إحصائيات الأداء:\n"
            . "- إجمالي المهام: {$data['total_tasks']}\n"
            . "- المهام المنتهية: {$data['done_tasks']}\n"
            . "- المهام المتأخرة: {$data['overdue_tasks']}\n"
            . "- المهام قيد التنفيذ: {$data['in_progress_tasks']}\n"
            . "- نسبة الإنجاز: {$data['task_completion_rate']}%\n"
            . "- إجمالي الإجازات: {$data['total_leaves']}\n"
            . "- الإجازات المعتمدة: {$data['approved_leaves']}\n"
            . "- التقارير المرسلة: {$data['sent_reports']}\n"
            . "- التقارير التي تم الرد عليها: {$data['replied_reports']}\n\n"
            . "قم بتقديم:\n"
            . "1. تقييم عام للأداء (ممتاز/جيد جداً/جيد/مقبول/ضعيف)\n"
            . "2. نقاط القوة\n"
            . "3. نقاط تحتاج لتحسين\n"
            . "4. توصيات عملية";
    }
}
