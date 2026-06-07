<?php

namespace App\Services;

class ResumeAnalysisService
{
    public function __construct(private AiService $aiService) {}

    public function analyzeResume(array $resumeData, string $keywords): ?array
    {
        $employeeName = $resumeData['employee_name'] ?? 'غير محدد';
        $jobTitle = $resumeData['job_title'] ?? 'غير محدد';
        $department = $resumeData['department'] ?? 'غير محدد';
        $skills = $resumeData['skills'] ?? 'لا توجد مهارات مسجلة';
        $resumeText = $resumeData['resume_text'] ?? 'لا يوجد نص للسيرة الذاتية';
        $salary = $resumeData['salary'] ?? 'غير محدد';
        $status = $resumeData['status'] ?? 'غير محدد';

        $messages = [
            [
                'role' => 'system',
                'content' => 'أنت خبير في الموارد البشرية وتحليل السير الذاتية. مهمتك هي تحليل السيرة الذاتية للمتقدم ومطابقتها مع متطلبات الوظيفة المحددة. أجب دائماً باللغة العربية. أجب بصيغة JSON فقط بدون أي نص إضافي.'
            ],
            [
                'role' => 'user',
                'content' => <<<PROMPT
قم بتحليل السيرة الذاتية التالية للمتقدم لشغل وظيفة، وقم بتقييم مدى مطابقة المتقدم لمتطلبات الوظيفة.

## بيانات المتقدم:
- الاسم: {$employeeName}
- المسمى الوظيفي المتقدم له: {$jobTitle}
- القسم: {$department}
- الحالة: {$status}
- الراتب المتوقع: {$salary}

## المهارات المسجلة:
{$skills}

## نص السيرة الذاتية:
{$resumeText}

## الكلمات المفتاحية المطلوبة للوظيفة:
{$keywords}

---

المطلوب:
1. تقييم من 100 (score)
2. تقرير مفصل يوضح أسباب التقييم (report)
3. نقاط القوة (strengths) - قائمة
4. نقاط الضعف (weaknesses) - قائمة
5. التوصية النهائية (recommendation): مقبول / مشروط / مرفوض
6. ملخص تنفيذي (summary): فقرة واحدة

أجب بصيغة JSON التالية فقط بدون أي نص إضافي:
{
    "score": 85,
    "report": "التقرير المفصل هنا",
    "strengths": ["نقطة قوة 1", "نقطة قوة 2"],
    "weaknesses": ["نقطة ضعف 1", "نقطة ضعف 2"],
    "recommendation": "مقبول",
    "summary": "ملخص تنفيذي"
}
PROMPT
            ],
        ];

        $response = $this->aiService->chat($messages);

        if (!$response) {
            return null;
        }

        $content = $response['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return null;
        }

        $content = preg_replace('/```json\s*|\s*```/', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'score' => 0,
                'report' => $content,
                'strengths' => [],
                'weaknesses' => [],
                'recommendation' => 'خطأ في التحليل',
                'summary' => 'لم يتمكن الذكاء الاصطناعي من تحليل السيرة الذاتية بشكل صحيح.',
                'raw_response' => $content,
            ];
        }

        return $decoded;
    }
}
