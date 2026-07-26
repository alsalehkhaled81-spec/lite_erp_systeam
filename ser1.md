# تقرير تحليلي مفصل — `AiService.php`

> **الملف:** `app/Services/AiService.php`
> **السطور:** 109 سطر
> **النوع:** خدمة (Service) — الطبقة الأساسية للاتصال بالذكاء الاصطناعي
> **التبعيات:** `Illuminate\Support\Facades\Http`, `Illuminate\Support\Facades\Log`

---

## ١. الدور في النظام

`AiService` هي **الطبقة الأساسية** التي يتصل من خلالها النظام بخدمة الذكاء الاصطناعي التوليدي (Generative AI). تعمل كـ **واجهة موحّدة (Unified Gateway)** تُغلف تفاصيل البروتوكول (HTTP POST)، والمصادقة (API Key)، والمعالجة (JSON parsing)، ومعالجة الأخطاء.

```
النظام (Controllers/Resources/Services الأخرى)
            │
            ▼
    ┌───────────────┐
    │  AiService    │ ← هذا الملف
    │───────────────│
    │ chat()        │
    │ processFile() │
    └───────┬───────┘
            │ HTTP POST (120s timeout)
            ▼
    ┌───────────────────┐
    │ LiteLLM Proxy     │
    │ (x-litellm-api-key)│
    └───────┬───────────┘
            │
            ▼
    ┌───────────────────┐
    │ Gemini-3-Flash    │
    │ (نموذج لغوي)      │
    └───────────────────┘
```

### من يستخدم هذه الخدمة؟

| المستدعي | الملف | الوظيفة |
|----------|------|---------|
| `AiEvaluationService` | `app/Services/AiEvaluationService.php` | تقييم أداء الموظفين |
| `ResumeAnalysisService` | `app/Services/ResumeAnalysisService.php` | تحليل السير الذاتية |
| `PayslipPdfService` | `app/Services/PayslipPdfService.php` | توليد رسائل تحفيزية في قسائم الرواتب |

---

## ٢. الكود الكامل مع التحليل سطراً بسطر

### ٢.١ تعريف الكلاس والـ Namespace

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
```

- **`namespace App\Services`**: الكلاس ضمن طبقة الخدمات، منفصل عن Controllers و Models.
- **`Http` Facade**: لإنشاء طلبات HTTP إلى وسيط الذكاء الاصطناعي.
- **`Log` Facade**: لتسجيل الأخطاء في ملفات الـ log.

> الكلاس **لا يرث** من أي كلاس آخر ولا يطبّق أي واجهة (Interface) — هو كلاس مستقل بسيط.

---

### ٢.٢ الدالة الأساسية: `chat()`

```php
/**
 * Send a chat request to the AI proxy.
 *
 * @param array $messages
 * @return array|null
 */
public function chat(array $messages): ?array
{
    $url = config('ai.api_url');
    $key = config('ai.api_key');
    $model = config('ai.model');
```

**التحليل:**
- **المُدخل:** مصفوفة `$messages` بصيغة OpenAI Chat Completions، كل عنصر يحتوي على `role` (system/user/assistant) و `content` (النص).
- **المُخرجات:** `array|null` — الاستجابة كاملة كـ JSON، أو `null` عند الفشل.
- تُجلب الإعدادات من `config/ai.php` الذي يقرأ من `.env`:
  - `AI_API_URL`: رابط الوسيط (LiteLLM Proxy)
  - `AI_API_KEY`: مفتاح الوصول
  - `AI_MODEL`: اسم النموذج (gemini-3-flash-preview)

```php
    try {
        $response = Http::withHeaders([
            'x-litellm-api-key' => $key,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($url, [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => config('ai.max_tokens', 4096),
            'temperature' => config('ai.temperature', 0.7),
        ]);
```

**التحليل التفصيلي:**

| العنصر | الوصف |
|--------|------|
| `Http::withHeaders()` | إضافة ترويسات مخصصة للطلب |
| `x-litellm-api-key` | ترويسة المصادقة الخاصة بوسيط LiteLLM (وليست `Authorization: Bearer` القياسية) |
| `->timeout(120)` | مهلة انتظار **120 ثانية** — مدة طويلة مقصودة لأن توليد النصوص الطويلة يستغرق وقتاً |
| `->post($url, [...])` | إرسال طلب HTTP POST |
| `'model'` | اسم النموذج المطلوب استخدامه |
| `'messages'` | محادثة الرسائل (system + user) |
| `'max_tokens'` | الحد الأقصى للرموز المُولّدة (افتراضي 4096) — يتحكم بطول الاستجابة |
| `'temperature'` | درجة الإبداع/العشوائية (0.0 = حتمي، 1.0 = إبداعي جداً). 0.7 = توازن معقول |

```php
        if ($response->successful()) {
            return $response->json();
        }
```

- إذا كان كود الاستجابة HTTP 2xx، يُرجع الاستجابة كاملة كمصفوفة PHP (JSON decoded).

```php
        Log::error('AI Service Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
```

- إذا فشل الطلب (4xx أو 5xx)، يسجّل الخطأ مع كود الحالة ونص الاستجابة، ثم يُرجع `null`.

```php
    } catch (\Exception $e) {
        Log::error('AI Service Exception', ['message' => $e->getMessage()]);
        return null;
    }
}
```

- **معالجة الاستثناءات الشاملة:** أي استثناء (انقطاع شبكة، DNS failure، timeout exceeded) يُلتقط ويُسجّل ويُرجع `null`.

> **نمط تصميمي مهم:** الدالة **لا ترمي استثناءات (no throws)** أبداً — بل تُرجع `null` دائماً عند الفشل. هذا يجعل المتصلين لا يحتاجون لـ try-catch، ويقررون بأنفسهم كيفية التعامل مع `null`.

---

### ٢.٣ الدالة الثانوية: `processUploadedFile()`

```php
/**
 * Process an uploaded file (Livewire TemporaryUploadedFile) and convert it to the format required by the AI.
 *
 * @param mixed $file
 * @return array|null
 */
public function processUploadedFile($file): ?array
{
    if (!$file) return null;
```

- تتحقق من وجود الملف أولاً. إذا لم يوجد، تُرجع `null`.

```php
    $mimeType = $file->getMimeType();
    $extension = strtolower($file->getClientOriginalExtension());
    $path = $file->getRealPath();
```

| المتغير | المصدر | الوصف |
|---------|--------|------|
| `$mimeType` | `$file->getMimeType()` | نوع MIME الفعلي للملف (مثل `image/png`) |
| `$extension` | `$file->getClientOriginalExtension()` | امتداد الملف الأصلي (مثل `png`) |
| `$path` | `$file->getRealPath()` | المسار الكامل للملف المؤقت على القرص |

```php
    // Correct MIME for certain files
    $mimeType = $this->resolveMimeType($extension, $mimeType);
```

- تصحيح نوع MIME باستدعاء دالة مساعدة (مشرّحة في القسم التالي).

```php
    // Handle standard media (images, audio, video) natively supported by Gemini / Multimodal
    if (str_starts_with($mimeType, 'image/')
        || str_starts_with($mimeType, 'audio/')
        || str_starts_with($mimeType, 'video/')) {
        $base64 = base64_encode(file_get_contents($path));
        return [
            'type' => 'image_url',
            'image_url' => [
                'url' => "data:{$mimeType};base64,{$base64}"
            ]
        ];
    }
```

**معالجة الوسائط (Multimodal):**

| الخطوة | الوصف |
|--------|------|
| فحص النوع | إذا كان الملف صورة (`image/`) أو صوتاً (`audio/`) أو فيديو (`video/`) |
| قراءة المحتوى | `file_get_contents($path)` يقرأ الملف الثنائي بالكامل |
| ترميز Base64 | `base64_encode()` يحوّل البيانات الثنائية إلى نص آمن للنقل |
| بناء Data URI | `data:{$mimeType};base64,{$base64}` — تنسيق URI مضمّن يفهمه النموذج مباشرة |

> النموذج Gemini يدعم الوسائط المتعددة (Multimodal)، لذا يمكنه "رؤية" الصور و"سماع" الصوت مباشرة من Base64.

```php
    // Handle text/documents by embedding content directly
    $textTypes = ['txt', 'json', 'xml', 'md', 'csv', 'js', 'yaml', 'php', 'html', 'css'];
    if (str_starts_with($mimeType, 'text/') || in_array($extension, $textTypes)) {
        $content = file_get_contents($path);
        return [
            'type' => 'text',
            'text' => "File Name: {$file->getClientOriginalName()}\n\nContent:\n{$content}"
        ];
    }
```

**معالجة الملفات النصية:**

| الخطوة | الوصف |
|--------|------|
| قائمة الأنواع المدعومة | `txt, json, xml, md, csv, js, yaml, php, html, css` |
| الفحص | إذا كان MIME يبدأ بـ `text/` أو الامتداد في القائمة |
| القراءة | يقرأ المحتوى كنص عادي (وليس Base64) |
| التنسيق | يُسبق باسم الملف ثم المحتوى: `File Name: example.php\n\nContent:\n<?php echo "Hello";` |

> الملفات النصية لا تحتاج إلى Base64 لأنها نص أصلاً — يمكن تضمينها مباشرة في الرسالة.

```php
    // Fallback for unsupported or complex files (could try base64 for download by AI, but text is safer here)
    return null;
}
```

- **أنواع غير مدعومة:** ملفات PDF، DOCX، Excel، ZIP، إلخ — تُرجع `null` (لا يمكن للخدمة معالجتها). هذه الملفات تُعالج بواسطة `ResumeParserService` المستقل.

---

### ٢.٤ الدالة المساعدة الخاصة: `resolveMimeType()`

```php
/**
 * Resolve missing or incorrect MIME types based on extension.
 */
private function resolveMimeType($extension, $originalMime)
{
    $map = [
        'weba' => 'audio/webm',
        'ogg'  => 'audio/ogg',
        'oga'  => 'audio/ogg',
        'm4a'  => 'audio/mp4',
        'mp3'  => 'audio/mpeg',
        'wav'  => 'audio/wav',
        'flac' => 'audio/flac',
        'aac'  => 'audio/aac',
    ];

    return $map[$extension] ?? $originalMime;
}
```

**المشكلة التي تحلها:**

بعض متصفحات الويب وخوادم PHP لا تُحدّد نوع MIME بشكل صحيح لبعض امتدادات الملفات الصوتية. مثلاً:
- ملف `.weba` قد يُحدد MIME كـ `application/octet-stream` بدلاً من `audio/webm`
- ملف `.mp3` قد يُحدد كـ `application/x-mp3` بدلاً من `audio/mpeg`

**الحل:**
جدول خرائط (Mapping Table) يربط الامتداد بالنوع الصحيح. إذا وُجد الامتداد في الجدول، يُستبدل MIME الخاطئ بالصحيح؛ وإلا يُترك الأصلي.

> **ملاحظة:** الجدول يغطي الملفات الصوتية فقط لأنها الأكثر عرضة لأخطاء MIME. الصور والفيديو عادة ما تُحدد بشكل صحيح.

---

## ٣. مخطط تدفق المنطق

```
                    chat(array $messages)
                           │
                           ▼
              ┌────────────────────────┐
              │ جلب الإعدادات من config │
              │ (url, key, model)      │
              └───────────┬────────────┘
                          │
                          ▼
              ┌────────────────────────┐
              │ HTTP POST → LiteLLM    │
              │ Timeout: 120 ثانية     │
              │ Headers: x-litellm-key │
              └───────────┬────────────┘
                          │
                    ┌─────┴──────┐
                    │            │
               نجاح │            │ فشل
                    │            │
                    ▼            ▼
            ┌──────────┐  ┌───────────────┐
            │ JSON ←   │  │ Log::error()  │
            │ response │  │ return null   │
            └──────────┘  └───────────────┘


         processUploadedFile($file)
                    │
                    ▼
           ┌──────────────────┐
           │ file == null?    │──► return null
           └────────┬─────────┘
                    │ لا
                    ▼
           ┌──────────────────┐
           │ resolveMimeType  │
           └────────┬─────────┘
                    │
            ┌───────┼───────────────┐
            │       │               │
        صورة/  نص/            أخرى
        صوت/   xml,md,
        فيديو  php...
            │       │               │
            ▼       ▼               ▼
      Base64   نص مباشر        return null
      Encode   embedding
```

---

## ٤. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Facade (واجهة)** | الكلاس بأكمله | يُخفي تعقيد HTTP POST خلف دالة بسيطة `chat()` |
| **Null Object Pattern** | جميع قيم الإرجاع | يُرجع `null` بدلاً من رمي استثناءات |
| **Strategy** | `processUploadedFile()` | استراتيجيات مختلفة حسب نوع الملف (Base64 للوسائط، نص للملفات النصية) |
| **Mapping Table** | `resolveMimeType()` | جدول بيانات لاستبدال القيم بدلاً من switch/match |

---

## ٥. نقاط القوة في التصميم

| النقطة | الوصف |
|--------|------|
| **مهلة انتظار طويلة (120s)** | استيعاب زمن توليد النماذج الكبيرة التي قد تستغرق دقائق |
| **معالجة أخطاء شاملة** | `try-catch` + `Log::error()` يضمن عدم تعطل النظام عند فشل الذكاء الاصطناعي |
| **فصل الإعدادات** | جميع الإعدادات في `config/ai.php` مع قيم افتراضية، يسهل التغيير بدون تعديل الكود |
| **دعم الوسائط المتعددة** | معالجة الصور والصوت والفيديو والنصوص في دالة واحدة |
| **بساطة الواجهة** | دالة واحدة `chat()` للوصول لكل قدرات الذكاء الاصطناعي |
| **تصحيح MIME ذكي** | حل مشكلة شائعة في متصفحات الويب بشكل أنيق |

---

## ٦. ملاحظات تقنية دقيقة

### ٦.١ لماذا `x-litellm-api-key` وليس `Authorization: Bearer`؟

الوسيط LiteLLM يستخدم ترويسة مخصصة `x-litellm-api-key` بدلاً من الترويسة القياسية `Authorization: Bearer`. هذا لأن LiteLLM قد يعمل كوكيل (proxy) بين عدة مزودين للذكاء الاصطناعي (OpenAI, Gemini, Claude)، ولكل مزود طريقة مصادقة مختلفة، فيوحّدها LiteLLM خلف ترويسة واحدة.

### ٦.٢ لماذا `temperature = 0.7`؟

```
temperature = 0.0 → استجابات حتمية (نفس الإجابة دائماً)
temperature = 0.5 → استجابات متوازنة
temperature = 0.7 → استجابات متنوعة لكن متماسكة (الاختيار الأمثل للتقييمات والتقارير)
temperature = 1.0 → استجابات إبداعية جداً (قد تكون غير متماسكة)
```

### ٦.٣ بروتوكول OpenAI-compatible

رغم أن النموذج هو **Gemini** (من Google)، إلا أن الوسيط LiteLLM يترجم الطلب إلى بروتوكول **OpenAI Chat Completions API** الموحّد:

```json
{
    "model": "gemini-3-flash-preview",
    "messages": [
        {"role": "system", "content": "..."},
        {"role": "user", "content": "..."}
    ],
    "max_tokens": 4096,
    "temperature": 0.7
}
```

والاستجابة تأتي بنفس صيغة OpenAI:

```json
{
    "choices": [
        {
            "message": {
                "role": "assistant",
                "content": "النص المُولّد..."
            }
        }
    ]
}
```

> لذلك في كل الخدمات الأخرى، يتم استخراج النص عبر `$response['choices'][0]['message']['content']`.

---

## ٧. سيناريوهات الاستخدام

### ٧.١ تقييم موظف (عبر AiEvaluationService)

```php
$aiService = app(AiService::class);

$response = $aiService->chat([
    ['role' => 'system', 'content' => 'أنت مدير موارد بشرية خبير...'],
    ['role' => 'user', 'content' => 'قم بتقييم أداء الموظف: أحمد...'],
]);

// $response['choices'][0]['message']['content'] → تقرير Markdown
```

### ٧.٢ تحليل سيرة ذاتية (عبر ResumeAnalysisService)

```php
$response = $aiService->chat([
    ['role' => 'system', 'content' => 'أجب بصيغة JSON فقط...'],
    ['role' => 'user', 'content' => 'حلل هذه السيرة الذاتية...'],
]);

// $response['choices'][0]['message']['content'] → JSON string
```

### ٧.٣ رسالة تحفيزية (في PayslipPdfService)

```php
$response = $aiService->chat([
    ['role' => 'system', 'content' => 'You are a kind HR manager...'],
    ['role' => 'user', 'content' => 'Write a thank you note to Ahmed...'],
]);

// $response['choices'][0]['message']['content'] → "Great job this month, Ahmed!"
```

---

## ٨. التكامل مع بقية النظام

```
                    ┌──────────────────┐
                    │   config/ai.php  │
                    │  (إعدادات AI)    │
                    └────────┬─────────┘
                             │
                    ┌────────▼─────────┐
                    │   AiService      │ ← هذا الملف
                    │──────────────────│
                    │ • chat()         │
                    │ • processFile()  │
                    │ • resolveMime()  │
                    └──┬─────┬─────┬───┘
                       │     │     │
          ┌────────────┘     │     └────────────┐
          ▼                  ▼                  ▼
┌─────────────────┐ ┌──────────────┐ ┌──────────────────┐
│AiEvaluation     │ │ResumeAnalysis│ │PayslipPdf        │
│Service          │ │Service       │ │Service           │
│─────────────────│ │──────────────│ │──────────────────│
│تقييم الموظفين    │ │تحليل السير   │ │رسائل تحفيزية     │
│Markdown Output  │ │JSON Output   │ │English Note      │
└─────────────────┘ └──────────────┘ └──────────────────┘
```

---

*نهاية تقرير `AiService.php`*
