# تقييم أداء الموظف بالذكاء الاصطناعي — شرح مفصل

هذا الملف يشرح كيف يعمل تقييم الموظف عبر الذكاء الاصطناعي ضمن نظام ERP، والأساس الذي يُبنى عليه التقييم، وآلية المعالجة خطوة بخطوة.

---

## الملفات الأساسية المعنية

| الملف | الدور |
|------|-------|
| `app/Services/AiEvaluationService.php` | الخدمة الأساسية: تجمع بيانات الموظف، تبني الـ Prompt، تستدعي الذكاء الاصطناعي، وتُرجع التقييم. |
| `app/Services/AiService.php` | خدمة اتصال عامة بالذكاء الاصطناعي عبر وسيط LiteLLM (HTTP POST). |
| `app/Filament/Resources/EmployeeResource.php` | يعرّف زر "تقييم ذكاء اصطناعي" ويفتح المودال بالنتيجة. |
| `resources/views/filament/pages/ai-evaluation.blade.php` | قالب عرض نتيجة التقييم كـ Markdown. |
| `config/ai.php` (أو `.env`) | إعدادات الاتصال: الرابط، المفتاح، النموذج، temperature، max_tokens. |

---

## 1. نقطة الدخول (كيف يبدأ التقييم)

ضمن صفحة الموظفين في لوحة الأدمن/الموارد البشرية يوجد زر إجراء:

```php
// app/Filament/Resources/EmployeeResource.php
Tables\Actions\Action::make('ai_evaluate')
    ->label(__('filament.actions.ai_evaluate'))
    ->modalHeading(__('filament.actions.ai_evaluate_heading'))
    ->modalContent(fn (Employee $record) => view(
        'filament.pages.ai-evaluation',
        ['evaluation' => app(AiEvaluationService::class)->evaluate($record)]
    ))
```

عند الضغط على الزر يُستدعى `AiEvaluationService::evaluate($record)` ويُمرَّر الناتج إلى قالب المودال.

---

## 2. الأساس: البيانات التي يُبنى عليها التقييم

دالة `gatherEmployeeData()` تجمع البيانات الفعلية للموظف من قاعدة البيانات عبر العلاقات:

```php
$employee->load(['tasks', 'sentReports', 'receivedReports', 'leaves', 'user']);
```

### البيانات المُجمَّعة

#### أ) المهام (الأداء المهامي)
- **إجمالي المهام** (`total_tasks`)
- **المهام المنتهية** (`done_tasks`) — الحالة `done`
- **المهام المتأخرة** (`overdue_tasks`) — تجاوزت `due_date` ولم تُنجز
- **المهام قيد التنفيذ** (`in_progress_tasks`)
- **نسبة الإنجاز** (`task_completion_rate`) — تُحسب كنسبة مئوية: `(done / total) × 100`

```php
$task_completion_rate = $totalTasks > 0
    ? round(($doneTasks / $totalTasks) * 100, 1)
    : 0;
```

#### ب) الإجازات
- **إجمالي الإجازات** (`total_leaves`)
- **الإجازات المعتمدة** (`approved_leaves`) — الحالة `approved_by_hr`

#### ج) التقارير (التواصل)
- **التقارير المرسلة** (`sent_reports`)
- **التقارير التي تم الرد عليها** (`replied_reports`) — الحالة `replied`

#### د) بيانات الموظف الأساسية
- الاسم، المسمى الوظيفي، القسم، الحالة الوظيفية، الراتب، تاريخ التعيين.

### شكل مصفوفة البيانات النهائية

```php
return [
    'name'                 => $employee->user?->name,
    'job_title'            => $employee->job_title,
    'department'           => $employee->department?->name,
    'status'               => $employee->status,
    'salary'               => $employee->salary,
    'hire_date'            => $employee->hire_date?->format('Y-m-d'),
    'total_tasks'          => $totalTasks,
    'done_tasks'           => $doneTasks,
    'overdue_tasks'        => $overdueTasks,
    'in_progress_tasks'    => $inProgressTasks,
    'task_completion_rate' => ...,
    'total_leaves'         => $totalLeaves,
    'approved_leaves'      => $approvedLeaves,
    'sent_reports'         => $sentReports,
    'replied_reports'      => $repliedReports,
];
```

> **ملاحظة:** كل البيانات إحصائية رقمية واقعية من قاعدة البيانات — لا توجد آراء أو تقديرات يدوية مُدخلة، فقط أرقام.

---

## 3. بناء الـ Prompt

دالة `buildPrompt()` تصيغ البيانات كنص عربي منظّم يُرسل للنموذج:

```
قم بتقييم أداء الموظف التالي:

الاسم: {name}
المسمى الوظيفي: {job_title}
القسم: {department}
الحالة: {status}
تاريخ التعيين: {hire_date}

إحصائيات الأداء:
- إجمالي المهام: {total_tasks}
- المهام المنتهية: {done_tasks}
- المهام المتأخرة: {overdue_tasks}
- المهام قيد التنفيذ: {in_progress_tasks}
- نسبة الإنجاز: {task_completion_rate}%
- إجمالي الإجازات: {total_leaves}
- الإجازات المعتمدة: {approved_leaves}
- التقارير المرسلة: {sent_reports}
- التقارير التي تم الرد عليها: {replied_reports}

قم بتقديم:
1. تقييم عام للأداء (ممتاز/جيد جداً/جيد/مقبول/ضعيف)
2. نقاط القوة
3. نقاط تحتاج لتحسين
4. توصيات عملية
```

بالإضافة إلى **رسالة النظام (System Message)** التي توجّه سلوك النموذج:

```
أنت مدير موارد بشرية خبير ومتخصص في تقييم الأداء الوظيفي.
قم بتقييم أداء الموظف بناءً على البيانات المقدمة.
أجب باللغة العربية بتنسيق Markdown مفصل ومنظم.

يجب أن تكون الاستجابة بالشكل التالي:

## 📊 التقييم العام
## 📈 تحليل الأداء المهامي
## 💪 نقاط القوة
## ⚠️ نقاط تحتاج لتحسين
## 📋 تحليل الحضور والإجازات
## 📝 تحليل التواصل والتقارير
## 🎯 التوصيات وخطة التحسين
## 📌 الخلاصة
```

---

## 4. المعالجة: الاتصال بالذكاء الاصطناعي

دالة `evaluate()` تستدعي `AiService::chat()`:

```php
$aiService = app(\App\Services\AiService::class);

$response = $aiService->chat([
    ['role' => 'system', 'content' => "... رسالة النظام ..."],
    ['role' => 'user',   'content' => $prompt],
]);
```

### داخل `AiService::chat()`

يُرسَل طلب HTTP POST إلى وسيط **LiteLLM**:

```php
$response = Http::withHeaders([
    'x-litellm-api-key' => $key,
    'Content-Type'      => 'application/json',
])->timeout(120)->post($url, [
    'model'       => config('ai.model'),
    'messages'    => $messages,
    'max_tokens'  => config('ai.max_tokens', 4096),
    'temperature' => config('ai.temperature', 0.7),
]);
```

**تفاصيل الإعدادات** (من `config/ai.php` / `.env`):
- `ai.api_url` — رابط وسيط LiteLLM
- `ai.api_key` — مفتاح الوصول
- `ai.model` — اسم النموذج المستخدم
- `ai.max_tokens` — الحد الأقصى للرموز (افتراضي 4096)
- `ai.temperature` — درجة الإبداع (افتراضي 0.7)

### استخراج النتيجة

```php
if ($response && isset($response['choices'][0]['message']['content'])) {
    return $response['choices'][0]['message']['content'];
}
return 'لم يتم تلقي استجابة صحيحة من الذكاء الاصطناعي.';
```

مع معالجة الأخطاء:

```php
catch (\Exception $e) {
    return 'خطأ في الاتصال بخدمة الذكاء الاصطناعي: ' . $e->getMessage();
}
```

---

## 5. عرض النتيجة

يصل نص Markdown إلى قالب المودال ويُعرَض منسّقاً:

```php
// resources/views/filament/pages/ai-evaluation.blade.php
{!! Illuminate\Support\Str::markdown($evaluation) !!}
```

---

## 6. مخطط التدفق الكامل

```
زر "تقييم ذكاء اصطناعي" (EmployeeResource)
        │
        ▼
AiEvaluationService::evaluate($employee)
        │
        ├──► gatherEmployeeData()   ← جمع البيانات من DB
        │        ├── المهام (منجزة/متأخرة/جارية/نسبة الإنجاز)
        │        ├── الإجازات (الإجمالي/المعتمدة)
        │        ├── التقارير (المرسلة/المُردودة)
        │        └── بيانات الموظف (الاسم/الوظيفة/القسم...)
        │
        ├──► buildPrompt()          ← صياغة النص العربي
        │
        ├──► AiService::chat()      ← إرسال HTTP POST إلى LiteLLM
        │        ├── رسالة النظام (دور خبير HR + هيكل Markdown)
        │        └── رسالة المستخدم (البيانات الإحصائية)
        │
        └──► إرجاع نص التقييم (Markdown)
                │
                ▼
        عرض في المودال (ai-evaluation.blade.php)
```

---

## 7. ملاحظات مهمة

- **تقييم توليدي (Generative):** النتيجة نصّية تُولَّد في كل مرة — لا توجد درجة رقمية ثابتة تُحفظ، بل تُعاد توليدها عند كل ضغطة على الزر.
- **عدم حفظ النتيجة:** لا يُخزَّن التقييم في قاعدة البيانات؛ يظهر فقط في المودال.
- **الاعتماد على جودة البيانات:** دقة التقييم مرتبطة بمدى اكتمال البيانات (مهام، إجازات، تقارير). موظف بلا بيانات سيحصل على تقييم غير ذي معنى.
- **اللغة:** الاستجابة دائماً بالعربية بصيغة Markdown.
- **مهلة الانتظار:** مهلة الطلب 120 ثانية (`timeout(120)`) لاستيعاب زمن توليد النماذج الكبيرة.
- **عشوائية النتائج:** بسبب `temperature = 0.7`، قد يختلف التقييم قليلاً بين استدعاءين لنفس الموظف.
