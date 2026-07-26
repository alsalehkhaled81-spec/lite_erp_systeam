# تقرير تحليلي مفصل — `ResumeAnalysisService.php`

> **الملف:** `app/Services/ResumeAnalysisService.php`
> **السطور:** 101 سطر
> **النوع:** خدمة (Service) — تحليل السير الذاتية ومطابقتها للوظائف بالذكاء الاصطناعي
> **التبعيات:** `App\Services\AiService` (محقونة عبر Constructor)

---

## ١. الدور في النظام

`ResumeAnalysisService` هي خدمة متخصصة في **فرز وتقييم السير الذاتية** للمتقدمين للوظائف. تأخذ نص السيرة الذاتية المستخرج + الكلمات المفتاحية للوظيفة، وترسلها للذكاء الاصطناعي للحصول على تقييم منظّم بصيغة **JSON** (تقييم رقمي، نقاط قوة/ضعف، توصية قبول/رفض).

```
موارد بشرية يضغط "تحليل بالذكاء الاصطناعي"
            │
            ├──► يدخل: الكلمات المفتاحية + المسمى المستهدف
            │
            ▼
    ┌───────────────────────────────┐
    │   ResumeAnalysisService       │ ← هذا الملف
    │───────────────────────────────│
    │ 1. بناء System Message (JSON) │
    │ 2. بناء User Message (بيانات) │
    │ 3. AiService::chat()          │
    │ 4. تنظيف وفك JSON             │
    │ 5. حفظ النتائج في DB          │
    └───────────────────────────────┘
            │
            ▼
    ┌───────────────────────────────┐
    │ JSON Output:                  │
    │ • score: 0-100               │
    │ • report: تقرير مفصل          │
    │ • strengths: [...]           │
    │ • weaknesses: [...]          │
    │ • recommendation: مقبول/مشروط/مرفوض │
    │ • summary: ملخص تنفيذي        │
    └───────────────────────────────┘
```

---

## ٢. الكود الكامل مع التحليل سطراً بسطر

### ٢.١ تعريف الكلاس وحقن التبعية

```php
namespace App\Services;

class ResumeAnalysisService
{
    public function __construct(private AiService $aiService) {}
```

**حقن التبعية عبر Constructor Promotion (PHP 8+):**

```php
public function __construct(private AiService $aiService) {}
```

هذا السطر المكثّف يُكافئ:

```php
protected AiService $aiService;

public function __construct(AiService $aiService)
{
    $this->aiService = $aiService;
}
```

| الميزة | الوصف |
|--------|------|
| **Constructor Promotion** | PHP 8 يتيح تعريف الخاصية وتمريرها في سطر واحد |
| **Type Hinting** | `AiService` مُحدد كنوع، مما يتيح للحاوية (Service Container) حقنه تلقائياً |
| **Dependency Injection** | الخدمة لا تنشئ `AiService` بنفسها، بل تستلمه — مما يسهّل الاختبار (Mocking) |

> **الفرق عن `AiEvaluationService`:** هذا الكلاس يستخدم Constructor Injection، بينما `AiEvaluationService` يستخدم `app(AiService::class)` (Service Location). كلاهما صالح، لكن Constructor Injection أنظف وأكثر قابلية للاختبار.

---

### ٢.٢ الدالة الرئيسية: `analyzeResume()`

```php
public function analyzeResume(array $resumeData, string $keywords, string $targetJob = ''): ?array
{
    set_time_limit(180);
```

**الترويسة:**

| المعامل | النوع | الإلزامية | الوصف |
|---------|------|----------|--------|
| `$resumeData` | array | مطلوب | بيانات المتقدم (الاسم، المسمى، المهارات، نص السيرة) |
| `$keywords` | string | مطلوب | الكلمات المفتاحية للوظيفة (مثل: PHP, Laravel, MySQL) |
| `$targetJob` | string | اختياري | المسمى الوظيفي المستهدف للتقييم عليه |

**القيمة المُرجعة:** `?array` — مصفوفة JSON مفكوكة، أو `null` عند الفشل التام.

**`set_time_limit(180)`:**
- يرفع الحد الأقصى لزمن تنفيذ السكربت من 30 ثانية (الافتراضي) إلى **180 ثانية** (3 دقائق).
- ضروري لأن استدعاء الذكاء الاصطناعي قد يستغرق وقتاً طويلاً (حتى 120 ثانية timeout + وقت المعالجة).

```php
    $employeeName = $resumeData['employee_name'] ?? 'غير محدد';
    $jobTitle = $resumeData['job_title'] ?? 'غير محدد';
    $department = $resumeData['department'] ?? 'غير محدد';
    $skills = $resumeData['skills'] ?? 'لا توجد مهارات مسجلة';
    $resumeText = $resumeData['resume_text'] ?? 'لا يوجد نص للسيرة الذاتية';
    $salary = $resumeData['salary'] ?? 'غير محدد';
    $status = $resumeData['status'] ?? 'غير محدد';
```

**استخراج البيانات مع قيم افتراضية:**

كل حقل له قيمة افتراضية عربية إذا لم يوجد في `$resumeData`. هذا يضمن أن الـ Prompt لن يحتوي على `null` أو قيم فارغة قد تربك النموذج.

| المتغير | المصدر | القيمة الافتراضية |
|---------|--------|-------------------|
| `$employeeName` | `$resumeData['employee_name']` | 'غير محدد' |
| `$jobTitle` | `$resumeData['job_title']` | 'غير محدد' |
| `$department` | `$resumeData['department']` | 'غير محدد' |
| `$skills` | `$resumeData['skills']` | 'لا توجد مهارات مسجلة' |
| `$resumeText` | `$resumeData['resume_text']` | 'لا يوجد نص للسيرة الذاتية' |
| `$salary` | `$resumeData['salary']` | 'غير محدد' |
| `$status` | `$resumeData['status']` | 'غير محدد' |

---

### ٢.٣ بناء الرسائل (Messages)

#### رسالة النظام (System Message)

```php
    $messages = [
        [
            'role' => 'system',
            'content' => 'أنت خبير في الموارد البشرية وتحليل السير الذاتية. مهمتك هي تحليل السيرة الذاتية للمتقدم ومطابقتها مع متطلبات الوظيفة المستهدفة. أجب دائماً باللغة العربية. أجب بصيغة JSON فقط بدون أي نص إضافي.'
        ],
```

**تحليل رسالة النظام:**

| العنصر | القيمة | الأهمية |
|--------|--------|---------|
| **الدور** | "خبير في الموارد البشرية وتحليل السير الذاتية" | يوجّه النموذج لطريقة تفكير HR |
| **المهمة** | "تحليل السيرة الذاتية ومطابقتها مع متطلبات الوظيفة" | يحدد الهدف بدقة |
| **اللغة** | "أجب دائماً باللغة العربية" | كل النصوص بالعربية |
| **⚠️ صيغة الإخراج** | **"أجب بصيغة JSON فقط بدون أي نص إضافي"** | **حرج جداً** — يمنع النموذج من إضافة مقدمة أو خاتمة |

> **لماذا JSON فقط؟** لأن النتيجة ستُفكّ (decoded) لاحقاً بـ `json_decode()` وتُحفظ في أعمدة منفصلة في قاعدة البيانات. أي نص إضافي سيفشل فك الترميز.

#### رسالة المستخدم (User Message) — Heredoc Syntax

```php
        [
            'role' => 'user',
            'content' => <<<PROMPT
قم بتحليل السيرة الذاتية التالية وتقييم مدى مطابقة المتقدم لشغل الوظيفة المستهدفة.

## بيانات المتقدم:
- الاسم: {$employeeName}
- المسمى الوظيفي المستهدف (المراد التقييم عليه): {$targetJob}
- المسمى الوظيفي الحالي: {$jobTitle}
- القسم الحالي: {$department}
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
```

**تحليل بنية الـ Prompt (Heredoc `<<<PROMPT ... PROMPT`):**

```
┌──────────────────────────────────────────────────┐
│ قسم 1: تعليمة opening                            │
│   "قم بتحليل السيرة الذاتية وتقييم المطابقة"     │
├──────────────────────────────────────────────────┤
│ قسم 2: بيانات المتقدم (6 حقول)                   │
│   الاسم، المستهدف، الحالي، القسم، الحالة، الراتب │
├──────────────────────────────────────────────────┤
│ قسم 3: المهارات المسجلة                          │
│   (نص حر قد يكون قائمة طويلة)                    │
├──────────────────────────────────────────────────┤
│ قسم 4: نص السيرة الذاتية ★ (المصدر الأساسي)     │
│   (المستخرج من PDF/DOCX بواسطة ResumeParser)     │
├──────────────────────────────────────────────────┤
│ قسم 5: الكلمات المفتاحية للوظيفة ★ (معيار المطابقة)│
│   "PHP, Laravel, MySQL, JavaScript, REST API"    │
├──────────────────────────────────────────────────┤
│ قسم 6: المطلوب (6 مخرجات)                        │
│   score, report, strengths, weaknesses,          │
│   recommendation, summary                        │
├──────────────────────────────────────────────────┤
│ قسم 7: قالب JSON الإلزامي                        │
│   (مثال محدد يمنع التشتت)                        │
└──────────────────────────────────────────────────┘
```

> **لماذا Heredoc (`<<<PROMPT`):** يتيح كتابة نص متعدد الأسطر مع تضمين المتغيرات (`{$variable}`) بدون الحاجة لدمج سلاسل معقدة بعلامات اقتباس ونقاط.

---

### ٢.٤ الإرسال والاستقبال

```php
    $response = $this->aiService->chat($messages);

    if (!$response) {
        return null;
    }
```

- يرسل الرسائل عبر `AiService` المُحقونة.
- إذا رجعت `null` (فشل الاتصال)، يُرجع `null` فوراً.

```php
    $content = $response['choices'][0]['message']['content'] ?? null;

    if (!$content) {
        return null;
    }
```

- يستخرج النص المُولّد من استجابة OpenAI-compatible.
- إذا كان فارغاً، يُرجع `null`.

---

### ٢.٥ تنظيف وفك ترميز JSON

```php
    $content = preg_replace('/```json\s*|\s*```/', '', $content);
    $content = trim($content);
```

**لماذا التنظيف ضروري؟**

بعض النماذج (رغم التعليمات الصارمة) قد تُحاط استجابة JSON بـ Markdown code fences:

```
```json
{
    "score": 85,
    ...
}
```
```

**التعابير النمطية (Regex):**

| النمط | الوصف |
|-------|------|
| `` ```json\s* `` | يطابق `` ```json `` متبوعاً بأي مسافات |
| `` \s*``` `` | يطابق `` ``` `` مسبوقاً بأي مسافات |
| `|` | أو (بدائل) |

النتيجة: إزالة fences وتبقى JSON نظيفة.

**`trim($content)`:** يزيل المسافات والأسطر الزائدة من البداية والنهاية.

```php
    $decoded = json_decode($content, true);
```

- `json_decode($content, true)` يفك ترميز JSON إلى مصفوفة PHP ترابطية (associative array).
- المعامل `true` يُرجع مصفوفة (وليس كائن stdClass).

---

### ٢.٦ معالجة فشل فك الترميز

```php
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
```

**منطق متسامح مع الأخطاء (Graceful Degradation):**

عند فشل `json_decode` (النموذج لم يُرجع JSON صحيح):

| الحقل | القيمة | السبب |
|-------|--------|------|
| `score` | `0` | تقييم محايد (ليس صفراً حقيقياً) |
| `report` | `$content` (النص الخام) | قد يحتوي معلومات مفيدة رغم عدم كونه JSON |
| `strengths` | `[]` | قائمة فارغة |
| `weaknesses` | `[]` | قائمة فارغة |
| `recommendation` | `'خطأ في التحليل'` | إشارة واضحة للفشل |
| `summary` | رسالة خطأ عربية | تفسير للمستخدم |
| `raw_response` | `$content` | **احتفاظ بالنص الخام** للتشخيص |

> **لماذا `raw_response`؟** يسمح للمطور بفحص ما أرجعه النموذج فعلاً عند فشل التحليل، مما يساعد في تحسين الـ Prompt لاحقاً.

> **الفرق عن `AiEvaluationService`:** هذه الخدمة تُرجع `array` (بيانات منظّمة)، بينما تُرجع الأخرى `string` (Markdown).

---

## ٣. مخطط تدفق المنطق الكامل

```
        ┌─────────────────────────┐
        │ المدخلات:               │
        │ • $resumeData (array)   │
        │ • $keywords (string)    │
        │ • $targetJob (string)   │
        └───────────┬─────────────┘
                    │
                    ▼
        ┌─────────────────────────┐
        │ set_time_limit(180)     │
        │ رفع مهلة التنفيذ        │
        └───────────┬─────────────┘
                    │
                    ▼
        ┌─────────────────────────┐
        │ استخراج البيانات        │
        │ مع قيم افتراضية         │
        └───────────┬─────────────┘
                    │
                    ▼
    ┌───────────────────────────────┐
    │ بناء الرسائل (Messages):      │
    │───────────────────────────────│
    │ System: JSON فقط + عربي      │
    │ User:   Heredoc مع 7 أقسام   │
    └───────────────┬───────────────┘
                    │
                    ▼
    ┌───────────────────────────────┐
    │ $this->aiService->chat()      │
    │ (عبر Constructor Injection)   │
    └───────────────┬───────────────┘
                    │
            ┌───────┴────────┐
            │                │
        null │                │ response
            │                │
            ▼                ▼
     ┌──────────┐  ┌────────────────────┐
     │ return   │  │ استخراج content    │
     │ null     │  └────────┬───────────┘
     └──────────┘           │
                    ┌───────┴────────┐
                    │                │
                فارغ │                │ موجود
                    │                │
                    ▼                ▼
             ┌──────────┐  ┌────────────────────┐
             │ return   │  │ preg_replace()     │
             │ null     │  │ إزالة ```json```   │
             └──────────┘  └────────┬───────────┘
                                    │
                                    ▼
                          ┌────────────────────┐
                          │ json_decode()      │
                          └────────┬───────────┘
                                   │
                         ┌─────────┴──────────┐
                         │                    │
                     نجاح │                    │ فشل
                         │                    │
                         ▼                    ▼
                ┌────────────┐     ┌────────────────────┐
                │ return     │     │ return array(      │
                │ $decoded   │     │   score=0,         │
                │ (JSON Array)│     │   recommendation=  │
                └────────────┘     │     'خطأ',         │
                                   │   raw_response=... │
                                   │ )                  │
                                   └────────────────────┘
```

---

## ٤. مقارنة مع `AiEvaluationService`

| المعيار | ResumeAnalysisService | AiEvaluationService |
|---------|----------------------|---------------------|
| **المدخل** | بيانات سيرة ذاتية + كلمات مفتاحية | نموذج Employee كامل |
| **حقن التبعية** | Constructor Injection | Service Location (`app()`) |
| **صيغة الإخراج** | **JSON** (منظّم قابل للفك) | **Markdown** (نص حر) |
| **الحفظ في DB** | ✅ يُحفظ في 5 أعمدة | ❌ لا يُحفظ (يُعرض فقط) |
| **معالجة فشل JSON** | ✅ قيم افتراضية + raw_response | N/A (Markdown لا يُفك) |
| **مهلة التنفيذ** | 180 ثانية (set_time_limit) | يعتمد على AiService (120s) |
| **عدد المؤشرات** | 7 حقول إدخال | 15 مؤشر محسوب |
| **الاستخدام** | فرز المتقدمين للوظائف | تقييم الموظفين الحاليين |

---

## ٥. حفظ النتائج في قاعدة البيانات

النتائج تُحفظ في جدول `resumes` في 5 أعمدة مخصصة للذكاء الاصطناعي:

```php
// في ResumeResource.php (بعد استدعاء analyzeResume)
$resume->update([
    'ai_score' => $result['score'],                    // int (0-100)
    'ai_summary' => $result['summary'],                // longText
    'ai_report' => $result['report'],                  // longText
    'ai_recommendation' => $result['recommendation'],  // varchar
    'analyzed_at' => now(),                            // timestamp
]);
```

| العمود | النوع | المصدر |
|--------|------|--------|
| `ai_score` | integer | `$result['score']` |
| `ai_summary` | longText | `$result['summary']` |
| `ai_report` | longText | `$result['report']` |
| `ai_recommendation` | varchar | `$result['recommendation']` |
| `analyzed_at` | timestamp | `now()` |

> نقاط القوة والضعف (`strengths`, `weaknesses`) لا تُحفظ في أعمدة منفصلة بل ضمن `ai_report`.

---

## ٦. مثال عملي كامل

### المدخلات:

```php
$resumeData = [
    'employee_name' => 'فراس العبيد',
    'job_title' => 'Backend Developer',
    'department' => 'تطوير الويب',
    'skills' => 'PHP, Laravel, MySQL, JavaScript, Git',
    'resume_text' => 'مطور Backend بخبرة 4 سنوات في Laravel...',
    'salary' => 9500,
    'status' => 'pending',
];

$keywords = 'PHP, Laravel, MySQL, REST API, Git';
$targetJob = 'مطور Backend Laravel';
```

### المُخرجات (JSON مفكوك):

```php
[
    'score' => 82,
    'report' => 'يملك المتقدم خبرة قوية في Laravel و MySQL...',
    'strengths' => [
        'خبرة 4 سنوات في Laravel',
        'إتقان MySQL والاستعلامات المحسّنة',
        'معرفة بـ Git وإدارة الإصدارات',
    ],
    'weaknesses' => [
        'لا توجد إشارة لخبرة في REST API',
        'لا توجد خبرة في Redis أو الطوابير',
    ],
    'recommendation' => 'مشروط',
    'summary' => 'متقدم قوي في Laravel لكن يفتقر لخبرة API.',
]
```

---

## ٧. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Service Layer** | الكلاس بأكمله | منطق فرز السير الذاتية منفصل |
| **Constructor DI** | `__construct(private AiService $aiService)` | حقن تبعية نظيف |
| **Graceful Degradation** | معالجة فشل JSON | قيم افتراضية بدل التعطل |
| **Prompt Template** | Heredoc `<<<PROMPT` | قالب Prompt ثابت مع متغيرات ديناميكية |
| **JSON Contract** | "أجب بصيغة JSON فقط" | عقد صارم بين الخدمة والنموذج |

---

## ٨. نقاط القوة والقيود

### نقاط القوة

| النقطة | الوصف |
|--------|------|
| **JSON منظّم** | نتائج قابلة للحفظ والفلترة والترتيب آلياً |
| **تقييم موضوعي** | مطابقة بناءً على كلمات مفتاحية محددة |
| **قيم افتراضية ذكية** | عدم تعطل حتى عند فشل JSON |
| **احتفاظ بـ raw_response** | يساعد في التشخيص والتحسين |
| **حقن تبعية نظيف** | قابل للاختبار بسهولة |

### القيود

| القيد | الوصف |
|------|------|
| **اعتماد على جودة النص المستخرج** | إذا فشل استخراج PDF/DOCX، التحليل يكون بلا معنى |
| **الكلمات المفتاحية يدوية** | HR يجب إدخالها يدوياً لكل تحليل |
| **عشوائية التقييم** | نفس السيرة قد تحصل على تقييم مختلف قليلاً |
| **تكلفة API** | كل تحليل يستهلك tokens |
| **لا يحفظ strengths/weaknesses** | تُدمج ضمن report بدلاً من أعمدة منفصلة |

---

*نهاية تقرير `ResumeAnalysisService.php`*
