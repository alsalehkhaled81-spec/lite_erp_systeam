# تقرير تحليل قاعدة البيانات الشامل — نظام Lite ERP

> هذا التقرير يحلل جميع ملفات الهجرة (Migrations) الـ **49** في المشروع، ويوثّق كل جدول وأعمدته وأنواع البيانات والعلاقات بين الجداول، مع كود جاهز لتوليد مخطط قاعدة البيانات على منصة [dbdiagram.io](https://dbdiagram.io).

---

## جدول المحتويات

1. [نظرة عامة على ملفات الهجرة](#١-نظرة-عامة-على-ملفات-الهجرة)
2. [تصنيف ملفات الهجرة](#٢-تصنيف-ملفات-الهجرة)
3. [الخط الزمني لتطوّر قاعدة البيانات](#٣-الخط-الزمني-لتطوّر-قاعدة-البيانات)
4. [الجداول النظامية (Laravel Built-in)](#٤-الجداول-النظامية-laravel-built-in)
5. [شرح مفصّل لكل جدول أعمال](#٥-شرح-مفصّل-لكل-جدول-أعمال)
6. [العلاقات (Foreign Keys) الشاملة](#٦-العلاقات-foreign-keys-الشاملة)
7. [جداول Pivot (الربط متعدد إلى متعدد)](#٧-جداول-pivot-الربط-متعدد-إلى-متعدد)
8. [تحليل سياسات الحذف (Delete cascades)](#٨-تحليل-سياسات-الحذف-delete-cascades)
9. [القيود والفهارس (Constraints & Indexes)](#٩-القيود-والفهارس-constraints--indexes)
10. [كود dbdiagram.io لتوليد مخطط القاعدة](#١٠-كود-dbdiagramio-لتوليد-مخطط-القاعدة)
11. [مخطط العلاقات النصي (ERD)](#١١-مخطط-العلاقات-النصي-erd)
12. [الإحصائيات الختامية](#١٢-الإحصائيات-الختامية)

---

## ١. نظرة عامة على ملفات الهجرة

يحتوي مجلد `database/migrations/` على **49 ملف هجرة** موزّعة على فترة تطوير تمتد من **أبريل 2026** إلى **يونيو 2026**. تتبع هذه الملفات نمط التسمية القياسي في Laravel:

```
YYYY_MM_DD_HHMMSS_description_of_change.php
```

تنقسم هذه الملفات إلى نوعين رئيسيين:

| النوع | العدد | الوصف |
|------|-------|------|
| **ملفات إنشاء جداول** (Create) | 30 ملف | تنشئ جداول جديدة في قاعدة البيانات |
| **ملفات تعديل جداول** (Alter/Add) | 19 ملف | تضيف أو تعدّل أعمدة في جداول موجودة |

---

## ٢. تصنيف ملفات الهجرة

### ٢.١ تصنيف حسب نوع العملية

#### أ) ملفات إنشاء الجداول (30 جدول)

| # | اسم الملف | الجدول المُنشأ |
|---|---------|----------------|
| 1 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` |
| 2 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` |
| 3 | `2026_04_04_104617_create_roles_table.php` | `roles` |
| 4 | `2026_04_04_104618_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` |
| 5 | `2026_04_04_104707_create_employees_table.php` | `employees` |
| 6 | `2026_04_04_104707_create_resumes_table.php` | `resumes` |
| 7 | `2026_04_04_104708_create_skills_table.php` | `skills` |
| 8 | `2026_04_04_104721_create_employee_skill_table.php` | `employee_skill` (Pivot) |
| 9 | `2026_04_04_104729_create_clients_table.php` | `clients` |
| 10 | `2026_04_04_104729_create_projects_table.php` | `projects` |
| 11 | `2026_04_04_104730_create_tasks_table.php` | `tasks` |
| 12 | `2026_04_04_104737_create_employee_project_table.php` | `employee_project` (Pivot) |
| 13 | `2026_04_04_104745_create_expenses_table.php` | `expenses` |
| 14 | `2026_04_04_104745_create_invoices_table.php` | `invoices` |
| 15 | `2026_05_06_100001_create_departments_table.php` | `departments` |
| 16 | `2026_05_06_100003_create_reports_table.php` | `reports` |
| 17 | `2026_05_06_100004_create_leaves_table.php` | `leaves` |
| 18 | `2026_05_06_100005_create_payrolls_table.php` | `payrolls` |
| 19 | `2026_05_06_123850_create_notifications_table.php` | `notifications` |
| 20 | `2026_06_06_115208_create_task_comments_table.php` | `task_comments` |
| 21 | `2026_06_06_115209_create_task_attachments_table.php` | `task_attachments` |
| 22 | `2026_06_06_115211_create_project_templates_table.php` | `project_templates` |
| 23 | `2026_06_06_115211_create_time_entries_table.php` | `time_entries` |
| 24 | `2026_06_06_115213_create_task_templates_table.php` | `task_templates` |
| 25 | `2026_06_06_121935_create_attendances_table.php` | `attendances` |
| 26 | `2026_06_06_121936_create_trainings_table.php` | `trainings` |
| 27 | `2026_06_06_121937_create_employee_training_table.php` | `employee_training` (Pivot) |
| 28 | `2026_06_06_121938_create_certificates_table.php` | `certificates` |
| 29 | `2026_06_06_121938_create_invoice_items_table.php` | `invoice_items` |
| 30 | `2026_06_06_121939_create_career_plans_table.php` | `career_plans` |
| 31 | `2026_06_25_160000_create_vacancies_table.php` | `vacancies` |

#### ب) ملفات تعديل الجداول (19 ملف)

| # | اسم الملف | الجدول المُعدّل | نوع التعديل |
|---|---------|----------------|------------|
| 1 | `2026_05_02_115743_alter_employees_table_for_applications.php` | `employees` | تعديل ENUM للحالة + إضافة `rejection_reason` |
| 2 | `2026_05_06_100002_add_department_id_to_employees_table.php` | `employees` | إضافة `department_id` + FK |
| 3 | `2026_06_02_182432_add_priority_to_tasks_table.php` | `tasks` | إضافة `priority` |
| 4 | `2026_06_02_220000_add_profile_photo_to_users_table.php` | `users` | إضافة `profile_photo_path` |
| 5 | `2026_06_02_230000_add_is_approved_to_users_table.php` | `users` | إضافة `is_approved` + تحديث البيانات |
| 6 | `2026_06_06_115158_add_start_date_to_tasks_table.php` | `tasks` | إضافة `start_date` |
| 7 | `2026_06_06_121934_add_leave_balance_to_employees_table.php` | `employees` | إضافة `annual_leave_balance` + `used_leave_days` |
| 8 | `2026_06_06_121939_add_project_and_approval_to_expenses_table.php` | `expenses` | إضافة `project_id` + `status` + `approved_by` |
| 9 | `2026_06_06_121939_add_vat_to_invoices_table.php` | `invoices` | إضافة `vat_rate` + `vat_amount` + `total_with_vat` |
| 10 | `2026_06_06_121940_add_allowances_to_payrolls_table.php` | `payrolls` | إضافة 7 أعمدة بدلات وخصومات |
| 11 | `2026_06_14_173823_add_sort_order_to_tasks_table.php` | `tasks` | إضافة `sort_order` + تعبئة البيانات |
| 12 | `2026_06_15_092346_make_employee_id_nullable_in_tasks_table.php` | `tasks` | جعل `employee_id` nullable |
| 13 | `2026_06_24_000000_add_password_to_clients_table.php` | `clients` | إضافة `password` + `is_active` + `remember_token` |
| 14 | `2026_06_25_160100_add_vacancy_id_to_employees_table.php` | `employees` | إضافة `vacancy_id` + FK |
| 15 | `2026_06_25_160200_add_ai_columns_to_resumes_table.php` | `resumes` | إضافة 5 أعمدة ذكاء اصطناعي |
| 16 | `2026_06_26_165244_add_password_and_profile_photo_to_clients_table.php` | `clients` | إضافة `profile_photo_path` + جعل `password` nullable |
| 17 | `2026_06_28_121223_alter_status_in_attendances_table.php` | `attendances` | تعديل ENUM للحالة (إضافة `over_time`) |
| 18 | `2026_06_28_121547_add_overtime_hours_to_attendances_table.php` | `attendances` | إضافة `overtime_hours` |

---

## ٣. الخط الزمني لتطوّر قاعدة البيانات

```
أبريل 2026 (المرحلة الأولى — النواة الأساسية)
├── roles, users, employees, resumes, skills
├── employee_skill (pivot)
├── clients, projects, tasks
├── employee_project (pivot)
├── expenses, invoices
└── جداول النظام: cache, jobs, sessions

مايو 2026 (المرحلة الثانية — التوسّع الإداري)
├── تعديل employees (دعم طلبات التوظيف)
├── departments + ربطها بالموظفين
├── reports (التواصل الداخلي)
├── leaves (الإجازات)
├── payrolls (الرواتب)
└── notifications (الإشعارات)

يونيو 2026 (المرحلة الثالثة — الإثراء والذكاء الاصطناعي)
├── tasks: priority, start_date, sort_order, employee_id nullable
├── users: profile_photo, is_approved
├── task_comments, task_attachments, time_entries
├── project_templates, task_templates
├── attendances (مع overtime_hours)
├── trainings, employee_training, certificates
├── career_plans
├── invoices: أعمدة الضريبة (VAT)
├── expenses: الموافقات والمشاريع
├── payrolls: البدلات والتأمينات
├── clients: كلمة المرور والصورة (بوابة العميل)
├── vacancies (الوظائف الشاغرة)
└── resumes: أعمدة الذكاء الاصطناعي (AI)
```

---

## ٤. الجداول النظامية (Laravel Built-in)

هذه الجداول ينشئها Laravel افتراضياً لدعم وظائفه الداخلية ولا تمثّل بيانات أعمال مباشرة:

### ٤.١ جدول `cache` و `cache_locks`
| العمود | النوع | الوصف |
|--------|------|------|
| `key` | varchar (PK) | مفتاح التخزين المؤقت |
| `value` | mediumText | القيمة المخزّنة |
| `expiration` | integer (index) | وقت انتهاء الصلاحية |

> `cache_locks`: جدول منفصل للأقفال (Locks) لمنع التضارب في العمليات المتزامنة.

### ٤.٢ جدول `jobs` و `job_batches` و `failed_jobs`
يدعم نظام الطوابير (Queue) في Laravel لتأجيل المهام الثقيلة (مثل إرسال الإيميلات).

### ٤.٣ جدول `password_reset_tokens`
| العمود | النوع | الوصف |
|--------|------|------|
| `email` | varchar (PK) | بريد المستخدم |
| `token` | varchar | توكن إعادة التعيين |
| `created_at` | timestamp | تاريخ الإنشاء |

### ٤.٤ جدول `sessions`
| العمود | النوع | الوصف |
|--------|------|------|
| `id` | varchar (PK) | معرّف الجلسة |
| `user_id` | bigint (nullable, index) | المستخدم المرتبط |
| `ip_address` | varchar(45) | عنوان IP |
| `user_agent` | text | معلومات المتصفح |
| `payload` | longText | بيانات الجلسة |
| `last_activity` | integer (index) | آخر نشاط |

### ٤.٥ جدول `notifications`
| العمود | النوع | الوصف |
|--------|------|------|
| `id` | uuid (PK) | معرّف فريد (UUID) |
| `type` | varchar | نوع الإشعار (اسم الكلاس) |
| `notifiable_type` | varchar | نوع الكيان المُشعَر (Morph) |
| `notifiable_id` | bigint | معرّف الكيان المُشعَر (Morph) |
| `data` | text | بيانات الإشعار (JSON) |
| `read_at` | timestamp (nullable) | وقت القراءة |
| `created_at`, `updated_at` | timestamps | التواريخ |

> يستخدم نمط **Polymorphic Relationship** عبر `morphs('notifiable')` لدعم الإشعارات لأي نموذج (User، Client، إلخ).

---

## ٥. شرح مفصّل لكل جدول أعمال

### ٥.١ جدول `roles` — الأدوار والصلاحيات

**الملف:** `2026_04_04_104617_create_roles_table.php`
**الغرض:** تخزين الأدوار الوظيفية في النظام (super_admin, hr_manager, project_manager, accountant, employee).

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `name` | varchar | NOT NULL | — | UNIQUE | اسم الدور (فريد) |
| `description` | text | NULL | NULL | — | وصف الدور |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**العلاقات الصادرة:** `users.role_id` ← `roles.id` (علاقة واحد-إلى-متعدد)

---

### ٥.٢ جدول `users` — المستخدمون

**الملفات:**
- `2026_04_04_104618_create_users_table.php` (الإنشاء)
- `2026_06_02_220000_add_profile_photo_to_users_table.php` (إضافة الصورة)
- `2026_06_02_230000_add_is_approved_to_users_table.php` (إضافة الموافقة)

**الغرض:** حسابات دخول جميع مستخدمي النظام (المدير، HR، PM، المحاسب، الموظف).

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `role_id` | bigint (unsigned) | NULL | NULL | FK → `roles.id`, nullOnDelete | الدور الوظيفي |
| `name` | varchar | NOT NULL | — | — | الاسم الكامل |
| `email` | varchar | NOT NULL | — | UNIQUE | البريد الإلكتروني |
| `profile_photo_path` | varchar | NULL | NULL | — | مسار الصورة الشخصية |
| `is_approved` | boolean | NOT NULL | `false` | — | هل تمت الموافقة على الحساب |
| `email_verified_at` | timestamp | NULL | NULL | — | وقت تأكيد البريد |
| `password` | varchar | NOT NULL | — | — | كلمة المرور (مشفّرة bcrypt) |
| `remember_token` | varchar(100) | NULL | NULL | — | توكن "تذكرني" |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

> **ملاحظة هامة:** ملف `add_is_approved` يقوم أيضاً بتحديث بيانات المستخدمين الموجودين، حيث يوافق تلقائياً على حسابات `super_admin`.

**العلاقات:**
- الواردة: `role_id` → `roles.id`
- الصادرة: `employees.user_id`, `expenses.user_id`, `expenses.approved_by`, `task_comments.user_id`, `task_attachments.user_id`, `vacancies.created_by` ← `users.id`

---

### ٥.٣ جدول `employees` — الموظفون

**الملفات (4 ملفات تعديل + 1 إنشاء):**
- `2026_04_04_104707_create_employees_table.php` (الإنشاء الأساسي)
- `2026_05_02_115743_alter_employees_table_for_applications.php` (دعم التوظيف)
- `2026_05_06_100002_add_department_id_to_employees_table.php` (القسم)
- `2026_06_06_121934_add_leave_balance_to_employees_table.php` (أرصدة الإجازات)
- `2026_06_25_160100_add_vacancy_id_to_employees_table.php` (الوظيفة المتقدم لها)

**الغرض:** البيانات الوظيفية لكل موظف. هذا الجدول هو **محور العلاقات الرئيسي** في النظام، حيث تتفرّع منه 14 علاقة.

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `user_id` | bigint (unsigned) | NOT NULL | — | FK → `users.id`, UNIQUE, cascadeOnDelete | حساب المستخدم المرتبط (1:1) |
| `department_id` | bigint (unsigned) | NULL | NULL | FK → `departments.id`, nullOnDelete | القسم |
| `vacancy_id` | bigint (unsigned) | NULL | NULL | FK → `vacancies.id`, nullOnDelete | الوظيفة الشاغرة المتقدّم لها |
| `job_title` | varchar | NULL | NULL | — | المسمى الوظيفي |
| `salary` | decimal(10,2) unsigned | NULL | NULL | — | الراتب |
| `status` | ENUM | NOT NULL | `'pending'` | — | حالة الموظف |
| `rejection_reason` | text | NULL | NULL | — | سبب الرفض (للمتقدمين) |
| `hire_date` | date | NULL | NULL | — | تاريخ التعيين |
| `annual_leave_balance` | integer | NOT NULL | `21` | — | رصيد الإجازات السنوي |
| `used_leave_days` | integer | NOT NULL | `0` | — | الأيام المستخدمة |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):**
- `pending` — قيد المراجعة (للمتقدمين الجدد)
- `active` — على رأس العمل
- `on_leave` — في إجازة
- `terminated` — منتهي الخدمة
- `rejected` — مرفوض (طلب التوظيف)

> **تطوّر التصميم:** الجدول بدأ بـ 3 حالات فقط (`active`, `on_leave`, `terminated`)، ثم تمّ توسيعه في `alter_employees_table_for_applications` لإضافة `pending` و `rejected` لدعم دورة التوظيف الكاملة.

**العلاقات الصادرة (14 علاقة):**
`resumes`, `tasks`, `leaves`, `payrolls`, `attendances`, `reports (sender)`, `reports (receiver)`, `certificates`, `career_plans`, `time_entries`, `employee_skill`, `employee_project`, `employee_training`, `departments.head_id`

---

### ٥.٤ جدول `resumes` — السير الذاتية

**الملفات:**
- `2026_04_04_104707_create_resumes_table.php` (الإنشاء)
- `2026_06_25_160200_add_ai_columns_to_resumes_table.php` (أعمدة الذكاء الاصطناعي)

**الغرض:** تخزين السير الذاتية للموظفين/المتقدمين، النص المستخرج، ونتائج التحليل بالذكاء الاصطناعي.

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, UNIQUE, cascadeOnDelete | الموظف المرتبط (1:1) |
| `file_path` | varchar | NULL | NULL | — | مسار ملف السيرة الذاتية |
| `resume_text` | longText | NULL | NULL | — | النص المستخرج للتحليل |
| `ai_score` | integer | NULL | NULL | — | تقييم الذكاء الاصطناعي (0-100) |
| `ai_summary` | longText | NULL | NULL | — | الملخص التنفيذي |
| `ai_report` | longText | NULL | NULL | — | التقرير المفصّل |
| `ai_recommendation` | varchar | NULL | NULL | — | التوصية (مقبول/مشروط/مرفوض) |
| `analyzed_at` | timestamp | NULL | NULL | — | وقت إجراء التحليل |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٥ جدول `skills` — المهارات

**الملف:** `2026_04_04_104708_create_skills_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `name` | varchar | NOT NULL | — | UNIQUE | اسم المهارة (فريد) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٦ جدول `departments` — الأقسام

**الملف:** `2026_05_06_100001_create_departments_table.php`

**الغرض:** الأقسام الإدارية في الشركة. يتميّز بعلاقة مرجعية ذاتية (Self-Referencing) عبر `head_id` الذي يشير إلى `employees.id`.

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `name` | varchar | NOT NULL | — | — | اسم القسم |
| `head_id` | bigint (unsigned) | NULL | NULL | FK → `employees.id`, nullOnDelete | رئيس القسم (موظف) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

> **علاقة دائرية (Circular Reference):** `departments.head_id` → `employees.id` و `employees.department_id` → `departments.id`. هذا تصميم مقصود لتمثيل العلاقة الحقيقية بين الأقسام ورؤسائها.

---

### ٥.٧ جدول `clients` — العملاء

**الملفات:**
- `2026_04_04_104729_create_clients_table.php` (الإنشاء)
- `2026_06_24_000000_add_password_to_clients_table.php` (كلمة المرور والتفعيل)
- `2026_06_26_165244_add_password_and_profile_photo_to_clients_table.php` (الصورة)

**الغرض:** بيانات العملاء الخارجيين. يتميّز بأنه يمتلك نظام مصادقة مستقل (Auth Guard منفصل) لدخول بوابة العميل.

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `name` | varchar | NOT NULL | — | — | اسم العميل/المندوب |
| `company_name` | varchar | NULL | NULL | — | اسم الشركة |
| `email` | varchar | NULL | NULL | UNIQUE | البريد الإلكتروني |
| `password` | varchar | NULL | NULL | — | كلمة المرور (مشفّرة) |
| `is_active` | boolean | NOT NULL | `true` | — | حالة تفعيل الحساب |
| `profile_photo_path` | varchar | NULL | NULL | — | مسار الصورة |
| `remember_token` | varchar(100) | NULL | NULL | — | توكن "تذكرني" |
| `phone` | varchar | NULL | NULL | — | رقم الهاتف |
| `address` | text | NULL | NULL | — | العنوان |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٨ جدول `projects` — المشاريع

**الملف:** `2026_04_04_104729_create_projects_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `client_id` | bigint (unsigned) | NULL | NULL | FK → `clients.id`, nullOnDelete | العميل صاحب المشروع |
| `name` | varchar | NOT NULL | — | — | اسم المشروع |
| `description` | text | NULL | NULL | — | وصف المشروع |
| `budget` | decimal(12,2) unsigned | NULL | NULL | — | الميزانية |
| `start_date` | date | NULL | NULL | — | تاريخ البداية |
| `end_date` | date | NULL | NULL | — | تاريخ النهاية |
| `status` | ENUM | NOT NULL | `'pending'` | — | حالة المشروع |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `pending` (قيد الانتظار), `in_progress` (قيد التنفيذ), `completed` (مكتمل), `canceled` (ملغى)

---

### ٥.٩ جدول `tasks` — المهام

**الملفات (1 إنشاء + 4 تعديلات):**
- `2026_04_04_104730_create_tasks_table.php` (الإنشاء)
- `2026_06_02_182432_add_priority_to_tasks_table.php` (الأولوية)
- `2026_06_06_115158_add_start_date_to_tasks_table.php` (تاريخ البداية)
- `2026_06_14_173823_add_sort_order_to_tasks_table.php` (ترتيب كانبان + تعبئة البيانات)
- `2026_06_15_092346_make_employee_id_nullable_in_tasks_table.php` (جعل الموظف nullable)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `project_id` | bigint (unsigned) | NOT NULL | — | FK → `projects.id`, cascadeOnDelete | المشروع المرتبط |
| `employee_id` | bigint (unsigned) | NULL | NULL | FK → `employees.id`, cascadeOnDelete | الموظف المسؤول |
| `title` | varchar | NOT NULL | — | — | عنوان المهمة |
| `description` | text | NULL | NULL | — | وصف المهمة |
| `start_date` | date | NULL | NULL | — | تاريخ البداية |
| `due_date` | date | NULL | NULL | — | تاريخ التسليم |
| `status` | ENUM | NOT NULL | `'todo'` | — | حالة المهمة |
| `priority` | ENUM | NOT NULL | `'medium'` | — | الأولوية |
| `sort_order` | integer | NOT NULL | `0` | — | ترتيب العرض (كانبان) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `todo` (مطلوبة), `in_progress` (قيد التنفيذ), `review` (للمراجعة), `done` (منتهية)
**قيم ENUM للأولوية (`priority`):** `low` (منخفضة), `medium` (متوسطة), `high` (عالية)

> **ملاحظة تصميمية:** في الأصل كان `employee_id` إجبارياً، ثم جُعل nullable في `2026_06_15` للسماح بإنشاء مهام غير مُسندة بعد (مثل المهام في مرحلة التخطيط).

---

### ٥.١٠ جدول `task_comments` — تعليقات المهام

**الملف:** `2026_06_06_115208_create_task_comments_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `task_id` | bigint (unsigned) | NOT NULL | — | FK → `tasks.id`, cascadeOnDelete | المهمة المرتبطة |
| `user_id` | bigint (unsigned) | NOT NULL | — | FK → `users.id`, cascadeOnDelete | كاتب التعليق |
| `comment` | text | NOT NULL | — | — | نص التعليق |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.١١ جدول `task_attachments` — مرفقات المهام

**الملف:** `2026_06_06_115209_create_task_attachments_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `task_id` | bigint (unsigned) | NOT NULL | — | FK → `tasks.id`, cascadeOnDelete | المهمة المرتبطة |
| `user_id` | bigint (unsigned) | NOT NULL | — | FK → `users.id`, cascadeOnDelete | رافع الملف |
| `file_name` | varchar | NOT NULL | — | — | اسم الملف |
| `file_path` | varchar | NOT NULL | — | — | مسار التخزين |
| `file_type` | varchar | NULL | NULL | — | نوع MIME |
| `file_size` | bigint (unsigned) | NULL | NULL | — | الحجم بالبايت |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.١٢ جدول `time_entries` — تتبع الوقت

**الملف:** `2026_06_06_115211_create_time_entries_table.php`

**الغرض:** تسجيل ساعات العمل الفعلية على كل مهمة لكل موظف.

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `task_id` | bigint (unsigned) | NOT NULL | — | FK → `tasks.id`, cascadeOnDelete | المهمة |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `date` | date | NOT NULL | — | — | التاريخ |
| `hours` | decimal(5,2) | NOT NULL | `0` | — | عدد الساعات |
| `description` | text | NULL | NULL | — | وصف العمل المنجز |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.١٣ جدول `invoices` — الفواتير

**الملفات:**
- `2026_04_04_104745_create_invoices_table.php` (الإنشاء)
- `2026_06_06_121939_add_vat_to_invoices_table.php` (الضريبة)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `client_id` | bigint (unsigned) | NOT NULL | — | FK → `clients.id`, cascadeOnDelete | العميل |
| `project_id` | bigint (unsigned) | NULL | NULL | FK → `projects.id`, nullOnDelete | المشروع (اختياري) |
| `invoice_number` | varchar | NOT NULL | — | UNIQUE | رقم الفاتورة (فريد) |
| `amount` | decimal(12,2) unsigned | NOT NULL | — | — | المبلغ الأساسي |
| `vat_rate` | decimal(5,2) | NOT NULL | `0` | — | نسبة الضريبة % |
| `vat_amount` | decimal(12,2) | NOT NULL | `0` | — | مبلغ الضريبة |
| `total_with_vat` | decimal(12,2) | NOT NULL | `0` | — | الإجمالي شامل الضريبة |
| `issue_date` | date | NULL | NULL | — | تاريخ الإصدار |
| `due_date` | date | NULL | NULL | — | تاريخ الاستحقاق |
| `status` | ENUM | NOT NULL | `'unpaid'` | — | حالة الفاتورة |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `unpaid` (غير مدفوعة), `paid` (مدفوعة), `overdue` (متأخرة)

---

### ٥.١٤ جدول `invoice_items` — بنود الفواتير

**الملف:** `2026_06_06_121938_create_invoice_items_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `invoice_id` | bigint (unsigned) | NOT NULL | — | FK → `invoices.id`, cascadeOnDelete | الفاتورة |
| `description` | varchar | NOT NULL | — | — | وصف البند |
| `quantity` | decimal(10,2) | NOT NULL | `1` | — | الكمية |
| `unit_price` | decimal(12,2) | NOT NULL | `0` | — | سعر الوحدة |
| `total` | decimal(12,2) | NOT NULL | `0` | — | الإجمالي (الكمية × السعر) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.١٥ جدول `expenses` — المصروفات

**الملفات:**
- `2026_04_04_104745_create_expenses_table.php` (الإنشاء)
- `2026_06_06_121939_add_project_and_approval_to_expenses_table.php` (الموافقات)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `user_id` | bigint (unsigned) | NOT NULL | — | FK → `users.id`, cascadeOnDelete | المسجِّل |
| `project_id` | bigint (unsigned) | NULL | NULL | FK → `projects.id`, nullOnDelete | المشروع المرتبط |
| `title` | varchar | NOT NULL | — | — | عنوان المصروف |
| `category` | varchar | NULL | NULL | — | التصنيف |
| `amount` | decimal(12,2) unsigned | NOT NULL | — | — | المبلغ |
| `expense_date` | date | NULL | NULL | — | تاريخ المصروف |
| `receipt_url` | varchar | NULL | NULL | — | مسار صورة الإيصال |
| `status` | ENUM | NOT NULL | `'pending'` | — | حالة المصروف |
| `approved_by` | bigint (unsigned) | NULL | NULL | FK → `users.id`, nullOnDelete | المعتمِد |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `pending` (قيد الانتظار), `approved` (معتمد), `rejected` (مرفوض)

---

### ٥.١٦ جدول `reports` — التقارير الداخلية

**الملف:** `2026_05_06_100003_create_reports_table.php`

**الغرض:** نظام تقارير داخلي بين الموظفين (مرسل ← مستقبل).

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `sender_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | المرسل |
| `receiver_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | المستقبل |
| `title` | varchar | NOT NULL | — | — | عنوان التقرير |
| `content` | text | NOT NULL | — | — | المحتوى |
| `feedback` | text | NULL | NULL | — | الرد/التغذية الراجعة |
| `status` | ENUM | NOT NULL | `'unread'` | — | حالة التقرير |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `unread` (غير مقروء), `read` (مقروء), `replied` (تم الرد)

> **ملاحظة:** الجدول يحتوي على علاقتين指向 نفس الجدول `employees` (Self-Referencing إلى نفس الجدول المستهدف)، باستخدام `sender_id` و `receiver_id`.

---

### ٥.١٧ جدول `leaves` — الإجازات

**الملف:** `2026_05_06_100004_create_leaves_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `type` | varchar | NOT NULL | — | — | نوع الإجازة (Annual/Sick/Emergency) |
| `start_date` | date | NOT NULL | — | — | تاريخ البداية |
| `end_date` | date | NOT NULL | — | — | تاريخ النهاية |
| `reason` | text | NOT NULL | — | — | سبب الإجازة |
| `status` | ENUM | NOT NULL | `'pending'` | — | حالة الطلب |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`) ومراحل الموافقة:**
```
pending → approved_by_head → approved_by_hr  (سلسلة موافقات من مستويين)
       ↘ rejected
```

> التصميم يدعم **سلسلة موافقات متعددة المستويات**: أولاً رئيس القسم، ثم الموارد البشرية.

---

### ٥.١٨ جدول `payrolls` — مسيرات الرواتب

**الملفات:**
- `2026_05_06_100005_create_payrolls_table.php` (الإنشاء)
- `2026_06_06_121940_add_allowances_to_payrolls_table.php` (البدلات والتأمينات)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `month_year` | varchar | NOT NULL | — | — | الشهر/السنة (مثل: 2026-06) |
| `basic_salary` | decimal(10,2) unsigned | NOT NULL | — | — | الراتب الأساسي |
| `bonuses` | decimal(10,2) unsigned | NOT NULL | `0` | — | المكافآت |
| `deductions` | decimal(10,2) unsigned | NOT NULL | `0` | — | الخصومات |
| `housing_allowance` | decimal(10,2) | NOT NULL | `0` | — | بدل السكن |
| `transport_allowance` | decimal(10,2) | NOT NULL | `0` | — | بدل النقل |
| `phone_allowance` | decimal(10,2) | NOT NULL | `0` | — | بدل الهاتف |
| `social_insurance_rate` | decimal(5,2) | NOT NULL | `0` | — | نسبة التأمينات % |
| `social_insurance_amount` | decimal(10,2) | NOT NULL | `0` | — | مبلغ التأمينات |
| `absence_days` | integer | NOT NULL | `0` | — | أيام الغياب |
| `absence_deduction` | decimal(10,2) | NOT NULL | `0` | — | خصم الغياب |
| `net_salary` | decimal(10,2) unsigned | NOT NULL | — | — | صافي الراتب |
| `status` | varchar | NOT NULL | `'unpaid'` | — | الحالة (paid/unpaid) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

> **معادلة حساب صافي الراتب:**
> `net = basic + bonuses + (housing + transport + phone) - deductions - insurance_amount - absence_deduction`

---

### ٥.١٩ جدول `attendances` — الحضور والانصراف

**الملفات:**
- `2026_06_06_121935_create_attendances_table.php` (الإنشاء)
- `2026_06_28_121223_alter_status_in_attendances_table.php` (إضافة حالة over_time)
- `2026_06_28_121547_add_overtime_hours_to_attendances_table.php` (الساعات الإضافية)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `date` | date | NOT NULL | — | — | التاريخ |
| `check_in` | dateTime | NULL | NULL | — | وقت الحضور |
| `check_out` | dateTime | NULL | NULL | — | وقت الانصراف |
| `hours_worked` | decimal(5,2) | NOT NULL | `0` | — | ساعات العمل |
| `overtime_hours` | decimal(5,2) | NOT NULL | `0` | — | الساعات الإضافية |
| `status` | ENUM | NOT NULL | `'present'` | — | حالة الحضور |
| `notes` | text | NULL | NULL | — | ملاحظات |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `present` (حاضر), `late` (متأخر), `absent` (غائب), `half_day` (نصف يوم), `over_time` (وقت إضافي)

**القيود الفريدة:** `UNIQUE(employee_id, date)` — لا يمكن وجود سجلّين لنفس الموظف في نفس اليوم.

---

### ٥.٢٠ جدول `vacancies` — الوظائف الشاغرة

**الملف:** `2026_06_25_160000_create_vacancies_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `title` | varchar | NOT NULL | — | — | عنوان الوظيفة |
| `description` | longText | NULL | NULL | — | وصف الوظيفة |
| `requirements` | longText | NULL | NULL | — | المتطلبات (كلمات مفتاحية للـ AI) |
| `location` | varchar | NULL | NULL | — | الموقع |
| `employment_type` | varchar | NOT NULL | `'full_time'` | — | نوع التوظيف |
| `salary_min` | decimal(10,2) unsigned | NULL | NULL | — | الحد الأدنى للراتب |
| `salary_max` | decimal(10,2) unsigned | NULL | NULL | — | الحد الأعلى للراتب |
| `department_id` | bigint (unsigned) | NULL | NULL | FK → `departments.id`, nullOnDelete | القسم |
| `status` | varchar | NOT NULL | `'open'` | — | الحالة (open/closed) |
| `positions_count` | integer | NOT NULL | `1` | — | عدد الشواغر |
| `created_by` | bigint (unsigned) | NULL | NULL | FK → `users.id`, nullOnDelete | منشئ الوظيفة |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٢١ جدول `trainings` — الدورات التدريبية

**الملف:** `2026_06_06_121936_create_trainings_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `title` | varchar | NOT NULL | — | — | عنوان التدريب |
| `description` | text | NULL | NULL | — | الوصف |
| `trainer` | varchar | NULL | NULL | — | المدرب/الجهة |
| `start_date` | date | NOT NULL | — | — | تاريخ البداية |
| `end_date` | date | NOT NULL | — | — | تاريخ النهاية |
| `status` | ENUM | NOT NULL | `'upcoming'` | — | حالة التدريب |
| `location` | varchar | NULL | NULL | — | الموقع |
| `max_participants` | integer | NULL | NULL | — | الحد الأقصى للمشاركين |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `upcoming` (قادم), `ongoing` (جاري), `completed` (مكتمل)

---

### ٥.٢٢ جدول `certificates` — الشهادات

**الملف:** `2026_06_06_121938_create_certificates_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `title` | varchar | NOT NULL | — | — | اسم الشهادة |
| `issuer` | varchar | NOT NULL | — | — | الجهة المانحة |
| `issue_date` | date | NOT NULL | — | — | تاريخ الإصدار |
| `expiry_date` | date | NULL | NULL | — | تاريخ الانتهاء |
| `certificate_url` | varchar | NULL | NULL | — | مسار ملف الشهادة |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٢٣ جدول `career_plans` — خطط التطوير الوظيفي

**الملف:** `2026_06_06_121939_create_career_plans_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `employee_id` | bigint (unsigned) | NOT NULL | — | FK → `employees.id`, cascadeOnDelete | الموظف |
| `current_role` | varchar | NOT NULL | — | — | الدور الحالي |
| `target_role` | varchar | NOT NULL | — | — | الدور المستهدف |
| `timeline_months` | integer | NOT NULL | — | — | المدة الزمنية (أشهر) |
| `required_skills` | text | NULL | NULL | — | المهارات المطلوبة |
| `notes` | text | NULL | NULL | — | ملاحظات |
| `status` | ENUM | NOT NULL | `'draft'` | — | حالة الخطة |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

**قيم ENUM للحالة (`status`):** `draft` (مسودة), `active` (نشط), `completed` (مكتمل)

---

### ٥.٢٤ جدول `project_templates` — قوالب المشاريع

**الملف:** `2026_06_06_115211_create_project_templates_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `name` | varchar | NOT NULL | — | — | اسم القالب |
| `description` | text | NULL | NULL | — | الوصف |
| `budget` | decimal(12,2) unsigned | NULL | NULL | — | الميزانية التقديرية |
| `estimated_days` | integer (unsigned) | NULL | NULL | — | المدة التقديرية (أيام) |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

### ٥.٢٥ جدول `task_templates` — قوالب المهام

**الملف:** `2026_06_06_115213_create_task_templates_table.php`

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint (unsigned) | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | المعرّف الفريد |
| `project_template_id` | bigint (unsigned) | NOT NULL | — | FK → `project_templates.id`, cascadeOnDelete | قالب المشروع الأب |
| `title` | varchar | NOT NULL | — | — | عنوان المهمة |
| `description` | text | NULL | NULL | — | الوصف |
| `priority` | ENUM | NOT NULL | `'medium'` | — | الأولوية |
| `estimated_hours` | integer (unsigned) | NULL | NULL | — | الساعات التقديرية |
| `sort_order` | integer (unsigned) | NOT NULL | `0` | — | ترتيب العرض |
| `created_at` | timestamp | NULL | NULL | — | تاريخ الإنشاء |
| `updated_at` | timestamp | NULL | NULL | — | تاريخ آخر تعديل |

---

## ٦. العلاقات (Foreign Keys) الشاملة

يحتوي النظام على **39 علاقة مفتاح أجنبي** موزّعة كالتالي:

### ٦.١ العلاقات حسب نوعها

| # | من (Column) | إلى (Table.Column) | النوع | سياسة الحذف | الوصف |
|---|------------|-------------------|------|------------|------|
| 1 | `users.role_id` | `roles.id` | N:1 | nullOnDelete | المستخدم ينتمي لدور |
| 2 | `employees.user_id` | `users.id` | 1:1 | cascadeOnDelete | الموظف مرتبط بمستخدم |
| 3 | `employees.department_id` | `departments.id` | N:1 | nullOnDelete | الموظف في قسم |
| 4 | `employees.vacancy_id` | `vacancies.id` | N:1 | nullOnDelete | الموظف متقدم لوظيفة |
| 5 | `departments.head_id` | `employees.id` | N:1 | nullOnDelete | رئيس القسم (مرجعية ذاتية) |
| 6 | `resumes.employee_id` | `employees.id` | 1:1 | cascadeOnDelete | السيرة الذاتية للموظف |
| 7 | `employee_skill.employee_id` | `employees.id` | M:N | cascadeOnDelete | (Pivot) مهارات الموظف |
| 8 | `employee_skill.skill_id` | `skills.id` | M:N | cascadeOnDelete | (Pivot) موظفو المهارة |
| 9 | `projects.client_id` | `clients.id` | N:1 | nullOnDelete | مشروع لعميل |
| 10 | `employee_project.project_id` | `projects.id` | M:N | cascadeOnDelete | (Pivot) موظفو المشروع |
| 11 | `employee_project.employee_id` | `employees.id` | M:N | cascadeOnDelete | (Pivot) مشاريع الموظف |
| 12 | `tasks.project_id` | `projects.id` | N:1 | cascadeOnDelete | مهمة في مشروع |
| 13 | `tasks.employee_id` | `employees.id` | N:1 | cascadeOnDelete | مهمة لموظف (nullable) |
| 14 | `task_comments.task_id` | `tasks.id` | N:1 | cascadeOnDelete | تعليق على مهمة |
| 15 | `task_comments.user_id` | `users.id` | N:1 | cascadeOnDelete | كاتب التعليق |
| 16 | `task_attachments.task_id` | `tasks.id` | N:1 | cascadeOnDelete | مرفق على مهمة |
| 17 | `task_attachments.user_id` | `users.id` | N:1 | cascadeOnDelete | رافع المرفق |
| 18 | `time_entries.task_id` | `tasks.id` | N:1 | cascadeOnDelete | وقت على مهمة |
| 19 | `time_entries.employee_id` | `employees.id` | N:1 | cascadeOnDelete | وقت لموظف |
| 20 | `invoices.client_id` | `clients.id` | N:1 | cascadeOnDelete | فاتورة لعميل |
| 21 | `invoices.project_id` | `projects.id` | N:1 | nullOnDelete | فاتورة لمشروع (اختياري) |
| 22 | `invoice_items.invoice_id` | `invoices.id` | N:1 | cascadeOnDelete | بند في فاتورة |
| 23 | `expenses.user_id` | `users.id` | N:1 | cascadeOnDelete | مسجِّل المصروف |
| 24 | `expenses.project_id` | `projects.id` | N:1 | nullOnDelete | مصروف لمشروع (اختياري) |
| 25 | `expenses.approved_by` | `users.id` | N:1 | nullOnDelete | معتمِد المصروف |
| 26 | `reports.sender_id` | `employees.id` | N:1 | cascadeOnDelete | مرسل التقرير |
| 27 | `reports.receiver_id` | `employees.id` | N:1 | cascadeOnDelete | مستقبل التقرير |
| 28 | `leaves.employee_id` | `employees.id` | N:1 | cascadeOnDelete | إجازة لموظف |
| 29 | `payrolls.employee_id` | `employees.id` | N:1 | cascadeOnDelete | راتب لموظف |
| 30 | `attendances.employee_id` | `employees.id` | N:1 | cascadeOnDelete | حضور لموظف |
| 31 | `employee_training.employee_id` | `employees.id` | M:N | cascadeOnDelete | (Pivot) تدريب الموظف |
| 32 | `employee_training.training_id` | `trainings.id` | M:N | cascadeOnDelete | (Pivot) موظفو التدريب |
| 33 | `certificates.employee_id` | `employees.id` | N:1 | cascadeOnDelete | شهادة لموظف |
| 34 | `career_plans.employee_id` | `employees.id` | N:1 | cascadeOnDelete | خطة لموظف |
| 35 | `vacancies.department_id` | `departments.id` | N:1 | nullOnDelete | وظيفة في قسم |
| 36 | `vacancies.created_by` | `users.id` | N:1 | nullOnDelete | منشئ الوظيفة |
| 37 | `task_templates.project_template_id` | `project_templates.id` | N:1 | cascadeOnDelete | مهمة في قالب مشروع |
| 38 | `notifications.notifiable` | (Polymorphic) | Morph | — | إشعار متعدد الأشكال |

### ٦.٢ العلاقات حسب الجدول المحوري

```
الجدول الأكثر ارتباطاً: employees (14 علاقة صادرة/واردة)
يتبعه: users (6 علاقات واردة)
يتبعه: projects (5 علاقات صادرة/واردة)
يتبعه: tasks (5 علاقات واردة)
```

---

## ٧. جداول Pivot (الربط متعدد إلى متعدد)

### ٧.١ `employee_skill` — مهارات الموظف (M:N بسيط)

| العمود | النوع | القيود |
|--------|------|--------|
| `employee_id` | bigint | FK → employees.id, cascadeOnDelete |
| `skill_id` | bigint | FK → skills.id, cascadeOnDelete |
| **PRIMARY KEY** | — | (employee_id, skill_id) مفتاح مركّب |

> **لا يحتوي على طوابع زمنية** (`timestamps`) — جدول ربط نقي.

### ٧.٢ `employee_project` — فرق المشاريع (M:N بسيط)

| العمود | النوع | القيود |
|--------|------|--------|
| `project_id` | bigint | FK → projects.id, cascadeOnDelete |
| `employee_id` | bigint | FK → employees.id, cascadeOnDelete |
| **PRIMARY KEY** | — | (project_id, employee_id) مفتاح مركّب |

### ٧.٣ `employee_training` — تسجيل الموظفين في التدريب (M:N غني بالبيانات)

| العمود | النوع | القابلية للفراغ | القيمة الافتراضية | القيود | الوصف |
|--------|------|----------------|-------------------|--------|------|
| `id` | bigint | NOT NULL | AUTO_INCREMENT | PRIMARY KEY | معرّف مستقل |
| `employee_id` | bigint | NOT NULL | — | FK → employees.id | الموظف |
| `training_id` | bigint | NOT NULL | — | FK → trainings.id | التدريب |
| `status` | ENUM | NOT NULL | `'enrolled'` | — | enrolled/completed/certified |
| `certificate_url` | varchar | NULL | NULL | — | مسار الشهادة |
| `completion_date` | date | NULL | NULL | — | تاريخ الإكمال |
| `created_at`, `updated_at` | timestamps | NULL | NULL | — | الطوابع الزمنية |
| **UNIQUE** | — | — | — | — | (employee_id, training_id) |

> هذا الجدول **ليس Pivot نقي** — يحتوي على معرّف خاص (`id`)، طوابع زمنية، وبيانات إضافية (الحالة، الشهادة، تاريخ الإكمال).

---

## ٨. تحليل سياسات الحذف (Delete Cascades)

| السياسة | العدد | الاستخدام | السلوك |
|---------|------|----------|--------|
| `cascadeOnDelete` | 26 علاقة | العلاقات القوية (الأصل ← الأبناء) | حذف الأبناء تلقائياً عند حذف الأصل |
| `nullOnDelete` | 13 علاقة | العلاقات المرنة (اختيارية) | تعيين القيمة إلى NULL عند حذف الأصل |
| `restrict` | 0 | غير مستخدم | — |

### أمثلة:
- **cascadeOnDelete:** حذف `project` يحذف جميع `tasks` المرتبطة به، وحذف `task` يحذف `task_comments` و `task_attachments` و `time_entries`.
- **nullOnDelete:** حذف `department` يجعل `department_id` في جدول `employees` يساوي NULL (الموظفون يبقون بدون قسم).

---

## ٩. القيود والفهارس (Constraints & Indexes)

### ٩.١ القيود الفريدة (UNIQUE Constraints)

| الجدول | العمود/الأعمدة | النوع |
|--------|----------------|------|
| `roles` | `name` | UNIQUE مفرد |
| `users` | `email` | UNIQUE مفرد |
| `employees` | `user_id` | UNIQUE مفرد (علاقة 1:1) |
| `resumes` | `employee_id` | UNIQUE مفرد (علاقة 1:1) |
| `skills` | `name` | UNIQUE مفرد |
| `clients` | `email` | UNIQUE مفرد |
| `invoices` | `invoice_number` | UNIQUE مفرد |
| `attendances` | `(employee_id, date)` | UNIQUE مركّب |
| `employee_skill` | `(employee_id, skill_id)` | PRIMARY KEY مركّب |
| `employee_project` | `(project_id, employee_id)` | PRIMARY KEY مركّب |
| `employee_training` | `(employee_id, training_id)` | UNIQUE مركّب |

### ٩.٢ الفهارس (Indexes)

| الجدول | العمود | السبب |
|--------|--------|------|
| `sessions` | `user_id` | للبحث السريع عن جلسات المستخدم |
| `sessions` | `last_activity` | لتنظيف الجلسات المنتهية |
| `cache` | `expiration` | لحذف التخزين المؤقت المنتهي |
| `cache_locks` | `expiration` | لتحرير الأقفال المنتهية |
| `jobs` | `queue` | لتوزيع المهام على الطوابير |
| `notifications` | `(notifiable_type, notifiable_id)` | فهارس Morph تلقائية |

---

## ١٠. كود dbdiagram.io لتوليد مخطط القاعدة

> **طريقة الاستخدام:** انسخ الكود التالي بالكامل والصقه في [dbdiagram.io/d](https://dbdiagram.io/d) لرؤية المخطط البصري التفاعلي الكامل.

```dbml
// ===========================================
// نظام Lite ERP — مخطط قاعدة البيانات الكامل
// ===========================================

Table roles {
  id bigint [pk, increment, not null]
  name varchar [unique, not null]
  description text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table users {
  id bigint [pk, increment, not null]
  role_id bigint [null]
  name varchar [not null]
  email varchar [unique, not null]
  profile_photo_path varchar [null]
  is_approved boolean [not null, default: false]
  email_verified_at timestamp [null]
  password varchar [not null]
  remember_token varchar [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employees {
  id bigint [pk, increment, not null]
  user_id bigint [unique, not null]
  department_id bigint [null]
  vacancy_id bigint [null]
  job_title varchar [null]
  salary decimal(10,2) [null]
  status enum('pending','active','on_leave','terminated','rejected') [not null, default: 'pending']
  rejection_reason text [null]
  hire_date date [null]
  annual_leave_balance integer [not null, default: 21]
  used_leave_days integer [not null, default: 0]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table resumes {
  id bigint [pk, increment, not null]
  employee_id bigint [unique, not null]
  file_path varchar [null]
  resume_text longtext [null]
  ai_score integer [null]
  ai_summary longtext [null]
  ai_report longtext [null]
  ai_recommendation varchar [null]
  analyzed_at timestamp [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table skills {
  id bigint [pk, increment, not null]
  name varchar [unique, not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employee_skill {
  employee_id bigint [not null]
  skill_id bigint [not null]

  Note: 'Pivot: M:N بين الموظفين والمهارات'
}

Table departments {
  id bigint [pk, increment, not null]
  name varchar [not null]
  head_id bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'head_id يشير إلى employees.id (مرجعية ذاتية دائرية)'
}

Table vacancies {
  id bigint [pk, increment, not null]
  title varchar [not null]
  description longtext [null]
  requirements longtext [null]
  location varchar [null]
  employment_type varchar [not null, default: 'full_time']
  salary_min decimal(10,2) [null]
  salary_max decimal(10,2) [null]
  department_id bigint [null]
  status varchar [not null, default: 'open']
  positions_count integer [not null, default: 1]
  created_by bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table clients {
  id bigint [pk, increment, not null]
  name varchar [not null]
  company_name varchar [null]
  email varchar [unique, null]
  password varchar [null]
  is_active boolean [not null, default: true]
  profile_photo_path varchar [null]
  remember_token varchar [null]
  phone varchar [null]
  address text [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'نظام مصادقة مستقل (Client Guard)'
}

Table projects {
  id bigint [pk, increment, not null]
  client_id bigint [null]
  name varchar [not null]
  description text [null]
  budget decimal(12,2) [null]
  start_date date [null]
  end_date date [null]
  status enum('pending','in_progress','completed','canceled') [not null, default: 'pending']
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employee_project {
  project_id bigint [not null]
  employee_id bigint [not null]

  Note: 'Pivot: M:N بين المشاريع والموظفين (فرق العمل)'
}

Table tasks {
  id bigint [pk, increment, not null]
  project_id bigint [not null]
  employee_id bigint [null]
  title varchar [not null]
  description text [null]
  start_date date [null]
  due_date date [null]
  status enum('todo','in_progress','review','done') [not null, default: 'todo']
  priority enum('low','medium','high') [not null, default: 'medium']
  sort_order integer [not null, default: 0]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_comments {
  id bigint [pk, increment, not null]
  task_id bigint [not null]
  user_id bigint [not null]
  comment text [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_attachments {
  id bigint [pk, increment, not null]
  task_id bigint [not null]
  user_id bigint [not null]
  file_name varchar [not null]
  file_path varchar [not null]
  file_type varchar [null]
  file_size bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table time_entries {
  id bigint [pk, increment, not null]
  task_id bigint [not null]
  employee_id bigint [not null]
  date date [not null]
  hours decimal(5,2) [not null, default: 0]
  description text [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'تتبع ساعات العمل على المهام'
}

Table project_templates {
  id bigint [pk, increment, not null]
  name varchar [not null]
  description text [null]
  budget decimal(12,2) [null]
  estimated_days integer [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_templates {
  id bigint [pk, increment, not null]
  project_template_id bigint [not null]
  title varchar [not null]
  description text [null]
  priority enum('low','medium','high') [not null, default: 'medium']
  estimated_hours integer [null]
  sort_order integer [not null, default: 0]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table invoices {
  id bigint [pk, increment, not null]
  client_id bigint [not null]
  project_id bigint [null]
  invoice_number varchar [unique, not null]
  amount decimal(12,2) [not null]
  vat_rate decimal(5,2) [not null, default: 0]
  vat_amount decimal(12,2) [not null, default: 0]
  total_with_vat decimal(12,2) [not null, default: 0]
  issue_date date [null]
  due_date date [null]
  status enum('unpaid','paid','overdue') [not null, default: 'unpaid']
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table invoice_items {
  id bigint [pk, increment, not null]
  invoice_id bigint [not null]
  description varchar [not null]
  quantity decimal(10,2) [not null, default: 1]
  unit_price decimal(12,2) [not null, default: 0]
  total decimal(12,2) [not null, default: 0]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table expenses {
  id bigint [pk, increment, not null]
  user_id bigint [not null]
  project_id bigint [null]
  title varchar [not null]
  category varchar [null]
  amount decimal(12,2) [not null]
  expense_date date [null]
  receipt_url varchar [null]
  status enum('pending','approved','rejected') [not null, default: 'pending']
  approved_by bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table reports {
  id bigint [pk, increment, not null]
  sender_id bigint [not null]
  receiver_id bigint [not null]
  title varchar [not null]
  content text [not null]
  feedback text [null]
  status enum('unread','read','replied') [not null, default: 'unread']
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'sender_id و receiver_id كلاهما يشير إلى employees.id'
}

Table leaves {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  type varchar [not null]
  start_date date [not null]
  end_date date [not null]
  reason text [not null]
  status enum('pending','approved_by_head','approved_by_hr','rejected') [not null, default: 'pending']
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'سلسلة موافقات متعددة المستويات'
}

Table payrolls {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  month_year varchar [not null]
  basic_salary decimal(10,2) [not null]
  bonuses decimal(10,2) [not null, default: 0]
  deductions decimal(10,2) [not null, default: 0]
  housing_allowance decimal(10,2) [not null, default: 0]
  transport_allowance decimal(10,2) [not null, default: 0]
  phone_allowance decimal(10,2) [not null, default: 0]
  social_insurance_rate decimal(5,2) [not null, default: 0]
  social_insurance_amount decimal(10,2) [not null, default: 0]
  absence_days integer [not null, default: 0]
  absence_deduction decimal(10,2) [not null, default: 0]
  net_salary decimal(10,2) [not null]
  status varchar [not null, default: 'unpaid']
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table attendances {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  date date [not null]
  check_in datetime [null]
  check_out datetime [null]
  hours_worked decimal(5,2) [not null, default: 0]
  overtime_hours decimal(5,2) [not null, default: 0]
  status enum('present','late','absent','half_day','over_time') [not null, default: 'present']
  notes text [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'UNIQUE(employee_id, date) — لا تكرار لنفس الموظف في نفس اليوم'
}

Table trainings {
  id bigint [pk, increment, not null]
  title varchar [not null]
  description text [null]
  trainer varchar [null]
  start_date date [not null]
  end_date date [not null]
  status enum('upcoming','ongoing','completed') [not null, default: 'upcoming']
  location varchar [null]
  max_participants integer [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employee_training {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  training_id bigint [not null]
  status enum('enrolled','completed','certified') [not null, default: 'enrolled']
  certificate_url varchar [null]
  completion_date date [null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Note: 'Pivot غني بالبيانات — يحتوي على حالة التسجيل والشهادة'
}

Table certificates {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  title varchar [not null]
  issuer varchar [not null]
  issue_date date [not null]
  expiry_date date [null]
  certificate_url varchar [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table career_plans {
  id bigint [pk, increment, not null]
  employee_id bigint [not null]
  current_role varchar [not null]
  target_role varchar [not null]
  timeline_months integer [not null]
  required_skills text [null]
  notes text [null]
  status enum('draft','active','completed') [not null, default: 'draft']
  created_at timestamp [null]
  updated_at timestamp [null]
}

// ===========================================
// العلاقات (Foreign Keys)
// ===========================================

// --- النظام والمستخدمون ---
Ref: users.role_id > roles.id
Ref: employees.user_id > users.id
Ref: departments.head_id > employees.id

// --- الموظفون والأقسام ---
Ref: employees.department_id > departments.id
Ref: employees.vacancy_id > vacancies.id

// --- السير الذاتية ---
Ref: resumes.employee_id > employees.id

// --- المهارات ---
Ref: employee_skill.employee_id > employees.id
Ref: employee_skill.skill_id > skills.id

// --- العملاء والمشاريع ---
Ref: projects.client_id > clients.id
Ref: employee_project.project_id > projects.id
Ref: employee_project.employee_id > employees.id

// --- المهام ---
Ref: tasks.project_id > projects.id
Ref: tasks.employee_id > employees.id
Ref: task_comments.task_id > tasks.id
Ref: task_comments.user_id > users.id
Ref: task_attachments.task_id > tasks.id
Ref: task_attachments.user_id > users.id
Ref: time_entries.task_id > tasks.id
Ref: time_entries.employee_id > employees.id

// --- القوالب ---
Ref: task_templates.project_template_id > project_templates.id

// --- المالية ---
Ref: invoices.client_id > clients.id
Ref: invoices.project_id > projects.id
Ref: invoice_items.invoice_id > invoices.id
Ref: expenses.user_id > users.id
Ref: expenses.project_id > projects.id
Ref: expenses.approved_by > users.id
Ref: payrolls.employee_id > employees.id

// --- الموارد البشرية ---
Ref: reports.sender_id > employees.id
Ref: reports.receiver_id > employees.id
Ref: leaves.employee_id > employees.id
Ref: attendances.employee_id > employees.id
Ref: certificates.employee_id > employees.id
Ref: career_plans.employee_id > employees.id

// --- التدريب ---
Ref: employee_training.employee_id > employees.id
Ref: employee_training.training_id > trainings.id

// --- التوظيف ---
Ref: vacancies.department_id > departments.id
Ref: vacancies.created_by > users.id
```

---

## ١١. مخطط العلاقات النصي (ERD)

```
                    ┌──────────────┐
                    │    roles     │
                    │──────────────│
                    │ id (PK)      │
                    │ name (UQ)    │
                    │ description  │
                    └──────┬───────┘
                           │ 1:N
                    ┌──────▼───────┐
          ┌─────────│    users     │──────────┐
          │         │──────────────│          │
          │         │ id (PK)      │          │
          │         │ role_id (FK) │          │
          │         │ name         │          │
          │         │ email (UQ)   │          │
          │         │ is_approved  │          │
          │         │ password     │          │
          │         └──────────────┘          │
          │ 1:1                               │ 1:N
    ┌─────▼──────┐                    ┌──────▼──────┐ ┌──────────┐
    │ employees  │◄───────────────────│  expenses   │ │vacancies │
    │────────────│                    └─────────────┘ │──────────│
    │ id (PK)    │                                          │ created_by
    │ user_id(FK)│◄──┐                                      └────┬─────┘
    │ dept_id(FK)│   │                                           │
    │ vacancy_id │   │     ┌──────────────────────────────────────┘
    │ job_title  │   │     │ N:1
    │ salary     │   │  ┌──▼──────────┐
    │ status     │   │  │ departments │
    │ hire_date  │   │  │─────────────│
    │ leave_bal  │   │  │ id (PK)     │
    └─────┬──────┘   │  │ name        │
          │          │  │ head_id(FK)─┼─► (مرجعية ذاتية إلى employees)
          │          │  └─────────────┘
          │          │
    ┌─────┼──────────┼──────────────────────────────────────────┐
    │     │          │                                              │
    │  1:1│          │ 1:N                                          │
┌───▼──┐ ┌▼──────┐ ┌─▼────────┐ ┌──────────┐ ┌────────────┐ ┌────▼──────┐
│resumes│ │leaves │ │ payrolls │ │attends   │ │certificates│ │career_pln│
│──────│ │───────│ │──────────│ │──────────│ │────────────│ │──────────│
│emp_id│ │emp_id│ │ emp_id   │ │ emp_id   │ │ emp_id     │ │ emp_id   │
│text  │ │type  │ │ salary   │ │ check_in │ │ title      │ │ target   │
│ai_*  │ │dates │ │ allowances│ │ check_out│ │ issuer     │ │ timeline │
└──────┘ └──────┘ └──────────┘ │ status   │ └────────────┘ └──────────┘
                                └──────────┘

          ┌───────────┐
          │  clients  │◄─────────────────────────────────────┐
          │───────────│                                       │ 1:N
          │ id (PK)   │       ┌──────────┐               ┌───▼──────┐
          │ name      │◄──────│ projects │──────────────►│ invoices │
          │ company   │  1:N  │──────────│ 1:N           │──────────│
          │ email(UQ) │       │ client_id│               │ client_id│
          │ password  │       │ budget   │               │ amount   │
          │ is_active │       │ status   │               │ vat_*    │
          └───────────┘       └────┬─────┘               │ status   │
                                  │ 1:N                  └────┬─────┘
                           ┌──────▼──────┐                    │ 1:N
                           │    tasks    │               ┌────▼────────┐
                           │─────────────│               │invoice_items│
                           │ project_id  │               │─────────────│
                           │ employee_id │               │ invoice_id  │
                           │ title       │               │ description │
                           │ status      │               │ qty × price │
                           │ priority    │               └─────────────┘
                           │ sort_order  │
                           └──┬───┬───┬──┘
                              │   │   │
                    ┌─────────┘   │   └──────────┐
                    │ 1:N         │ 1:N          │ 1:N
              ┌─────▼──────┐ ┌───▼────────┐ ┌───▼──────────┐
              │task_comments│ │task_attach.│ │ time_entries │
              │────────────│ │────────────│ │──────────────│
              │ task_id    │ │ task_id    │ │ task_id      │
              │ user_id    │ │ user_id    │ │ employee_id  │
              │ comment    │ │ file_path  │ │ hours        │
              └────────────┘ └────────────┘ └──────────────┘

          ┌───────────┐         ┌──────────────┐
          │  skills   │         │  trainings   │
          │───────────│         │──────────────│
          │ id (PK)   │         │ id (PK)      │
          │ name (UQ) │         │ title        │
          └─────┬─────┘         │ trainer      │
                │ M:N           │ status       │
     ┌──────────▼──────────┐   └──────┬───────┘
     │   employee_skill    │          │ M:N
     │─────────────────────│   ┌──────▼───────────┐
     │ (employee_id,skill) │   │ employee_training│
     │   PK مركّب          │   │──────────────────│
     └─────────────────────┘   │ status           │
                               │ certificate_url  │
          ┌─────────────────────│ completion_date  │
          │                     └──────────────────┘
          │ M:N
     ┌────▼──────────┐
     │employee_project│
     │───────────────│
     │(proj,employee) │
     │  PK مركّب     │
     └───────────────┘

     ┌──────────────────┐     ┌───────────────┐
     │ project_templates│     │ task_templates│
     │──────────────────│     │───────────────│
     │ id (PK)         │◄────│ project_tmpl_id│
     │ name            │ 1:N │ title         │
     │ estimated_days  │     │ estimated_hrs │
     └──────────────────┘     └───────────────┘
```

---

## ١٢. الإحصائيات الختامية

| المقياس | القيمة |
|---------|--------|
| **إجمالي ملفات الهجرة** | 49 ملف |
| **ملفات الإنشاء (Create)** | 31 ملف |
| **ملفات التعديل (Alter/Add)** | 18 ملف |
| **إجمالي جداول الأعمال** | 25 جدول |
| **جداول Pivot** | 3 جداول (`employee_skill`, `employee_project`, `employee_training`) |
| **جداول نظامية** | 7 جداول (`cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `sessions`, `notifications`) |
| **إجمالي العلاقات (FK)** | 39 علاقة |
| **العلاقات cascadeOnDelete** | 26 علاقة |
| **العلاقات nullOnDelete** | 13 علاقة |
| **القيود الفريدة (UNIQUE)** | 11 قيد |
| **الجداول التي تستخدم ENUM** | 9 جداول |
| **الجدول الأكثر ارتباطاً** | `employees` (14 علاقة) |
| **أعمدة الذكاء الاصطناعي** | 5 أعمدة في جدول `resumes` |
| **فترة التطوير** | أبريل 2026 — يونيو 2026 |

---

*تم إعداد هذا التقرير بناءً على تحليل كامل لجميع ملفات الهجرة الـ 49 في مجلد `database/migrations/`.*
