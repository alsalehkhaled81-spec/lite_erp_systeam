# التقرير الشامل لتحليل الكود المصدري — مشروع Lite ERP

> هذا التقرير يحلل كل ملف ومجلد في المشروع بشكل تفصيلي دقيق، مع شرح الكود البرمجي والوظائف المنطقية لكل مكون.

---

## جدول المحتويات

1. [شجرة المشروع الكاملة](#١-شجرة-المشروع-الكاملة)
2. [ملفات الإعداد والتكوين](#٢-ملفات-الإعداد-والتكوين)
3. [مجلد `app/` — تفصيل كامل](#٣-مجلد-app--تفصيل-كامل)
   - 3.1 النماذج (Models)
   - 3.2 الخدمات (Services)
   - 3.3 وحدات التحكم (Controllers)
   - 3.4 مكوّنات Livewire
   - 3.5 الإشعارات (Notifications)
   - 3.6 Filament Resources
   - 3.7 Filament Pages
   - 3.8 Filament Widgets
   - 3.9 Filament Concerns (Traits)
   - 3.10 Providers
   - 3.11 Exports
   - 3.12 Support
4. [مجلد `resources/` — تفصيل كامل](#٤-مجلد-resources--تفصيل-كامل)
5. [مجلد `routes/` — تفصيل كامل](#٥-مجلد-routes--تفصيل-كامل)
6. [مجلد `config/` — تفصيل كامل](#٦-مجلد-config--تفصيل-كامل)
7. [مجلد `database/` — تفصيل كامل](#٧-مجلد-database--تفصيل-كامل)
8. [مجلد `lang/` — الترجمة](#٨-مجلد-lang--الترجمة)
9. [ملخص البنية المعمارية](#٩-ملخص-البنية-المعمارية)

---

## ١. شجرة المشروع الكاملة

```
lite_erp_systeam/
├── app/                          # ★ الكود المصدري الأساسي
│   ├── Exports/                  # تصدير البيانات (1 ملف)
│   ├── Filament/                 # لوحات Filament (6 لوحات + مشترك)
│   │   ├── Accountant/           # لوحة المحاسب
│   │   ├── Client/               # لوحة العميل
│   │   ├── Concerns/             # Traits مشتركة
│   │   ├── Employee/             # لوحة الموظف
│   │   ├── Hr/                   # لوحة الموارد البشرية
│   │   ├── Pages/                # صفحات المدير العام
│   │   ├── Pm/                   # لوحة مدير المشاريع
│   │   ├── Resources/            # موارد المدير العام (17 مورد)
│   │   └── Widgets/              # واجهات المدير الإحصائية
│   ├── Http/
│   │   ├── Controllers/          # المتحكمات (3)
│   │   └── Responses/            # استجابات مخصصة
│   ├── Livewire/
│   │   └── Auth/                 # مصادقة Livewire (4)
│   ├── Models/                   # نماذج Eloquent (25)
│   ├── Notifications/            # إشعارات النظام (4)
│   ├── Providers/
│   │   └── Filament/             # مزودات اللوحات (6)
│   ├── Services/                 # طبقة الخدمات (7)
│   └── Support/                  # أدوات مساعدة
├── bootstrap/                    # إقلاع Laravel
├── config/                       # ملفات الإعداد (11)
├── database/
│   ├── factories/                # مصانع البيانات
│   ├── migrations/               # الهجرات (49)
│   └── seeders/                  # بذور البيانات (1)
├── lang/                         # ملفات الترجمة (ar/en)
├── public/                       # الملفات العامة
├── resources/
│   ├── css/                      # أنماط CSS
│   ├── js/                       # سكربتات JS
│   └── views/                    # قوالب Blade
│       ├── application/          # قوالب التقديم على الوظائف
│       ├── components/layouts/   # التخطيطات الأساسية
│       ├── filament/             # قوالب Filament المخصصة
│       ├── livewire/auth/        # قوالب المصادقة
│       └── pdf/                  # قوالب PDF
├── routes/
│   ├── console.php               # مسارات الـ CLI
│   └── web.php                   # مسارات الويب
├── storage/                      # التخزين
├── tests/                        # الاختبارات
├── composer.json                 # حزم PHP
├── package.json                  # حزم Node.js
├── phpunit.xml                   # إعداد الاختبارات
├── vite.config.js                # إعداد Vite
└── .env.example                  # مثال المتغيرات البيئية
```

---

## ٢. ملفات الإعداد والتكوين

### ٢.١ `composer.json` — تبعيات PHP

```json
{
    "require": {
        "php": "^8.2",
        "ar-php/ar-php": "*",              // معالجة الحروف العربية
        "barryvdh/laravel-dompdf": "^3.1", // توليد PDF
        "bezhansalleh/filament-language-switch": "^3.1", // مبدّل اللغة
        "filament/filament": "3.3",        // لوحة الإدارة
        "laravel/framework": "^12.0",      // إطار العمل
        "laravel/tinker": "^2.10.1",       // REPL تفاعلي
        "mokhosh/filament-kanban": "*",    // لوحات كانبان
        "openai-php/laravel": "*",         // تكامل OpenAI
        "parsedown/laravel": "*",          // تحويل Markdown
        "smalot/pdfparser": "^2.12"        // استخراج نص PDF
    }
}
```

**سكربتات Composer المهمة:**

| السكربت | الوصف |
|---------|------|
| `composer setup` | تثبيت شامل: `composer install` → `key:generate` → `migrate` → `npm install` → `npm run build` |
| `composer dev` | تشغيل بيئة التطوير: خادم Laravel + طوابير + Vite بالتوازي (`concurrently`) |
| `composer test` | `config:clear` ثم `artisan test` |

### ٢.٢ `package.json` — تبعيات JavaScript

```json
{
    "devDependencies": {
        "@tailwindcss/vite": "^4.0.0",     // Tailwind CSS 4
        "axios": "^1.11.0",                 // عميل HTTP
        "concurrently": "^9.0.1",           // تشغيل أوامر متوازية
        "laravel-vite-plugin": "^2.0.0",    // تكامل Laravel + Vite
        "tailwindcss": "^4.0.0",            // إطار CSS
        "vite": "^7.0.7"                    // أداة البناء
    }
}
```

### ٢.٣ `vite.config.js` — إعداد البناء

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,   // تحديث تلقائي عند التعديل
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'], // تجاهل قوالب Blade المخزنة
        },
    },
});
```

### ٢.٤ `phpunit.xml` — إعداد الاختبارات

يستخدم **Pest PHP** كإطار اختبار، مع قاعدة بيانات SQLite في الذاكرة (`:memory:`) للاختبارات السريعة:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

---

## ٣. مجلد `app/` — تفصيل كامل

### ٣.١ النماذج (Models) — `app/Models/` (25 نموذج)

النماذج هي جوهر النظام، تمثل جداول قاعدة البيانات والعلاقات بينها عبر Eloquent ORM.

#### ٣.١.١ `User.php` — المستخدم

```php
class User extends Authenticatable implements FilamentUser
{
    protected $fillable = ['role_id', 'name', 'email', 'password',
                           'profile_photo_path', 'is_approved'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',        // تشفير تلقائي
        'is_approved' => 'boolean',
    ];
```

**المنطق الحاسم — `canAccessPanel()`:**

```php
public function canAccessPanel(Panel $panel): bool
{
    if (!$this->role) return false;

    return match ($panel->getId()) {
        'admin'      => $roleName === 'super_admin',
        'hr'         => $roleName === 'hr_manager',
        'pm'         => $roleName === 'project_manager',
        'accountant' => $roleName === 'accountant',
        'employee'   => $roleName === 'employee',
        default      => false,
    };
}
```

> هذه الدالة هي **حارس البوابة** — تحدد من يدخل أي لوحة. المدير العام فقط يدخل `/admin`، مدير HR فقط يدخل `/hr`، إلخ.

**العلاقات:**
- `role()` ← `BelongsTo` → `Role` (المستخدم ينتمي لدور واحد)
- `employee()` ← `HasOne` → `Employee` (1:1 — قد يكون لدى المستخدم سجل موظف)
- `expenses()` ← `HasMany` → `Expense` (المصروفات التي سجلها المستخدم)

#### ٣.١.٢ `Employee.php` — الموظف (المحور)

هذا النموذج هو **أكثر نموذج ترتبط به علاقات** في النظام (14 علاقة).

```php
class Employee extends Model
{
    protected $fillable = ['user_id', 'department_id', 'vacancy_id',
        'job_title', 'salary', 'status', 'hire_date',
        'annual_leave_balance', 'used_leave_days'];
```

**العلاقات الـ 14:**

| الدالة | النوع | النموذج الهدف | الوصف |
|--------|------|--------------|------|
| `user()` | BelongsTo | User | حساب المستخدم |
| `department()` | BelongsTo | Department | القسم |
| `vacancy()` | BelongsTo | Vacancy | الوظيفة المتقدم لها |
| `headOfDepartment()` | HasOne | Department | الأقسام التي يرأسها |
| `resume()` | HasOne | Resume | السيرة الذاتية (1:1) |
| `skills()` | BelongsToMany | Skill | المهارات (M:N) |
| `projects()` | BelongsToMany | Project | المشاريع (M:N) |
| `tasks()` | HasMany | Task | المهام المسندة |
| `leaves()` | HasMany | Leave | طلبات الإجازات |
| `payrolls()` | HasMany | Payroll | مسيرات الرواتب |
| `sentReports()` | HasMany | Report | التقارير المرسلة |
| `receivedReports()` | HasMany | Report | التقارير المستلمة |
| `attendances()` | HasMany | Attendance | سجلات الحضور |
| `certificates()` | HasMany | Certificate | الشهادات |
| `careerPlans()` | HasMany | CareerPlan | خطط التطوير |
| `trainings()` | BelongsToMany | Training | التدريبات (M:N مع Pivot غني) |

**Accessor ذكي — الرصيد المتبقي:**

```php
public function getRemainingLeaveBalanceAttribute(): int
{
    return $this->annual_leave_balance - $this->used_leave_days;
}
```

**Scope مخصص — المرشحون لرئاسة القسم:**

```php
public function scopeEligibleDepartmentHead($query, ?int $currentHeadId = null)
{
    return $query
        ->with('user')
        ->whereDoesntHave('user.role', fn ($q) => $q->where('name', 'super_admin'))
        ->where(function ($q) use ($currentHeadId) {
            $q->whereDoesntHave('headOfDepartment')
                ->when($currentHeadId, fn ($q2) => $q2->orWhere('employees.id', $currentHeadId));
        });
}
```

> يستثني المدير العام والموظفين الذين يرأسون أقساماً أخرى، مع استثناء الرئيس الحالي (لأنه يجب أن يظهر عند التعديل).

#### ٣.١.٣ `Task.php` — المهمة (مع أحداث تلقائية)

```php
class Task extends Model
{
    protected static function booted(): void
    {
        // عند إنشاء مهمة جديدة → إشعار الموظف
        static::created(function (Task $task) {
            if ($task->employee && $task->employee->user) {
                Notification::make()
                    ->title('مهمة جديدة: ' . $task->title)
                    ->body('تم تعيين مهمة جديدة لك في مشروع: '
                        . ($task->project->name ?? 'بدون مشروع'))
                    ->success()
                    ->sendToDatabase($task->employee->user);
            }
        });

        // عند تحديث حالة المهمة → إشعار الموظف
        static::updated(function (Task $task) {
            if ($task->isDirty('status') && $task->employee && $task->employee->user) {
                Notification::make()
                    ->title('تحديث حالة المهمة: ' . $task->title)
                    ->body('تم تغيير حالة المهمة إلى: '
                        . __("filament.status.{$task->status}"))
                    ->info()
                    ->sendToDatabase($task->employee->user);
            }
        });
    }
```

> يستخدم **Model Events** (`booted`) لإطلاق إشعارات تلقائية. `isDirty('status')` يتحقق من تغيير الحالة فقط.

**Accessor — إجمالي الساعات:**

```php
public function getTotalHoursAttribute(): float
{
    return $this->timeEntries->sum('hours');
}
```

#### ٣.١.٤ `Leave.php` — الإجازة (مع إشعارات متعددة المستفيدين)

```php
protected static function booted(): void
{
    static::created(function (Leave $leave) {
        // إشعار جميع مدراء HR والمدير العام
        $hrUsers = User::whereHas('role', function($q) {
            $q->where('name', 'hr_manager')->orWhere('name', 'super_admin');
        })->get();

        foreach ($hrUsers as $user) {
            Notification::make()
                ->title('طلب إجازة جديد')
                ->body('قدم ' . ($leave->employee->user->name ?? 'موظف')
                    . ' طلب إجازة جديد.')
                ->info()
                ->sendToDatabase($user);
        }
    });

    static::updated(function (Leave $leave) {
        if ($leave->isDirty('status') && $leave->employee && $leave->employee->user) {
            Notification::make()
                ->title('تحديث حالة الإجازة')
                ->body('تم تغيير حالة طلب الإجازة إلى: '
                    . __("filament.status.{$leave->status}"))
                ->success()
                ->sendToDatabase($leave->employee->user);
        }
    });
}
```

**Accessor — المدة بالأيام:**

```php
public function getDurationInDaysAttribute(): int
{
    return $this->start_date->diffInDays($this->end_date) + 1;
}
```

#### ٣.١.٥ `Project.php` — المشروع (مع نسبة إنجاز تلقائية)

```php
protected $appends = ['completion_percentage']; // إضافة افتراضية للتسلسل

public function getCompletionPercentageAttribute(): float
{
    $total = $this->tasks()->count();
    if ($total === 0) return 0;
    $completed = $this->tasks()->where('status', 'done')->count();
    return round(($completed / $total) * 100, 1);
}

public function getTotalTrackedHoursAttribute(): float
{
    return $this->tasks->sum(fn ($task) => $task->timeEntries->sum('hours'));
}
```

> `$appends` يضيف الـ Accessor تلقائياً عند تحويل النموذج إلى JSON/Array، مما يسمح لـ Filament بعرضه.

#### ٣.١.٦ `Invoice.php` — الفاتورة (مع حساب الإجماليات)

```php
public function calculateTotals(): void
{
    $subtotal = $this->items->sum('total');
    $this->amount = $subtotal;
    $this->vat_amount = round($subtotal * ($this->vat_rate / 100), 2);
    $this->total_with_vat = $subtotal + $this->vat_amount;
    $this->saveQuietly(); // حفظ بدون إطلاق أحداث
}
```

> `saveQuietly()` يحفظ بدون إطلاق Model Events لتفادي الحلقات اللانهائية.

#### ٣.١.٧ `Payroll.php` — مسيرة الراتب (مع معادلة الحساب)

```php
public static function calculateNetSalary(
    float $basic, float $bonuses, float $deductions,
    float $housing = 0, float $transport = 0, float $phone = 0,
    float $insuranceRate = 0, float $absenceDeduction = 0
): float {
    $allowances = $housing + $transport + $phone;
    $insuranceAmount = $basic * ($insuranceRate / 100);
    return max(0, $basic + $bonuses + $allowances
        - $deductions - $insuranceAmount - $absenceDeduction);
}
```

> **معادلة صافي الراتب:**
> `صافي = أساسي + مكافآت + (سكن + نقل + هاتف) − خصومات − تأمينات − خصم غياب`

#### ٣.١.٨ `Attendance.php` — الحضور (مع حسابات ذكية)

```php
public static function calculateHoursWorked($checkIn, $checkOut): float
{
    if (!$checkIn || !$checkOut) return 0;
    $checkIn = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
    $checkOut = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);
    return round($checkIn->diffInMinutes($checkOut) / 60, 2);
}

public function getIsLateAttribute(): bool
{
    if (!$this->check_in) return false;
    return $this->check_in->format('H:i:s') > '09:15:00'; // التأخير بعد 09:15
}
```

#### ٣.١.٩ باقي النماذج (مختصر)

| النموذج | الوصف | علاقات مميزة |
|---------|------|--------------|
| `Role` | الأدوار | `users()` HasMany |
| `Client` | العملاء | `canAccessPanel()` للوحة العميل، Auth Guard مستقل |
| `Department` | الأقسام | `head_id` ← مرجعية ذاتية لـ `employees` |
| `Vacancy` | الوظائف الشاغرة | `applicants()`, `pendingApplicants()` scopes |
| `Resume` | السير الذاتية | أعمدة AI (ai_score, ai_report, ...) |
| `Skill` | المهارات | علاقة M:N عبر `employee_skill` |
| `Report` | التقارير | sender_id + receiver_id ← `employees` (Self-ref) |
| `Certificate` | الشهادات | BelongsTo Employee |
| `CareerPlan` | خطط التطوير | BelongsTo Employee |
| `Training` | الدورات | BelongsToMany Employee (Pivot غني) |
| `TaskComment` | تعليقات المهام | BelongsTo Task + User |
| `TaskAttachment` | مرفقات المهام | BelongsTo Task + User |
| `TimeEntry` | تتبع الوقت | BelongsTo Task + Employee |
| `InvoiceItem` | بنود الفواتير | BelongsTo Invoice |
| `Expense` | المصروفات | BelongsTo User + Project + approved_by |
| `ProjectTemplate` | قوالب المشاريع | HasMany TaskTemplate |
| `TaskTemplate` | قوالب المهام | BelongsTo ProjectTemplate |

---

### ٣.٢ الخدمات (Services) — `app/Services/` (7 خدمات)

طبقة الخدمات تفصل منطق الأعمال المعقد عن وحدات التحكم والموارد.

#### ٣.٢.١ `AiService.php` — خدمة الذكاء الاصطناعي الأساسية

```php
class AiService
{
    public function chat(array $messages): ?array
    {
        $url = config('ai.api_url');
        $key = config('ai.api_key');
        $model = config('ai.model');

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

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('AI Service Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('AI Service Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
```

**معالجة الملفات متعددة الوسائط:**

```php
public function processUploadedFile($file): ?array
{
    $mimeType = $file->getMimeType();
    $extension = strtolower($file->getClientOriginalExtension());
    $path = $file->getRealPath();

    $mimeType = $this->resolveMimeType($extension, $mimeType);

    // الصور والصوت والفيديو → Base64
    if (str_starts_with($mimeType, 'image/')
        || str_starts_with($mimeType, 'audio/')
        || str_starts_with($mimeType, 'video/')) {
        $base64 = base64_encode(file_get_contents($path));
        return [
            'type' => 'image_url',
            'image_url' => ['url' => "data:{$mimeType};base64,{$base64}"]
        ];
    }

    // الملفات النصية → محتوى مباشر
    $textTypes = ['txt', 'json', 'xml', 'md', 'csv', 'js', 'yaml', 'php', 'html', 'css'];
    if (str_starts_with($mimeType, 'text/') || in_array($extension, $textTypes)) {
        $content = file_get_contents($path);
        return [
            'type' => 'text',
            'text' => "File Name: {$file->getClientOriginalName()}\n\nContent:\n{$content}"
        ];
    }

    return null; // غير مدعوم
}
```

#### ٣.٢.٢ `AiEvaluationService.php` — تقييم أداء الموظف

```php
class AiEvaluationService
{
    public function evaluate(Employee $employee): string
    {
        $data = $this->gatherEmployeeData($employee);
        $prompt = $this->buildPrompt($data);

        $response = app(AiService::class)->chat([
            ['role' => 'system', 'content' =>
                "أنت مدير موارد بشرية خبير ومتخصص في تقييم الأداء الوظيفي. "
                . "أجب باللغة العربية بتنسيق Markdown مفصل ومنظم.\n\n"
                . "يجب أن تكون الاستجابة بالشكل التالي:\n\n"
                . "## 📊 التقييم العام\n## 📈 تحليل الأداء المهامي\n"
                . "## 💪 نقاط القوة\n## ⚠️ نقاط تحتاج لتحسين\n"
                . "## 📋 تحليل الحضور والإجازات\n## 📝 تحليل التواصل والتقارير\n"
                . "## 🎯 التوصيات وخطة التحسين\n## 📌 الخلاصة"],
            ['role' => 'user', 'content' => $prompt],
        ]);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }
        return 'لم يتم تلقي استجابة صحيحة من الذكاء الاصطناعي.';
    }
```

**جمع البيانات (15 مؤشر):**

```php
protected function gatherEmployeeData(Employee $employee): array
{
    $employee->load(['tasks', 'sentReports', 'receivedReports', 'leaves', 'user']);

    $tasks = $employee->tasks;
    $totalTasks = $tasks->count();
    $doneTasks = $tasks->where('status', 'done')->count();
    $overdueTasks = $tasks->filter(fn ($t) =>
        $t->due_date && $t->due_date < now() && $t->status !== 'done')->count();
    $inProgressTasks = $tasks->where('status', 'in_progress')->count();

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
        'task_completion_rate' => $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100, 1) : 0,
        'total_leaves' => $employee->leaves->count(),
        'approved_leaves' => $employee->leaves->where('status', 'approved_by_hr')->count(),
        'sent_reports' => $employee->sentReports->count(),
        'replied_reports' => $employee->sentReports->where('status', 'replied')->count(),
    ];
}
```

#### ٣.٢.٣ `ResumeAnalysisService.php` — تحليل السير الذاتية

```php
public function analyzeResume(array $resumeData, string $keywords,
    string $targetJob = ''): ?array
{
    set_time_limit(180); // تمديد مهلة التنفيذ

    $messages = [
        ['role' => 'system', 'content' =>
            'أنت خبير في الموارد البشرية وتحليل السير الذاتية. '
            . 'أجب دائماً باللغة العربية. '
            . 'أجب بصيغة JSON فقط بدون أي نص إضافي.'],
        ['role' => 'user', 'content' => <<<PROMPT
قم بتحليل السيرة الذاتية التالية وتقييم مدى مطابقة المتقدم لشغل الوظيفة المستهدفة.

## بيانات المتقدم:
- الاسم: {$employeeName}
- المسمى الوظيفي المستهدف: {$targetJob}
...

أجب بصيغة JSON التالية فقط:
{
    "score": 85,
    "report": "التقرير المفصل هنا",
    "strengths": ["نقطة قوة 1", "نقطة قوة 2"],
    "weaknesses": ["نقطة ضعف 1"],
    "recommendation": "مقبول",
    "summary": "ملخص تنفيذي"
}
PROMPT],
    ];

    $response = $this->aiService->chat($messages);
    // ... تنظيف وفك ترميز JSON
    $content = preg_replace('/```json\s*|\s*```/', '', $content);
    $decoded = json_decode($content, true);
    // ... معالجة الأخطاء
}
```

#### ٣.٢.٤ `ResumeParserService.php` — استخراج نص السير الذاتية

```php
class ResumeParserService
{
    public function parse(string $absolutePath, string $mimeType = ''): string
    {
        if (!file_exists($absolutePath)) {
            Log::error("ResumeParserService: File not found at {$absolutePath}");
            return '';
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if ($extension === 'pdf' || str_contains($mimeType, 'pdf')) {
            return $this->parsePdf($absolutePath);
        } elseif ($extension === 'docx'
            || str_contains($mimeType, 'wordprocessingml.document')) {
            return $this->parseDocx($absolutePath);
        }
        return '';
    }

    private function parsePdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return trim($pdf->getText());
    }

    private function parseDocx(string $path): string
    {
        $zip = new ZipArchive();
        $text = '';
        if ($zip->open($path) === true) {
            // قراءة word/document.xml من داخل الـ ZIP
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $xmlData = $zip->getFromIndex($index);
                $zip->close();
                // تحويل وسوم الفقرات إلى أسطر جديدة
                $xmlData = str_replace(['</w:p>', '</w:br>'], "\n", $xmlData);
                $text = strip_tags($xmlData);
                $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            }
        }
        return trim($text);
    }
}
```

#### ٣.٢.٥ `InvoicePdfService.php` — توليد فواتير PDF

```php
class InvoicePdfService
{
    public function generate(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'items']);

        $arabic = new \Arphp\Glyphs();

        // معالجة الحروف العربية لـ PDF
        $invoice->client->name = $arabic->utf8Glyphs($invoice->client->name);
        $invoice->client->company_name = $invoice->client->company_name
            ? $arabic->utf8Glyphs($invoice->client->company_name) : null;

        foreach ($invoice->items as $item) {
            $item->description = $arabic->utf8Glyphs($item->description);
        }

        $pdf = Pdf::loadView('pdf.invoice', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'invoice_' . $invoice->invoice_number . '.pdf');
    }
}
```

#### ٣.٢.٦ `PayslipPdfService.php` — توليد قسائم الرواتب PDF

يولّد قسيمة راتب PDF مع **رسالة تحفيزية مولّدة بالذكاء الاصطناعي**:

```php
$aiNote = null;
try {
    $aiService = app(AiService::class);
    $prompt = "Write a very short (one sentence) thank you note in English "
        . "to an employee named {$payroll->employee->user->name} "
        . "for receiving their salary for the month of {$payroll->month_year}. "
        . "Be friendly and motivational.";

    $response = $aiService->chat([
        ['role' => 'system', 'content' =>
            'You are a kind HR manager who writes very short thank you notes.'],
        ['role' => 'user', 'content' => $prompt]
    ]);

    if ($response && isset($response['choices'][0]['message']['content'])) {
        $aiNote = $response['choices'][0]['message']['content'];
    }
} catch (\Exception $e) {
    // فشل صامت — لا يتعطل توليد الـ PDF
}
```

#### ٣.٢.٧ `DashboardExportService.php` — تصدير لوحة المعلومات PDF

يجمع إحصائيات شاملة (KPIs، بيانات شهرية لـ 12 شهراً، نشاطات، إحصائيات موظفين) ويولّد PDF بتنسيق A4 أفقي.

---

### ٣.٣ وحدات التحكم (Controllers) — `app/Http/Controllers/` (3)

#### ٣.٣.١ `LandingController.php` — الصفحة الرئيسية

```php
class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'employees' => Employee::where('status', 'active')->count(),
            'projects' => Project::count(),
            'tasks_completed' => Task::where('status', 'done')->count(),
            'clients' => Client::count(),
        ];

        $vacancies = Vacancy::withCount('applicants')
            ->where('status', 'open')
            ->latest()
            ->take(3)
            ->get();

        return view('landing', compact('stats', 'vacancies'));
    }

    public function vacancies()
    {
        $vacancies = Vacancy::withCount('applicants')
            ->where('status', 'open')
            ->latest()
            ->paginate(12);

        return view('vacancies', compact('vacancies'));
    }
}
```

#### ٣.٣.٢ `JobApplicationController.php` — التقديم على الوظائف

يدير دورة حياة طلب التوظيف:

```php
public function store(Request $request)
{
    $request->validate([
        'vacancy_id' => 'required|exists:vacancies,id',
        'expected_salary' => 'required|numeric|min:0',
        'resume_file' => 'required|mimes:pdf,doc,docx|max:2048',
        'resume_text' => 'nullable|string',
    ]);

    // 1. إنشاء سجل الموظف بحالة pending
    $employee = Employee::create([
        'user_id' => $user->id,
        'vacancy_id' => $vacancy->id,
        'job_title' => $vacancy->title,
        'salary' => $request->expected_salary,
        'status' => 'pending',
    ]);

    // 2. رفع السيرة الذاتية واستخراج النص
    $file = $request->file('resume_file');
    $path = $file->store('resumes', 'public');

    $parser = new ResumeParserService();
    $fullPath = storage_path('app/public/' . $path);
    $extractedText = $parser->parse($fullPath, $file->getClientMimeType());

    // 3. حفظ السيرة الذاتية
    Resume::create([
        'employee_id' => $employee->id,
        'file_path' => $path,
        'resume_text' => $resumeText,
    ]);

    return redirect()->route('job.apply')
        ->with('success', 'تم تقديم طلبك بنجاح!');
}
```

#### ٣.٣.٣ `LogoutResponse.php` — تخصيص صفحة الخروج

```php
class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('login'); // توجيه لتسجيل الدخول بدلاً من الصفحة الافتراضية
    }
}
```

---

### ٣.٤ مكوّنات Livewire — `app/Livewire/Auth/` (4)

#### ٣.٤.١ `Login.php` — تسجيل الدخول المركزي

```php
class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // محاولة الدخول كمستخدم نظام
        if (Auth::attempt(['email' => $this->email,
            'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $roleName = Auth::user()->role->name ?? null;

            // التحقق من الموافقة الإدارية
            if (! Auth::user()->is_approved && $roleName !== 'employee') {
                Auth::logout();
                $this->addError('email',
                    'حسابك في انتظار موافقة الإدارة.');
                return;
            }

            // التوجيه الذكي حسب الدور
            $defaultPath = match ($roleName) {
                'super_admin' => '/admin',
                'hr_manager' => '/hr',
                'project_manager' => '/pm',
                'accountant' => '/accountant',
                'employee' => '/employee',
                default => null,
            };

            return redirect($defaultPath);
        }

        // محاولة الدخول كعميل (Guard منفصل)
        if (Auth::guard('client')->attempt([...])) {
            return redirect('/client');
        }

        $this->addError('email', 'بيانات الدخول غير صحيحة.');
    }
}
```

> **نقطة تصميمية مهمة:** الدخول مركزي — صفحة `/login` واحدة تخدم **جميع الأدوار** (الموظفين والعملاء)، مع توجيه ذكي تلقائي.

#### ٣.٤.٢ `Register.php` — تسجيل حساب موظف جديد

```php
public function register()
{
    $validated = $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $role = Role::where('name', 'employee')->first();

    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
        'role_id' => $role->id,
        'is_approved' => true, // الموظفون معتمدون تلقائياً للتقديم
    ]);

    Auth::login($user);
    return redirect()->route('job.apply'); // توجيه لصفحة التقديم
}
```

#### ٣.٤.٣ `ForgotPassword.php` و `ResetPassword.php`

يستخدمان **Laravel Password Broker** لإرسال روابط استعادة كلمة المرور وإعادة تعيينها:

```php
// ForgotPassword
$status = Password::sendResetLink(['email' => $this->email]);

// ResetPassword
$status = Password::reset([...], function ($user, $password) {
    $user->password = Hash::make($password);
    $user->setRememberToken(Str::random(60));
    $user->save();
    event(new PasswordResetEvent($user));
});
```

---

### ٣.٥ الإشعارات (Notifications) — `app/Notifications/` (4)

جميع الإشعارات تستخدم قناة `database` فقط (تُخزّن في جدول `notifications`):

| الإشعار | المُطلِق | المُستلِم | المحتوى |
|---------|---------|----------|--------|
| `TaskAssignedNotification` | إنشاء مهمة | الموظف المسؤول | عنوان المهمة + اسم المشروع |
| `LeaveStatusNotification` | تحديث إجازة | صاحب الطلب | الحالة الجديدة (موافقة/رفض) |
| `ReportReceivedNotification` | إرسال تقرير | المستلِم | اسم المرسل + عنوان التقرير |
| `JobApplicationStatusNotification` | قبول/رفض متقدم | المتقدم | القبول أو الرفض |

> **ملاحظة:** النماذج تستخدم `Filament\Notifications\Notification` مباشرة في أحداث `booted()`، بينما تستخدم بعض الأماكن فئات الإشعارات المنفصلة.

---

### ٣.٦ Filament — البنية الكاملة

```
app/Filament/
├── Accountant/     # 5 Resources + 2 Pages + 8 Widgets
├── Client/         # 3 Resources + 2 Pages + 3 Widgets
├── Concerns/       # 1 Trait (HandlesPersonalReports)
├── Employee/       # 3 Resources + 3 Pages + 7 Widgets
├── Hr/             # 11 Resources + 2 Pages
├── Pages/          # 3 Pages (AdminDashboard, AdminProfile, TasksKanbanBoard)
├── Pm/             # 4 Resources + 5 Pages
├── Resources/      # 17 Resources للمدير العام
└── Widgets/        # 6 Widgets للمدير العام
```

#### ٣.٦.١ `AdminPanelProvider.php` (وغيره من المزودات)

كل لوحة لها `PanelProvider` مستقل يعرّف:
- **المعرّف والمسار:** `->id('admin')`, `->path('admin')`
- **اللون:** `->colors(['primary' => Color::Amber])`
- **الميزات:** `->darkMode(true)`, `->spa()`, `->databaseNotifications()`
- **الخط:** `->font('Cairo')`
- **اكتشاف الموارد:** `->discoverResources(in: app_path('Filament/Resources'), ...)`

#### ٣.٦.٢ `TasksKanbanBoard.php` — لوحة كانبان

صفحة مخصصة تعرض المهام في 4 أعمدة (todo, in_progress, review, done) مع:
- **سحب وإفلات** لتحديث الحالة
- **إعادة ترتيب** `sort_order` تلقائياً
- **تصفية** بالمشروع والموظف

```php
public function updateTaskStatus(int $taskId, string $newStatus, int $targetIndex = 0): void
{
    $task = Task::find($taskId);
    if (!$task || !in_array($newStatus, ['todo', 'in_progress', 'review', 'done'])) return;

    $oldStatus = $task->status;
    $task->update(['status' => $newStatus]);

    // إعادة ترتيب المهام في العمود الجديد
    $siblings = Task::where('status', $newStatus)
        ->where('id', '!=', $taskId)
        ->orderBy('sort_order', 'asc')
        ->get();

    $ordered = $siblings->slice(0, $targetIndex)
        ->push($task)
        ->concat($siblings->slice($targetIndex));

    $ordered->values()->each(function ($t, $i) {
        if ($t->sort_order !== $i) {
            Task::where('id', $t->id)->update(['sort_order' => $i]);
        }
    });
}
```

#### ٣.٦.٣ `HandlesPersonalReports.php` — Trait مشترك

يُستخدم في تقارير PM و Employee و Accountant لتوفير منطق موحّد:

```php
trait HandlesPersonalReports
{
    public static function personalEmployeeId(): ?int
    {
        return Employee::where('user_id', auth()->id())->value('id');
    }

    public static function allowedReceiversQuery(): Builder
    {
        $employeeId = self::personalEmployeeId();
        return Employee::with('user')
            ->whereKeyNot($employeeId)
            ->whereHas('user', function ($query) {
                $query->where('is_approved', true)
                      ->whereHas('role', function ($roleQuery) {
                          $roleQuery->whereNotIn('name',
                              ['super_admin', 'hr_manager', 'accountant']);
                      });
            });
    }

    public static function getEloquentQuery(): Builder
    {
        $employeeId = self::personalEmployeeId();
        $user = auth()->user();

        // super_admin يرى كل التقارير
        if ($user && $user->role && $user->role->name === 'super_admin') {
            return parent::getEloquentQuery();
        }

        // باقي الأدوار يرون تقاريرهم فقط
        return parent::getEloquentQuery()->where(function (Builder $query) use ($employeeId) {
            $query->where('sender_id', $employeeId)
                ->orWhere('receiver_id', $employeeId);
        });
    }
}
```

> **عزل البيانات:** كل مستخدم يرى فقط التقارير التي أرسلها أو استلمها، ما عدا المدير العام.

---

### ٣.٧ Providers — `app/Providers/`

#### `AppServiceProvider.php`

```php
public function register(): void
{
    // تخصيص استجابة الخروج
    $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
}

public function boot(): void
{
    // تكوين مبدّل اللغة
    LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        $switch
            ->locales(['ar', 'en'])
            ->visible(outsidePanels: true)
            ->outsidePanelRoutes([
                'filament.admin.auth.login',
                'filament.hr.auth.login',
                // ... جميع مسارات الدخول
            ])
            ->labels(['ar' => 'العربية', 'en' => 'English']);
    });

    // توجيهات Blade مخصصة
    Blade::directive('dir', function () {
        return "<?php echo app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; ?>";
    });

    Blade::directive('ar', function ($expression) {
        return "<?php echo e(\App\Support\Arabic::shape((string) (\$expression))); ?>";
    });
}
```

### ٣.٨ `Support/Arabic.php` — أداة معالجة الحروف العربية

```php
class Arabic
{
    protected static ?Glyphs $instance = null;

    public static function glyph(): Glyphs
    {
        return static::$instance ??= new Glyphs(); // Singleton
    }

    public static function shape(string $text): string
    {
        if ($text === '') return '';
        return static::glyph()->utf8Glyphs($text);
    }
}
```

> Singleton Pattern لتفادي إنشاء كائن جديد في كل مرة. يستخدم في توجيه Blade `@ar()` لمعالجة النصوص العربية في PDF.

---

## ٤. مجلد `resources/` — تفصيل كامل

### ٤.١ `resources/views/` — قوالب Blade

```
resources/views/
├── application/           # صفحات التقديم على الوظائف
│   ├── form.blade.php     # نموذج التقديم (اختيار الوظيفة + رفع السيرة)
│   └── status.blade.php   # حالة الطلب (pending/active/rejected)
├── components/
│   └── layouts/
│       └── app.blade.php  # ★ تخطيط صفحات المصادقة (Login/Register/Password)
├── filament/
│   ├── pages/             # 9 صفحات مخصصة لـ Filament
│   │   ├── admin-tasks-kanban.blade.php
│   │   ├── ai-evaluation.blade.php       # عرض تقييم AI (Markdown)
│   │   ├── client-profile.blade.php
│   │   ├── employee-profile.blade.php
│   │   ├── gantt-chart.blade.php         # مخطط جانت (Frappe-Gantt)
│   │   ├── my-attendance.blade.php       # الحضور الشخصي
│   │   ├── my-tasks-kanban.blade.php
│   │   ├── tasks-kanban.blade.php
│   │   └── team-calendar.blade.php       # تقويم FullCalendar
│   ├── resources/
│   │   └── attendance-resource/          # صفحات الحضور
│   └── widgets/             # 5 واجهات مخصصة
│       ├── admin-activity-feed.blade.php
│       ├── admin-employee-heatmap.blade.php
│       ├── employee-calendar.blade.php
│       ├── employee-progress-ring.blade.php
│       └── tax-report.blade.php
├── livewire/auth/           # قوالب مصادقة Livewire
│   ├── forgot-password.blade.php
│   ├── login.blade.php
│   ├── register.blade.php
│   └── reset-password.blade.php
├── pdf/                     # قوالب PDF
│   ├── dashboard-report.blade.php    # تقرير لوحة المعلومات (A4 أفقي)
│   ├── invoice.blade.php             # فاتورة احترافية
│   └── payslip.blade.php             # قسيمة راتب
├── landing.blade.php        # ★ الصفحة الرئيسية للموقع (464 سطر)
├── vacancies.blade.php      # صفحة الوظائف الشاغرة
└── welcome.blade.php        # صفحة الترحيب الافتراضية
```

#### ٤.١.١ `landing.blade.php` — الصفحة الرئيسية (464 سطر)

صفحة احترافية كاملة تحتوي على:

| القسم | الوصف |
|------|------|
| **Hero Section** | عنوان كبير، شعار "نظام إدارة مركزي متكامل ذكي"، زرّان للبدء والاكتشاف |
| **Quick Stats** | 4 بطاقات إحصائية حية (موظفين، مشاريع، مهام منجزة، عملاء) |
| **About Section** | نبذة عن النظام مع 4 بطاقات ميزات (أداء، أمان، تقارير، فريق) |
| **Services Section** | 3 بطاقات للوحدات الرئيسية (HR، PM، Finance) |
| **Vacancies Section** | عرض آخر 3 وظائف شاغرة مع نوع التوظيف وعدد المتقدمين |
| **Contact Section** | نموذج تواصل + معلومات الشركة |
| **Footer** | حقوق النشر + روابط اجتماعية |

**التقنيات المستخدمة:**
- **Tailwind CSS (CDN)** مع تكوين مخصص (ألوان primary، حركات float)
- **Alpine.js** للقوائم التفاعلية (mobile menu, scroll detection)
- **Dark Mode Toggle** عبر `localStorage`
- **Glass Morphism** (`.glass` class مع `backdrop-filter: blur`)
- **خلفيات متوهجة** (Blur Orbs بـ `blur-[120px]`)
- **دعم RTL/LTR** كامل عبر `$isAr`

#### ٤.١.٢ `components/layouts/app.blade.php` — تخطيط المصادقة (353 سطر)

تخطيط احترافي لصفحات تسجيل الدخول والتسجيل يتميز بـ:
- **خلفية متدرّجة** (gradient) مختلفة للوضع النهاري والليلي
- **كرات متوهجة متحركة** (`@keyframes floatOrb`)
- **بطاقة زجاجية** (`backdrop-filter: blur(28px)`)
- **حركات دخول** (`cardFadeIn 0.7s cubic-bezier`)
- **زر تبديل اللغة والوضع الليلي** في الزاوية

```css
:root {
    --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 30%, #cbd5e1 60%, #f1f5f9 100%);
    --card-bg: rgba(255, 255, 255, 0.9);
    --card-shadow: 0 32px 80px rgba(0,0,0,0.1), 0 0 120px rgba(99,102,241,0.04);
}

.dark {
    --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 30%, #312e81 60%, #1e293b 100%);
    --card-bg: rgba(255, 255, 255, 0.06);
}
```

> استخدام **CSS Custom Properties** (`:root` + `.dark`) لتبديل سلس بين الوضعين.

#### ٤.١.٣ قوالب PDF (`pdf/`)

3 قوالب احترافية لتوليد المستندات عبر DomPDF:

| القالب | الوصف |
|--------|------|
| `invoice.blade.php` | فاتورة كاملة (بيانات العميل، بنود الفاتورة، الضريبة، الإجمالي) |
| `payslip.blade.php` | قسيمة راتب (تفصيل الراتب، البدلات، الخصومات، صافي الراتب، رسالة AI) |
| `dashboard-report.blade.php` | تقرير لوحة المعلومات (KPIs، بيانات شهرية، نشاطات) |

> جميع القوالب تستخدم `@ar` directive لمعالجة الحروف العربية في PDF.

#### ٤.١.٤ `ai-evaluation.blade.php` — عرض تقييم الذكاء الاصطناعي

```blade
{!! Illuminate\Support\Str::markdown($evaluation) !!}
```

> يحوّل نص Markdown المُولّد من الذكاء الاصطناعي إلى HTML منسّق.

---

## ٥. مجلد `routes/` — تفصيل كامل

### ٥.١ `routes/web.php`

```php
// الصفحات العامة
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/vacancies', [LandingController::class, 'vacancies'])->name('vacancies.index');

// تبديل اللغة
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session()->put('locale', $locale);
        cookie()->queue(cookie()->forever('filament_language_switch_locale', $locale));
    }
    return redirect()->back();
})->name('switch-language');

// المصادقة
Route::get('/forgot-password', ForgotPassword::class)->name('password.request')->middleware('guest');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset')->middleware('guest');
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');

// تسجيل الخروج
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// التقديم على الوظائف (يتطلب مصادقة)
Route::middleware('auth')->group(function () {
    Route::get('/apply', [JobApplicationController::class, 'index'])->name('job.apply');
    Route::post('/apply', [JobApplicationController::class, 'store'])->name('job.store');
});
```

**تحليل المسارات:**

| المسار | الطريقة | المُحقّق | الحماية | الوصف |
|--------|--------|---------|--------|------|
| `/` | GET | `LandingController@index` | عام | الصفحة الرئيسية |
| `/vacancies` | GET | `LandingController@vacancies` | عام | قائمة الوظائف |
| `/lang/{locale}` | GET | Closure | عام | تبديل اللغة |
| `/login` | GET | `Login` (Livewire) | guest | تسجيل الدخول |
| `/register` | GET | `Register` (Livewire) | guest | التسجيل |
| `/forgot-password` | GET | `ForgotPassword` | guest | نسيان كلمة المرور |
| `/reset-password/{token}` | GET | `ResetPassword` | guest | إعادة التعيين |
| `/logout` | POST | Closure | عام | تسجيل الخروج |
| `/apply` | GET/POST | `JobApplicationController` | auth | التقديم للوظائف |

> **ملاحظة:** مسارات Filament (`/admin`, `/hr`, `/pm`, `/accountant`, `/employee`, `/client`) مُسجّلة تلقائياً عبر `PanelProvider` وليست في `web.php`.

### ٥.٢ `routes/console.php`

```php
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

> المسار الافتراضي فقط — لا توجد أوامر مخصصة (Artisan Commands) في المشروع.

---

## ٦. مجلد `config/` — تفصيل كامل

| الملف | الوصف |
|------|------|
| `app.php` | إعدادات التطبيق العامة (الاسم، البيئة، اللغة الافتراضية 'ar') |
| `auth.php` | إعدادات المصادقة (Guards: web, client؛ Providers: users, clients) |
| `ai.php` | ★ إعدادات الذكاء الاصطناعي |
| `database.php` | إعدادات قاعدة البيانات (MySQL/SQLite) |
| `filesystems.php` | أنظمة الملفات (local, public, s3) |
| `cache.php` | نظام التخزين المؤقت |
| `queue.php` | نظام الطوابير |
| `session.php` | إعدادات الجلسات |
| `mail.php` | إعدادات البريد |
| `logging.php` | إعدادات تسجيل الأخطاء |
| `services.php` | خدمات الطرف الثالث |

### `config/ai.php` — إعدادات الذكاء الاصطناعي

```php
return [
    'api_url'     => env('AI_API_URL',
        'https://api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions'),
    'api_key'     => env('AI_API_KEY'),
    'model'       => env('AI_MODEL', 'gemini-3-flash-preview'),
    'max_tokens'  => env('AI_MAX_TOKENS', 4096),
    'temperature' => env('AI_TEMPERATURE', 0.7),
];
```

---

## ٧. مجلد `database/` — تفصيل كامل

### ٧.١ `database/migrations/` — 49 ملف هجرة

تم تحليلها بالكامل في ملف `DBReport.md`. هنا ملخص البنية:

| الفئة | عدد الجداول | أبرز الجداول |
|------|------------|--------------|
| المستخدمون | 3 | roles, users, clients |
| الموارد البشرية | 7 | employees, departments, resumes, skills, certificates, career_plans |
| التوظيف | 1 | vacancies |
| المشاريع | 8 | projects, tasks, task_comments, task_attachments, time_entries, templates |
| المالية | 4 | invoices, invoice_items, expenses, payrolls |
| الإجازات والحضور | 2 | leaves, attendances |
| التدريب | 2 | trainings, employee_training |
| التواصل | 2 | reports, notifications |
| نظامية | 7 | cache, jobs, sessions, password_reset_tokens |

### ٧.٢ `database/seeders/DatabaseSeeder.php` — بذور البيانات

ملف ضخم (~950+ سطر) يولّد بيانات تجريبية واقعية:

1. **الأدوار** (5): super_admin, hr_manager, project_manager, accountant, employee
2. **المهارات** (30): PHP, Laravel, Vue.js, React, Docker, ...
3. **الأقسام** (8): تطوير الويب، تطبيقات الجوال، HR، المالية، ...
4. **المستخدمون والموظفون** (11): حسابات حقيقية بأسماء عربية
5. **الوظائف الشاغرة** (8): مطور Backend، Frontend، Flutter، DevOps، ...
6. **المتقدمون** (12): طلبات بحالات متنوعة (pending)
7. **العملاء** (5): شركات بأسماء واقعية
8. **قوالب المشاريع** (3): CMS، تطبيق جوال، لوحة تحليلية
9. **المشاريع** (5): ERP، تطبيق توصيل، منصة تعليم، ...
10. **المهام** (4-7 لكل مشروع): مع تعليقات ومرفقات وتتبع وقت
11. **الفواتير** (2 لكل مشروع): مع بنود وضريبة 15%
12. **المصروفات** (10): بفئات وحالات متنوعة
13. **التقارير** (5): بين موظفين مختلفين
14. **الإجازات** (12): بأنواع وحالات متنوعة
15. **الرواتب** (2 شهر لكل موظف): مع بدلات وتأمينات
16. **الحضور** (20 يوم لكل موظف): مع حالات متنوعة
17. **التدريبات** (5): بحالات متنوعة
18. **الشهادات** و **خطط التطوير**

> يستخدم `Faker::create('ar_SY')` لتوليد بيانات عربية واقعية.

---

## ٨. مجلد `lang/` — الترجمة

```
lang/
├── ar/
│   ├── filament.php      # 893 سطر — جميع نصوص النظام بالعربية
│   └── validation.php    # رسائل التحقق العربية
└── en/
    ├── filament.php      # النسخة الإنجليزية
    └── validation.php
```

### بنية `lang/ar/filament.php`

```php
return [
    'brand' => [           // أسماء اللوحات
        'admin' => 'لوحة المدير العام',
        'pm' => 'إدارة المشاريع',
        'hr' => 'الموارد البشرية',
        // ...
    ],
    'nav' => [...],        // عناصر القائمة الجانبية
    'model' => [...],      // أسماء النماذج (مفرد/جمع)
    'sections' => [...],   // عناوين الأقسام في النماذج
    'fields' => [...],     // تسميات الحقول
    'columns' => [...],    // عناوين أعمدة الجداول
    'status' => [...],     // ترجمات الحالات
    'filters' => [...],    // تسميات الفلاتر
    'actions' => [...],    // تسميات الأزرار
    'widgets' => [...],    // عناوين الواجهات الإحصائية
    'notifications' => [...], // رسائل الإشعارات
    'attendance' => [...], // نصوص الحضور
    'reports' => [...],    // نصوص التقارير
    // ... المزيد
];
```

---

## ٩. ملخص البنية المعمارية

### ٩.١ أنماط التصميم المستخدمة

| النمط | الموقع | الوصف |
|------|--------|------|
| **MVC** | المشروع كاملاً | Model-View-Controller |
| **Service Layer** | `app/Services/` | فصل منطق الأعمال |
| **Repository** | ضمنياً في Models | عبر Eloquent Scopes |
| **Singleton** | `Arabic::glyph()` | كائن واحد مشترك |
| **Factory** | `DatabaseSeeder` | توليد بيانات تجريبية |
| **Observer** | `Task::booted()`, `Leave::booted()` | أحداث النموذج |
| **Strategy** | `canAccessPanel()` | استراتيجيات وصول مختلفة |
| **Trait Reuse** | `HandlesPersonalReports` | إعادة استخدام الكود |

### ٩.٢ تدفق البيانات

```
المستخدم (Browser)
    │
    ▼
Livewire/Blade (View)  ←──→  Filament Resources
    │                              │
    ▼                              ▼
Controllers (app/Http)      Filament Pages/Widgets
    │                              │
    ▼                              ▼
Services (app/Services) ──────────┘
    │
    ├──► Models (app/Models) ──► Database
    │
    ├──► AiService ──► LiteLLM Proxy ──► Gemini AI
    │
    └──► Notifications ──► Database
```

### ٩.٣ الإحصائيات النهائية للمشروع

| المقياس | العدد |
|---------|------|
| **نماذج Eloquent** | 25 |
| **خدمات** | 7 |
| **متحكمات** | 3 |
| **مكوّنات Livewire** | 4 |
| **إشعارات** | 4 |
| **Filament Resources** | 40+ (موزعة على 6 لوحات) |
| **Filament Pages** | 15+ |
| **Filament Widgets** | 24+ |
| **ملفات الهجرة** | 49 |
| **جداول قاعدة البيانات** | 25 (أعمال) + 7 (نظامية) |
| **العلاقات (FK)** | 39 |
| **ملفات View (Blade)** | 20+ |
| **أسطر الترجمة العربية** | 893+ |
| **لوحات تحكم مستقلة** | 6 |
| **أدوار وظيفية** | 5 + عميل + متقدم |
| **إجمالي ملفات PHP** | 150+ |

---

*تم إعداد هذا التقرير بناءً على قراءة وتحليل كل ملف PHP و Blade في المشروع.*
