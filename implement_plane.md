# 📋 خطة تنفيذ مشروع ERP-Lite & Smart ATS
# Implementation Plan - ERP-Lite & Smart ATS System

---

## 📌 1. تحليل الوضع الحالي للمشروع (Current State Analysis)

### التقنيات المستخدمة
| التقنية | الإصدار |
|---------|---------|
| Laravel | 12 (PHP 8.2+) |
| FilamentPHP | v3.3 |
| Authentication | Livewire 3 + Alpine.js + TailwindCSS (مخصص) |
| Database | MySQL (`erp_lite_app`) |
| Queue/Cache/Session | Database Driver |

### البنية الحالية - اللوحات (Panels)
المشروع يحتوي على **5 لوحات Filament** مستقلة:

| اللوحة | المسار | اللون | الدور المطلوب |
|--------|--------|-------|---------------|
| Admin | `/admin` | Amber | `super_admin` |
| HR | `/hr` | Rose | `hr_manager` |
| PM | `/pm` | Blue | `project_manager` |
| Accountant | `/accountant` | Emerald | `accountant` |
| Employee | `/employee` | Indigo | `employee` |

### النماذج الموجودة (Models) - 10 نماذج
- `User` ← مرتبط بـ Role, Employee, Expenses
- `Role` ← الأدوار (super_admin, hr_manager, project_manager, accountant, employee)
- `Employee` ← مرتبط بـ User, Resume, Skills, Projects, Tasks
- `Resume` ← السيرة الذاتية (مرتبط بـ Employee)
- `Skill` ← المهارات (Many-to-Many مع Employee)
- `Client` ← العملاء (مرتبط بـ Projects, Invoices)
- `Project` ← المشاريع (مرتبط بـ Client, Employees, Tasks, Invoices)
- `Task` ← المهام (مرتبط بـ Project, Employee)
- `Invoice` ← الفواتير (مرتبط بـ Client, Project)
- `Expense` ← المصروفات (مرتبط بـ User)

### الموارد في كل لوحة (Filament Resources)

**Admin Panel:** UserResource, RoleResource, EmployeeResource, ClientResource, ProjectResource, TaskResource, InvoiceResource, ExpenseResource, SkillResource + Widgets (StatsOverview, ProjectsChart)

**HR Panel:** EmployeeResource (مع ATS workflow: قبول/رفض), ResumeResource, SkillResource

**PM Panel:** ProjectResource, TaskResource

**Accountant Panel:** ClientResource, InvoiceResource, ExpenseResource

**Employee Panel:** TaskResource

### ما تم إنجازه ✅
- [x] Multi-Panel Architecture (5 لوحات)
- [x] Centralized Auth (Login/Register مخصص بـ Livewire)
- [x] Role-based Access Control (canAccessPanel)
- [x] ATS Workflow (التسجيل = تقديم طلب توظيف → HR يقبل/يرفض)
- [x] CRUD كامل لجميع الموارد + RelationManagers + Widgets
- [x] Strict Financial Logic (unsignedDecimal + minValue)
- [x] Database Seeder بالعربية

### ⚠️ فجوات ملاحظة في الكود الحالي
1. **Employee Model** لا يحتوي على `department_id` في fillable
2. **لا يوجد جدول `departments`** حتى الآن
3. **لا يوجد جدول `leaves`** أو `payrolls` أو `reports`
4. **لا يوجد نظام إشعارات** (Notifications) مفعّل
5. **لا يوجد Kanban Board** للمهام
6. **لا يوجد تكامل AI** (لا OpenAI API key في .env)

---

## 🚀 2. الوحدات السبع المطلوبة (7 New Modules)

### الترتيب المقترح للتنفيذ (حسب التبعيات)

```
الوحدة 1: Departments & Heads ──→ أساس لكل الوحدات اللاحقة
    ↓
الوحدة 2: Internal Reports ──→ يعتمد على Departments
    ↓
الوحدة 3: Leaves & Attendance ──→ يعتمد على Departments + سلسلة الموافقات
    ↓
الوحدة 4: Notifications ──→ يُربط مع Reports + Leaves + Tasks
    ↓
الوحدة 5: Kanban Board ──→ تحسين عرض Tasks
    ↓
الوحدة 6: Payroll & Payslips ──→ يعتمد على Employees + Leaves
    ↓
الوحدة 7: AI Evaluation ──→ يعتمد على Tasks + Reports (آخر شيء)
```

---

## 📦 الوحدة 1: الأقسام ورؤساء الأقسام (Departments & Heads)

### الملفات المطلوبة

#### 1.1 Migration
**ملف جديد:** `database/migrations/xxxx_create_departments_table.php`
```
Schema: departments
- id (bigint, PK, auto_increment)
- name (varchar)
- head_id (bigint, nullable, FK → employees.id)
- timestamps
```

**ملف جديد:** `database/migrations/xxxx_add_department_id_to_employees.php`
```
- إضافة department_id (bigint, nullable, FK → departments.id) لجدول employees
```

#### 1.2 Model
**ملف جديد:** `app/Models/Department.php`
- العلاقات: `head()` → BelongsTo Employee, `employees()` → HasMany Employee

**تعديل:** `app/Models/Employee.php`
- إضافة `department_id` إلى `$fillable`
- إضافة علاقة `department()` → BelongsTo Department
- إضافة علاقة `headOfDepartment()` → HasOne Department

#### 1.3 Filament Resources
**ملفات جديدة:**
- `app/Filament/Resources/DepartmentResource.php` (Admin Panel)
- `app/Filament/Resources/DepartmentResource/Pages/` (List, Create, Edit)
- `app/Filament/Hr/Resources/DepartmentResource.php` (HR Panel)

**تعديل:** EmployeeResource (Admin + HR) → إضافة حقل department_id في Form + عمود القسم في Table

#### 1.4 Seeder
**تعديل:** `DatabaseSeeder.php` → إضافة بيانات أقسام (IT, HR, Finance, Marketing, Operations)

---

## 📦 الوحدة 2: نظام التقارير الداخلية (Internal Reports)

### الملفات المطلوبة

#### 2.1 Migration
**ملف جديد:** `database/migrations/xxxx_create_reports_table.php`
```
Schema: reports
- id (bigint, PK)
- sender_id (bigint, FK → employees.id)
- receiver_id (bigint, FK → employees.id)
- title (varchar)
- content (text)
- feedback (text, nullable)
- status (enum: unread, read, replied) default 'unread'
- timestamps
```

#### 2.2 Model
**ملف جديد:** `app/Models/Report.php`
- العلاقات: `sender()` → BelongsTo Employee, `receiver()` → BelongsTo Employee

**تعديل:** `app/Models/Employee.php`
- إضافة `sentReports()` → HasMany Report (sender_id)
- إضافة `receivedReports()` → HasMany Report (receiver_id)

#### 2.3 Filament Resources
**ملفات جديدة:**
- `app/Filament/Resources/ReportResource.php` (Admin - عرض جميع التقارير)
- `app/Filament/Hr/Resources/ReportResource.php` (HR - التقارير الواردة)
- `app/Filament/Employee/Resources/ReportResource.php` (Employee - إرسال تقارير)

**المميزات:**
- الموظف يرسل تقرير يومي/أسبوعي لرئيس القسم أو المدير العام
- رئيس القسم يستقبل التقارير ويرد عليها (feedback)
- حالات: غير مقروء ← مقروء ← تم الرد
- فلاتر: حسب الحالة، التاريخ، المرسل

---

## 📦 الوحدة 3: نظام الإجازات والمغادرات (Leaves & Attendance)

### الملفات المطلوبة

#### 3.1 Migration
**ملف جديد:** `database/migrations/xxxx_create_leaves_table.php`
```
Schema: leaves
- id (bigint, PK)
- employee_id (bigint, FK → employees.id)
- type (varchar: Sick, Annual, Emergency)
- start_date (date)
- end_date (date)
- reason (text)
- status (enum: pending, approved_by_head, approved_by_hr, rejected) default 'pending'
- timestamps
```

#### 3.2 Model
**ملف جديد:** `app/Models/Leave.php`
- العلاقات: `employee()` → BelongsTo Employee
- Accessors: `durationInDays()` لحساب عدد الأيام

**تعديل:** `app/Models/Employee.php`
- إضافة `leaves()` → HasMany Leave

#### 3.3 Filament Resources
**ملفات جديدة:**
- `app/Filament/Employee/Resources/LeaveResource.php` → تقديم طلب إجازة
- `app/Filament/Hr/Resources/LeaveResource.php` → موافقة نهائية HR
- `app/Filament/Resources/LeaveResource.php` → Admin يرى الكل

**سلسلة الموافقات (Workflow):**
```
Employee يقدم طلب (pending)
    → رئيس القسم يوافق (approved_by_head)
        → HR يوافق نهائياً (approved_by_hr)
أو → الرفض في أي مرحلة (rejected)
```

**الأزرار (Actions):**
- `approve_head` → يظهر لرئيس القسم فقط (عند pending)
- `approve_hr` → يظهر لـ HR فقط (عند approved_by_head)
- `reject` → يظهر لرئيس القسم و HR (مع حقل سبب الرفض)

---

## 📦 الوحدة 4: الإشعارات الفورية (Real-time Notifications)

### الملفات المطلوبة

#### 4.1 Migration
- جدول `notifications` موجود مسبقاً في Laravel (يجب تشغيل `php artisan notifications:table`)

#### 4.2 التعديلات
**تعديل:** `app/Models/User.php` → التأكد من وجود `use Notifiable` (✅ موجود)

**تعديل:** جميع الـ Panel Providers → إضافة `->databaseNotifications()` لتفعيل جرس الإشعارات

#### 4.3 إنشاء Notification Classes
**ملفات جديدة في** `app/Notifications/`:
- `TaskAssignedNotification.php` → عند تعيين مهمة جديدة
- `LeaveStatusNotification.php` → عند قبول/رفض إجازة
- `JobApplicationStatusNotification.php` → عند قبول/رفض طلب توظيف
- `ReportReceivedNotification.php` → عند استلام تقرير جديد

#### 4.4 إضافة Triggers
**تعديل الملفات التالية لإرسال الإشعارات:**
- `TaskResource` (جميع اللوحات) → عند إنشاء مهمة
- `LeaveResource` → عند تغيير حالة الإجازة
- `EmployeeResource` (HR) → عند قبول/رفض طلب التوظيف
- `ReportResource` → عند إرسال تقرير

**طريقة الإرسال:**
```php
Notification::make()
    ->title('تم تعيين مهمة جديدة لك')
    ->body('...')
    ->sendToDatabase($user);
```

---

## 📦 الوحدة 5: لوحة كانبان للمهام (Kanban Board)

### الملفات المطلوبة

#### 5.1 تثبيت الحزمة
```bash
composer require mokhosh/filament-kanban
```

#### 5.2 ملفات جديدة
**ملف جديد:** `app/Filament/Pm/Pages/TasksKanbanBoard.php`
```php
// صفحة Kanban مخصصة في لوحة PM
// الأعمدة: todo → in_progress → review → done
// السحب والإفلات لتغيير حالة المهمة
```

**ملف جديد:** `app/Filament/Employee/Pages/MyTasksKanban.php`
```php
// لوحة Kanban للموظف (مهامه فقط)
```

**ملف جديد:** `resources/views/filament/pages/tasks-kanban.blade.php` (إن لزم)

#### 5.3 التكامل
- أعمدة الكانبان تعكس `task_status` enum: `todo`, `in_progress`, `review`, `done`
- عند السحب والإفلات: يتم تحديث `status` في قاعدة البيانات
- يمكن إضافة فلتر حسب المشروع أو الموظف

---

## 📦 الوحدة 6: مسيرات الرواتب (Payroll & Payslips)

### الملفات المطلوبة

#### 6.1 Migration
**ملف جديد:** `database/migrations/xxxx_create_payrolls_table.php`
```
Schema: payrolls
- id (bigint, PK)
- employee_id (bigint, FK → employees.id)
- month_year (varchar, e.g. '2026-05')
- basic_salary (decimal 10,2, unsigned)
- bonuses (decimal 10,2, unsigned, default 0)
- deductions (decimal 10,2, unsigned, default 0)
- net_salary (decimal 10,2, unsigned)
- status (varchar: paid, unpaid) default 'unpaid'
- timestamps
```

#### 6.2 Model
**ملف جديد:** `app/Models/Payroll.php`
- العلاقات: `employee()` → BelongsTo Employee
- Mutator: حساب `net_salary = basic_salary + bonuses - deductions`

**تعديل:** `app/Models/Employee.php`
- إضافة `payrolls()` → HasMany Payroll

#### 6.3 Filament Resources
**ملف جديد:** `app/Filament/Accountant/Resources/PayrollResource.php`
- Form: اختيار الموظف، الشهر، الراتب الأساسي (يملأ تلقائياً من Employee.salary)، المكافآت، الخصومات
- حساب تلقائي لصافي الراتب
- Table: عرض جميع المسيرات مع فلاتر (الشهر، الموظف، الحالة)

**ملف جديد:** `app/Filament/Resources/PayrollResource.php` (Admin)

#### 6.4 توليد PDF
**ملف جديد:** `app/Services/PayslipPdfService.php`
- استخدام `barryvdh/laravel-dompdf` أو `spatie/laravel-pdf`
- تصميم قسيمة الراتب بتنسيق احترافي

```bash
composer require barryvdh/laravel-dompdf
```

**ملف جديد:** `resources/views/pdf/payslip.blade.php`

**Action في PayrollResource:**
```php
Tables\Actions\Action::make('download_payslip')
    ->label('تحميل قسيمة الراتب')
    ->icon('heroicon-o-document-arrow-down')
    ->action(fn (Payroll $record) => /* generate & download PDF */)
```

---

## 📦 الوحدة 7: تقييم الموظف بالذكاء الاصطناعي (AI Employee Evaluation)

### الملفات المطلوبة

#### 7.1 التكوين
**تعديل:** `.env` → إضافة `OPENAI_API_KEY=`
**ملف جديد:** `config/ai.php` → إعدادات API

#### 7.2 Service
**ملف جديد:** `app/Services/AiEvaluationService.php`
```php
// يجمع بيانات الموظف:
// 1. المهام المكتملة (done) والمتأخرة (overdue)
// 2. التقارير المرسلة وتقييمات الرؤساء
// 3. أيام الإجازة
// يبني prompt منظم ويرسله لـ OpenAI API
// يستقبل النتيجة ويعرضها
```

#### 7.3 Filament Action
**تعديل:** `app/Filament/Hr/Resources/EmployeeResource.php`
**تعديل:** `app/Filament/Resources/EmployeeResource.php` (Admin)

```php
Tables\Actions\Action::make('ai_evaluate')
    ->label('تقييم ذكاء اصطناعي')
    ->icon('heroicon-o-cpu-chip')
    ->color('info')
    ->modalContent(fn (Employee $record) => /* عرض التقييم في Modal */)
    ->visible(fn (Employee $record) => $record->status === 'active')
```

#### 7.4 تثبيت HTTP Client
```bash
composer require openai-php/laravel
```

---

## 🗂 3. ملخص الملفات الجديدة والمعدلة

### ملفات جديدة (New Files) ~35 ملف

| النوع | العدد | الملفات |
|-------|-------|---------|
| Migrations | 4 | departments, add_dept_to_employees, leaves, payrolls, reports |
| Models | 4 | Department, Report, Leave, Payroll |
| Filament Resources | ~12 | DepartmentResource, ReportResource, LeaveResource, PayrollResource (في عدة لوحات) |
| Filament Pages | ~6 | List/Create/Edit لكل Resource + Kanban Pages |
| Notifications | 4 | TaskAssigned, LeaveStatus, JobApplication, ReportReceived |
| Services | 2 | PayslipPdfService, AiEvaluationService |
| Views | 2 | payslip PDF, kanban blade |
| Config | 1 | ai.php |

### ملفات معدلة (Modified Files) ~12 ملف

| الملف | التعديل |
|-------|---------|
| `Employee.php` (Model) | إضافة department_id, علاقات جديدة (department, leaves, payrolls, reports) |
| `User.php` (Model) | لا تعديل (Notifiable موجود) |
| `DatabaseSeeder.php` | إضافة بيانات departments, reports, leaves, payrolls |
| `EmployeeResource.php` (Admin) | إضافة حقل القسم + AI Action |
| `EmployeeResource.php` (HR) | إضافة حقل القسم + AI Action |
| 5x PanelProviders | إضافة `->databaseNotifications()` |
| `.env` | إضافة OPENAI_API_KEY |

---

## 📦 4. الحزم المطلوب تثبيتها (Dependencies)

```bash
# 1. Kanban Board
composer require mokhosh/filament-kanban

# 2. PDF Generation
composer require barryvdh/laravel-dompdf

# 3. OpenAI Integration
composer require openai-php/laravel

# 4. Notifications Table
php artisan notifications:table
php artisan migrate
```

---

## ⏱ 5. تقدير الوقت والأولوية

| # | الوحدة | الأولوية | الوقت التقديري | التبعيات |
|---|--------|----------|----------------|----------|
| 1 | Departments & Heads | 🔴 عالية | 2-3 ساعات | لا شيء |
| 2 | Internal Reports | 🔴 عالية | 3-4 ساعات | الوحدة 1 |
| 3 | Leaves & Attendance | 🔴 عالية | 4-5 ساعات | الوحدة 1 |
| 4 | Notifications | 🟡 متوسطة | 2-3 ساعات | الوحدات 1-3 |
| 5 | Kanban Board | 🟡 متوسطة | 2-3 ساعات | لا شيء |
| 6 | Payroll & Payslips | 🟡 متوسطة | 4-5 ساعات | الوحدة 3 |
| 7 | AI Evaluation | 🟢 منخفضة | 3-4 ساعات | الوحدات 2-3 |
| **المجموع** | | | **20-27 ساعة** | |

---

## ✅ 6. خطة التحقق (Verification Plan)

### لكل وحدة:
1. **Migration:** تشغيل `php artisan migrate` بنجاح
2. **Seeder:** تشغيل `php artisan db:seed` بدون أخطاء
3. **CRUD:** التحقق من إنشاء/عرض/تعديل/حذف السجلات في كل لوحة
4. **Relations:** التحقق من العلاقات تعمل بشكل صحيح
5. **Workflow:** اختبار سلاسل الموافقات (الإجازات خصوصاً)
6. **Notifications:** التحقق من ظهور الإشعارات في الجرس
7. **PDF:** تحميل قسيمة راتب والتحقق من المحتوى
8. **AI:** اختبار التقييم مع API key صالح

### الأوامر الأساسية للاختبار:
```bash
php artisan migrate:fresh --seed
php artisan serve
# ثم الدخول بكل دور واختبار الوظائف
```

---

## 🔄 7. التوصية بترتيب التنفيذ

> **نبدأ بالوحدة 1 (Departments & Heads)** لأنها الأساس الذي تعتمد عليه الوحدات 2 و 3 و 7.
> ثم ننتقل بالترتيب: 2 → 3 → 4 → 5 → 6 → 7

**هل تريد البدء بتنفيذ الوحدة الأولى (الأقسام ورؤساء الأقسام)؟**
