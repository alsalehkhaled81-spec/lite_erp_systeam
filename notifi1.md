# تقرير تحليلي شامل — نظام الإشعارات في Lite ERP

> **المجلد:** `app/Notifications/` (4 ملفات) + الإشعارات المدمجة في النماذج
> **نظام الإشعارات:** ثلاثي المستويات (Laravel Notifications + Filament Notifications + Model Events)

---

## جدول المحتويات

1. [نظرة عامة على نظام الإشعارات](#١-نظرة-عامة-على-نظام-الإشعارات)
2. [البنية المعمارية للإشعارات](#٢-البنية-المعمارية-للإشعارات)
3. [ملفات `app/Notifications/` التفصيلية](#٣-ملفات-appnotifications-التفصيلية)
   - 3.1 `TaskAssignedNotification.php`
   - 3.2 `LeaveStatusNotification.php`
   - 3.3 `ReportReceivedNotification.php`
   - 3.4 `JobApplicationStatusNotification.php`
4. [الإشعارات المدمجة في النماذج (Model Events)](#٤-الإشعارات-المدمجة-في-النماذج-model-events)
5. [الإشعارات الفورية في Filament (Flash Notifications)](#٥-الإشعارات-الفورية-في-filament-flash-notifications)
6. [جدول الإشعارات الشامل](#٦-جدول-الإشعارات-الشامل)
7. [تخزين الإشعارات وتقديمها](#٧-تخزين-الإشعارات-وتقديمها)

---

## ١. نظرة عامة على نظام الإشعارات

يعتمد نظام Lite ERP على **نظام إشعارات متكامل متعدد المستويات** يضمن وصول المعلومات في الوقت المناسب لكل المستخدمين المناسبين. النظام يخدم ثلاثة أغراض رئيسية:

| الغرض | الآلية | مثال |
|------|--------|------|
| **إشعارات قاعدة بيانات (Database)** | تُخزّن في جدول `notifications` وتظهر في جرس الإشعارات | "مهمة جديدة تم تعيينك لها" |
| **إشعارات فورية (Toast/Flash)** | تظهر لحظياً أعلى الشاشة وتختفي | "تم الحفظ بنجاح" |
| **إشعارات بريد إلكتروني (Mail)** | عبر Laravel Password Broker (لإعادة التعيين) | "رابط استعادة كلمة المرور" |

---

## ٢. البنية المعمارية للإشعارات

```
┌─────────────────────────────────────────────────────────────────┐
│                    مشغلات الإشعارات (Triggers)                    │
├──────────────────┬──────────────────┬───────────────────────────┤
│  Model Events    │  Filament Actions│  Manual Calls             │
│  (في النماذج)    │  (في الموارد)    │  (في Controllers/Pages)   │
├──────────────────┼──────────────────┼───────────────────────────┤
│ Task::created    │ acceptApp()      │ checkIn()                 │
│ Task::updated    │ rejectApp()      │ checkOut()                │
│ Leave::created   │ approveLeave()   │ save() (Profile)          │
│ Leave::updated   │ rejectLeave()    │ analyzeResume()           │
│ TaskComment::created│              │ createProject()            │
│ Expense::created │                  │                           │
│ Expense::updated │                  │                           │
└────────┬─────────┴────────┬─────────┴────────────┬──────────────┘
         │                  │                      │
         ▼                  ▼                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                    طبقة الإرسال                                  │
├──────────────────────────┬──────────────────────────────────────┤
│  Filament Notification   │  Laravel Notification Classes        │
│  Notification::make()    │  $user->notify(new XxxNotification)  │
│  ->sendToDatabase($user) │  (app/Notifications/)                │
└────────────┬─────────────┴──────────────┬───────────────────────┘
             │                            │
             ▼                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    قناة التخزين (Database Channel)               │
│                                                                 │
│  جدول: notifications                                            │
│  ├── id (UUID)                                                  │
│  ├── type (نوع الإشعار)                                         │
│  ├── notifiable_type + notifiable_id (Polymorphic)              │
│  ├── data (JSON: title, body, icon, actions)                    │
│  └── read_at (وقت القراءة)                                      │
└─────────────────────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    عرض الإشعارات للمستخدم                        │
│                                                                 │
│  🔔 جرس الإشعارات في شريط Filament                              │
│  ├── عدّاد غير المقروء (Polling كل 30 ثانية)                    │
│  ├── قائمة الإشعارات المنسدلة                                    │
│  └── زر "تعليم كمقروء"                                          │
└─────────────────────────────────────────────────────────────────┘
```

### الطريقتان المستخدمتان لإرسال الإشعارات:

| الطريقة | المكتبة | الاستخدام |
|--------|---------|----------|
| **Filament Notifications** | `\Filament\Notifications\Notification::make()` | في Model Events و Pages و Actions |
| **Laravel Notification Classes** | `$user->notify(new XxxNotification())` | في Filament Resources (أحياناً) |

> كلاهما يخزّن في نفس جدول `notifications` ويظهر في نفس جرس الإشعارات.

---

## ٣. ملفات `app/Notifications/` التفصيلية

### ٣.١ `TaskAssignedNotification.php` — إشعار تعيين مهمة

**الملف:** `app/Notifications/TaskAssignedNotification.php` (34 سطر)

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    protected $taskTitle;
    protected $projectName;

    public function __construct(string $taskTitle, string $projectName)
    {
        $this->taskTitle = $taskTitle;
        $this->projectName = $projectName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'مهمة جديدة',
            'body' => "تم تعيين مهمة جديدة لك: {$this->taskTitle} في مشروع {$this->projectName}",
            'icon' => 'heroicon-o-clipboard-document-check',
        ];
    }
}
```

#### التحليل التفصيلي:

| المكوّن | الوصف |
|---------|------|
| `use Queueable` | Trait يتيح وضع الإشعار في الطابور (Queue) للمعالجة غير المتزامنة |
| `$taskTitle`, `$projectName` | بيانات تُمرر عبر Constructor من المُرسِل |
| `via()` | تحدد القناة: `['database']` فقط (لا بريد، لا SMS) |
| `toDatabase()` | تحدد بنية البيانات المخزّنة في عمود `data` كـ JSON |

**بنية JSON المُخزّنة:**
```json
{
    "title": "مهمة جديدة",
    "body": "تم تعيين مهمة جديدة لك: تطوير واجهة API في مشروع ERP",
    "icon": "heroicon-o-clipboard-document-check"
}
```

> **ملاحظة مهمة:** رغم وجود هذه الفئة، النظام الفعلي يستخدم `Filament\Notifications\Notification::make()` في `Task::booted()` مباشرة بدلاً منها (انظر القسم ٤). هذه الفئة موجودة كبديل قابل للتوسع.

---

### ٣.٢ `LeaveStatusNotification.php` — إشعار حالة الإجازة

**الملف:** `app/Notifications/LeaveStatusNotification.php` (41 سطر)

```php
class LeaveStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $leaveType;

    public function __construct(string $status, string $leaveType)
    {
        $this->status = $status;
        $this->leaveType = $leaveType;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = match ($this->status) {
            'approved_by_head' => 'تمت موافقة رئيس القسم على',
            'approved_by_hr'   => 'تمت الموافقة النهائية على',
            'rejected'         => 'تم رفض',
            default            => $this->status,
        };

        return [
            'title' => 'تحديث الإجازة',
            'body'  => "{$statusText} طلب الإجازة ({$this->leaveType})",
            'icon'  => 'heroicon-o-calendar-days',
        ];
    }
}
```

#### التحليل التفصيلي:

**الذكاء في `match` expression:**

```php
$statusText = match ($this->status) {
    'approved_by_head' => 'تمت موافقة رئيس القسم على',
    'approved_by_hr'   => 'تمت الموافقة النهائية على',
    'rejected'         => 'تم رفض',
    default            => $this->status,
};
```

| الحالة الواردة | النص المُولّد |
|----------------|---------------|
| `approved_by_head` | "تمت موافقة رئيس القسم على طلب الإجازة (سنوية)" |
| `approved_by_hr` | "تمت الموافقة النهائية على طلب الإجازة (مرضية)" |
| `rejected` | "تم رفض طلب الإجازة (طارئة)" |

> **لماذا match وليس switch?** `match` هو تعبير (expression) يُرجع قيمة، أحدث وأكثر إيجازاً من `switch`. وهو صارم النوع (strict type comparison).

**الاستخدام الفعلي في النظام:**

```php
// في LeaveResource.php (HR)
$record->employee->user->notify(
    new LeaveStatusNotification('approved_by_hr', $record->type)
);

// أو
$record->employee->user->notify(
    new LeaveStatusNotification('rejected', $record->type)
);
```

---

### ٣.٣ `ReportReceivedNotification.php` — إشعار استلام تقرير

**الملف:** `app/Notifications/ReportReceivedNotification.php` (41 سطر)

```php
use App\Models\Report;

class ReportReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $senderName = $this->report->sender?->user?->name
            ?? __('filament.reports.unknown_sender');

        return [
            'title' => __('filament.reports.notification_title'),
            'body' => __('filament.reports.notification_body', [
                'sender' => $senderName,
                'title' => $this->report->title,
            ]),
            'icon' => 'heroicon-o-document-text',
            'iconColor' => 'info',
            'actions' => [
                [
                    'label' => __('filament.reports.view_report'),
                    'url' => $this->report->viewUrl(),
                    'shouldMarkAsRead' => true,
                ],
            ],
        ];
    }
}
```

#### التحليل التفصيلي:

**Constructor Promotion مع تمرير نموذج كامل:**

```php
public function __construct(public Report $report) {}
```

على عكس الإشعارات الأخرى التي تمرر نصوصاً، هذا الإشعار يمرر **النموذج كاملاً** (`Report $report`). هذا يتيح الوصول لجميع علاقاته (sender, receiver, title) مباشرة.

**دعم متعدد اللغات كامل:**

```php
'title' => __('filament.reports.notification_title'),
'body' => __('filament.reports.notification_body', [
    'sender' => $senderName,
    'title' => $this->report->title,
]),
```

| المعامل | المثال |
|---------|--------|
| `notification_title` | "تقرير جديد" |
| `notification_body` | "أرسل {sender} تقريراً بعنوان: {title}" |

**إجراءات تفاعلية (Actions):**

```php
'actions' => [
    [
        'label' => __('filament.reports.view_report'),
        'url' => $this->report->viewUrl(),
        'shouldMarkAsRead' => true,
    ],
],
```

| المكوّن | الوصف |
|---------|------|
| `label` | نص الزر: "عرض التقرير" |
| `url` | رابط مباشر للتقرير: `/hr/reports/42/edit` |
| `shouldMarkAsRead` | عند النقر، يُعلَّم الإشعار كمقروء تلقائياً |

> **الفرق عن باقي الإشعارات:** هذا الإشعار الوحيد الذي يحتوي على `actions` تفاعلية وأزرار قابلة للنقر.

**دالة `viewUrl()`:**

```php
// في Report.php
public function viewUrl(): string
{
    return '/' . $this->receiverPanelId() . '/reports/' . $this->id . '/edit';
}
```

توجيه ذكي للمستلم إلى لوحته الخاصة (`/employee/reports/42/edit` للموظف، `/pm/reports/42/edit` لمدير المشاريع، إلخ).

---

### ٣.٤ `JobApplicationStatusNotification.php` — إشعار حالة التوظيف

**الملف:** `app/Notifications/JobApplicationStatusNotification.php` (36 سطر)

```php
class JobApplicationStatusNotification extends Notification
{
    use Queueable;

    protected $status;
    protected $applicantName;

    public function __construct(string $status, string $applicantName)
    {
        $this->status = $status;
        $this->applicantName = $applicantName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusText = $this->status === 'active' ? 'تم قبول' : 'تم رفض';

        return [
            'title' => 'تحديث طلب التوظيف',
            'body'  => "{$statusText} طلب التوظيف للمتقدم {$this->applicantName}",
            'icon'  => 'heroicon-o-user-plus',
        ];
    }
}
```

#### التحليل التفصيلي:

**منطق ثنائي مبسّط:**

```php
$statusText = $this->status === 'active' ? 'تم قبول' : 'تم رفض';
```

| الحالة | النص |
|--------|------|
| `'active'` | "تم قبول طلب التوظيف للمتقدم أحمد محمد" |
| أي قيمة أخرى | "تم رفض طلب التوظيف للمتقدم أحمد محمد" |

**الاستخدام الفعلي في النظام:**

```php
// في JobApplicantResource.php (عند القبول)
$record->user->notify(
    new JobApplicationStatusNotification('active', $record->user->name)
);

// وعند الرفض
$record->user->notify(
    new JobApplicationStatusNotification('rejected', $record->user->name)
);
```

> الإشعار يُرسل إلى **المستخدم المتقدم نفسه** (`$record->user`)، ليبلغه بقرار الموارد البشرية.

---

## ٤. الإشعارات المدمجة في النماذج (Model Events)

هذه هي **الآلية الأساسية** الفعلية لإرسال الإشعارات في النظام — تستخدم `Filament\Notifications\Notification::make()` داخل أحداث النموذج (`booted()`).

### ٤.١ `Task.php` — إشعارات المهام (حدثان)

```php
protected static function booted(): void
{
    // 1. عند إنشاء مهمة جديدة → إشعار الموظف المسؤول
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

    // 2. عند تحديث حالة المهمة → إشعار الموظف
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

| الحدث | المُطلِق | المُستلِم | اللون |
|-------|---------|----------|------|
| `created` (مهمة جديدة) | مدير المشاريع ينشئ مهمة | الموظف المسؤول | `success` (أخضر) |
| `updated` (تغيير حالة) | أي شخص يغيّر `status` | الموظف المسؤول | `info` (أزرق) |

> **`isDirty('status')`:** يتحقق من تغيير عمود `status` فقط، وليس أي عمود آخر. يمنع إرسال إشعار عند تعديل الوصف أو التاريخ بدون تغيير الحالة.

---

### ٤.٢ `Leave.php` — إشعارات الإجازات (حدثان)

```php
protected static function booted(): void
{
    // 1. عند إنشاء طلب إجازة → إشعار جميع مدراء HR والمدير العام
    static::created(function (Leave $leave) {
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

    // 2. عند تحديث حالة الإجازة → إشعار صاحب الطلب
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

| الحدث | المُطلِق | المُستلِم | عدد المستلمين |
|-------|---------|----------|---------------|
| `created` (طلب جديد) | الموظف يطلب إجازة | **جميع** مدراء HR + المدير العام | متعدد (N) |
| `updated` (تحديث حالة) | HR يوافق/يرفض | صاحب الطلب | واحد |

> **البث المتعدد (Broadcast):** عند طلب إجازة جديد، يُرسل الإشعار لكل المستخدمين بدور `hr_manager` أو `super_admin` عبر حلقة `foreach`.

---

### ٤.٣ `TaskComment.php` — إشعار تعليق جديد

```php
static::created(function (TaskComment $comment) {
    $task = $comment->task;
    if ($task && $task->employee && $task->employee->user) {
        // لا تُشعر الشخص الذي أضاف التعليق نفسه
        if ($comment->user_id !== $task->employee->user->id) {
            Notification::make()
                ->title('تعليق جديد على مهمة')
                ->body('تمت إضافة تعليق جديد على المهمة: ' . $task->title)
                ->info()
                ->sendToDatabase($task->employee->user);
        }
    }
});
```

> **منع الإشعار الذاتي:** `if ($comment->user_id !== $task->employee->user->id)` — إذا علّق الموظف المسؤول عن المهمة على مهمته، لا يستلم إشعاراً لنفسه.

---

### ٤.٤ `Expense.php` — إشعارات المصروفات (حدثان)

```php
// 1. عند إنشاء مصروف → إشعار المحاسبين والمدير العام
static::created(function (Expense $expense) {
    $financeUsers = User::whereHas('role', function($q) {
        $q->where('name', 'accountant')->orWhere('name', 'super_admin');
    })->get();

    foreach ($financeUsers as $user) {
        Notification::make()
            ->title('طلب مصروف جديد')
            ->body('قدم ' . ($expense->user->name ?? 'مستخدم')
                . ' طلب مصروف جديد بقيمة ' . $expense->amount)
            ->info()
            ->sendToDatabase($user);
    }
});

// 2. عند اعتماد/رفض المصروف → إشعار مُسجّل المصروف
static::updated(function (Expense $expense) {
    if ($expense->isDirty('status') && $expense->user) {
        Notification::make()
            ->title('تحديث حالة المصروف')
            ->body('تم تغيير حالة طلب المصروف (' . $expense->title
                . ') إلى: ' . __("filament.status.{$expense->status}"))
            ->success()
            ->sendToDatabase($expense->user);
    }
});
```

| الحدث | المُطلِق | المُستلِم |
|-------|---------|----------|
| `created` | أي مستخدم يسجّل مصروفاً | المحاسب + المدير العام (متعدد) |
| `updated` | المحاسب يعتمد/يرفض | مُسجّل المصروف الأصلي |

---

### ٤.٥ `Report.php` — إشعارات التقارير (3 دوال)

نموذج `Report` هو الأكثر تعقيداً — يحتوي على **3 دوال إشعار** منفصلة:

```php
// 1. إشعار المستلم عند وصول تقرير جديد
public function notifyReceiver(): void

// 2. إشعار المرسل عند تسليم تقريره (تأكيد الإرسال)
public function notifySender(): void

// 3. إشعار المرسل عند تلقي رد على تقريره
public function notifyReplied(): void
```

| الدالة | المُستلِم | العنوان | الأيقونة | اللون |
|--------|----------|---------|----------|------|
| `notifyReceiver()` | المستلم | "تقرير جديد" | document-text | info |
| `notifySender()` | المرسل | "تم إرسال التقرير" | paper-airplane | success |
| `notifyReplied()` | المرسل | "رد على التقرير" | chat-bubble-left-right | warning |

**كل إشعار يحتوي على زر تفاعلي (Action):**

```php
->actions([
    Action::make('view_report')
        ->button()
        ->label(__('filament.reports.view_report'))
        ->url($this->viewUrl())
        ->markAsRead(),
])
```

> الزر يوجّه المستلم/المرسل إلى لوحته الخاصة (`/employee/reports/42/edit` أو `/pm/reports/42/edit`) بناءً على دوره.

---

## ٥. الإشعارات الفورية في Filament (Flash Notifications)

إلى جانب الإشعارات المخزّنة، يستخدم النظام إشعارات فورية (Toast) تعرض مؤقتاً أعلى الشاشة:

### أمثلة من `MyAttendance.php`:

```php
Notification::make()->title(__('filament.attendance.already_checked_in'))->warning()->send();
Notification::make()->title(__('filament.attendance.check_in_success'))->success()->send();
Notification::make()->title(__('filament.attendance.must_check_in_first'))->warning()->send();
Notification::make()->title(__('filament.attendance.already_checked_out'))->warning()->send();
Notification::make()->title(__('filament.attendance.check_out_success'))->success()->send();
```

| الإشعار | اللون | الموقف |
|---------|------|--------|
| "تم تسجيل الحضور بنجاح" | success | Check-in ناجح |
| "سبق وتم تسجيل الحضور اليوم" | warning | محاولة Check-in مكررة |
| "يجب تسجيل الحضور أولاً" | warning | محاولة Check-out بدون Check-in |

> **الفرق:** هذه الإشعارات `->send()` (تظهر وتختفي) وليست `->sendToDatabase()` (تُخزّن دائماً).

---

## ٦. جدول الإشعارات الشامل

| # | الإشعار | المُطلِق | المُستلِم | القناة | الآلية |
|---|---------|---------|----------|--------|--------|
| 1 | مهمة جديدة | إنشاء Task | الموظف المسؤول | DB | Model Event (`Task::created`) |
| 2 | تحديث حالة المهمة | تغيير `status` | الموظف المسؤول | DB | Model Event (`Task::updated`) |
| 3 | طلب إجازة جديد | الموظف يطلب | جميع HR + Admin | DB | Model Event (`Leave::created`) |
| 4 | تحديث حالة الإجازة | HR يوافق/يرفض | صاحب الطلب | DB | Model Event (`Leave::updated`) + `LeaveStatusNotification` |
| 5 | تعليق جديد على مهمة | إضافة تعليق | الموظف المسؤول | DB | Model Event (`TaskComment::created`) |
| 6 | طلب مصروف جديد | تسجيل مصروف | المحاسب + Admin | DB | Model Event (`Expense::created`) |
| 7 | تحديث حالة المصروف | اعتماد/رفض | مُسجّل المصروف | DB | Model Event (`Expense::updated`) |
| 8 | تقرير جديد وصل | إرسال تقرير | المستلم | DB | `Report::notifyReceiver()` |
| 9 | تم إرسال التقرير | إرسال تقرير | المرسل (تأكيد) | DB | `Report::notifySender()` |
| 10 | رد على التقرير | إضافة feedback | المرسل | DB | `Report::notifyReplied()` |
| 11 | قبول طلب التوظيف | HR يقبل | المتقدم | DB | `JobApplicationStatusNotification` |
| 12 | رفض طلب التوظيف | HR يرفض | المتقدم | DB | `JobApplicationStatusNotification` |
| 13 | نجاح استخراج نص السيرة | HR يرفع | HR نفسه | Flash | Filament Action |
| 14 | فشل تحليل AI | خطأ API | HR نفسه | Flash | Filament Action |
| 15 | تسجيل حضور/انصراف | الموظف | الموظف نفسه | Flash | Page Action |
| 16 | إنشاء مشروع من قالب | PM ينشئ | PM نفسه | Flash | Filament Action |

---

## ٧. تخزين الإشعارات وتقديمها

### ٧.١ جدول `notifications`

```sql
CREATE TABLE notifications (
    id              UUID PRIMARY KEY,
    type            VARCHAR,           -- اسم فئة الإشعار (أو 'filament')
    notifiable_type VARCHAR,           -- 'App\Models\User' (Polymorphic)
    notifiable_id   BIGINT,            -- معرّف المستخدم
    data            TEXT,              -- JSON: {title, body, icon, actions}
    read_at         TIMESTAMP NULL,    -- وقت القراءة (NULL = غير مقروء)
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);
```

### ٧.٢ Polymorphic Relationship

```php
// في Notification model
Schema::table('notifications', function ($table) {
    $table->morphs('notifiable'); // notifiable_type + notifiable_id
});
```

> يدعم الإشعارات لأي نموذج (`User`, `Client`, أي كيان مستقبلي).

### ٧.٣ Polling (التحديث الدوري)

```php
// في كل PanelProvider
->databaseNotifications()
->databaseNotificationsPolling('30s') // تحديث كل 30 ثانية
```

```
[المتصفح] → كل 30 ثانية → [AJAX Request] → [DB Query] → [تحديث الجرس]
```

### ٧.٤ بنية بيانات الإشعار في JSON

```json
{
    "title": "مهمة جديدة: تطوير API",
    "body": "تم تعيين مهمة جديدة لك في مشروع: ERP System",
    "icon": "heroicon-o-clipboard-document-check",
    "iconColor": "success",
    "actions": [
        {
            "name": "view",
            "label": "عرض المهمة",
            "url": "/employee/tasks/42/edit",
            "shouldMarkAsRead": true
        }
    ]
}
```

---

## ٨. مقارنة بين آليتي الإشعار

| المعيار | Laravel Notification Classes | Filament Notifications |
|---------|-----------------------------|----------------------|
| **المجلد** | `app/Notifications/` | مدمجة في النماذج/الموارد |
| **طريقة الإرسال** | `$user->notify(new Xxx())` | `Notification::make()->sendToDatabase($user)` |
| **الأناقة** | فئة منفصلة قابلة لإعادة الاستخدام | استدعاء مباشر مختصر |
| **المرونة** | عالية (دعم multi-channel) | متوسطة (database فقط) |
| **عدد الاستخدامات الفعلية** | 4 فئات، ~6 استدعاءات | 10+ مواقع |
| **الأزرار التفاعلية** | عبر `toDatabase()` | عبر `->actions([Action::make()])` |

> **الخلاصة:** النظام يستخدم **كلا الآليتين** بالتوازي. Filament Notifications للإشعارات السريعة المدمجة، و Laravel Notification Classes للإشعارات الأكثر تنظيماً.

---

*نهاية تقرير نظام الإشعارات*
