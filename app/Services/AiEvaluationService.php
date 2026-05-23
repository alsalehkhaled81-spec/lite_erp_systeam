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
                ['role' => 'system', 'content' => "أنت مدير موارد بشرية خبير ومتخصص في تقييم الأداء الوظيفي. قم بتقييم أداء الموظف بناءً على البيانات المقدمة. أجب باللغة العربية بتنسيق Markdown مفصل ومنظم.\n\nيجب أن تكون الاستجابة بالشكل التالي:\n\n## 📊 التقييم العام\n[تحديد مستوى الأداء: ممتاز / جيد جداً / جيد / مقبول / ضعيف مع تبرير مختصر]\n\n## 📈 تحليل الأداء المهامي\n[تحليل مفصل لأداء المهام: نسبة الإنجاز، التأخير، الكفاءة]\n\n## 💪 نقاط القوة\n[قائمة بنقاط القوة الملاحظة]\n\n## ⚠️ نقاط تحتاج لتحسين\n[قائمة بنقاط الضعف والمجالات التي تحتاج تطوير]\n\n## 📋 تحليل الحضور والإجازات\n[تقييم أنماط الحضور والإجازات]\n\n## 📝 تحليل التواصل والتقارير\n[تقييم نشاط التقارير والتواصل]\n\n## 🎯 التوصيات وخطة التحسين\n[توصيات عملية مفصلة وخطوات محددة للتحسين]\n\n## 📌 الخلاصة\n[ملخص نهائي مختصر]\n\nاستخدم العناوين والقوائم والخط العريض والتنسيقات الأخرى في Markdown لجعل التقرير واضحاً ومنظماً ومفصلاً."],
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
