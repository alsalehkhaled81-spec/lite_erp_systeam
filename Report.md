# 📋 تقرير شامل كامل عن مشروع Lite ERP System

---

## 1. نظرة عامة على المشروع

| البند | التفاصيل |
|---|---|
| **اسم المشروع** | Lite ERP System - نظام تخطيط موارد المؤسسات الخفيف |
| **الإطار** | Laravel 12 + Filament 3.3 |
| **الغرض** | نظام ERP متكامل للشركات الصغيرة والمتوسطة مع نظام توظيف ذكي (ATS) |
| **اللغات** | عربي/إنجليزي ثنائي اللغة (RTL/LTR) |
| **قاعدة البيانات** | MySQL/SQLite - 27 جدول تطبيقي + 7 جداول نظام |
| **عدد اللوحات** | 5 لوحات (إدارة عامة، موارد بشرية، إدارة مشاريع، محاسب، موظف) |
| **عدد النماذج** | 24 نموذج Eloquent |
| **عدد الموارد** | 14 مورد إدارة + 9 موارد HR + 3 موارد PM + 4 موارد محاسب + 3 موارد موظف |
| **عدد الخدمات** | 6 خدمات (3 AI + 3 PDF/تصدير) |
| **عدد الإشعارات** | 4 إشعارات قاعدة بيانات |

---

## 2. التقنيات والمكتبات المستخدمة

| التقنية | الإصدار/التفاصيل | الغرض |
|---|---|---|
| **Laravel** | 12.x (PHP ^8.2) | إطار العمل الرئيسي |
| **Filament** | 3.3 | لوحة الإدارة والواجهة الخلفية |
| **Livewire** | 3.x | المكونات التفاعلية |
| **Tailwind CSS** | 4.x | تنسيق الواجهة |
| **Alpine.js** | مدمج مع Livewire | التفاعل من جانب العميل |
| **Vite** | 7.x | بناء الأصول |
| **barryvdh/laravel-dompdf** | 3.1 | توليد ملفات PDF |
| **openai-php/laravel** | 0.19.1 | تكامل OpenAI (غير مستخدم مباشرة - يتم استخدام LiteLLM Proxy) |
| **ar-php/ar-php** | * | معالجة النصوص العربية في PDF (utf8Glyphs) |
| **mokhosh/filament-kanban** | 2.11 | لوحة كانبان للمهام |
| **bezhansalleh/filament-language-switch** | 3.1 | تبديل اللغة عربي/إنجليزي |
| **parsedown/laravel** | * | معالجة Markdown لتقارير AI |
| **axios** | ^1.11.0 | طلبات HTTP |
| **concurrently** | ^9.0.1 | تشغيل عدة عمليات بالتوازي |

---

## 3. هيكل مجلدات المشروع الكامل

```
lite_erp_systeam/
├── .editorconfig
├── .env / .env.example
├── .gitattributes / .gitignore
├── artisan
├── composer.json / composer.lock
├── package.json / package-lock.json
├── phpunit.xml
├── vite.config.js
├── README.md
├── implement_plane.md                      # خطة التنفيذ التفصيلية
├── aiConnect.md                             # توثيق تكامل الذكاء الاصطناعي
├── prompt.md                                # وثيقة التطوير الرئيسية
├── userReport.md                            # تقرير إنجاز التنفيذ
├── add_translations.php                      # سكريبت إضافة ترجمات
├── add_relation_translations.php             # سكريبت إضافة ترجمات العلاقات
├── check_missing.php                         # فحص الترجمات المفقودة
├── extract_keys.php                          # استخراج مفاتيح الترجمة
├── scratch_restore.php                       # استعادة من الصفر
│
├── app/
│   ├── Exports/
│   │   └── PayrollExport.php                 # تصدير الرواتب CSV
│   │
│   ├── Filament/
│   │   ├── Accountant/                       # لوحة المحاسب
│   │   │   ├── Resources/
│   │   │   │   ├── ClientResource.php
│   │   │   │   ├── ClientResource/ (Pages + RelationManagers)
│   │   │   │   ├── ExpenseResource.php
│   │   │   │   ├── ExpenseResource/ (Pages)
│   │   │   │   ├── InvoiceResource.php
│   │   │   │   ├── InvoiceResource/ (Pages + RelationManagers)
│   │   │   │   ├── PayrollResource.php
│   │   │   │   └── PayrollResource/ (Pages)
│   │   │   └── Widgets/
│   │   │       ├── CashflowChart.php         # مخطط التدفق النقدي
│   │   │       ├── FinanceStatsOverview.php  # إحصائيات مالية
│   │   │       ├── OverdueInvoicesAlert.php  # تنبيه الفواتير المتأخرة
│   │   │       ├── ProjectBudgetChart.php    # مخطط ميزانية المشاريع
│   │   │       └── TaxReportWidget.php       # تقرير الضرائب
│   │   │
│   │   ├── Employee/                        # لوحة الموظف
│   │   │   ├── Pages/
│   │   │   │   ├── EmployeeProfile.php       # الملف الشخصي
│   │   │   │   ├── MyAttendance.php          # الحضور الخاص
│   │   │   │   └── MyTasksKanban.php         # كانبان المهام الخاصة
│   │   │   ├── Resources/
│   │   │   │   ├── LeaveResource.php + LeaveResource/
│   │   │   │   ├── ReportResource.php + ReportResource/
│   │   │   │   └── TaskResource.php + TaskResource/
│   │   │   └── Widgets/
│   │   │       ├── EmployeeCalendarWidget.php   # تقويم الموظف
│   │   │       ├── EmployeeProgressRing.php     # حلقة التقدم
│   │   │       ├── EmployeeStatsOverview.php     # إحصائيات الموظف
│   │   │       └── MyLatestTasksTable.php        # آخر المهام
│   │   │
│   │   ├── Hr/                              # لوحة الموارد البشرية
│   │   │   ├── Pages/
│   │   │   │   └── TeamCalendar.php          # تقويم الفريق (FullCalendar)
│   │   │   ├── Resources/
│   │   │   │   ├── AttendanceResource.php + AttendanceResource/
│   │   │   │   ├── CareerPlanResource.php + CareerPlanResource/
│   │   │   │   ├── DepartmentResource.php + DepartmentResource/
│   │   │   │   ├── EmployeeResource.php + EmployeeResource/
│   │   │   │   │   └── RelationManagers/ (SkillsRelationManager)
│   │   │   │   ├── LeaveResource.php + LeaveResource/
│   │   │   │   ├── ReportResource.php + ReportResource/
│   │   │   │   ├── ResumeResource.php + ResumeResource/
│   │   │   │   ├── SkillResource.php + SkillResource/
│   │   │   │   └── TrainingResource.php + TrainingResource/
│   │   │   └── Widgets/
│   │   │       ├── AttendanceSummaryWidget.php  # ملخص الحضور
│   │   │       ├── EmployeesChart.php            # مخطط الموظفين
│   │   │       └── HrStatsOverview.php           # إحصائيات HR
│   │   │
│   │   ├── Pages/                            # صفحات الإدارة العامة
│   │   │   ├── AdminDashboard.php             # لوحة القيادة مع زر تصدير PDF
│   │   │   └── TasksKanbanBoard.php           # لوحة كانبان المهام
│   │   │
│   │   ├── Pm/                               # لوحة إدارة المشاريع
│   │   │   ├── Pages/
│   │   │   │   ├── GanttChart.php             # مخطط جانت (Frappe-Gantt)
│   │   │   │   └── TasksKanbanBoard.php       # كانبان المهام
│   │   │   ├── Resources/
│   │   │   │   ├── ProjectResource.php + ProjectResource/
│   │   │   │   │   └── RelationManagers/ (EmployeesRM, TasksRM)
│   │   │   │   ├── ProjectTemplateResource.php + ProjectTemplateResource/
│   │   │   │   │   └── RelationManagers/ (TaskTemplatesRM)
│   │   │   │   └── TaskResource.php + TaskResource/
│   │   │   │       └── RelationManagers/ (CommentsRM, AttachmentsRM, TimeEntriesRM)
│   │   │   └── Widgets/
│   │   │       ├── PmStatsOverview.php        # إحصائيات PM
│   │   │       └── TasksChart.php             # مخطط المهام
│   │   │
│   │   ├── Resources/                        # موارد الإدارة العامة (14 مورد)
│   │   │   ├── ClientResource.php + ClientResource/
│   │   │   │   └── RelationManagers/ (InvoicesRM - من Accountant)
│   │   │   ├── DepartmentResource.php + DepartmentResource/
│   │   │   ├── EmployeeResource.php + EmployeeResource/
│   │   │   │   └── RelationManagers/ (SkillsRM - من HR)
│   │   │   ├── ExpenseResource.php + ExpenseResource/
│   │   │   ├── InvoiceResource.php + InvoiceResource/
│   │   │   ├── LeaveResource.php + LeaveResource/
│   │   │   ├── PayrollResource.php + PayrollResource/
│   │   │   ├── ProjectResource.php + ProjectResource/
│   │   │   │   └── RelationManagers/ (EmployeesRM, TasksRM - من PM)
│   │   │   ├── ProjectTemplateResource.php + ProjectTemplateResource/
│   │   │   │   └── RelationManagers/ (TaskTemplatesRM)
│   │   │   ├── ReportResource.php + ReportResource/
│   │   │   ├── RoleResource.php + RoleResource/
│   │   │   ├── SkillResource.php + SkillResource/
│   │   │   ├── TaskResource.php + TaskResource/
│   │   │   │   └── RelationManagers/ (CommentsRM, AttachmentsRM, TimeEntriesRM)
│   │   │   └── UserResource.php + UserResource/
│   │   │
│   │   └── Widgets/                          # ويدجات الإدارة العامة
│   │       ├── AdminActivityFeed.php          # آخر 15 نشاط
│   │       ├── AdminEmployeeHeatmap.php       # خريطة حرارية للموظفين
│   │       ├── AdminKpiIndicators.php         # 4 مؤشرات أداء رئيسية
│   │       ├── AdminProjectsChart.php         # مخطط دائري لحالات المشاريع
│   │       ├── AdminRevenueExpensesChart.php  # مخطط خطي للإيرادات والمصروفات
│   │       └── AdminStatsOverview.php         # 3 بطاقات إحصائية
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php                 # المتحكم الأساسي (فارغ)
│   │   │   ├── JobApplicationController.php   # معالجة طلبات التوظيف
│   │   │   └── LandingController.php           # صفحة الهبوط
│   │   ├── Middleware/
│   │   │   └── SetLocaleMiddleware.php         # ضبط اللغة من الجلسة/الكوكي
│   │   └── Responses/
│   │       └── LogoutResponse.php              # إعادة توجيه تسجيل الخروج
│   │
│   ├── Livewire/Auth/
│   │   ├── ForgotPassword.php                  # استعادة كلمة المرور
│   │   ├── Login.php                           # تسجيل الدخول المركزي
│   │   ├── Register.php                        # التسجيل (مع نظام ATS)
│   │   └── ResetPassword.php                  # إعادة تعيين كلمة المرور
│   │
│   ├── Models/                                # 24 نموذج Eloquent
│   │   ├── Attendance.php
│   │   ├── CareerPlan.php
│   │   ├── Certificate.php
│   │   ├── Client.php
│   │   ├── Department.php
│   │   ├── Employee.php
│   │   ├── Expense.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Leave.php
│   │   ├── Payroll.php
│   │   ├── Project.php
│   │   ├── ProjectTemplate.php
│   │   ├── Report.php
│   │   ├── Resume.php
│   │   ├── Role.php
│   │   ├── Skill.php
│   │   ├── Task.php
│   │   ├── TaskAttachment.php
│   │   ├── TaskComment.php
│   │   ├── TaskTemplate.php
│   │   ├── TimeEntry.php
│   │   ├── Training.php
│   │   └── User.php
│   │
│   ├── Notifications/                         # 4 إشعارات
│   │   ├── JobApplicationStatusNotification.php
│   │   ├── LeaveStatusNotification.php
│   │   ├── ReportReceivedNotification.php
│   │   └── TaskAssignedNotification.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php              # تسجيل LogoutResponse + Blade directives
│   │   └── Filament/                           # 5 مزودي لوحات
│   │       ├── AccountantPanelProvider.php
│   │       ├── AdminPanelProvider.php
│   │       ├── EmployeePanelProvider.php
│   │       ├── HrPanelProvider.php
│   │       └── PmPanelProvider.php
│   │
│   └── Services/                              # 6 خدمات
│       ├── AiEvaluationService.php             # تقييم الموظفين بالذكاء الاصطناعي
│       ├── AiService.php                       # بوابة AI المركزية (LiteLLM Proxy)
│       ├── DashboardExportService.php          # تصدير لوحة القيادة PDF
│       ├── InvoicePdfService.php               # توليد فواتير PDF
│       ├── PayslipPdfService.php                # توليد قسائم رواتب PDF مع ملاحظة AI
│       └── ResumeAnalysisService.php            # تحليل السير الذاتية بالذكاء الاصطناعي
│
├── bootstrap/
│   └── app.php                                 # إعداد Laravel 12 + SetLocaleMiddleware
│
├── config/
│   ├── ai.php                                  # إعدادات AI (LiteLLM Proxy)
│   ├── app.php                                  # إعدادات التطبيق
│   ├── auth.php                                 # إعدادات المصادقة
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── factories/
│   ├── migrations/                              # 42 ملف هجرة
│   └── seeders/
│
├── lang/
│   ├── ar/
│   │   ├── filament.php                         # 741 سطر ترجمة عربية شاملة
│   │   └── validation.php                       # 30 سطر رسائل تحقق عربية
│   └── en/
│       └── filament.php                         # 741 سطر ترجمة إنجليزية شاملة
│
├── public/                                      # الأصول العامة
│
├── resources/
│   ├── css/
│   │   ├── app.css                              # Tailwind CSS v4
│   │   ├── filament-theme.css                   # ثيم Filament العام
│   │   └── filament/
│   │       ├── _base.css                        # أنماط أساسية
│   │       ├── accountant/theme.css             # ثيم لوحة المحاسب (Emerald)
│   │       ├── admin/theme.css                  # ثيم لوحة الإدارة (Amber)
│   │       ├── employee/theme.css                # ثيم لوحة الموظف (Indigo)
│   │       ├── hr/theme.css                     # ثيم لوحة HR (Rose)
│   │       └── pm/theme.css                     # ثيم لوحة PM (Blue)
│   │
│   ├── js/
│   │   ├── app.js                               # نقطة دخول JS
│   │   └── bootstrap.js                          # إعداد Axios
│   │
│   └── views/
│       ├── application/
│       │   ├── form.blade.php                    # نموذج التقدم للوظيفة
│       │   └── status.blade.php                  # حالة الطلب (معلق/مرفوض)
│       ├── components/layouts/
│       │   └── app.blade.php                     # تخطيط صفحات المصادقة (241 سطر)
│       ├── filament/
│       │   ├── pages/
│       │   │   ├── admin-tasks-kanban.blade.php  # كانبان المهام (إدارة)
│       │   │   ├── ai-evaluation.blade.php       # محتوى تقييم AI
│       │   │   ├── employee-profile.blade.php     # الملف الشخصي للموظف
│       │   │   ├── gantt-chart.blade.php          # مخطط جانت
│       │   │   ├── my-attendance.blade.php         # حضور الموظف
│       │   │   ├── my-tasks-kanban.blade.php       # كانبان مهام الموظف
│       │   │   ├── tasks-kanban.blade.php          # كانبان مهام PM
│       │   │   └── team-calendar.blade.php         # تقويم الفريق
│       │   └── widgets/
│       │       ├── admin-activity-feed.blade.php
│       │       ├── admin-employee-heatmap.blade.php
│       │       ├── employee-calendar.blade.php
│       │       ├── employee-progress-ring.blade.php
│       │       └── tax-report.blade.php
│       ├── livewire/auth/
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── reset-password.blade.php
│       ├── pdf/
│       │   ├── dashboard-report.blade.php        # قالب PDF تقرير لوحة القيادة
│       │   ├── invoice.blade.php                  # قالب PDF الفاتورة
│       │   └── payslip.blade.php                  # قالب PDF قسيمة الراتب
│       ├── landing.blade.php                     # صفحة الهبوط العامة (864 سطر)
│       └── welcome.blade.php                      # صفحة Laravel الافتراضية
│
├── routes/
│   ├── web.php                                   # 9 مسارات ويب
│   └── console.php                               # أوامر Artisan
│
├── storage/                                       # ملفات التخزين
├── tests/                                         # اختبارات
├── vendor/                                        # حزم Composer
└── node_modules/                                  # حزم NPM
```

---

## 4. قاعدة البيانات - المخطط الكامل

### 4.1 جداول نظام Laravel (7 جداول)

| الجدول | الوصف |
|---|---|
| `cache` | تخزين مؤقت (key, value, expiration) |
| `cache_locks` | أقفال التخزين المؤقت (key, owner, expiration) |
| `jobs` | طوابير المهام (queue, payload, attempts, reserved_at, available_at, created_at) |
| `job_batches` | مجموعات المهام (id, name, total_jobs, pending_jobs, failed_jobs, etc.) |
| `failed_jobs` | المهام الفاشلة (uuid, connection, queue, payload, exception) |
| `password_reset_tokens` | رموز إعادة التعيين (email, token, created_at) |
| `sessions` | جلسات المستخدمين (id, user_id, ip_address, user_agent, payload, last_activity) |

### 4.2 جدول `roles`

| العمود | النوع | Nullable | فريد | القيمة الافتراضية |
|---|---|---|---|---|
| id | bigint (auto) | NO | PK | - |
| name | string | NO | UNIQUE | - |
| description | text | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**العلاقات:** `users` (HasMany)

---

### 4.3 جدول `users`

| العمود | النوع | Nullable | فريد | القيمة الافتراضية | FK |
|---|---|---|---|---|---|
| id | bigint (auto) | NO | PK | - | - |
| role_id | foreignId | YES | - | - | roles.id (nullOnDelete) |
| name | string | NO | - | - | - |
| email | string | NO | UNIQUE | - | - |
| email_verified_at | timestamp | YES | - | - | - |
| password | string (hashed) | NO | - | - | - |
| remember_token | string(100) | YES | - | - | - |
| profile_photo_path | string | YES | - | - | - |
| is_approved | boolean | YES | - | false | - |
| created_at | timestamp | YES | - | - | - |
| updated_at | timestamp | YES | - | - | - |

**النموذج (User):**
- **Fillable:** role_id, name, email, password, profile_photo_path, is_approved
- **Casts:** email_verified_at => datetime, password => hashed, is_approved => boolean
- **Hidden:** password, remember_token
- **Traits:** HasFactory, Notifiable
- **الواجهات:** implements FilamentUser
- **العلاقات:** role (BelongsTo Role), employee (HasOne Employee), expenses (HasMany Expense)
- **المنطق المخصص:**
  - `canAccessPanel(Panel $panel): bool` — توجيه حسب الدور:
    - `super_admin` → `/admin`
    - `hr_manager` → `/hr`
    - `project_manager` → `/pm`
    - `accountant` → `/accountant`
    - `employee` → `/employee`

---

### 4.4 جدول `employees`

| العمود | النوع | Nullable | فريد | القيمة الافتراضية | FK |
|---|---|---|---|---|---|
| id | bigint (auto) | NO | PK | - | - |
| user_id | foreignId | NO | UNIQUE | - | users.id (cascadeOnDelete) |
| department_id | unsignedBigInteger | YES | - | - | departments.id (nullOnDelete) |
| job_title | string | YES | - | - | - |
| salary | decimal(10,2) unsigned | YES | - | - | - |
| status | enum('pending','active','on_leave','terminated','rejected') | NO | - | 'pending' | - |
| rejection_reason | text | YES | - | - | - |
| hire_date | date | YES | - | - | - |
| annual_leave_balance | integer | NO | - | 21 | - |
| used_leave_days | integer | NO | - | 0 | - |
| created_at | timestamp | YES | - | - | - |
| updated_at | timestamp | YES | - | - | - |

**النموذج (Employee):**
- **Fillable:** user_id, department_id, job_title, salary, status, hire_date, annual_leave_balance, used_leave_days
- **Casts:** hire_date => date
- **الواجهات:** HasFactory
- **العلاقات (14 علاقة):**
  - user (BelongsTo User)
  - department (BelongsTo Department)
  - headOfDepartment (HasOne Department) — القسم الذي يرأسه الموظف
  - resume (HasOne Resume)
  - skills (BelongsToMany Skill) — عبر employee_skill
  - projects (BelongsToMany Project) — عبر employee_project
  - tasks (HasMany Task)
  - leaves (HasMany Leave)
  - payrolls (HasMany Payroll)
  - sentReports (HasMany Report, FK sender_id)
  - receivedReports (HasMany Report, FK receiver_id)
  - attendances (HasMany Attendance)
  - certificates (HasMany Certificate)
  - careerPlans (HasMany CareerPlan)
  - trainings (BelongsToMany Training) — عبر employee_training مع pivot: status, certificate_url, completion_date
- **المنطق المخصص:**
  - `getRemainingLeaveBalanceAttribute(): int` — Accessor: `annual_leave_balance - used_leave_days`
  - `scopeEligibleDepartmentHead($query, ?int $currentHeadId = null)` — نطاق لفلترة الموظفين المؤهلين لرئاسة القسم

---

### 4.5 جدول `departments`

| العمود | النوع | Nullable | FK |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| name | string | NO | - |
| head_id | unsignedBigInteger | YES | employees.id (nullOnDelete) |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (Department):**
- **Fillable:** name, head_id
- **العلاقات:** head (BelongsTo Employee), employees (HasMany Employee)

---

### 4.6 جدول `resumes`

| العمود | النوع | Nullable | فريد | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| employee_id | foreignId | NO | UNIQUE | employees.id (cascadeOnDelete) |
| file_path | string | YES | - | - |
| resume_text | longText | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Resume):**
- **Fillable:** employee_id, file_path, resume_text
- **العلاقات:** employee (BelongsTo Employee)

---

### 4.7 جدول `skills`

| العمود | النوع | فريد |
|---|---|---|
| id | bigint (auto) | - |
| name | string | UNIQUE |
| created_at | timestamp | - |
| updated_at | timestamp | - |

**النموذج (Skill):** Fillable: name. العلاقات: employees (BelongsToMany Employee)

---

### 4.8 جدول `employee_skill` (محوري)

| العمود | النوع | FK |
|---|---|---|
| employee_id | foreignId | employees.id (cascadeOnDelete) |
| skill_id | foreignId | skills.id (cascadeOnDelete) |
| **PK مركب:** (employee_id, skill_id) | | |

---

### 4.9 جدول `clients`

| العمود | النوع | Nullable | فريد |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| name | string | NO | - |
| company_name | string | YES | - |
| email | string | YES | UNIQUE |
| phone | string | YES | - |
| address | text | YES | - |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (Client):** Fillable: name, company_name, email, phone, address. العلاقات: projects (HasMany), invoices (HasMany)

---

### 4.10 جدول `projects`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| client_id | foreignId | YES | - | clients.id (nullOnDelete) |
| name | string | NO | - | - |
| description | text | YES | - | - |
| budget | decimal(12,2) unsigned | YES | - | - |
| start_date | date | YES | - | - |
| end_date | date | YES | - | - |
| status | enum('pending','in_progress','completed','canceled') | NO | 'pending' | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Project):**
- **Fillable:** client_id, name, description, budget, start_date, end_date, status
- **Appends:** completion_percentage
- **العلاقات:** client (BelongsTo Client), employees (BelongsToMany Employee), tasks (HasMany Task), invoices (HasMany Invoice), expenses (HasMany Expense)
- **المنطق المخصص:**
  - `getCompletionPercentageAttribute(): float` — نسبة المهام المكتملة = (مهام done / إجمالي المهام) × 100
  - `getTotalTrackedHoursAttribute(): float` — مجموع ساعات التتبع عبر جميع المهام

---

### 4.11 جدول `tasks`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| project_id | foreignId | NO | - | projects.id (cascadeOnDelete) |
| employee_id | foreignId | **YES** | - | employees.id (cascadeOnDelete) |
| title | string | NO | - | - |
| description | text | YES | - | - |
| start_date | date | YES | - | - |
| due_date | date | YES | - | - |
| status | enum('todo','in_progress','review','done') | NO | 'todo' | - |
| priority | enum('low','medium','high') | NO | 'medium' | - |
| sort_order | integer | NO | 0 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Task):**
- **Fillable:** project_id, employee_id, title, description, start_date, due_date, status, priority, sort_order
- **العلاقات:** project (BelongsTo), employee (BelongsTo), comments (HasMany TaskComment), attachments (HasMany TaskAttachment), timeEntries (HasMany TimeEntry)
- **المنطق المخصص:** `getTotalHoursAttribute(): float` — مجموع ساعات timeEntries

---

### 4.12 جدول `employee_project` (محوري)

| العمود | النوع | FK |
|---|---|---|
| project_id | foreignId | projects.id (cascadeOnDelete) |
| employee_id | foreignId | employees.id (cascadeOnDelete) |
| **PK مركب:** (project_id, employee_id) | | |

---

### 4.13 جدول `invoices`

| العمود | النوع | Nullable | القيمة الافتراضية | فريد | FK |
|---|---|---|---|---|---|
| id | bigint (auto) | NO | - | - | - |
| client_id | foreignId | NO | - | - | clients.id (cascadeOnDelete) |
| project_id | foreignId | YES | - | - | projects.id (nullOnDelete) |
| invoice_number | string | NO | - | UNIQUE | - |
| amount | decimal(12,2) unsigned | NO | - | - | - |
| vat_rate | decimal(5,2) | NO | 0 | - | - |
| vat_amount | decimal(12,2) | NO | 0 | - | - |
| total_with_vat | decimal(12,2) | NO | 0 | - | - |
| issue_date | date | YES | - | - | - |
| due_date | date | YES | - | - | - |
| status | enum('unpaid','paid','overdue') | NO | 'unpaid' | - | - |
| created_at | timestamp | YES | - | - | - |
| updated_at | timestamp | YES | - | - | - |

**النموذج (Invoice):**
- **Fillable:** client_id, project_id, invoice_number, amount, vat_rate, vat_amount, total_with_vat, issue_date, due_date, status
- **Casts:** issue_date => date, due_date => date
- **العلاقات:** client (BelongsTo Client), project (BelongsTo Project), items (HasMany InvoiceItem)
- **المنطق المخصص:** `calculateTotals(): void` — إعادة حساب: `amount = sum(items.total)`, `vat_amount = amount * (vat_rate / 100)`, `total_with_vat = amount + vat_amount`

---

### 4.14 جدول `invoice_items`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| invoice_id | foreignId | NO | - | invoices.id (cascadeOnDelete) |
| description | string | NO | - | - |
| quantity | decimal(10,2) | NO | 1 | - |
| unit_price | decimal(12,2) | NO | 0 | - |
| total | decimal(12,2) | NO | 0 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (InvoiceItem):** Fillable: invoice_id, description, quantity, unit_price, total. Casts: quantity/total/unit_price => decimal:2. العلاقات: invoice (BelongsTo Invoice)

---

### 4.15 جدول `expenses`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| user_id | foreignId | NO | - | users.id (cascadeOnDelete) |
| project_id | foreignId | YES | - | projects.id (nullOnDelete) |
| title | string | NO | - | - |
| category | string | YES | - | - |
| amount | decimal(12,2) unsigned | NO | - | - |
| expense_date | date | YES | - | - |
| receipt_url | string | YES | - | - |
| status | enum('pending','approved','rejected') | NO | 'pending' | - |
| approved_by | foreignId | YES | - | users.id (nullOnDelete) |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Expense):** Fillable: user_id, project_id, title, category, amount, expense_date, receipt_url, status, approved_by. العلاقات: user (BelongsTo), project (BelongsTo), approvedBy (BelongsTo User)

---

### 4.16 جدول `leaves`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| employee_id | unsignedBigInteger | NO | - | employees.id (cascadeOnDelete) |
| type | string | NO | - | - |
| start_date | date | NO | - | - |
| end_date | date | NO | - | - |
| reason | text | NO | - | - |
| status | enum('pending','approved_by_head','approved_by_hr','rejected') | NO | 'pending' | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Leave):**
- **Fillable:** employee_id, type, start_date, end_date, reason, status
- **Casts:** start_date => date, end_date => date
- **العلاقات:** employee (BelongsTo Employee)
- **المنطق المخصص:** `getDurationInDaysAttribute(): int` — `start_date->diffInDays(end_date) + 1`

---

### 4.17 جدول `payrolls`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| employee_id | unsignedBigInteger | NO | - | employees.id (cascadeOnDelete) |
| month_year | string | NO | - | - |
| basic_salary | decimal(10,2) unsigned | NO | - | - |
| bonuses | decimal(10,2) unsigned | NO | 0 | - |
| deductions | decimal(10,2) unsigned | NO | 0 | - |
| housing_allowance | decimal(10,2) | NO | 0 | - |
| transport_allowance | decimal(10,2) | NO | 0 | - |
| phone_allowance | decimal(10,2) | NO | 0 | - |
| social_insurance_rate | decimal(5,2) | NO | 0 | - |
| social_insurance_amount | decimal(10,2) | NO | 0 | - |
| absence_days | integer | NO | 0 | - |
| absence_deduction | decimal(10,2) | NO | 0 | - |
| net_salary | decimal(10,2) unsigned | NO | - | - |
| status | string | NO | 'unpaid' | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Payroll):**
- **Fillable:** employee_id, month_year, basic_salary, bonuses, deductions, housing_allowance, transport_allowance, phone_allowance, social_insurance_rate, social_insurance_amount, absence_days, absence_deduction, net_salary, status
- **Casts:** basic_salary/bonuses/deductions/housing_allowance/transport_allowance/phone_allowance => decimal:2, social_insurance_rate/social_insurance_amount => decimal:2, absence_days => integer, absence_deduction/net_salary => decimal:2
- **العلاقات:** employee (BelongsTo Employee)
- **المنطق المخصص:**
  - `static calculateNetSalary(float $basic, float $bonuses, float $deductions, float $housing=0, float $transport=0, float $phone=0, float $insuranceRate=0, float $absenceDeduction=0): float`
  - المعادلة: `max(0, basic + bonuses + housing + transport + phone - deductions - (basic * insuranceRate / 100) - absenceDeduction)`

---

### 4.18 جدول `reports`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| sender_id | unsignedBigInteger | NO | - | employees.id (cascadeOnDelete) |
| receiver_id | unsignedBigInteger | NO | - | employees.id (cascadeOnDelete) |
| title | string | NO | - | - |
| content | text | NO | - | - |
| feedback | text | YES | - | - |
| status | enum('unread','read','replied') | NO | 'unread' | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (Report):** Fillable: sender_id, receiver_id, title, content, feedback, status. العلاقات: sender (BelongsTo Employee), receiver (BelongsTo Employee)

---

### 4.19 جدول `attendances`

| العمود | النوع | Nullable | القيمة الافتراضية | فريد | FK |
|---|---|---|---|---|---|
| id | bigint (auto) | NO | - | - | - |
| employee_id | foreignId | NO | - | - | employees.id (cascadeOnDelete) |
| date | date | NO | - | - | - |
| check_in | dateTime | YES | - | - | - |
| check_out | dateTime | YES | - | - | - |
| hours_worked | decimal(5,2) | NO | 0 | - | - |
| status | enum('present','late','absent','half_day') | NO | 'present' | - | - |
| notes | text | YES | - | - | - |
| created_at | timestamp | YES | - | - | - |
| updated_at | timestamp | YES | - | - | - |
| **فريد مركب:** (employee_id, date) | | | | | |

**النموذج (Attendance):**
- **Fillable:** employee_id, date, check_in, check_out, hours_worked, status, notes
- **Casts:** date => date, check_in => datetime, check_out => datetime
- **العلاقات:** employee (BelongsTo Employee)
- **المنطق المخصص:**
  - `static calculateHoursWorked($checkIn, $checkOut): float` — حساب ساعات العمل
  - `static combineDateTime($date, $time): ?Carbon` — دمج التاريخ والوقت
  - `static calculateHoursFromTimes($checkInTime, $checkOutTime): float` — حساب الساعات من الأوقات فقط
  - `getIsLateAttribute(): bool` — فحص التأخر (بعد 09:15)

---

### 4.20 جدول `trainings`

| العمود | النوع | Nullable | القيمة الافتراضية |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| title | string | NO | - |
| description | text | YES | - |
| trainer | string | YES | - |
| start_date | date | NO | - |
| end_date | date | NO | - |
| status | enum('upcoming','ongoing','completed') | NO | 'upcoming' |
| location | string | YES | - |
| max_participants | integer | YES | - |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (Training):** Fillable: title, description, trainer, start_date, end_date, status, location, max_participants. Casts: start_date/end_date => date. العلاقات: employees (BelongsToMany Employee عبر employee_training)

---

### 4.21 جدول `employee_training` (محوري مع أعمدة إضافية)

| العمود | النوع | Nullable | القيمة الافتراضية | فريد | FK |
|---|---|---|---|---|---|
| id | bigint (auto) | NO | - | - | - |
| employee_id | foreignId | NO | - | مركب | employees.id (cascadeOnDelete) |
| training_id | foreignId | NO | - | مركب | trainings.id (cascadeOnDelete) |
| status | enum('enrolled','completed','certified') | NO | 'enrolled' | - | - |
| certificate_url | string | YES | - | - | - |
| completion_date | date | YES | - | - | - |
| created_at | timestamp | YES | - | - | - |
| updated_at | timestamp | YES | - | - | - |

---

### 4.22 جدول `certificates`

| العمود | النوع | Nullable | FK |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| employee_id | foreignId | NO | employees.id (cascadeOnDelete) |
| title | string | NO | - |
| issuer | string | NO | - |
| issue_date | date | NO | - |
| expiry_date | date | YES | - |
| certificate_url | string | YES | - |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (Certificate):** Fillable: employee_id, title, issuer, issue_date, expiry_date, certificate_url. Casts: issue_date/expiry_date => date. العلاقات: employee (BelongsTo Employee)

---

### 4.23 جدول `career_plans`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| employee_id | foreignId | NO | - | employees.id (cascadeOnDelete) |
| current_role | string | NO | - | - |
| target_role | string | NO | - | - |
| timeline_months | integer | NO | - | - |
| required_skills | text | YES | - | - |
| notes | text | YES | - | - |
| status | enum('draft','active','completed') | NO | 'draft' | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (CareerPlan):** Fillable: employee_id, current_role, target_role, timeline_months, required_skills, notes, status. العلاقات: employee (BelongsTo Employee)

---

### 4.24 جدول `project_templates`

| العمود | النوع | Nullable |
|---|---|---|
| id | bigint (auto) | NO |
| name | string | NO |
| description | text | YES |
| budget | decimal(12,2) unsigned | YES |
| estimated_days | unsignedInteger | YES |
| created_at | timestamp | YES |
| updated_at | timestamp | YES |

**النموذج (ProjectTemplate):** Fillable: name, description, budget, estimated_days. العلاقات: taskTemplates (HasMany TaskTemplate). المنطق المخصص: `createProject(array $overrides = []): Project` — إنشاء مشروع من القالب

---

### 4.25 جدول `task_templates`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| project_template_id | foreignId | NO | - | project_templates.id (cascadeOnDelete) |
| title | string | NO | - | - |
| description | text | YES | - | - |
| priority | enum('low','medium','high') | NO | 'medium' | - |
| estimated_hours | unsignedInteger | YES | - | - |
| sort_order | unsignedInteger | NO | 0 | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (TaskTemplate):** Fillable: project_template_id, title, description, priority, estimated_hours, sort_order. العلاقات: projectTemplate (BelongsTo ProjectTemplate)

---

### 4.26 جدول `task_comments`

| العمود | النوع | Nullable | FK |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| task_id | foreignId | NO | tasks.id (cascadeOnDelete) |
| user_id | foreignId | NO | users.id (cascadeOnDelete) |
| comment | text | NO | - |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (TaskComment):** Fillable: task_id, user_id, comment. العلاقات: task (BelongsTo Task), user (BelongsTo User)

---

### 4.27 جدول `task_attachments`

| العمود | النوع | Nullable | FK |
|---|---|---|---|
| id | bigint (auto) | NO | - |
| task_id | foreignId | NO | tasks.id (cascadeOnDelete) |
| user_id | foreignId | NO | users.id (cascadeOnDelete) |
| file_name | string | NO | - |
| file_path | string | NO | - |
| file_type | string | YES | - |
| file_size | unsignedBigInteger | YES | - |
| created_at | timestamp | YES | - |
| updated_at | timestamp | YES | - |

**النموذج (TaskAttachment):** Fillable: task_id, user_id, file_name, file_path, file_type, file_size. العلاقات: task (BelongsTo Task), user (BelongsTo User)

---

### 4.28 جدول `time_entries`

| العمود | النوع | Nullable | القيمة الافتراضية | FK |
|---|---|---|---|---|
| id | bigint (auto) | NO | - | - |
| task_id | foreignId | NO | - | tasks.id (cascadeOnDelete) |
| employee_id | foreignId | NO | - | employees.id (cascadeOnDelete) |
| date | date | NO | - | - |
| hours | decimal(5,2) | NO | 0 | - |
| description | text | YES | - | - |
| created_at | timestamp | YES | - | - |
| updated_at | timestamp | YES | - | - |

**النموذج (TimeEntry):** Fillable: task_id, employee_id, date, hours, description. Casts: date => date. العلاقات: task (BelongsTo Task), employee (BelongsTo Employee)

---

### 4.29 جدول `notifications` (Laravel polymorphic)

| العمود | النوع | Nullable |
|---|---|---|
| id | uuid | NO |
| type | string | NO |
| notifiable_type | string | NO |
| notifiable_id | bigint | NO |
| data | text | NO |
| read_at | timestamp | YES |
| created_at | timestamp | YES |
| updated_at | timestamp | YES |

**فهرس:** (notifiable_type, notifiable_id)

---

## 5. خريطة العلاقات الكاملة

```
roles ──1:N──> users
users ──1:1──> employees
users ──1:N──> expenses (كمسجل)
users ──1:N──> expenses (كموافق: approved_by)

employees ──N:1──> departments
departments ──1:1──> employees (head_id: رئيس القسم)
employees ──1:1──> resumes
employees ──M:N──> skills (عبر employee_skill)
employees ──M:N──> projects (عبر employee_project)
employees ──1:N──> tasks
employees ──1:N──> leaves
employees ──1:N──> payrolls
employees ──1:N──> attendances
employees ──1:N──> certificates
employees ──1:N──> career_plans
employees ──M:N──> trainings (عبر employee_training مع pivot: status, certificate_url, completion_date)
employees ──1:N──> reports (كمُرسل: sender_id)
employees ──1:N──> reports (كمستقبل: receiver_id)

clients ──1:N──> projects
clients ──1:N──> invoices

projects ──1:N──> tasks
projects ──1:N──> invoices
projects ──1:N──> expenses

tasks ──1:N──> task_comments
tasks ──1:N──> task_attachments
tasks ──1:N──> time_entries

project_templates ──1:N──> task_templates

invoices ──1:N──> invoice_items
```

---

## 6. لوحات Filament الخمس - تفاصيل كاملة

### 6.1 لوحة الإدارة العامة (Admin Panel)

| البند | القيمة |
|---|---|
| **معرف اللوحة** | `admin` |
| **المسار** | `/admin` |
| **اللون الأساسي** | Amber |
| **العلامة التجارية (عربي)** | لوحة المدير العام |
| **العلامة التجارية (إنجليزي)** | Admin Panel |
| **الخط** | Cairo |
| **SPA** | مُفعّل |
| **الوضع الداكن** | مُفعّل |
| **الشريط الجانبي** | قابل للطي على سطح المكتب |
| **البحث العام** | مُفعّل (Ctrl+K) |
| **عرض المحتوى** | full |
| **الوصول** | `super_admin` فقط |

**الموارد (14):**
1. ClientResource — مجموعة: finance — أيقونة: heroicon-o-user-group
2. DepartmentResource — مجموعة: employee_management — أيقونة: heroicon-o-building-office-2
3. EmployeeResource — مجموعة: employee_management — أيقونة: heroicon-o-users — **إجراء مخصص: ai_evaluate (تقييم AI)**
4. ExpenseResource — مجموعة: finance — أيقونة: heroicon-o-receipt-percent
5. InvoiceResource — مجموعة: finance — أيقونة: heroicon-o-document-currency-dollar
6. LeaveResource — مجموعة: employee_management — أيقونة: heroicon-o-calendar-days
7. PayrollResource — مجموعة: finance — أيقونة: heroicon-o-banknotes — **حساب تلقائي لصافي الراتب**
8. ProjectResource — مجموعة: projects_tasks — أيقونة: heroicon-o-briefcase — RelationManagers: Employees, Tasks
9. ProjectTemplateResource — مجموعة: projects_tasks — أيقونة: heroicon-o-document-duplicate — **إجراء مخصص: create_project**
10. ReportResource — مجموعة: reports — أيقونة: heroicon-o-document-chart-bar
11. RoleResource — مجموعة: system — أيقونة: heroicon-o-key
12. SkillResource — مجموعة: employee_management — أيقونة: heroicon-o-academic-cap
13. TaskResource — مجموعة: projects_tasks — أيقونة: heroicon-o-clipboard-document-check — RelationManagers: Comments, Attachments, TimeEntries
14. UserResource — مجموعة: system — أيقونة: heroicon-o-shield-check — **إجراءات مخصصة: approve / deactivate**

**الصفحات المخصصة:**
- AdminDashboard — مع زر "تصدير PDF" يولد تقريراً شاملاً عبر DashboardExportService

**الويدجات (6):**
1. AdminStatsOverview (ترتيب 1) — 3 بطاقات: إجمالي الموظفين، المشاريع النشطة، إجمالي الإيرادات
2. AdminProjectsChart (ترتيب 2) — مخطط دائري لحالات المشاريع (pending, in_progress, completed, canceled)
3. AdminKpiIndicators (ترتيب 3) — 4 مؤشرات: معدل إنجاز المهام، متوسط مدة المشاريع، رضا العملاء، المهام المتأخرة
4. AdminActivityFeed (ترتيب 4) — آخر 15 نشاط عبر المشاريع والمهام والفواتير والإجازات والموظفين
5. AdminEmployeeHeatmap (ترتيب 5) — خريطة حرارية لنشاط الموظفين خلال آخر 7 أيام
6. AdminRevenueExpensesChart (ترتيب 5) — مخطط خطي للإيرادات مقابل المصروفات خلال آخر 12 شهر

---

### 6.2 لوحة الموارد البشرية (HR Panel)

| البند | القيمة |
|---|---|
| **معرف اللوحة** | `hr` |
| **المسار** | `/hr` |
| **اللون الأساسي** | Rose |
| **العلامة التجارية (عربي)** | الموارد البشرية |
| **العلامة التجارية (إنجليزي)** | Human Resources |
| **الخط** | Cairo |
| **SPA** | مُفعّل |
| **الوصول** | `hr_manager` فقط |
| **تحسينات** | حقن FullCalendar JS (vendor/fullcalendar/index.global.min.js + ar.global.min.js) |

**الموارد (9):**
1. AttendanceResource — الحضور والانصراف
2. CareerPlanResource — خطط المسيرة المهنية
3. DepartmentResource — الأقسام
4. EmployeeResource — الموظفون (مع SkillsRelationManager)
5. LeaveResource — الإجازات
6. ReportResource — التقارير الداخلية
7. ResumeResource — السير الذاتية
8. SkillResource — المهارات
9. TrainingResource — التدريبات

**الصفحات المخصصة:**
- TeamCalendar — تقويم الفريق باستخدام FullCalendar

**الويدجات (3):**
1. AttendanceSummaryWidget — ملخص الحضور
2. EmployeesChart — مخطط الموظفين
3. HrStatsOverview — إحصائيات HR

---

### 6.3 لوحة إدارة المشاريع (PM Panel)

| البند | القيمة |
|---|---|
| **معرف اللوحة** | `pm` |
| **المسار** | `/pm` |
| **اللون الأساسي** | Blue |
| **العلامة التجارية (عربي)** | إدارة المشاريع |
| **العلامة التجارية (إنجليزي)** | Project Management |
| **الخط** | Cairo |
| **SPA** | مُفعّل |
| **الوصول** | `project_manager` فقط |
| **تحسينات** | حقن Frappe-Gantt CSS + JS (vendor/frappe-gantt/) |

**الموارد (3):**
1. ProjectResource — المشاريع (مع EmployeesRM + TasksRM)
2. ProjectTemplateResource — قوالب المشاريع (مع TaskTemplatesRM)
3. TaskResource — المهام (مع CommentsRM + AttachmentsRM + TimeEntriesRM)

**الصفحات المخصصة:**
- GanttChart — مخطط جانت باستخدام Frappe-Gantt
- TasksKanbanBoard — لوحة كانبان المهام

**الويدجات (2):**
1. PmStatsOverview — إحصائيات إدارة المشاريع
2. TasksChart — مخطط المهام

---

### 6.4 لوحة المحاسب (Accountant Panel)

| البند | القيمة |
|---|---|
| **معرف اللوحة** | `accountant` |
| **المسار** | `/accountant` |
| **اللون الأساسي** | Emerald |
| **العلامة التجارية (عربي)** | لوحة المحاسب |
| **العلامة التجارية (إنجليزي)** | Accountant Panel |
| **الخط** | Cairo |
| **SPA** | مُفعّل |
| **الوصول** | `accountant` فقط |

**الموارد (4):**
1. ClientResource — العملاء (مع InvoicesRelationManager)
2. ExpenseResource — المصروفات
3. InvoiceResource — الفواتير
4. PayrollResource — الرواتب

**الويدجات (5):**
1. CashflowChart — مخطط التدفق النقدي
2. FinanceStatsOverview — إحصائيات مالية
3. OverdueInvoicesAlert — تنبيه الفواتير المتأخرة
4. ProjectBudgetChart — مخطط ميزانية المشاريع
5. TaxReportWidget — تقرير الضرائب

---

### 6.5 لوحة الموظف (Employee Panel)

| البند | القيمة |
|---|---|
| **معرف اللوحة** | `employee` |
| **المسار** | `/employee` |
| **اللون الأساسي** | Indigo |
| **العلامة التجارية (عربي)** | لوحة الموظف |
| **العلامة التجارية (إنجليزي)** | Employee Portal |
| **الخط** | Cairo |
| **SPA** | مُفعّل |
| **الوصول** | `employee` فقط |

**الموارد (3):**
1. LeaveResource — الإجازات (خاصة بالموظف)
2. ReportResource — التقارير (خاصة بالموظف)
3. TaskResource — المهام المعينة للموظف

**الصفحات المخصصة:**
- EmployeeProfile — الملف الشخصي للموظف
- MyAttendance — حضور الموظف
- MyTasksKanban — كانبان المهام الخاصة

**الويدجات (4):**
1. EmployeeCalendarWidget — تقويم الموظف
2. EmployeeProgressRing — حلقة تقدم المهام
3. EmployeeStatsOverview — إحصائيات الموظف
4. MyLatestTasksTable — جدول آخر المهام

---

## 7. نظام المصادقة المركزي

### 7.1 مسارات المصادقة

| الطريقة | المسار | الاسم | الوصف | الوسيطة |
|---|---|---|---|---|
| GET | `/login` | `login` | صفحة الدخول المركزية (Livewire) | guest |
| GET | `/register` | `register` | صفحة التسجيل/التقدم للوظيفة | guest |
| GET | `/forgot-password` | `password.request` | طلب إعادة تعيين كلمة المرور | guest |
| GET | `/reset-password/{token}` | `password.reset` | إعادة تعيين كلمة المرور | guest |
| POST | `/logout` | `logout` | تسجيل الخروج | - |
| GET | `/apply` | `job.apply` | نموذج التقدم للوظيفة | auth |
| POST | `/apply` | `job.store` | تقديم طلب التوظيف | auth |
| GET | `/lang/{locale}` | `switch-language` | تبديل اللغة (ar/en) | - |
| GET | `/` | `landing` | صفحة الهبوط العامة | - |

### 7.2 تدفق التسجيل (Register.php)

1. المستخدم يختار الدور (باستثناء `super_admin` — مخفي من القائمة)
2. **أدوار الإدارة** (`hr_manager`, `project_manager`, `accountant`): يتم إنشاء المستخدم مع `is_approved = false` → يحتاج موافقة المدير
3. **دور الموظف** (`employee`): يتم إنشاء المستخدم مع `is_approved = true` → تسجيل دخول تلقائي → إعادة توجيه لصفحة التقدم للوظيفة (`/apply`)
4. رسالة نجاح: "تم إنشاء حسابك! بانتظار موافقة الإدارة." (للأدوار الإدارية)

### 7.3 تدفق تسجيل الدخول (Login.php)

1. التحقق من صحة البريد الإلكتروني وكلمة المرور
2. فحص `is_approved`: إذا كان `false` والدور ليس `employee` → تسجيل خروج + رسالة خطأ
3. توجيه حسب الدور:
   - `super_admin` → `/admin`
   - `hr_manager` → `/hr`
   - `project_manager` → `/pm`
   - `accountant` → `/accountant`
   - `employee` → `/employee`

### 7.4 LogoutResponse

يتم إعادة توجيه المستخدم بعد تسجيل الخروج إلى صفحة الدخول المركزية `/login` بدلاً من صفحة الدخول الخاصة باللوحة.

### 7.5 SetLocaleMiddleware

- يقرأ اللغة من الجلسة (`session('locale')`) أو من الكوكي (`filament_language_switch_locale`)
- يضبط `app()->setLocale()` لكل طلب
- مُسجل كوسيط ويب عام في `bootstrap/app.php`

---

## 8. نظام تكامل الذكاء الاصطناعي

### 8.1 البنية المعمارية

```
المستخدم/النظام → AiService → HTTP POST → LiteLLM Proxy (api.abdalgani.com)
                                                      ↓
                                              Gemini 3 Flash Preview
                                                      ↓
                                              استجابة OpenAI-compatible
```

### 8.2 إعدادات `config/ai.php`

| المفتاح | متغير البيئة | القيمة الافتراضية | الوصف |
|---|---|---|---|
| `api_url` | `AI_API_URL` | `https://api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions` | نقطة نهاية LiteLLM Proxy |
| `api_key` | `AI_API_KEY` | - | مفتاح API (يُرسل كرأس `x-litellm-api-key`) |
| `model` | `AI_MODEL` | `gemini-3-flash-preview` | معرف النموذج |
| `max_tokens` | `AI_MAX_TOKENS` | `4096` | الحد الأقصى للرموز في الاستجابة |
| `temperature` | `AI_TEMPERATURE` | `0.7` | درجة العشوائية |

### 8.3 AiService (البوابة المركزية)

| الطريقة | التوقيع | المدخلات | المخرجات | الوصف |
|---|---|---|---|---|
| `chat` | `chat(array $messages): ?array` | مصفوفة رسائل OpenAI [{role, content}] | استجابة JSON كاملة أو `null` عند الفشل | إرسال طلب للوكيل LiteLLM مع مهلة 120 ثانية |
| `processUploadedFile` | `processUploadedFile($file): ?array` | ملف مرفوع (TemporaryUploadedFile) | جزء محتوى بصيغة OpenAI Vision | معالجة الملفات: الصور/الصوت/الفيديو → base64، النصوص → محتوى نصي |
| `resolveMimeType` | `resolveMimeType($extension, $originalMime): string` | امتداد الملف، MIME الأصلي | سلسلة MIME الصحيحة | تصحيح أنواع MIME للملفات الصوتية |

### 8.4 AiEvaluationService (تقييم الموظفين)

| الطريقة | الوصف |
|---|---|
| `evaluate(Employee $employee): string` | توليد تقرير أداء شامل بالعربي بصيغة Markdown |
| `gatherEmployeeData(Employee $employee): array` | جمع بيانات الموظف: المهام (إجمالي، مكتملة، متأخرة، قيد التنفيذ)، الإجازات، التقارير |
| `buildPrompt(array $data): string` | بناء prompt عربي منظم يطلب تقييم شامل |

**هيكل التقرير المُولّد:**
1. التقييم العام
2. تحليل أداء المهام
3. نقاط القوة
4. مجالات التحسين
5. تحليل الحضور والإجازات
6. تحليل التواصل والتقارير
7. التوصيات
8. الملخص

### 8.5 ResumeAnalysisService (تحليل السير الذاتية)

| الطريقة | الوصف |
|---|---|
| `analyzeResume(array $resumeData, string $keywords, string $targetJob = ''): ?array` | تحليل السيرة الذاتية مقابل متطلبات الوظيفة |

**هيكل الاستجابة JSON:**
```json
{
    "score": 85,
    "report": "تقرير تحليلي تفصيلي",
    "strengths": ["نقطة قوة 1", "نقطة قوة 2"],
    "weaknesses": ["نقطة ضعف 1", "نقطة ضعف 2"],
    "recommendation": "accepted|conditional|rejected",
    "summary": "ملخص تنفيذي"
}
```

- يضبط `set_time_limit(180)` (3 دقائق) للاستجابات الطويلة
- يزيل علامات Markdown code fences من الاستجابة
- في حالة فشل تحليل JSON: يُرجع `{score: 0, report: raw_content, raw_response: content}`

### 8.6 PayslipPdfService (ملاحظة تحفيزية في قسائم الرواتب)

| الطريقة | الوصف |
|---|---|
| `generate(Payroll $payroll)` | توليد PDF قسيمة راتب مع ملاحظة تحفيزية من AI |

- **System Prompt:** "You are a kind HR manager who writes very short thank you and encouragement notes to employees in English."
- **User Prompt:** "Write a very short (one sentence) thank you note in English to an employee named {name} for receiving their salary for the month of {month_year}. Be friendly and motivational."
- الفشل في توليد الملاحظة لا يوقف توليد PDF (try/catch صامت)

---

## 9. نظام توليد PDF

### 9.1 DashboardExportService

| الطريقة | الوصف |
|---|---|
| `generatePdf(): mixed` | توليد تقرير PDF شامل للوحة القيادة |

**المحتوى:**
- 4 مؤشرات KPI (إجمالي الموظفين، المشاريع النشطة، المهام المكتملة، الإيرادات المدفوعة)
- بيانات 12 شهر للإيرادات والمصروفات
- آخر 15 مهمة كسجل أنشطة
- أعلى 10 موظفين بعدد المهام

**المعالجة:** جميع النصوص العربية تمر عبر `Arphp\Glyphs::utf8Glyphs()` لتصحيح العرض RTL في PDF

**الإخراج:** `response()->streamDownload()` بملف اسمه `dashboard_report_{timestamp}.pdf`

### 9.2 InvoicePdfService

| الطريقة | الوصف |
|---|---|
| `generate(Invoice $invoice)` | توليد فاتورة PDF |

**المحتوى:** بيانات العميل، بيانات المشروع، بنود الفاتورة، حسابات الضريبة (VAT)

**المعالجة:** النصوص العربية (اسم العميل، اسم الشركة، وصف البنود) عبر `Arphp\Glyphs::utf8Glyphs()`

**الإخراج:** `response()->streamDownload()` بملف اسمه `invoice_{invoice_number}.pdf`

### 9.3 PayslipPdfService

| الطريقة | الوصف |
|---|---|
| `generate(Payroll $payroll)` | توليد قسيمة راتب PDF مع ملاحظة تحفيزية |

**المحتوى:** بيانات الموظف، تفاصيل الراتب (الأساسي + البدلات - الخصومات - التأمينات - خصم الغياب = الصافي)، ملاحظة AI تحفيزية

**المعالجة:** النصوص العربية عبر `Arphp\Glyphs::utf8Glyphs()`

**الإخراج:** `response()->streamDownload()` بملف اسمه `payslip_{name}_{month_year}.pdf`

---

## 10. نظام التصدير CSV

### PayrollExport

| الطريقة | التوقيع | الوصف |
|---|---|---|
| `__construct` | `__construct(?string $monthYear = null)` | إنشاء مع فلتر شهر اختياري |
| `download` | `download(): StreamedResponse` | تصدير رواتب كملف CSV بترميز UTF-8 مع BOM |

**الأعمدة المُصدَّرة (14 عمود):**
1. اسم الموظف
2. الشهر
3. الراتب الأساسي
4. بدل السكن
5. بدل المواصلات
6. بدل الهاتف
7. المكافآت
8. نسبة التأمينات الاجتماعية
9. مبلغ التأمينات الاجتماعية
10. أيام الغياب
11. خصم الغياب
12. الخصومات
13. صافي الراتب
14. الحالة

**طريقة التصدير:** `fputcsv()` مع `cursor()` للاستعلام الفعال للذاكرة. اسم الملف: `payrolls[_{monthYear}].csv`

---

## 11. نظام الإشعارات

جميع الإشعارات تستخدم قناة `database` فقط (تخزين في جدول `notifications`). جميع النصوص باللغة العربية.

| الإشعار | العنوان | نص الرسالة | الأيقونة |
|---|---|---|---|
| **JobApplicationStatusNotification** | تحديث طلب التوظيف | تم قبول/رفض طلب التوظيف للمتقدم {name} | heroicon-o-user-plus |
| **LeaveStatusNotification** | تحديث الإجازة | تمت الموافقة/الرفض على طلب الإجازة ({type}) | heroicon-o-calendar-days |
| **ReportReceivedNotification** | تقرير جديد | استلمت تقريراً جديداً من {senderName}: {reportTitle} | heroicon-o-document-text |
| **TaskAssignedNotification** | مهمة جديدة | تم تعيين مهمة جديدة لك: {taskTitle} في مشروع {projectName} | heroicon-o-clipboard-document-check |

**هيكل البيانات:** `{title: string, body: string, icon: string}`

**تفاصيل LeaveStatusNotification:**
- `approved_by_head` → "تمت موافقة رئيس القسم على"
- `approved_by_hr` → "تمت الموافقة النهائية على"
- `rejected` → "تم رفض"

---

## 12. المتحكمات (Controllers)

### LandingController

| الطريقة | الوصف |
|---|---|
| `index()` | عرض صفحة الهبوط مع إحصائيات: موظفين نشطين، مشاريع، مهام مكتملة، عملاء |

### JobApplicationController

| الطريقة | الوصف |
|---|---|
| `index()` | فحص حالة الموظف: إذا `active` → إعادة توجيه `/employee`، إذا حالة أخرى → عرض صفحة الحالة، إذا لا يوجد سجل → عرض نموذج التقدم |
| `store(Request $request)` | إنشاء سجل موظف بحالة `pending` + رفع ملف السيرة (pdf/doc/docx, حد 2MB) + إنشاء سجل Resume → إعادة توجيه مع رسالة نجاح |

---

## 13. ويدجات لوحة الإدارة - تفاصيل إضافية

### AdminStatsOverview (ترتيب 1)
3 بطاقات إحصائية:
1. **إجمالي الموظفين** — `Employee::count()` مع sparkline `[7,3,4,5,6,count]` — أيقونة: heroicon-m-users — لون: primary
2. **المشاريع النشطة** — `Project::where('status','in_progress')->count()` — أيقونة: heroicon-m-briefcase — لون: warning
3. **إجمالي الإيرادات** — `Invoice::where('status','paid')->sum('amount')` بتنسيق عملة — أيقونة: heroicon-m-banknotes — لون: success

### AdminProjectsChart (ترتيب 2)
مخطط دائري (doughnut) يعرض توزيع المشاريع حسب الحالة (4 حالات مع ألوان مميزة)

### AdminKpiIndicators (ترتيب 3)
4 مؤشرات أداء رئيسية:
1. **معدل إنجاز المهام %** — مع اتجاه شهري (6 أشهر) — لون: success ≥ 70%, warning ≥ 40%, danger < 40%
2. **متوسط مدة المشاريع** — بالأيام للمشاريع المكتملة — لون: info
3. **رضا العملاء %** — نسبة الفواتير المدفوعة/إجمالي الفواتير — مع اتجاه شهري
4. **المهام المتأخرة** — عدد المهام المتجاوزة لتاريخ الاستحقاق — لون: danger إذا > 0

### AdminActivityFeed (ترتيب 4)
- عرض آخر 15 نشاط من 5 مصادر (مشاريع، مهام، فواتير، إجازات، موظفين)
- كل نشاط يحتوي على: type, icon, color, title, description, time

### AdminEmployeeHeatmap (ترتيب 5)
- خريطة حرارية لنشاط الموظفين خلال آخر 7 أيام
- تدرج لوني: gray (0) → green-100/900 → green-200/800 → green-400/600 → green-600/500 مع دعم الوضع الداكن

### AdminRevenueExpensesChart (ترتيب 5)
- مخطط خطي للإيرادات مقابل المصروفات خلال آخر 12 شهر
- خط الإيرادات: أخضر مع تعبئة
- خط المصروفات: أحمر مع تعبئة
- تنعيم: tension 0.4

---

## 14. نظام الترجمة (ثنائي اللغة)

### اللغات المتاحة
- **العربية (ar)** — `lang/ar/filament.php` (741 سطر) + `lang/ar/validation.php` (30 سطر)
- **الإنجليزية (en)** — `lang/en/filament.php` (741 سطر)

### مجموعات الترجمة

| المجموعة | المحتوى |
|---|---|
| `brand` | أسماء اللوحات الخمس (admin, pm, hr, employee, accountant) |
| `group` | مجموعات التنقل (employee_management, finance, projects_tasks, reports, system, leaves, tasks, attendance, training_development, resumes) |
| `nav` | عناصر التنقل (employees, departments, leaves, payrolls, projects, tasks, clients, invoices, expenses, reports, users, roles, skills, resumes, kanban_board, gantt_chart, team_calendar, my_attendance) |
| `model` | أسماء النماذج بالمفرد والجمع |
| `sections` | عناوين أقسام النماذج (employee_data, leave_data, payroll_data, salary_breakdown, allowances, invoice_items, etc.) |
| `fields` | 130+ تسمية حقل (name, email, phone, job_title, salary, status, etc.) |
| `columns` | 50+ تسمية عمود جدول |
| `status` | حالات جميع الكيانات (active, on_leave, terminated, pending, approved_by_head, approved_by_hr, rejected, paid, unpaid, overdue, todo, in_progress, review, done, etc.) |
| `leave_type` | أنواع الإجازات (Sick, Annual, Emergency) |
| `expense_category` | فئات المصروفات (salaries, operations, tools, marketing, other) |
| `validation` | رسائل تحقق مخصصة (budget_must_be_positive, end_date_after_start, checkout_after_checkin, etc.) |
| `filters` | تسميات الفلاتر حسب الحالة والمشروع والفئة والدور والأولوية والموظف |
| `actions` | 40+ تسمية إجراء (ai_evaluate, ai_analyze_resume, approve, reject, download_payslip, create_invoice, create_project_from_template, etc.) |
| `widgets` | 60+ تسمية ويدجت |
| `auth` | تسميات تسجيل الدخول والتسجيل وإعادة التعيين والتقدم للوظيفة |
| `notifications` | إشعارات (ai_analysis_complete, profile_updated, project_created_from_template, insufficient_leave_balance) |
| `attendance` | حالات الحضور (present, late, absent, half_day, رسائل تسجيل الحضور/الانصراف) |
| `training` | حالات التدريب (upcoming, ongoing, completed, enrolled, certified) |
| `gantt` | تسميات مخطط جانت (loading, no_tasks, view_task) |
| `calendar` | تسميات التقويم (loading, load_error, render_error, no_events) |
| `relation` | تسميات مديري العلاقات (project_tasks, project_team, employee_skills, client_invoices, task_comments, task_attachments, time_entries) |
| `priority` | مستويات الأولوية (low, medium, high) |
| `kanban` | تسميات لوحة كانبان (no_tasks, ai_evaluation) |

### آلية التبديل
- `FilamentLanguageSwitch` مُكوّن عالمياً عبر `AppServiceProvider::boot()`
- مرئي خارج اللوحات (على صفحات المصادقة)
- مسارات خارج اللوحات: جميع مسارات تسجيل الدخول للوحات الخمس
- **غير مسجل كإضافة داخل أي لوحة** — يظهر فقط على صفحات المصادقة
- مسار التبديل: `/lang/{locale}` — يضبط الجلسة والكوكي الدائم

### برمجات Blade المخصصة
- `@dir` → يُخرج `'rtl'` أو `'ltr'` حسب اللغة الحالية
- `@langDir` → يُخرج `'dir="rtl" lang="ar"'` أو `'dir="ltr" lang="en"'` حسب اللغة الحالية

---

## 15. AppServiceProvider

### register()
- تسجيل `LogoutResponseContract` → `LogoutResponse` كـ singleton (تخصيص إعادة توجيه تسجيل الخروج)

### boot()
- تكوين `FilamentLanguageSwitch` عالمياً (اللغات ar/en، مرئي خارج اللوحات)
- تسجيل برمجتي Blade المخصصتين: `@dir` و `@langDir`

---

## 16. صفحة الهبوط (landing.blade.php)

- ملف مستقل بـ 864 سطر HTML مع CSS و JS مدمجين
- خلفية متدرجة داكنة (`#0f172a` إلى `#1e293b`) مع كرات عائمة متحركة
- خط Google: Tajawal (عربي)
- اتجاه RTL ثابت مع `lang="ar"`
- الأقسام: شريط التنقل، البطل، الميزات (6 بطاقات)، الإحصائيات (4 عدادات متحركة)، اللوحات (5 بطاقات أدوار)، دعوة للعمل، التذييل
- إحصائيات حية من `$stats` (موظفين نشطين، مشاريع، مهام مكتملة، عملاء)
- متجاوب مع الأجهزة المحمولة مع قائمة هامبرغر
- روابط: `/login`، `/register`، `/lang/en`

---

## 17. مخططات الانسيابية الرئيسية

### تدفق الموافقة على الإجازات
```
الموظف (pending)
      ↓
رئيس القسم (approved_by_head)
      ↓                    ↓
الموارد البشرية          مرفوض (rejected)
(approved_by_hr) ✓
      ↓
مقبول نهائياً           مرفوض (rejected)
```

### تدفق التقدم للوظيفة (ATS)
```
التسجيل (/register)
      ↓
اختيار الدور (باستثناء super_admin)
      ↓
إنشاء مستخدم
      ├── إداري (hr_manager/project_manager/accountant)
      │     → is_approved = false
      │     → رسالة: "بانتظار موافقة الإدارة"
      │     → تسجيل الدخول بعد الموافقة
      │
      └── موظف (employee)
            → is_approved = true
            → تسجيل دخول تلقائي
            → إعادة توجيه لـ /apply
            → رفع السيرة الذاتية
            → إنشاء موظف بحالة 'pending'
            → تقييم AI اختياري
```

### تدفق الذكاء الاصطناعي
```
المستخدم/النظام
      ↓
AiService::chat(messages)
      ↓
HTTP POST → api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions
      ↓ (رأس: x-litellm-api-key)
نموذج Gemini 3 Flash Preview
      ↓
استجابة OpenAI-compatible (choices[0].message.content)
      ↓
├── AiEvaluationService → تقييم موظف (Markdown عربي منظم)
├── ResumeAnalysisService → تحليل سيرة (JSON: score, report, strengths, weaknesses, recommendation)
└── PayslipPdfService → ملاحظة تحفيزية (جملة إنجليزية قصيرة)
```

### تدفق توليد PDF
```
طلب المستخدم (زر في لوحة القيادة / فاتورة / قسيمة راتب)
      ↓
DashboardExportService / InvoicePdfService / PayslipPdfService
      ↓
استعلام قاعدة البيانات (مع eager loading)
      ↓
معالجة النصوص العربية عبر Arphp\Glyphs::utf8Glyphs()
      ↓
عرض قالب Blade (pdf.dashboard-report / pdf.invoice / pdf.payslip)
      ↓
DomPDF → PDF
      ↓
response()->streamDownload() → تحميل المتصفح
```

---

## 18. حساب صافي الراتب

```
net_salary = max(0,
    basic_salary
    + bonuses
    + housing_allowance
    + transport_allowance
    + phone_allowance
    - deductions
    - (basic_salary × social_insurance_rate / 100)   // التأمينات الاجتماعية
    - absence_deduction                                // خصم الغياب
)
```

حيث:
- `social_insurance_amount = basic_salary × (social_insurance_rate / 100)`
- `absence_deduction` يتم حسابه خارجياً بناءً على `absence_days`

---

## 19. ملخص تقنيات الواجهة الأمامية

| التقنية | الاستخدام |
|---|---|
| **Tailwind CSS v4** | تنسيق جميع الواجهات عبر `@tailwindcss/vite` |
| **Alpine.js** | تفاعل في صفحات الهبوط ونماذج التقدم |
| **FullCalendar** | تقويم الفريق في لوحة HR (JS + Arabic locale) |
| **Frappe-Gantt** | مخطط جانت في لوحة PM (CSS + JS) |
| **Chart.js** | مخططات الإحصائيات عبر Filament ChartWidget |
| **Livewire 3** | المكونات التفاعلية (تسجيل الدخول، التسجيل، كانبان) |
| **خط Cairo** | الخط الرئيسي لجميع اللوحات |
| **خط Tajawal** | خط صفحة الهبوط العامة |
| **Dark Glass Morphism** | تصميم صفحات المصادقة |
| **Animated Stats** | عدادات متحركة في صفحة الهبوط |

---

## 20. ملاحظات معمارية مهمة

1. **لا يوجد SoftDeletes** — لا يوجد نموذج يستخدم حذف ناعم
2. **لا يوجد UUID** — جميع المفاتيح الأساسية auto-increment
3. **لا يوجد نموذج تدقيق** — لا يوجد تتبع لتغييرات السجلات
4. **الحالات كنصوص** — جميع حقول الحالة مخزنة كنصوص (enum في قاعدة البيانات، سلاسل في النماذج) بدون إعادة تلبيس enum
5. **علاقة 1:1 صارمة** — كل دور يتوافق مع لوحة واحدة فقط في `canAccessPanel()`
6. **مديرو العلاقات المشتركة** — لوحة الإدارة تستخدم مديري علاقات من لوحات أخرى (ClientResource ← InvoicesRM من Accountant، EmployeeResource ← SkillsRM من HR، ProjectResource ← EmployeesRM+TasksRM من PM)
7. **لا يوجد API routes** — جميع المسارات هي ويب فقط (9 مسارات + مسارات Filament التلقائية)
8. **لا يوجد اختبارات مكتوبة** — مجلد `tests/` فارغ
9. **حسابات الموظفين التجريبية** — حسب `userReport.md`: 6 حسابات seeded (admin, hr, pm, accountant, emp1, emp2) بكلمة مرور `password`
10. **صفحة الهبوط مستقلة** — لا تعتمد على Vite أو مكونات Blade، HTML/CSS/JS مدمج بالكامل