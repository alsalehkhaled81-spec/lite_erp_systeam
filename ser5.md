# تقرير تحليلي مفصل — `InvoicePdfService.php` و `PayslipPdfService.php`

> **الملفات:**
> - `app/Services/InvoicePdfService.php` — 43 سطر
> - `app/Services/PayslipPdfService.php` — 58 سطر
>
> **النوع:** خدمات (Services) — توليد مستندات PDF احترافية
> **التبعيات:** `Barryvdh\DomPDF\Facade\Pdf`, `Arphp\Glyphs`, `App\Services\AiService` (في Payslip فقط)

---

## ١. الدور في النظام

هاتان الخدمتان مسؤولتان عن **توليد مستندات PDF احترافية** قابلة للتحميل والطباعة. التحدي الأكبر الذي تحلاّنه هو **عرض الحروف العربية بشكل صحيح** في ملفات PDF — وهو مشكلة معروفة في مكتبة DomPDF التي لا تدعم RTL بشكل أصلي.

```
موارد بشرية / محاسب يضغط "تحميل PDF"
            │
            ▼
    ┌───────────────────────────────────┐
    │ InvoicePdfService / PayslipPdfService │
    │───────────────────────────────────│
    │ 1. تحميل العلاقات (Eager Loading) │
    │ 2. معالجة الحروف العربية (ArPHP)  │ ← حل مشكلة RTL
    │ 3. توليد PDF عبر DomPDF           │
    │ 4. إرجاع Stream Download          │
    └───────────────────────────────────┘
            │
            ▼
    متصفح المستخدم: تحميل ملف PDF
```

---

## ٢. `InvoicePdfService.php` — توليد فواتير PDF

### ٢.١ الكود الكامل

```php
namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'items']);

        $arabic = new \Arphp\Glyphs();

        $clientName = $arabic->utf8Glyphs($invoice->client->name);
        $companyName = $invoice->client->company_name
            ? $arabic->utf8Glyphs($invoice->client->company_name) : null;

        $invoice->client->name = $clientName;
        $invoice->client->company_name = $companyName;

        foreach ($invoice->items as $item) {
            $item->description = $arabic->utf8Glyphs($item->description);
        }

        $company = $arabic->utf8Glyphs(config('app.name', 'ERP-Lite'));

        $data = [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'company' => $company,
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);

        $filename = 'invoice_' . $invoice->invoice_number . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
```

### ٢.٢ التحليل سطراً بسطر

#### تحميل العلاقات

```php
$invoice->load(['client', 'project', 'items']);
```

- **Eager Loading** لمنع N+1 Queries عند عرض القالب.
- العلاقات المطلوبة: `client` (العميل)، `project` (المشروع)، `items` (بنود الفاتورة).

#### معالجة الحروف العربية — المشكلة والحل

```php
$arabic = new \Arphp\Glyphs();

$clientName = $arabic->utf8Glyphs($invoice->client->name);
```

**المشكلة:**

مكتبة DomPDF تتعامل مع النصوص كحروف لاتينية متسلسلة (LTR). عند محاولة عرض نص عربي، تحدث المشاكل التالية:

```
المدخل:    "شركة الشام للتقنية"
المتوقع:   شركة الشام للتقنية
الفعلي:    ةينقتلل ماحشلا ةركش  ← (حروف منعكسة وغير متصلة)
```

**الحل: `Arphp\Glyphs::utf8Glyphs()`**

دالة `utf8Glyphs()` تقوم بثلاث عمليات:

| العملية | الوصف | مثال |
|---------|------|------|
| **1. تحويل الأشكال (Shaping)** | تحويل كل حرف إلى شكله الصحيح حسب موقعه (بداية، وسط، نهاية، منفصل) | ب + ر + ي + د → بـ ‹ـر› ‹ـي› ـد |
| **2. إنشاء الروابط (Ligatures)** | ربط الحروف ببعضها بشكل صحيح | لا = أ + ل |
| **3. عكس الترتيب البصري** | ترتيب الحروف من اليمين لليسار | "دمشق" → عرضها RTL في PDF |

> النتيجة: نص عربي يظهر بشكل صحيح في ملف PDF الذي يعمل بـ LTR أصلاً.

#### تطبيق المعالجة على كل الحقول

```php
$clientName = $arabic->utf8Glyphs($invoice->client->name);
$companyName = $invoice->client->company_name
    ? $arabic->utf8Glyphs($invoice->client->company_name) : null;

$invoice->client->name = $clientName;
$invoice->client->company_name = $companyName;

foreach ($invoice->items as $item) {
    $item->description = $arabic->utf8Glyphs($item->description);
}

$company = $arabic->utf8Glyphs(config('app.name', 'ERP-Lite'));
```

| الحقل | المعالجة | ملاحظة |
|------|---------|--------|
| `client->name` | `utf8Glyphs()` | اسم العميل (عربي عادة) |
| `client->company_name` | `utf8Glyphs()` مع فحص null | قد يكون فارغاً |
| `items[].description` | `utf8Glyphs()` في حلقة | كل وصف بند |
| `config('app.name')` | `utf8Glyphs()` | اسم الشركة (النظام) |

> **تحذير:** هذه المعالجة **تعدّل النموذج في الذاكرة** (in-memory mutation) — لكن لا تُحفظ في قاعدة البيانات (لا يوجد `$invoice->save()`). هذا آمن لأن النموذج يُرمى بعد توليد الـ PDF.

#### بناء البيانات وتوليد PDF

```php
$data = [
    'invoice' => $invoice,
    'client' => $invoice->client,
    'items' => $invoice->items,
    'company' => $company,
];

$pdf = Pdf::loadView('pdf.invoice', $data);
```

- `Pdf::loadView('pdf.invoice', $data)` يحمّل قالب Blade `resources/views/pdf/invoice.blade.php` ويملأه بالبيانات.
- DomPDF يحوّل HTML/CSS إلى PDF.

#### الاستجابة (Stream Download)

```php
$filename = 'invoice_' . $invoice->invoice_number . '.pdf';

return response()->streamDownload(function () use ($pdf) {
    echo $pdf->output();
}, $filename);
```

| العنصر | الوصف |
|--------|------|
| `$filename` | اسم الملف: `invoice_INV-2024-1234.pdf` |
| `streamDownload()` | إرجاع PDF كـ **Stream** (بدون تحميل كامل في الذاكرة) |
| `$pdf->output()` | توليد المحتوى الثنائي للـ PDF |
| `echo` | إخراج المحتوى في الـ Stream |

> **لماذا `streamDownload` وليس `download`؟** `streamDownload` يرسل البيانات على شكل تدفّق (chunks)، مما يقلل استهلاك الذاكرة للملفات الكبيرة.

---

## ٣. `PayslipPdfService.php` — توليد قسائم الرواتب PDF

### ٣.١ الكود الكامل

```php
namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipPdfService
{
    public function generate(Payroll $payroll)
    {
        $payroll->load('employee.user');

        $arabic = new \Arphp\Glyphs();

        // Generate a short AI motivational note for the payslip
        $aiNote = null;
        try {
            $aiService = app(\App\Services\AiService::class);
            $prompt = "Write a very short (one sentence) thank you note in English to an employee named {$payroll->employee->user->name} for receiving their salary for the month of {$payroll->month_year}. Be friendly and motivational.";

            $response = $aiService->chat([
                ['role' => 'system', 'content' => 'You are a kind HR manager who writes very short thank you and encouragement notes to employees in English.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $aiNote = $response['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            // Silently fail AI feature so PDF generation isn't interrupted
        }

        $payroll->employee->user->name = $arabic->utf8Glyphs($payroll->employee->user->name);
        if ($payroll->employee->job_title) {
            $payroll->employee->job_title = $arabic->utf8Glyphs($payroll->employee->job_title);
        }
        if ($payroll->employee->department && $payroll->employee->department->name) {
            $payroll->employee->department->name = $arabic->utf8Glyphs($payroll->employee->department->name);
        }

        $data = [
            'payroll' => $payroll,
            'employee' => $payroll->employee,
            'user' => $payroll->employee->user,
            'company' => $arabic->utf8Glyphs(config('app.name', 'ERP-Lite')),
            'ai_note' => $aiNote ? $arabic->utf8Glyphs($aiNote) : null,
        ];

        $pdf = Pdf::loadView('pdf.payslip', $data);

        $filename = 'payslip_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payroll->employee->user->name) . '_' . $payroll->month_year . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
```

### ٣.٢ التحليل سطراً بسطر

#### تحميل العلاقة

```php
$payroll->load('employee.user');
```

- يحمل الموظف المرتبط بالمسيرة، ثم المستخدم المرتبط بالموظف (سلسلة من مستويين).

#### ⭐ الميزة الفريدة: رسالة تحفيزية بالذكاء الاصطناعي

```php
$aiNote = null;
try {
    $aiService = app(\App\Services\AiService::class);
    $prompt = "Write a very short (one sentence) thank you note in English "
            . "to an employee named {$payroll->employee->user->name} "
            . "for receiving their salary for the month of {$payroll->month_year}. "
            . "Be friendly and motivational.";

    $response = $aiService->chat([
        ['role' => 'system', 'content' =>
            'You are a kind HR manager who writes very short thank you '
            . 'and encouragement notes to employees in English.'],
        ['role' => 'user', 'content' => $prompt]
    ]);

    if ($response && isset($response['choices'][0]['message']['content'])) {
        $aiNote = $response['choices'][0]['message']['content'];
    }
} catch (\Exception $e) {
    // Silently fail AI feature so PDF generation isn't interrupted
}
```

**تحليل ميزة الذكاء الاصطناعي:**

| العنصر | الوصف |
|--------|------|
| **اللغة** | إنجليزية (وليست عربية) |
| **الطول** | جملة واحدة فقط (very short) |
| **النبرة** | ودّية وتحفيزية (friendly and motivational) |
| **الشخصنة** | تستخدم اسم الموظف والشهر |

**أمثلة على الرسائل المُولّدة:**
- *"Great job this month, Ahmed! Your hard work truly makes a difference."*
- *"Thank you for your dedication this month, Rana. Keep up the excellent work!"*

**⭐ الفشل الصامت (Silent Fail) — أهم قرار تصميمي:**

```php
} catch (\Exception $e) {
    // Silently fail AI feature so PDF generation isn't interrupted
}
```

| السيناريو | النتيجة |
|-----------|---------|
| الذكاء الاصطناعي يعمل | ✅ رسالة تحفيزية تظهر في القسيمة |
| الذكاء الاصطناعي يفشل | ✅ القسيمة تُولّد بدون رسالة (`$aiNote = null`) |
| الذكاء الاصطناعي يرمي استثناء | ✅ يُلتقط بصمت، القسيمة تُولّد بدون رسالة |

> **المنطق:** توليد قسيمة الراتب هو وظيفة أساسية لا يجب أن تتعطل بسبب ميزة ثانوية (رسالة تحفيزية). الفشل الصامت يضمن أن الموظف يحصل على قسيمته دائماً.

#### معالجة الحروف العربية

```php
$payroll->employee->user->name = $arabic->utf8Glyphs($payroll->employee->user->name);
if ($payroll->employee->job_title) {
    $payroll->employee->job_title = $arabic->utf8Glyphs($payroll->employee->job_title);
}
if ($payroll->employee->department && $payroll->employee->department->name) {
    $payroll->employee->department->name = $arabic->utf8Glyphs($payroll->employee->department->name);
}
```

| الحقل | الفحص | السبب |
|------|------|------|
| `user->name` | لا فحص (دائماً موجود) | الاسم مطلوب |
| `employee->job_title` | `if ($payroll->employee->job_title)` | قد يكون null |
| `department->name` | `if ($department && $department->name)` | القسم قد لا يكون محمّلاً أو فارغاً |

#### معالجة الرسالة التحفيزية عربياً

```php
'ai_note' => $aiNote ? $arabic->utf8Glyphs($aiNote) : null,
```

- إذا وُجدت رسالة، تُعالج بـ `utf8Glyphs()` (لأنها قد تحتوي على نص عربي إذا غيّر النظام اللغة مستقبلاً).
- إذا لم توجد، تُمرر كـ `null`.

#### اسم الملف الآمن

```php
$filename = 'payslip_'
    . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payroll->employee->user->name)
    . '_' . $payroll->month_year . '.pdf';
```

**التعبير النمطي `preg_replace('/[^A-Za-z0-9_\-]/', '_', ...)`:**

| المدخل | المخرج | السبب |
|--------|--------|------|
| `Ahmed Hassan` | `Ahmed_Hassan` | المسافة → `_` |
| `رنا قدور` | `____` | الحروف العربية → `_` (لأنها ليست A-Za-z) |
| `John O'Brien` | `John_O_Brien` | `'` → `_` |

> **السبب:** أسماء الملفات يجب أن تكون آمنة لجميع أنظمة التشغيل. الحروف العربية والرموز الخاصة قد تسبب مشاكل في بعض المتصفحات.

**النتيجة:** `payslip_Ahmed_Hassan_2026-06.pdf`

---

## ٤. مقارنة بين الخدمتين

| المعيار | InvoicePdfService | PayslipPdfService |
|---------|-------------------|-------------------|
| **المدخل** | `Invoice` | `Payroll` |
| **العلاقات المحمّلة** | `client`, `project`, `items` | `employee.user` |
| **الذكاء الاصطناعي** | ❌ لا يستخدم | ✅ رسالة تحفيزية |
| **معالجة عربية** | 4 حقول (اسم، شركة، بنود، اسم النظام) | 4 حقول (اسم، مسمى، قسم، رسالة AI) |
| **اسم الملف** | `invoice_{invoice_number}.pdf` | `payslip_{name}_{month}.pdf` (مع تنظيف) |
| **القالب** | `pdf.invoice` | `pdf.payslip` |
| **الفشل الصامت** | N/A | ✅ لرسالة AI |
| **حجم البيانات** | متغير (حسب عدد البنود) | ثابت (قسيمة واحدة) |

---

## ٥. مخطط تدفق المنطق

### InvoicePdfService

```
Invoice $invoice
       │
       ▼
┌──────────────────────────┐
│ Eager Load:              │
│   client, project, items │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ ArPHP Glyphs:            │
│   • client.name          │
│   • client.company_name  │
│   • items[].description  │
│   • app.name             │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ DomPDF::loadView(        │
│   'pdf.invoice', $data   │
│ )                        │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ streamDownload:          │
│   invoice_{number}.pdf   │
└──────────────────────────┘
```

### PayslipPdfService

```
Payroll $payroll
       │
       ▼
┌──────────────────────────┐
│ Eager Load:              │
│   employee.user          │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ ⭐ AI Motivational Note  │
│   try {                  │
│     AiService::chat()    │
│   } catch {              │
│     silent fail ($null)  │← لا يتعطل PDF
│   }                      │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ ArPHP Glyphs:            │
│   • user.name            │
│   • employee.job_title   │
│   • department.name      │
│   • app.name             │
│   • ai_note (إن وُجدت)   │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ DomPDF::loadView(        │
│   'pdf.payslip', $data   │
│ )                        │
└───────────┬──────────────┘
            │
            ▼
┌──────────────────────────┐
│ streamDownload:          │
│   payslip_{name}_{month} │
│   .pdf                   │
└──────────────────────────┘
```

---

## ٦. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Service Layer** | كلاهما | منطق توليد PDF منفصل |
| **Decorator** | `utf8Glyphs()` | تزيين النص قبل عرضه |
| **Silent Fail** | Payslip AI note | عدم تعطل الوظيفة الأساسية |
| **In-memory Mutation** | تعديل خصائص النموذج | بدون حفظ في DB |
| **Stream Response** | `streamDownload()` | إرسال تدفّقي لتوفير الذاكرة |

---

## ٧. نقاط القوة والقيود

### نقاط القوة

| النقطة | الوصف |
|--------|------|
| **حل مشكلة العربية** | ArPHP Glyphs يحوّل النص لصيغة مفهومة لـ DomPDF |
| **Stream Download** | توفير الذاكرة للملفات الكبيرة |
| **فحص null ذكي** | لا خطأ عند غياب company_name أو job_title |
| **رسالة AI تحفيزية** | لمسة إنسانية في قسيمة الراتب |
| **فشل صامت** | PDF يُولّد دائماً بغض النظر عن حالة AI |

### القيود

| القيد | الوصف |
|------|------|
| **تعديل الذاكرة** | يعدّل خصائص النموذج (يجب الحذر من إعادة الاستخدام) |
| **أسماء عربية في الملفات** | تُستبدل بـ `_` في اسم ملف القسيمة |
| **زمن AI** | رسالة التحفيز قد تضيف 5-30 ثانية لتوليد القسيمة |
| **لا RTL كامل** | ArPHP يحل الحروف لكن لا يدعم محاذاة RTL في الجداول |

---

*نهاية تقرير `InvoicePdfService.php` و `PayslipPdfService.php`*
