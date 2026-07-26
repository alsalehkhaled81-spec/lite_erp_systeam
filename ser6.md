# تقرير تحليلي مفصل — `DashboardExportService.php`

> **الملف:** `app/Services/DashboardExportService.php`
> **السطور:** 103 سطر
> **النوع:** خدمة (Service) — تصدير تقرير شامل للوحة المعلومات بصيغة PDF
> **التبعيات:** `App\Models\Employee`, `Project`, `Task`, `Invoice`, `Expense`, `Barryvdh\DomPDF\Facade\Pdf`

---

## ١. الدور في النظام

`DashboardExportService` هي خدمة **تجميع وتصدير** — تجمع بيانات شاملة من 5 نماذج مختلفة (موظفين، مشاريع، مهام، فواتير، مصروفات)، تحسب مؤشرات الأداء (KPIs) والبيانات الشهرية وإحصائيات الموظفين، ثم تولّد تقرير PDF احترافي بتنسيق **A4 أفقي (Landscape)**.

```
المدير العام يضغط "تصدير التقرير PDF"
            │
            ▼
    ┌───────────────────────────────────┐
    │   DashboardExportService          │ ← هذا الملف
    │───────────────────────────────────│
    │ 1. تجميع KPIs (4 مؤشرات)         │
    │ 2. حساب بيانات 12 شهراً           │
    │ 3. جمع آخر 15 نشاطاً              │
    │ 4. إحصائيات 10 موظفين             │
    │ 5. توليد PDF (A4 أفقي)            │
    └───────────────────────────────────┘
            │
            ▼
    تحميل: dashboard_report_2026_07_11_160000.pdf
```

### من يستخدم هذه الخدمة؟

| المستدعي | الملف | الاستخدام |
|----------|------|-----------|
| `AdminDashboard` | `app/Filament/Pages/AdminDashboard.php` | زر "تصدير التقرير PDF" في رأس لوحة المدير |

```php
// في AdminDashboard.php
Action::make('export_pdf')
    ->label(__('filament.actions.export_dashboard_pdf'))
    ->icon('heroicon-o-document-arrow-down')
    ->color('success')
    ->action(fn () => app(DashboardExportService::class)->generatePdf()),
```

---

## ٢. الكود الكامل مع التحليل سطراً بسطر

### ٢.١ تعريف الكلاس والاستيرادات

```php
namespace App\Services;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardExportService
{
```

- يستورد **5 نماذج** مختلفة لتجميع البيانات من جداول متعددة في استعلام واحد.

### ٢.٢ الدالة الرئيسية: `generatePdf()`

```php
public function generatePdf(): mixed
{
```

- **لا توجد مدخلات** — كل البيانات تُجلب من قاعدة البيانات مباشرة.
- **القيمة المُرجعة:** `mixed` — استجابة HTTP Stream (للتحميل).

---

### ٢.٣ المرحلة 1: حساب KPIs الإجمالية

```php
    $totalEmployees = Employee::count();
    $activeEmployees = Employee::where('status', 'active')->count();
    $totalProjects = Project::count();
    $activeProjects = Project::where('status', 'in_progress')->count();
    $totalTasks = Task::count();
    $doneTasks = Task::where('status', 'done')->count();
    $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
    $totalExpenses = Expense::sum('amount');
    $netProfit = $totalRevenue - $totalExpenses;
    $taskRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
```

**10 استعلامات إحصائية:**

| المتغير | الاستعلام | الوصف |
|---------|----------|------|
| `$totalEmployees` | `Employee::count()` | إجمالي الموظفين |
| `$activeEmployees` | `->where('status', 'active')->count()` | الموظفون النشطون |
| `$totalProjects` | `Project::count()` | إجمالي المشاريع |
| `$activeProjects` | `->where('status', 'in_progress')->count()` | المشاريع النشطة |
| `$totalTasks` | `Task::count()` | إجمالي المهام |
| `$doneTasks` | `->where('status', 'done')->count()` | المهام المنجزة |
| `$totalRevenue` | `Invoice::where('status', 'paid')->sum('amount')` | الإيرادات (مدفوعة فقط) |
| `$totalExpenses` | `Expense::sum('amount')` | إجمالي المصروفات (بكل حالاتها) |
| `$netProfit` | `$totalRevenue - $totalExpenses` | **صافي الربح** |
| `$taskRate` | `round(($doneTasks / $totalTasks) * 100)` | نسبة إنجاز المهام % |

> **ملاحظة دقيقة:** `$totalExpenses` يجمع **جميع** المصروفات بما فيها `pending` و `rejected`، وليس فقط `approved`. هذا قد يكون قراراً مقصوداً (عرض الإنفاق الكلي) أو نقطة تحتاج مراجعة.

```php
    $kpis = [
        [
            'label' => __('filament.widgets.total_employees'),
            'value' => $totalEmployees,
        ],
        [
            'label' => __('filament.widgets.total_projects'),
            'value' => $totalProjects,
        ],
        [
            'label' => __('filament.widgets.total_tasks'),
            'value' => $totalTasks,
            'trend' => $taskRate,
        ],
        [
            'label' => __('filament.widgets.net_profit'),
            'value' => '$' . number_format($netProfit, 2),
            'trend' => $netProfit >= 0 ? 5 : -5,
        ],
    ];
```

**بناء مصفوفة KPIs (4 بطاقات):**

| KPI | القيمة | الاتجاه (trend) |
|-----|--------|----------------|
| إجمالي الموظفين | عدد صحيح | لا يوجد |
| إجمالي المشاريع | عدد صحيح | لا يوجد |
| إجمالي المهام | عدد صحيح | نسبة الإنجاز % |
| صافي الربح | `$X,XXX.XX` (منسّق) | +5 (ربح) أو -5 (خسارة) |

> **`number_format($netProfit, 2)`:** ينسّق الرقم بفواصل الآلاف ومنزلتين عشريتين: `12500.5` → `12,500.50`
>
> **`__('filament.widgets...')`:** الترجمة من ملف اللغة لضمان عرض النص بالعربية أو الإنجليزية حسب لغة المستخدم.

---

### ٢.٤ المرحلة 2: البيانات الشهرية (12 شهراً)

```php
    $monthlyData = [];
    for ($i = 11; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $rev = (float) Invoice::where('status', 'paid')
            ->whereYear('issue_date', $month->year)
            ->whereMonth('issue_date', $month->month)
            ->sum('amount');
        $exp = (float) Expense::whereYear('expense_date', $month->year)
            ->whereMonth('expense_date', $month->month)
            ->sum('amount');

        $monthlyData[] = [
            'month' => $month->translatedFormat('M Y'),
            'revenue' => $rev,
            'expense' => $exp,
            'net' => $rev - $exp,
        ];
    }
```

**تحليل الحلقة (Loop):**

| السطر | الوصف |
|-------|------|
| `for ($i = 11; $i >= 0; $i--)` | حلقة من 11 إلى 0 (12 تكراراً) |
| `$month = now()->subMonths($i)` | الشهر الحالي ناقص `$i` أشهر |

**التسلسل الزمني:**

```
$i=11  →  قبل 11 شهراً  →  أقدم شهر
$i=10  →  قبل 10 أشهر
...
$i=1   →  الشهر الماضي
$i=0   →  الشهر الحالي   →  أحدث شهر
```

> الترتيب من الأقدم للأحدث — مناسب للمخططات الزمنية.

**الاستعلامات لكل شهر:**

| البيانات | الاستعلام | الوصف |
|---------|----------|------|
| `$rev` | `Invoice::where('status', 'paid')->whereYear(...)->whereMonth(...)->sum('amount')` | إيرادات الشهر (مدفوعة فقط) |
| `$exp` | `Expense::whereYear(...)->whereMonth(...)->sum('amount')` | مصروفات الشهر |
| `net` | `$rev - $exp` | صافي الشهر |

**`(float)` Cast:**
- يضمن أن القيمة رقم عشري (وليس string من `sum()`).
- ضروري للحسابات والمقارنات اللاحقة.

**`translatedFormat('M Y')`:**
- ينسّق التاريخ باللغة الحالية: `Jul 2026` (إنجليزي) أو `تموز ٢٠٢٦` (عربي إذا كان Carbon مُهيّأ).
- `translatedFormat` بدلاً من `format` لدعم الترجمة.

**النتيجة:** مصفوفة من 12 عنصراً:

```php
[
    ['month' => 'Aug 2025', 'revenue' => 45000.00, 'expense' => 30000.00, 'net' => 15000.00],
    ['month' => 'Sep 2025', 'revenue' => 52000.00, 'expense' => 28000.00, 'net' => 24000.00],
    // ... 10 أشهر أخرى
    ['month' => 'Jul 2026', 'revenue' => 48000.00, 'expense' => 35000.00, 'net' => 13000.00],
]
```

---

### ٢.٥ المرحلة 3: آخر النشاطات

```php
    $activities = collect(Task::latest()->take(15)->get())->map(function ($task) {
        return [
            'color' => 'badge-info',
            'icon' => 'T',
            'description' => $task->title,
            'time' => $task->created_at->diffForHumans(),
        ];
    });
```

**سطراً بسطر:**

| السطر | الوصف |
|-------|------|
| `Task::latest()` | ترتيب تنازلي حسب `created_at` (الأحدث أولاً) |
| `->take(15)` | أخذ آخر 15 مهمة فقط |
| `->get()` | تنفيذ الاستعلام |
| `collect(...)` | تحويل Collection إلى Laravel Collection (لـ `map`) |
| `->map(function ($task) { ... })` | تحويل كل مهمة لصيغة مبسّطة |

**بنية كل نشاط:**

| المفتاح | القيمة | الوصف |
|---------|--------|------|
| `color` | `'badge-info'` | فئة CSS للون الشارة |
| `icon` | `'T'` | حرف يمثل المهمة (Task) |
| `description` | `$task->title` | عنوان المهمة |
| `time` | `$task->created_at->diffForHumans()` | "منذ ساعتين"، "أمس"، إلخ |

> **`diffForHumans()`:** تحوّل الفارق الزمني لنص بشري: `2026-07-11 14:00` → "منذ 3 ساعات"

> **ملاحظة:** هذا القسم يجلب المهام فقط (ليس المشاريع أو الفواتير). يمكن توسيعه ليشمل أنواع نشاطات متنوعة.

---

### ٢.٦ المرحلة 4: إحصائيات الموظفين

```php
    $employeeStats = Employee::with('user')
        ->withCount(['tasks',
            'tasks as done_tasks' => function ($query) {
                $query->where('status', 'done');
            }
        ])->take(10)->get()->map(function ($emp) {
            return [
                'name' => $emp->user->name,
                'tasks' => $emp->tasks_count,
                'rate' => $emp->tasks_count > 0
                    ? round(($emp->done_tasks / $emp->tasks_count) * 100) : 0,
            ];
        });
```

**تحليل الاستعلام المتقدم:**

| السطر | الوصف |
|-------|------|
| `Employee::with('user')` | Eager Load لعلاقة المستخدم |
| `->withCount(['tasks', ...])` | عدّ المهام الإجمالية لكل موظف |
| `'tasks as done_tasks' => function ($query) { $query->where('status', 'done'); }` | **عدّ مشروط** — عدّ المهام المنجزة فقط كـ `done_tasks` |
| `->take(10)` | أول 10 موظفين |

**`withCount` المتقدم:**

ينتج استعلام SQL مثل:

```sql
SELECT employees.*,
    (SELECT COUNT(*) FROM tasks WHERE tasks.employee_id = employees.id) AS tasks_count,
    (SELECT COUNT(*) FROM tasks WHERE tasks.employee_id = employees.id AND tasks.status = 'done') AS done_tasks
FROM employees
LIMIT 10
```

> هذا **استعلام واحد** يحصل على كل البيانات — بدلاً من 20+ استعلام منفصل (N+1).

**التحويل النهائي:**

```php
->map(function ($emp) {
    return [
        'name' => $emp->user->name,
        'tasks' => $emp->tasks_count,
        'rate' => $emp->tasks_count > 0
            ? round(($emp->done_tasks / $emp->tasks_count) * 100) : 0,
    ];
})
```

**النتيجة:**

```php
[
    ['name' => 'رنا قدور', 'tasks' => 24, 'rate' => 75],
    ['name' => 'كنان الشريف', 'tasks' => 18, 'rate' => 89],
    // ... 8 موظفين آخرين
]
```

> **حماية القسمة على صفر:** `$emp->tasks_count > 0 ? ... : 0` — موظف بدون مهام → 0%.

---

### ٢.٧ المرحلة 5: تجميع البيانات وتوليد PDF

```php
    $data = [
        'company' => config('app.name', 'ERP System'),
        'title' => __('filament.actions.export_dashboard_pdf'),
        'generatedAt' => now()->translatedFormat('Y-m-d H:i'),
        'kpis' => $kpis,
        'monthlyData' => $monthlyData,
        'activities' => $activities,
        'employeeStats' => $employeeStats,
    ];
```

**مصفوفة البيانات النهائية:**

| المفتاح | المصدر | المحتوى |
|---------|--------|---------|
| `company` | `config('app.name')` | اسم الشركة |
| `title` | الترجمة | عنوان التقرير |
| `generatedAt` | `now()->translatedFormat(...)` | وقت التوليد |
| `kpis` | محسوب | 4 مؤشرات أداء |
| `monthlyData` | محسوب | 12 شهراً من البيانات |
| `activities` | محسوب | آخر 15 نشاطاً |
| `employeeStats` | محسوب | إحصائيات 10 موظفين |

```php
    $pdf = Pdf::loadView('pdf.dashboard-report', $data);
    $pdf->setPaper('a4', 'landscape');
```

| السطر | الوصف |
|-------|------|
| `Pdf::loadView('pdf.dashboard-report', $data)` | تحميل قالب `resources/views/pdf/dashboard-report.blade.php` |
| `->setPaper('a4', 'landscape')` | ضبط الورقة إلى **A4 أفقي** (Landscape) |

> **لماذا A4 أفقي؟** لأن التقرير يحتوي على جداول ومخططات واسعة (12 شهراً، 10 موظفين) — الاتجاه الأفقي يوفر مساحة عرض أكبر.

```php
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->output();
    }, 'dashboard_report_' . now()->format('Y_m_d_His') . '.pdf');
}
```

**اسم الملف:** `dashboard_report_2026_07_11_160532.pdf`
- `Y_m_d_His` → `2026_07_11_160532` (سنة_شهر_يوم_ساعةدقيقةثانية)
- كل تقرير له اسم فريد (طابع زمني) لمنع الكتابة فوق ملفات سابقة.

---

## ٣. مخطط تدفق المنطق الكامل

```
           ┌─────────────────────────────┐
           │ generatePdf()               │
           │ (لا مدخلات)                 │
           └──────────────┬──────────────┘
                          │
         ┌────────────────┼────────────────┐
         │                │                │
         ▼                ▼                ▼
┌─────────────┐  ┌──────────────┐  ┌────────────────┐
│ المرحلة 1   │  │ المرحلة 2    │  │ المرحلة 3      │
│ KPIs        │  │ بيانات شهرية │  │ النشاطات       │
│─────────────│  │──────────────│  │────────────────│
│ 10 استعلام │  │ 24 استعلام  │  │ 1 استعلام     │
│ (5 نماذج)   │  │ (12 شهر × 2) │  │ (Task::latest) │
│             │  │              │  │                │
│ 4 مؤشرات    │  │ 12 شهراً     │  │ 15 نشاطاً      │
└──────┬──────┘  └──────┬───────┘  └───────┬────────┘
       │                │                  │
       │         ┌──────┴───────┐          │
       │         │              │          │
       │         ▼              │          │
       │  ┌──────────────┐      │          │
       │  │ المرحلة 4    │      │          │
       │  │ إحصائيات    │      │          │
       │  │ الموظفين     │      │          │
       │  │──────────────│      │          │
       │  │ 1 استعلام   │      │          │
       │  │ (withCount)  │      │          │
       │  │              │      │          │
       │  │ 10 موظفين    │      │          │
       │  └──────┬───────┘      │          │
       │         │              │          │
       └─────────┴──────────────┴──────────┘
                          │
                          ▼
           ┌─────────────────────────────┐
           │ تجميع $data (7 مفاتيح)     │
           └──────────────┬──────────────┘
                          │
                          ▼
           ┌─────────────────────────────┐
           │ DomPDF::loadView(           │
           │   'pdf.dashboard-report'    │
           │ )                           │
           │ → setPaper('a4','landscape')│
           └──────────────┬──────────────┘
                          │
                          ▼
           ┌─────────────────────────────┐
           │ streamDownload:             │
           │   dashboard_report_         │
           │   {Y_m_d_His}.pdf           │
           └─────────────────────────────┘
```

---

## ٤. أداء الاستعلامات

| المرحلة | عدد الاستعلامات | الوصف |
|---------|----------------|------|
| KPIs | 10 | 10 استعلامات `COUNT` و `SUM` منفصلة |
| بيانات شهرية | 24 | 12 شهر × 2 استعلام (إيراد + مصروف) |
| النشاطات | 1 | `Task::latest()->take(15)` |
| إحصائيات الموظفين | 1 | `withCount` متقدم (استعلام واحد) |
| **الإجمالي** | **36 استعلام** | لكل توليد تقرير |

> **ملاحظة أداء:** 36 استعلاماً قد يبدو كثيراً، لكنها جميعاً استعلامات `COUNT`/`SUM` خفيفة على فهارس. يمكن تحسينها مستقبلاً بـ:
> - تجميع الاستعلامات الشهرية في استعلام `GROUP BY` واحد
> - استخدام التخزين المؤقت (Caching) للنتائج

---

## ٥. قالب PDF (`resources/views/pdf/dashboard-report.blade.php`)

القالب يستلم 7 مفاتيح بيانات ويعرضها في تنسيق A4 أفقي:

```blade
{{-- معلومات الرأس --}}
<h1>{{ $company }}</h1>
<h2>{{ $title }}</h2>
<p>{{ $generatedAt }}</p>

{{-- 1. بطاقات KPIs --}}
@foreach($kpis as $kpi)
    <div class="kpi-card">
        <span>{{ $kpi['label'] }}</span>
        <strong>{{ $kpi['value'] }}</strong>
        @if(isset($kpi['trend']))
            <small>{{ $kpi['trend'] }}</small>
        @endif
    </div>
@endforeach

{{-- 2. جدول البيانات الشهرية --}}
<table>
    <tr><th>الشهر</th><th>الإيرادات</th><th>المصروفات</th><th>الصافي</th></tr>
    @foreach($monthlyData as $month)
        <tr>
            <td>{{ $month['month'] }}</td>
            <td>${{ number_format($month['revenue'], 2) }}</td>
            <td>${{ number_format($month['expense'], 2) }}</td>
            <td>${{ number_format($month['net'], 2) }}</td>
        </tr>
    @endforeach
</table>

{{-- 3. قائمة النشاطات --}}
@foreach($activities as $activity)
    <div class="{{ $activity['color'] }}">
        <span>{{ $activity['icon'] }}</span>
        <span>{{ $activity['description'] }}</span>
        <small>{{ $activity['time'] }}</small>
    </div>
@endforeach

{{-- 4. جدول إحصائيات الموظفين --}}
<table>
    <tr><th>الموظف</th><th>المهام</th><th>نسبة الإنجاز</th></tr>
    @foreach($employeeStats as $emp)
        <tr>
            <td>{{ $emp['name'] }}</td>
            <td>{{ $emp['tasks'] }}</td>
            <td>{{ $emp['rate'] }}%</td>
        </tr>
    @endforeach
</table>
```

---

## ٦. أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **Service Layer** | الكلاس بأكمله | منطق التصدير منفصل |
| **Aggregator** | تجميع من 5 نماذج | جمع بيانات من مصادر متعددة |
| **Data Mapper** | تحويل النماذج لمصفوفات | فصل البيانات عن العرض |
| **Stream Response** | `streamDownload()` | إرسال تدفّقي للذاكرة |
| **Internationalization** | `__()` و `translatedFormat()` | دعم اللغتين |

---

## ٧. نقاط القوة والقيود

### نقاط القوة

| النقطة | الوصف |
|--------|------|
| **تقرير شامل** | 4 أقسام تغطي كل جوانب الأعمال |
| **بيانات 12 شهراً** | رؤية تاريخية للأداء المالي |
| **withCount متقدم** | استعلام واحد لإحصائيات الموظفين (أداء ممتاز) |
| **A4 أفقي** | مناسب للجداول العريضة |
| **طابع زمني فريد** | كل تقرير له اسم فريد |
| **دعم اللغة** | ترجمة كاملة عبر `__()` |

### القيود

| القيد | الوصف |
|------|------|
| **36 استعلام** | يمكن تحسينها بـ GROUP BY |
| **لا Caching** | يُعاد حساب كل شيء في كل تصدير |
| **النشاطات مهام فقط** | لا يشمل المشاريع/الفواتير/الإجازات |
| **10 موظفين فقط** | بلا ترتيب (ليس الأفضل أداءً) |
| **لا معالجة عربية** | على عكس Invoice/Payslip — لا يستخدم ArPHP (قد يحتاج) |

---

*نهاية تقرير `DashboardExportService.php`*

---

## ملخص جميع تقارير الخدمات

| الملف | التقرير | السطور | الدور |
|------|---------|--------|------|
| `AiService.php` | `ser1.md` | 109 | اتصال أساسي بالذكاء الاصطناعي |
| `AiEvaluationService.php` | `ser2.md` | 92 | تقييم أداء الموظفين |
| `ResumeAnalysisService.php` | `ser3.md` | 101 | تحليل السير الذاتية |
| `ResumeParserService.php` | `ser4.md` | 81 | استخراج نص PDF/DOCX |
| `InvoicePdfService.php` + `PayslipPdfService.php` | `ser5.md` | 43 + 58 | توليد فواتير وقسائم PDF |
| `DashboardExportService.php` | `ser6.md` | 103 | تصدير تقرير لوحة المعلومات |

**إجمالي:** 7 خدمات، 587 سطر برمجي، موثّقة في 6 تقارير تفصيلية.

---

*نهاية سلسلة تقارير مجلد `app/Services/`*
