# تقرير تحليلي مفصل — `ResumeParserService.php`

> **الملف:** `app/Services/ResumeParserService.php`
> **السطور:** 81 سطر
> **النوع:** خدمة (Service) — استخراج النص من ملفات السير الذاتية
> **التبعيات:** `Smalot\PdfParser\Parser`, `ZipArchive`, `Illuminate\Support\Facades\Log`

---

## ١. الدور في النظام

`ResumeParserService` هي خدمة **استخراج النص (Text Extraction)** من ملفات السير الذاتية بتنسيقي **PDF** و **DOCX**. تعمل كمرحلة تمهيدية قبل تحليل الذكاء الاصطناعي — تحوّل الملف الثنائي إلى نص مقروء يمكن للنموذج اللغوي معالجته.

```
متقدم يرفع سيرته الذاتية (resume.pdf)
            │
            ▼
    ┌───────────────────────────────┐
    │   ResumeParserService         │ ← هذا الملف
    │───────────────────────────────│
    │ 1. التحقق من وجود الملف       │
    │ 2. تحديد نوع الملف (PDF/DOCX) │
    │ 3. استخراج النص               │
    │ 4. تنظيف وإرجاع النص          │
    └───────────────┬───────────────┘
                    │
                    ▼
    ┌───────────────────────────────┐
    │ نص مقروء (String)             │
    │ → يُحفظ في resumes.resume_text │
    │ → يُستخدم لاحقاً في تحليل AI   │
    └───────────────────────────────┘
```

### من يستخدم هذه الخدمة؟

| المستدعي | الملف | الاستخدام |
|----------|------|-----------|
| `JobApplicationController` | `app/Http/Controllers/JobApplicationController.php` | استخراج النص عند تقديم طلب توظيف |
| `ResumeResource` (HR) | `app/Filament/Hr/Resources/ResumeResource.php` | استخراج النص عند رفع سيرة ذاتية جديدة من لوحة HR |

```php
// في JobApplicationController
$parser = new ResumeParserService();
$fullPath = storage_path('app/public/' . $path);
$extractedText = $parser->parse($fullPath, $file->getClientMimeType());
```

---

## ٢. الكود الكامل مع التحليل سطراً بسطر

### ٢.١ تعريف الكلاس والاستيرادات

```php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use ZipArchive;

class ResumeParserService
{
```

| الاستيراد | المصدر | الوظيفة |
|-----------|--------|---------|
| `Exception` | PHP Core | التقاط الاستثناءات العامة |
| `Log` | Laravel Facade | تسجيل الأخطاء |
| `Smalot\PdfParser\Parser` | حزمة `smalot/pdfparser` | استخراج النص من PDF |
| `ZipArchive` | PHP Extension | فك ضغط ملفات DOCX (التي هي ZIP فعلياً) |

> **حقيقة تقنية:** ملفات DOCX هي في الواقع أرشيفات ZIP تحتوي على ملفات XML. `ZipArchive` يفتح الأرشيف ويقرأ `word/document.xml` الذي يحتوي على النص.

---

### ٢.٢ الدالة الرئيسية: `parse()`

```php
/**
 * Parse text from a PDF or DOCX file.
 *
 * @param string $absolutePath Absolute path to the file on disk.
 * @param string $mimeType Optional MIME type to enforce parsing logic.
 * @return string Extracted text or an empty string.
 */
public function parse(string $absolutePath, string $mimeType = ''): string
{
    if (!file_exists($absolutePath)) {
        Log::error("ResumeParserService: File not found at {$absolutePath}");
        return '';
    }
```

**الترويسة:**

| المعامل | النوع | الإلزامية | الوصف |
|---------|------|----------|--------|
| `$absolutePath` | string | مطلوب | المسار الكامل للملف على القرص |
| `$mimeType` | string | اختياري | نوع MIME لتعزيز منطق التحديد |

**القيمة المُرجعة:** `string` — النص المستخرج، أو سلسلة فارغة عند الفشل.

**التحقق من الوجود:**
```php
if (!file_exists($absolutePath)) {
    Log::error("ResumeParserService: File not found at {$absolutePath}");
    return '';
}
```
- `file_exists()` يتحقق من وجود الملف فعلياً على القرص الصلب.
- إذا لم يوجد، يسجّل الخطأ ويُرجع سلسلة فارغة (لا يرمي استثناء).

```php
    $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
```

- `pathinfo($absolutePath, PATHINFO_EXTENSION)` يستخرج امتداد الملف من المسار.
  - مثال: `/storage/app/resumes/cv_123.pdf` → `pdf`
- `strtolower()` يحوّل لحروف صغيرة لضمان المطابقة (`PDF` → `pdf`).

```php
    try {
        if ($extension === 'pdf' || str_contains($mimeType, 'pdf')) {
            return $this->parsePdf($absolutePath);
        } elseif ($extension === 'docx' || str_contains($mimeType, 'wordprocessingml.document')) {
            return $this->parseDocx($absolutePath);
        }
    } catch (Exception $e) {
        Log::error("ResumeParserService: Failed to parse {$absolutePath}. Error: " . $e->getMessage());
    }
```

**منطق توجيه الاستخراج (Routing Logic):**

| الشرط | التوجيه | الوصف |
|-------|---------|------|
| `$extension === 'pdf'` أو MIME يحتوي `pdf` | `parsePdf()` | ملف PDF |
| `$extension === 'docx'` أو MIME = `wordprocessingml.document` | `parseDocx()` | ملف Word 2007+ |

> **لماذا فحص مزدوج (امتداد + MIME)؟** لأن بعض المتصفحات قد لا ترسل MIME صحيح، وبعض السيرفرات قد لا تحفظ الامتداد بشكل صحيح. الفحص المزدوج يزيد الموثوقية.

**الاستثناءات:**
- `try-catch` يلتقط أي استثناء من المكتبتين (PDF تالف، DOCX معطوب).
- يسجّل الخطأ مع المسار ورسالة الاستثناء.

```php
    // Return empty string if format is unsupported or an error occurred
    return '';
}
```

- **أنواع غير مدعومة** (DOC القديم، RTF، TXT، صور): تُرجع سلسلة فارغة.
- **الأخطاء:** إذا فشل الاستخراج رغم النوع الصحيح، تُرجع سلسلة فارغة.

> **نمط تصميمي:** الدالة لا ترمي استثناءات أبداً — تُرجع سلسلة فارغة دائماً عند الفشل، مما يبسّط التعامل في المتصل.

---

### ٢.٣ استخراج نص PDF: `parsePdf()`

```php
/**
 * Parse PDF file using Smalot/PdfParser
 */
private function parsePdf(string $path): string
{
    $parser = new Parser();
    $pdf = $parser->parseFile($path);
    return trim($pdf->getText());
}
```

**سطراً بسطر:**

| السطر | الوصف |
|-------|------|
| `$parser = new Parser()` | إنشاء كائن من مكتبة Smalot PdfParser |
| `$pdf = $parser->parseFile($path)` | قراءة وتحليل ملف PDF بالكامل |
| `$pdf->getText()` | استخراج كل النص من جميع الصفحات |
| `trim(...)` | إزالة المسافات والأسطر الزائدة من البداية والنهاية |

**كيف تعمل مكتبة Smalot PdfParser؟**

```
ملف PDF (ثنائي)
    │
    ├──► قراءة بنية PDF (xref table, objects)
    │
    ├──► تحديد تدفقات المحتوى (Content Streams)
    │
    ├──► فك ترميز النصوص (FlateDecode, ASCII85, إلخ)
    │
    ├──► استخراج أوامر عرض النص (Tj, TJ operators)
    │
    └──► تجميع النص مع الحفاظ على الترتيب
         │
         ▼
    نص مقروء (String)
```

**قيود استخراج PDF:**

| الحالة | النتيجة |
|--------|---------|
| PDF نصي (Text-based) | ✅ استخراج ممتاز |
| PDF ممسوح ضوئياً (Scanned) | ❌ نص فارغ (يحتاج OCR) |
| PDF مشفّر | ❌ قد يفشل |
| PDF مع جدول محتويات معقّد | ⚠️ ترتيب قد يكون غير دقيق |

> لهذا السبب، في `JobApplicationController` هناك منطق احتياطي:
> ```php
> if (empty($resumeText)) {
>     $resumeText = 'تعذر استخراج النص تلقائياً من الملف المرفق. قد يكون الملف عبارة عن صور ممسوحة ضوئياً (Scanned).';
> }
> ```

---

### ٢.٤ استخراج نص DOCX: `parseDocx()`

```php
/**
 * Parse DOCX file by reading the zipped word/document.xml
 */
private function parseDocx(string $path): string
{
    $zip = new ZipArchive();
    $text = '';

    if ($zip->open($path) === true) {
        // Find the document.xml file inside the zip archive
        if (($index = $zip->locateName('word/document.xml')) !== false) {
            $xmlData = $zip->getFromIndex($index);
            $zip->close();
```

**بنية ملف DOCX الداخلية:**

ملف DOCX هو أرشيف ZIP يحتوي على:

```
my_resume.docx (ZIP)
├── [Content_Types].xml
├── _rels/
│   └── .rels
├── word/
│   ├── document.xml        ← ★ النص الأساسي هنا
│   ├── styles.xml
│   ├── fontTable.xml
│   └── media/              (صور، شعارات)
├── docProps/
│   ├── app.xml
│   └── core.xml
└── theme/
    └── theme1.xml
```

**سطراً بسطر:**

| السطر | الوصف |
|-------|------|
| `$zip = new ZipArchive()` | إنشاء كائن ZipArchive |
| `$zip->open($path)` | فتح ملف DOCX كأرشيف ZIP. يُرجع `true` عند النجاح |
| `$zip->locateName('word/document.xml')` | البحث عن ملف `document.xml` داخل الأرشيف. يُرجع فهرس (index) أو `false` |
| `$zip->getFromIndex($index)` | قراءة محتوى الملف كنص (XML string) |
| `$zip->close()` | إغلاق الأرشيف لتحرير الموارد |

```php
            // Replace paragraph tags with newlines for better readability
            $xmlData = str_replace(['</w:p>', '</w:br>'], "\n", $xmlData);

            // Strip all other XML tags
            $text = strip_tags($xmlData);

            // Decode HTML entities if any
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
```

**معالجة XML لاستخراج النص:**

محتوى `word/document.xml` يبدو هكذا:

```xml
<w:document>
  <w:body>
    <w:p>
      <w:r>
        <w:t>محمد أحمد</w:t>
      </w:r>
    </w:p>
    <w:p>
      <w:r>
        <w:t>مطور برمجيات</w:t>
      </w:r>
    </w:p>
  </w:body>
</w:document>
```

**خطوات المعالجة:**

| الخطوة | الكود | النتيجة |
|--------|------|---------|
| 1. استبدال وسوم الفقرات | `str_replace(['</w:p>', '</w:br>'], "\n", ...)` | وضع `\n` بدلاً من `</w:p>` و `</w:br>` |
| 2. إزالة وسوم XML | `strip_tags(...)` | إزالة جميع وسوم `<w:...>` |
| 3. فك ترميز الكيانات | `html_entity_decode(..., ENT_QUOTES, 'UTF-8')` | `&amp;` → `&`، `&lt;` → `<` |

**النتيجة النهائية:**
```
محمد أحمد
مطور برمجيات
```

> **لماذا استبدال `</w:p>` قبل `strip_tags`؟** لأن `strip_tags` يزيل كل الوسوم بدون تمييز، مما قد يدمج الفقرات معاً في سطر واحد. استبدال وسم نهاية الفقرة `\n` أولاً يحافظ على فواصل الأسطر.

```php
        } else {
            $zip->close();
        }
    }

    return trim($text);
}
```

- إذا لم يوجد `word/document.xml` (ملف DOCX معطوب)، يُغلق الأرشيف ويُرجع سلسلة فارغة.
- `trim()` ينظف المسافات الزائدة.

---

## ٣. مخطط تدفق المنطق الكامل

```
           ┌─────────────────────────────┐
           │ المدخلات:                   │
           │ • $absolutePath (string)    │
           │ • $mimeType (string)        │
           └──────────────┬──────────────┘
                          │
                          ▼
           ┌─────────────────────────────┐
           │ file_exists($absolutePath)? │
           └──────────────┬──────────────┘
                  ┌───────┴───────┐
                  │ لا            │ نعم
                  ▼               ▼
           ┌──────────┐  ┌─────────────────────┐
           │ Log error│  │ استخراج الامتداد     │
           │ return ''│  │ strtolower(pathinfo) │
           └──────────┘  └──────────┬──────────┘
                                    │
                                    ▼
                    ┌───────────────────────────────┐
                    │ تحديد نوع الملف               │
                    │───────────────────────────────│
                    │ PDF?    →  parsePdf()         │
                    │ DOCX?   →  parseDocx()        │
                    │ أخرى?   →  return ''          │
                    └───────────────┬───────────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    │               │               │
                    ▼               ▼               ▼
            ┌────────────┐  ┌────────────┐  ┌────────────┐
            │ parsePdf() │  │parseDocx() │  │   أخرى     │
            │────────────│  │────────────│  │────────────│
            │ Smalot     │  │ ZipArchive │  │ return ''  │
            │ PdfParser  │  │ + XML      │  └────────────┘
            │            │  │ parsing    │
            └──────┬─────┘  └─────┬──────┘
                   │              │
                   │    try-catch │
                   │      حوله    │
                   ▼              ▼
            ┌──────────────────────────────┐
            │ trim($text)                  │
            │ إزالة المسافات الزائدة       │
            └──────────────┬───────────────┘
                           │
                           ▼
                    ┌────────────┐
                    │ return     │
                    │ string     │
                    │ (نص أو '') │
                    └────────────┘
```

---

## ٤. مقارنة بين طريقتي الاستخراج

| المعيار | PDF (`parsePdf`) | DOCX (`parseDocx`) |
|---------|-----------------|-------------------|
| **المكتبة** | Smalot/PdfParser (Composer) | ZipArchive (PHP Extension) |
| **المنهجية** | تحليل بنية PDF الثنائية | فك ZIP + قراءة XML |
| **الدقة** | متوسطة (تعتمد على بنية PDF) | عالية (النص في XML صريح) |
| **سرعة المعالجة** | أبطأ (تحليل ثنائي معقد) | أسرع (قراءة XML مباشرة) |
| **الملفات الممسوحة ضوئياً** | ❌ تفشل (تحتاج OCR) | N/A (DOCX دائماً رقمي) |
| **الملفات المشفّرة** | ⚠️ قد تفشل | ❌ تفشل |
| **حفظ الترتيب** | ⚠️ قد يكون غير دقيق | ✅ دقيق (يتبع ترتيب XML) |
| **الاعتمادية** | مكتبة خارجية (composer) | PHP Extension مدمج |

---

## ٥. التكامل مع بقية النظام

```
مستخدم يرفع ملف (PDF/DOCX)
           │
           ▼
    ┌─────────────────────────────────────┐
    │  Livewire/Filament File Upload      │
    │  $file->store('resumes', 'public')  │
    └──────────────────┬──────────────────┘
                       │
                       ▼
    ┌─────────────────────────────────────┐
    │  ResumeParserService::parse()       │ ← هذا الملف
    │─────────────────────────────────────│
    │ استخراج النص من المسار             │
    └──────────────────┬──────────────────┘
                       │
                       ▼
    ┌─────────────────────────────────────┐
    │  حفظ في قاعدة البيانات              │
    │  Resume::create([                   │
    │    'file_path' => $path,            │
    │    'resume_text' => $extractedText, │ ← النص المستخرج
    │  ])                                 │
    └──────────────────┬──────────────────┘
                       │
                       ▼ (لاحقاً عند التحليل)
    ┌─────────────────────────────────────┐
    │  ResumeAnalysisService              │
    │  → AiService::chat()                │
    │  → تحليل بالذكاء الاصطناعي          │
    └─────────────────────────────────────┘
```

### في `JobApplicationController`:

```php
$parser = new \App\Services\ResumeParserService();
$fullPath = storage_path('app/public/' . $path);
$extractedText = $parser->parse($fullPath, $file->getClientMimeType());

$resumeText = trim($request->input('resume_text', ''));
if (empty($resumeText)) {
    $resumeText = trim($extractedText);
}
if (empty($resumeText)) {
    $resumeText = 'تعذر استخراج النص تلقائياً من الملف المرفق. '
                . 'قد يكون الملف عبارة عن صور ممسوحة ضوئياً (Scanned).';
}

Resume::create([
    'employee_id' => $employee->id,
    'file_path' => $path,
    'resume_text' => $resumeText,
]);
```

**منطق التراجع (Fallback):**
1. إذا أدخل المتقدم نصاً يدوياً → استخدامه أولاً
2. إذا لم يدخل → استخدام النص المستخرج تلقائياً
3. إذا فشل الاستخراج → رسالة افتراضية توضح المشكلة

---

## ٦. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Strategy Pattern** | `parse()` يوجّه لـ `parsePdf()` أو `parseDocx()` | استراتيجيات مختلفة حسب نوع الملف |
| **Null-safe Return** | إرجاع `''` بدلاً من الاستثناءات | عدم تعطل المتصل |
| **Template Method** | `parse()` كقالب + دوال خاصة كخطوات | هيكل ثابت مع خطوات قابلة للتبديل |
| **Graceful Degradation** | رسالة افتراضية في الـ Controller | تجربة مستخدم جيدة حتى عند الفشل |

---

## ٧. نقاط القوة والقيود

### نقاط القوة

| النقطة | الوصف |
|--------|------|
| **دعم نوعين** | PDF و DOCX (الأكثر شيوعاً للسير الذاتية) |
| **لا استثناءات** | إرجاع `''` دائماً، تبسيط المتصل |
| **تسجيل أخطاء** | `Log::error()` لكل فشل |
| **معالجة Unicode** | `html_entity_decode` مع `UTF-8` لدعم العربية |
| **حفظ فواصل الأسطر** | استبدال `</w:p>` بـ `\n` قبل `strip_tags` |

### القيود

| القيد | الوصف |
|------|------|
| **لا يدعم DOC القديم** | تنسيق Word 97-2003 غير مدعوم |
| **لا يدعم OCR** | الملفات الممسوحة ضوئياً تُرجع نصاً فارغاً |
| **لا يدعم الصور** | السير الذاتية المصوّرة غير مدعومة |
| **لا يحافظ على التنسيق** | جداول، أعمدة، ألوان تُفقد |
| **ترتيب PDF قد يكون غير دقيق** | بعض ملفات PDF ذات التخطيط المعقد قد تُستخرج بترتيب خاطئ |

---

*نهاية تقرير `ResumeParserService.php`*
