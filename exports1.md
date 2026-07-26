# تقرير تحليلي شامل — نظام تصدير البيانات في Lite ERP

> **المجلد:** `app/Exports/` (1 ملف) + خدمات التصدير (Services)
> **أنواع التصدير المدعومة:** PDF (فواتير، قسائم رواتب، تقرير لوحة معلومات) + CSV (مسيرات الرواتب)

---

## جدول المحتويات

1. [نظرة عامة على نظام التصدير](#١-نظرة-عامة-على-نظام-التصدير)
2. [البنية المعمارية للتصدير](#٢-البنية-المعمارية-للتصدير)
3. [ملف `app/Exports/PayrollExport.php` التفصيلي](#٣-ملف-appexportspayrollexportphp-التفصيلي)
4. [خدمات التصدير PDF](#٤-خدمات-التصدير-pdf)
5. [جدول مقارنة أنواع التصدير](#٥-جدول-مقارنة-أنواع-التصدير)
6. [التكامل مع Filament](#٦-التكامل-مع-filament)

---

## ١. نظرة عامة على نظام التصدير

يدعم نظام Lite ERP **أربعة أنواع من التصدير** موزعة على مجلدين:

| نوع التصدير | المجلد | الصيغة | الدور |
|------------|--------|--------|------|
| تصدير الرواتب | `app/Exports/` | **CSV** | تصدير مسيرات الرواتب للاستيراد في أنظمة أخرى |
| تصدير الفواتير | `app/Services/` | **PDF** | توليد فواتير احترافية للعملاء |
| تصدير قسائم الرواتب | `app/Services/` | **PDF** | توليد قسائم رواتب فردية للموظفين |
| تصدير لوحة المعلومات | `app/Services/` | **PDF** | تقرير شامل للإدارة العليا |

```
┌─────────────────────────────────────────────────────────────┐
│                    أنواع التصدير في النظام                   │
├─────────────────┬───────────────────────────────────────────┤
│                 │  app/Exports/                              │
│   CSV Export    │  ├── PayrollExport.php (76 سطر)            │
│   (جداول)       │      → رواتب بكل تفاصيلها                  │
│                 │      → دعم UTF-8 BOM للعربية                │
│                 │      → Stream Response                     │
├─────────────────┼───────────────────────────────────────────┤
│                 │  app/Services/                             │
│   PDF Export    │  ├── InvoicePdfService.php (43 سطر)        │
│   (مستندات)     │  │     → فواتير احترافية                   │
│                 │  ├── PayslipPdfService.php (58 سطر)        │
│                 │  │     → قسائم رواتب + رسالة AI             │
│                 │  └── DashboardExportService.php (103 سطر)  │
│                 │        → تقرير شامل A4 أفقي                │
└─────────────────┴───────────────────────────────────────────┘
```

---

## ٢. البنية المعمارية للتصدير

```
المستخدم يضغط زر التصدير في Filament
            │
            ├──► CSV (رواتب)
            │       │
            │       ▼
            │    PayrollExport
            │       ├── بناء استعلام (Cursor)
            │       ├── fputcsv() سطر بسطر
            │       ├── UTF-8 BOM للعربية
            │       └── Stream Response (php://output)
            │
            ├──► PDF (فواتير)
            │       │
            │       ▼
            │    InvoicePdfService
            │       ├── Eager Load (client, project, items)
            │       ├── ArPHP Glyphs (معالجة عربية)
            │       ├── DomPDF::loadView('pdf.invoice')
            │       └── streamDownload()
            │
            ├──► PDF (قسائم رواتب)
            │       │
            │       ▼
            │    PayslipPdfService
            │       ├── ⭐ AI Motivational Note
            │       │   (فشل صامت إن تعذر)
            │       ├── ArPHP Glyphs (معالجة عربية)
            │       ├── DomPDF::loadView('pdf.payslip')
            │       └── streamDownload()
            │
            └──► PDF (تقرير لوحة المعلومات)
                    │
                    ▼
                 DashboardExportService
                    ├── تجميع KPIs (10 استعلام)
                    ├── بيانات 12 شهراً (24 استعلام)
                    ├── آخر 15 نشاطاً
                    ├── إحصائيات 10 موظفين
                    ├── DomPDF::loadView('pdf.dashboard-report')
                    ├── setPaper('a4', 'landscape')
                    └── streamDownload()
```

---

## ٣. ملف `app/Exports/PayrollExport.php` التفصيلي

**الملف:** `app/Exports/PayrollExport.php` (76 سطر)
**النوع:** تصدير CSV باستخدام دوال PHP الأصلية (`fputcsv`)
**التبعيات:** `App\Models\Payroll`, `Symfony\Component\HttpFoundation\StreamedResponse`

### ٣.١ الكود الكامل

```php
namespace App\Exports;

use App\Models\Payroll;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollExport
{
    protected ?string $monthYear;

    public function __construct(?string $monthYear = null)
    {
        $this->monthYear = $monthYear;
    }

    public function download(): StreamedResponse
    {
        $filename = 'payrolls' . ($this->monthYear ? '_' . $this->monthYear : '') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                __('filament.fields.employee'),
                __('filament.fields.month'),
                __('filament.fields.basic_salary'),
                __('filament.fields.housing_allowance'),
                __('filament.fields.transport_allowance'),
                __('filament.fields.phone_allowance'),
                __('filament.fields.bonuses'),
                __('filament.fields.social_insurance_rate'),
                __('filament.fields.social_insurance_amount'),
                __('filament.fields.absence_days'),
                __('filament.fields.absence_deduction'),
                __('filament.fields.deductions'),
                __('filament.fields.net_salary'),
                __('filament.fields.status'),
            ]);

            $query = Payroll::with('employee.user');
            if ($this->monthYear) {
                $query->where('month_year', $this->monthYear);
            }

            foreach ($query->cursor() as $payroll) {
                fputcsv($file, [
                    $payroll->employee?->user?->name ?? '-',
                    $payroll->month_year,
                    $payroll->basic_salary,
                    $payroll->housing_allowance ?? 0,
                    $payroll->transport_allowance ?? 0,
                    $payroll->phone_allowance ?? 0,
                    $payroll->bonuses,
                    $payroll->social_insurance_rate ?? 0,
                    $payroll->social_insurance_amount ?? 0,
                    $payroll->absence_days ?? 0,
                    $payroll->absence_deduction ?? 0,
                    $payroll->deductions,
                    $payroll->net_salary,
                    $payroll->status === 'paid'
                        ? __('filament.status.paid')
                        : __('filament.status.unpaid'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
```

### ٣.٢ التحليل سطراً بسطر

#### تعريف الكلاس والـ Constructor

```php
class PayrollExport
{
    protected ?string $monthYear;

    public function __construct(?string $monthYear = null)
    {
        $this->monthYear = $monthYear;
    }
```

| العنصر | الوصف |
|--------|------|
| `?string $monthYear` | الشهر/السنة للتصدير (مثل `2026-06`). `null` يعني تصدير كل الرواتب |
| `protected` | الخاصية محمية — لا يمكن الوصول لها من خارج الكلاس مباشرة |
| `?string` | نوع قابل للفراغ (Nullable String) — PHP 8 union type |

> **نمط التصميم:** يسمح بإنشاء التصدير بمعلّمات اختيارية:
> ```php
> (new PayrollExport())->download();              // كل الرواتب
> (new PayrollExport('2026-06'))->download();     // فقط شهر يونيو 2026
> ```

#### بناء اسم الملف والترويسات

```php
    $filename = 'payrolls' . ($this->monthYear ? '_' . $this->monthYear : '') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
```

| المثال | اسم الملف الناتج |
|--------|------------------|
| بدون فلتر | `payrolls.csv` |
| `monthYear = '2026-06'` | `payrolls_2026-06.csv` |

**ترويسات HTTP:**

| الترويسة | القيمة | الوصف |
|----------|--------|------|
| `Content-Type` | `text/csv; charset=utf-8` | نوع المحتوى CSV مع ترميز UTF-8 |
| `Content-Disposition` | `attachment; filename="..."` | إجبار المتصفح على التحميل (وليس العرض) |

#### ⭐ دعم UTF-8 BOM للعربية

```php
    $callback = function () {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
```

**المشكلة التي تحلها:**

عند فتح ملف CSV عربي في Microsoft Excel، تظهر الحروف العربية كرموز غير مقروءة:

```
المتوقع:  اسم الموظف، الشهر، الراتب الأساسي
الفعلي:   Ø§Ø³Ù… Ø§Ù„Ù…ÙˆØ¸ÙØŒ Ø§Ù„Ø´Ù‡Ø±ØŒ Ø§Ù„Ø±Ø§ØªØ¨
```

**الحل — UTF-8 BOM (Byte Order Mark):**

```php
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
```

| المكوّن | القيمة | الوصف |
|---------|--------|------|
| `chr(0xEF)` | 239 | البايت الأول لـ BOM |
| `chr(0xBB)` | 187 | البايت الثاني |
| `chr(0xBF)` | 191 | البايت الثالث |

> **BOM** هو توقيع من 3 بايتات (`EF BB BF`) في بداية الملف يخبر Excel أن المحتوى بترميز UTF-8. بدونها، يفترض Excel ترميز Windows-1256 ويفشل في عرض العربية.

**`php://output`:**

`fopen('php://output', 'w')` يفتح تيار إخراج مباشر إلى متصفح المستخدم — لا يُكتب على القرص الصلب، بل يُرسل مباشرة عبر HTTP.

#### كتابة صف العناوين

```php
        fputcsv($file, [
            __('filament.fields.employee'),          // الموظف
            __('filament.fields.month'),             // الشهر
            __('filament.fields.basic_salary'),      // الراتب الأساسي
            __('filament.fields.housing_allowance'), // بدل السكن
            __('filament.fields.transport_allowance'),// بدل النقل
            __('filament.fields.phone_allowance'),   // بدل الهاتف
            __('filament.fields.bonuses'),           // المكافآت
            __('filament.fields.social_insurance_rate'),// نسبة التأمين
            __('filament.fields.social_insurance_amount'),// مبلغ التأمين
            __('filament.fields.absence_days'),      // أيام الغياب
            __('filament.fields.absence_deduction'), // خصم الغياب
            __('filament.fields.deductions'),        // الخصومات
            __('filament.fields.net_salary'),        // صافي الراتب
            __('filament.fields.status'),            // الحالة
        ]);
```

**14 عموداً** بترجمة كاملة عبر `__()`. العناوين تتبع لغة المستخدم الحالية.

`fputcsv()` يكتب المصفوفة كسطر CSV واحد مع فواصل مناسبة:
```
الموظف,الشهر,الراتب الأساسي,بدل السكن,بدل النقل,بدل الهاتف,المكافآت,...
```

#### الاستعلام وتصفية البيانات

```php
        $query = Payroll::with('employee.user');
        if ($this->monthYear) {
            $query->where('month_year', $this->monthYear);
        }
```

| السطر | الوصف |
|-------|------|
| `Payroll::with('employee.user')` | Eager Loading لمنع N+1 Queries |
| `->where('month_year', $this->monthYear)` | فلترة اختيارية بالشهر |

#### ⭐ استخدام Cursor للذاكرة الكفؤة

```php
        foreach ($query->cursor() as $payroll) {
            fputcsv($file, [...]);
        }
```

**لماذا `cursor()` وليس `get()`؟**

| الطريقة | الاستهلاك | المناسب لـ |
|---------|----------|-----------|
| `->get()` | يحمّل **كل** السجلات في الذاكرة دفعة واحدة | مجموعات صغيرة (<1000) |
| `->cursor()` | يحمّل **سجل واحد** في كل مرة من قاعدة البيانات | مجموعات كبيرة (>1000) |

> مع `cursor()`: إذا كان هناك 10,000 موظف، لا تستهلك الذاكرة أكثر من سجل واحد في كل لحظة.

#### كتابة صفوف البيانات

```php
            fputcsv($file, [
                $payroll->employee?->user?->name ?? '-',      // الموظف
                $payroll->month_year,                          // 2026-06
                $payroll->basic_salary,                        // 5000.00
                $payroll->housing_allowance ?? 0,              // 1000.00
                $payroll->transport_allowance ?? 0,            // 500.00
                $payroll->phone_allowance ?? 0,                // 200.00
                $payroll->bonuses,                             // 500.00
                $payroll->social_insurance_rate ?? 0,          // 7.5
                $payroll->social_insurance_amount ?? 0,        // 375.00
                $payroll->absence_days ?? 0,                   // 2
                $payroll->absence_deduction ?? 0,              // 333.33
                $payroll->deductions,                          // 100.00
                $payroll->net_salary,                          // 5391.67
                $payroll->status === 'paid'
                    ? __('filament.status.paid')               // مدفوع
                    : __('filament.status.unpaid'),             // غير مدفوع
            ]);
```

**معالجة القيم الفارغة:**
- `$payroll->employee?->user?->name ?? '-'` — استخدام Null-safe operator (`?->`) مع عامل دمج null (`??`) لعرض `-` بدلاً من خطأ
- `$payroll->housing_allowance ?? 0` — عرض `0` للقيم الفارغة

**ترجمة الحالة:**
```php
$payroll->status === 'paid' ? __('filament.status.paid') : __('filament.status.unpaid')
```
- `'paid'` → "مدفوع"
- أي قيمة أخرى → "غير مدفوع"

#### الإغلاق والاستجابة

```php
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
```

| السطر | الوصف |
|-------|------|
| `fclose($file)` | إغلاق تيار الإخراج |
| `response()->stream($callback, 200, $headers)` | إرجاع **StreamedResponse**Symfony |

> **`response()->stream()`:** بدلاً من بناء الاستجابة كاملة في الذاكرة، يبني Laravel استجابة متدفّقة (streaming) تُرسل قطعة قطعة. هذا يقلل استهلاك الذاكرة بشكل كبير للملفات الكبيرة.

### ٣.٣ مثال على الملف الناتج

```csv
﻿الموظف,الشهر,الراتب الأساسي,بدل السكن,بدل النقل,بدل الهاتف,المكافآت,نسبة التأمين,مبلغ التأمين,أيام الغياب,خصم الغياب,الخصومات,صافي الراتب,الحالة
رنا قدور,2026-06,5000.00,1000.00,500.00,200.00,500.00,7.5,375.00,0,0,100.00,5725.00,مدفوع
كنان الشريف,2026-06,4500.00,800.00,400.00,150.00,300.00,7.5,337.50,2,300.00,0,4812.50,غير مدفوع
محمد الأحمد,2026-06,6000.00,1200.00,600.00,250.00,0,7.5,450.00,0,0,0,6600.00,مدفوع
```

> أول 3 بايتات (`﻿`) هي BOM (غير مرئية في معظم المحررات لكن Excel يفهمها).

---

## ٤. خدمات التصدير PDF

خدمات الـ PDF تمّ شرحها بالتفصيل في ملفات `ser5.md` و `ser6.md`. هنا ملخص موجز للسياق:

### ٤.١ `InvoicePdfService.php` — تصدير الفواتير

| العنصر | التفاصيل |
|--------|----------|
| **القالب** | `pdf/invoice.blade.php` |
| **المعالجة العربية** | ArPHP Glyphs لكل الحقول النصية |
| **الاستجابة** | `streamDownload()` |
| **اسم الملف** | `invoice_{invoice_number}.pdf` |
| **المستدعي** | `InvoiceResource` (Admin, Accountant, Client) |

### ٤.٢ `PayslipPdfService.php` — تصدير قسائم الرواتب

| العنصر | التفاصيل |
|--------|----------|
| **القالب** | `pdf/payslip.blade.php` |
| **الميزة الفريدة** | ⭐ رسالة تحفيزية بالذكاء الاصطناعي |
| **الفشل الصامت** | إذا فشلت AI، يُولّد القسيمة بدون رسالة |
| **اسم الملف** | `payslip_{name}_{month}.pdf` (مع تنظيف الأحرف) |
| **المستدعي** | `PayrollResource` (Admin, Accountant) |

### ٤.٣ `DashboardExportService.php` — تصدير لوحة المعلومات

| العنصر | التفاصيل |
|--------|----------|
| **القالب** | `pdf/dashboard-report.blade.php` |
| **الورقة** | A4 أفقي (Landscape) |
| **الأقسام** | KPIs، بيانات 12 شهراً، نشاطات، إحصائيات موظفين |
| **عدد الاستعلامات** | 36 استعلام (KPIs + شهري + نشاطات + موظفين) |
| **اسم الملف** | `dashboard_report_{Y_m_d_His}.pdf` |
| **المستدعي** | `AdminDashboard` (Admin فقط) |

---

## ٥. جدول مقارنة أنواع التصدير

| المعيار | PayrollExport (CSV) | InvoicePdfService (PDF) | PayslipPdfService (PDF) | DashboardExport (PDF) |
|---------|--------------------|-----------------------|------------------------|---------------------|
| **الصيغة** | CSV | PDF | PDF | PDF |
| **المكتبة** | PHP Native (`fputcsv`) | DomPDF | DomPDF | DomPDF |
| **الاستهلاك** | `cursor()` (ذاكرة منخفضة) | `streamDownload` | `streamDownload` | `streamDownload` |
| **معالجة عربية** | ✅ UTF-8 BOM | ✅ ArPHP Glyphs | ✅ ArPHP Glyphs | ❌ لا يحتاج (أرقام فقط) |
| **حجم البيانات** | غير محدود (Cursor) | فاتورة واحدة | قسيمة واحدة | تجميع شامل |
| **الذكاء الاصطناعي** | ❌ | ❌ | ✅ رسالة تحفيزية | ❌ |
| **الورقة** | N/A | A4 عمودي | A4 عمودي | A4 أفقي |
| **الترجمة** | ✅ `__()` للعناوين والحالة | ✅ | ✅ | ✅ |
| **الفشل الصامت** | N/A | N/A | ✅ لرسالة AI | N/A |
| **عدد الاستدعاءات** | 2 مواقع | 3 لوحات | 2 لوحات | 1 لوحة |

---

## ٦. التكامل مع Filament

### ٦.١ زر تصدير CSV في `ListPayrolls`

```php
// app/Filament/Resources/PayrollResource/Pages/ListPayrolls.php
protected function getHeaderActions(): array
{
    return [
        Action::make('export_excel')
            ->label(__('filament.actions.export_excel'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn () => (new PayrollExport())->download()),
        CreateAction::make(),
    ];
}
```

> يظهر زر في **رأس صفحة قائمة الرواتب** بجانب زر الإنشاء.

### ٦.٢ زر تصدير PDF في الفواتير

```php
// في InvoiceResource.php
Tables\Actions\Action::make('download_pdf')
    ->label(__('filament.actions.download_invoice_pdf'))
    ->icon('heroicon-o-document-arrow-down')
    ->color('warning')
    ->action(fn (Invoice $record) => app(InvoicePdfService::class)->generate($record)),
```

### ٦.٣ زر تصدير قسيمة راتب PDF

```php
// في PayrollResource.php
Tables\Actions\Action::make('download_payslip')
    ->label(__('filament.actions.download_payslip'))
    ->icon('heroicon-o-document-arrow-down')
    ->color('success')
    ->action(fn (Payroll $record) => app(PayslipPdfService::class)->generate($record)),
```

### ٦.٤ زر تصدير تقرير لوحة المعلومات

```php
// في AdminDashboard.php
Action::make('export_pdf')
    ->label(__('filament.actions.export_dashboard_pdf'))
    ->icon('heroicon-o-document-arrow-down')
    ->color('success')
    ->action(fn () => app(DashboardExportService::class)->generatePdf()),
```

---

## ٧. قوالب Blade للتصدير PDF

```
resources/views/pdf/
├── invoice.blade.php          # فاتورة احترافية
│   ├── معلومات الشركة والعميل
│   ├── جدول بنود الفاتورة
│   ├── احتساب الضريبة والإجمالي
│   └── تذييل احترافي
│
├── payslip.blade.php          # قسيمة راتب
│   ├── معلومات الموظف (اسم، مسمى، قسم)
│   ├── تفصيل الراتب (أساسي، بدلات، خصومات)
│   ├── صافي الراتب (بارز)
│   └── رسالة تحفيزية AI (إن وُجدت)
│
└── dashboard-report.blade.php # تقرير لوحة المعلومات (A4 أفقي)
    ├── رأس التقرير (اسم الشركة، التاريخ)
    ├── بطاقات KPIs (4 مؤشرات)
    ├── جدول البيانات الشهرية (12 شهراً)
    ├── قائمة آخر النشاطات (15)
    └── جدول إحصائيات الموظفين (10)
```

> جميع القوالب تستخدم توجيه `@ar()` لمعالجة الحروف العربية عند الحاجة.

---

## ٨. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Service Layer** | `PayrollExport`, خدمات PDF | منطق التصدير منفصل عن العرض |
| **Strategy** | CSV vs PDF | استراتيجيات تصدير مختلفة |
| **Stream Response** | كلاهما | توفير الذاكرة |
| **Cursor Iterator** | `PayrollExport` | معالجة آلاف السجلات بدون نفاد الذاكرة |
| **Graceful Degradation** | `PayslipPdfService` | فشل صامت للذكاء الاصطناعي |
| **Optional Parameters** | `PayrollExport::__construct(?string)` | مرونة في التصفية |
| **In-memory Mutation** | خدمات PDF (ArPHP) | تعديل النص دون حفظ في DB |

---

## ٩. نقاط القوة والقيود

### نقاط القوة

| النقطة | الوصف |
|--------|------|
| **ذاكرة كفؤة** | `cursor()` + `stream()` لمعالجة ملفات كبيرة |
| **دعم عربي كامل** | BOM في CSV + ArPHP في PDF |
| **تصدير متعدد الأدوار** | المحاسب يصدّر، العميل يطّلع، المدير يرى |
| **فشل صامت** | PDF يُولّد دائماً حتى لو فشلت AI |
| **ترجمة كاملة** | عناوين CSV وحالات الفواتير بالعربية/الإنجليزية |

### القيور

| القيد | الوصف |
|------|------|
| **تصدير CSV واحد** | فقط الرواتب (لا فواتير/مصروفات CSV) |
| **لا Excel أصلي** | `.xlsx` غير مدعوم (فقط CSV) |
| **لا تصدير تفاعلي** | لا يمكن للمستخدم اختيار الأعمدة |
| **36 استعلام لتقرير لوحة المعلومات** | يمكن تحسينها بـ GROUP BY + Caching |
| **لا تصدير مجدول** | لا يمكن جدولة تصدير أسبوعي تلقائي |

---

## ١٠. ملخص الإحصائيات

| المقياس | القيمة |
|---------|--------|
| **ملفات `app/Exports/`** | 1 (`PayrollExport.php`) |
| **خدمات تصدير PDF** | 3 (`InvoicePdfService`, `PayslipPdfService`, `DashboardExportService`) |
| **أنواع التصدير** | 2 (CSV + PDF) |
| **قوالب Blade للتصدير** | 3 (`invoice`, `payslip`, `dashboard-report`) |
| **إجمالي السطور البرمجية** | 280 سطر (76 + 43 + 58 + 103) |
| **أعمدة تصدير الرواتب** | 14 عموداً |
| **اللوحات المستفيدة** | 4 (Admin, HR, Accountant, Client) |

---

*نهاية تقرير نظام التصدير*
