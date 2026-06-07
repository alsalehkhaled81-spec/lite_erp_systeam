<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\TimeEntry;
use App\Models\ProjectTemplate;
use App\Models\TaskTemplate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Expense;
use App\Models\Department;
use App\Models\Report;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Training;
use App\Models\Certificate;
use App\Models\CareerPlan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');

        Schema::disableForeignKeyConstraints();

        // ==========================================
        // 1. إنشاء الأدوار (Roles)
        // ==========================================
        $rolesData = [
            ['name' => 'super_admin', 'description' => 'المدير العام (صلاحيات كاملة على النظام)'],
            ['name' => 'hr_manager', 'description' => 'مدير الموارد البشرية (إدارة الموظفين والرواتب)'],
            ['name' => 'project_manager', 'description' => 'مدير المشاريع (إدارة المشاريع والمهام والعملاء)'],
            ['name' => 'accountant', 'description' => 'المحاسب المالي (إدارة الفواتير والمصروفات)'],
            ['name' => 'employee', 'description' => 'موظف (مطور، مصمم، مختبر)'],
        ];

        $roles = [];
        foreach ($rolesData as $data) {
            $roles[$data['name']] = Role::create($data);
        }

        // ==========================================
        // 2. إنشاء المهارات (Skills)
        // ==========================================
        $skillsList = [
            'PHP', 'Laravel', 'Vue.js', 'React', 'Node.js', 'Python', 'Django',
            'Flutter', 'Swift', 'Kotlin', 'Java', 'C#', '.NET',
            'UI/UX Design', 'Figma', 'Adobe XD',
            'DevOps', 'AWS', 'Docker', 'Kubernetes', 'CI/CD',
            'Project Management', 'Agile/Scrum', 'Jira',
            'Quality Assurance', 'Automated Testing', 'Selenium',
            'Accounting', 'Digital Marketing', 'SEO'
        ];

        foreach ($skillsList as $skill) {
            Skill::create(['name' => $skill]);
        }
        $allSkills = Skill::all();

        // ==========================================
        // 3. إنشاء الأقسام (Departments)
        // ==========================================
        $departmentsData = [
            ['name' => 'الإدارة العليا'],
            ['name' => 'تطوير الويب'],
            ['name' => 'تطبيقات الجوال'],
            ['name' => 'الموارد البشرية'],
            ['name' => 'المالية والمحاسبة'],
            ['name' => 'التسويق والمبيعات'],
            ['name' => 'ضمان الجودة (QA)'],
            ['name' => 'إدارة النظم والدعم (DevOps)'],
        ];

        $departments = [];
        foreach ($departmentsData as $deptData) {
            $departments[] = Department::create($deptData);
        }

        // ==========================================
        // 4. إنشاء حسابات مستخدمين واقعية (Users & Employees)
        // ==========================================
        $defaultPassword = Hash::make('password');

        $keyUsers = [
            ['name' => 'محمد عبدالله', 'email' => 'ceo@techcompany.com', 'role' => 'super_admin', 'job' => 'المدير التنفيذي', 'salary' => 25000, 'dept' => 0],
            ['name' => 'ريم الفاسي', 'email' => 'hr@techcompany.com', 'role' => 'hr_manager', 'job' => 'مدير الموارد البشرية', 'salary' => 12000, 'dept' => 3],
            ['name' => 'طارق النجار', 'email' => 'pm@techcompany.com', 'role' => 'project_manager', 'job' => 'مدير المشاريع', 'salary' => 15000, 'dept' => 1],
            ['name' => 'وليد السعيد', 'email' => 'finance@techcompany.com', 'role' => 'accountant', 'job' => 'رئيس الحسابات', 'salary' => 11000, 'dept' => 4],
            ['name' => 'سارة الخالدي', 'email' => 'sara@techcompany.com', 'role' => 'employee', 'job' => 'Senior Laravel Developer', 'salary' => 9500, 'dept' => 1],
            ['name' => 'عمر الفاروق', 'email' => 'omar@techcompany.com', 'role' => 'employee', 'job' => 'Frontend Vue.js Developer', 'salary' => 7500, 'dept' => 1],
            ['name' => 'نور حسن', 'email' => 'nour@techcompany.com', 'role' => 'employee', 'job' => 'Mobile App Developer (Flutter)', 'salary' => 8500, 'dept' => 2],
            ['name' => 'أحمد كمال', 'email' => 'ahmed@techcompany.com', 'role' => 'employee', 'job' => 'UI/UX Designer', 'salary' => 7000, 'dept' => 1],
            ['name' => 'ندى يوسف', 'email' => 'nada@techcompany.com', 'role' => 'employee', 'job' => 'QA Engineer', 'salary' => 6500, 'dept' => 6],
            ['name' => 'محمود زيدان', 'email' => 'mahmoud@techcompany.com', 'role' => 'employee', 'job' => 'DevOps Engineer', 'salary' => 10500, 'dept' => 7],
            ['name' => 'خالد منصور', 'email' => 'khaled@techcompany.com', 'role' => 'employee', 'job' => 'Digital Marketing Specialist', 'salary' => 6000, 'dept' => 5],
        ];

        $employeeIds = [];

        foreach ($keyUsers as $userData) {
            $user = User::create([
                'role_id' => $roles[$userData['role']]->id,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $defaultPassword,
                'is_approved' => true,
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $departments[$userData['dept']]->id,
                'job_title' => $userData['job'],
                'salary' => $userData['salary'],
                'status' => 'active',
                'hire_date' => Carbon::now()->subMonths(rand(6, 48)),
                'annual_leave_balance' => 21,
                'used_leave_days' => 0,
            ]);

            $employeeIds[] = $employee->id;

            Resume::create([
                'employee_id' => $employee->id,
                'file_path' => 'resumes/cv_' . strtolower(str_replace(' ', '_', $userData['name'])) . '.pdf',
                'resume_text' => "Professional Summary for {$userData['name']}: Highly skilled in software development and business operations. Experienced with modern technologies and agile methodologies.",
            ]);

            $employee->skills()->attach($allSkills->random(rand(3, 6))->pluck('id'));
        }

        // ==========================================
        // 5. تعيين رؤساء الأقسام (Department Heads)
        // ==========================================
        $departments[0]->update(['head_id' => $employeeIds[0]]);
        $departments[1]->update(['head_id' => $employeeIds[2]]);
        $departments[2]->update(['head_id' => $employeeIds[6]]);
        $departments[3]->update(['head_id' => $employeeIds[1]]);
        $departments[4]->update(['head_id' => $employeeIds[3]]);

        // ==========================================
        // 6. إنشاء العملاء (Clients)
        // ==========================================
        $realClients = [
            ['name' => 'ياسر القحطاني', 'company' => 'شركة التقنية المتقدمة', 'domain' => 'advancedtech.sa'],
            ['name' => 'فهد الدوسري', 'company' => 'مؤسسة التجارة السريعة', 'domain' => 'fasttrade.com'],
            ['name' => 'عبدالرحمن السالم', 'company' => 'منصة التعليم الذكي', 'domain' => 'smartedu.net'],
            ['name' => 'د. خالد الياس', 'company' => 'مستشفى الحياة', 'domain' => 'alhayathospital.com'],
            ['name' => 'سليمان الراجحي', 'company' => 'شركة النقل اللوجستي', 'domain' => 'logistics-trans.com'],
        ];

        $clients = [];
        foreach ($realClients as $clientData) {
            $clients[] = Client::create([
                'name' => $clientData['name'],
                'company_name' => $clientData['company'],
                'email' => 'info@' . $clientData['domain'],
                'phone' => '05' . rand(10000000, 99999999),
                'address' => 'المملكة العربية السعودية، الرياض، ' . $faker->streetName,
            ]);
        }

        // ==========================================
        // 7. إنشاء قوالب المشاريع (Project Templates)
        // ==========================================
        $projectTemplatesData = [
            [
                'name' => 'نظام إدارة محتوى (CMS)',
                'description' => 'قالب لتطوير نظام إدارة محتوى متكامل يشمل إدارة الصفحات والمدونة والوسائط.',
                'budget' => 60000,
                'estimated_days' => 60,
                'tasks' => [
                    ['title' => 'تحليل المتطلبات وتصميم قاعدة البيانات', 'priority' => 'high', 'hours' => 40, 'sort' => 1],
                    ['title' => 'تصميم واجهات المستخدم', 'priority' => 'high', 'hours' => 30, 'sort' => 2],
                    ['title' => 'برمجة واجهات API الخلفية', 'priority' => 'high', 'hours' => 60, 'sort' => 3],
                    ['title' => 'برمجة الواجهة الأمامية', 'priority' => 'medium', 'hours' => 50, 'sort' => 4],
                    ['title' => 'نظام الصلاحيات وإدارة المستخدمين', 'priority' => 'high', 'hours' => 25, 'sort' => 5],
                    ['title' => 'اختبارات الجودة والنشر', 'priority' => 'medium', 'hours' => 20, 'sort' => 6],
                ],
            ],
            [
                'name' => 'تطبيق جوال للتجارة الإلكترونية',
                'description' => 'قالب لتطوير تطبيق تجارة إلكترونية متعدد المنصات مع نظام دفع متكامل.',
                'budget' => 90000,
                'estimated_days' => 90,
                'tasks' => [
                    ['title' => 'دراسة الجدوى وتحليل المتطلبات', 'priority' => 'high', 'hours' => 30, 'sort' => 1],
                    ['title' => 'تصميم تجربة وواجهة المستخدم (UX/UI)', 'priority' => 'high', 'hours' => 40, 'sort' => 2],
                    ['title' => 'تطوير خادم API والخدمات الخلفية', 'priority' => 'high', 'hours' => 70, 'sort' => 3],
                    ['title' => 'تطوير تطبيق iOS', 'priority' => 'medium', 'hours' => 60, 'sort' => 4],
                    ['title' => 'تطوير تطبيق Android', 'priority' => 'medium', 'hours' => 60, 'sort' => 5],
                    ['title' => 'دمج بوابة الدفع الإلكتروني', 'priority' => 'high', 'hours' => 20, 'sort' => 6],
                    ['title' => 'اختبارات شاملة وإطلاق التطبيق', 'priority' => 'medium', 'hours' => 25, 'sort' => 7],
                ],
            ],
            [
                'name' => 'نظام لوحة تحكم تحليلية',
                'description' => 'قالب لتطوير لوحة تحكم بيانات مع رسوم بيانية وتقارير تفصيلية.',
                'budget' => 45000,
                'estimated_days' => 45,
                'tasks' => [
                    ['title' => 'تحليل مؤشرات الأداء وتصميم لوحة التحكم', 'priority' => 'high', 'hours' => 25, 'sort' => 1],
                    ['title' => 'إعداد طبقة البيانات والاستعلامات', 'priority' => 'high', 'hours' => 35, 'sort' => 2],
                    ['title' => 'تطوير واجهة المستخدم والرسوم البيانية', 'priority' => 'medium', 'hours' => 40, 'sort' => 3],
                    ['title' => 'نظام التصدير والتقارير', 'priority' => 'medium', 'hours' => 20, 'sort' => 4],
                    ['title' => 'الاختبار والنشر', 'priority' => 'low', 'hours' => 15, 'sort' => 5],
                ],
            ],
        ];

        foreach ($projectTemplatesData as $ptData) {
            $pt = ProjectTemplate::create([
                'name' => $ptData['name'],
                'description' => $ptData['description'],
                'budget' => $ptData['budget'],
                'estimated_days' => $ptData['estimated_days'],
            ]);

            foreach ($ptData['tasks'] as $ttData) {
                TaskTemplate::create([
                    'project_template_id' => $pt->id,
                    'title' => $ttData['title'],
                    'description' => 'وصف تفصيلي للمهمة: ' . $ttData['title'],
                    'priority' => $ttData['priority'],
                    'estimated_hours' => $ttData['hours'],
                    'sort_order' => $ttData['sort'],
                ]);
            }
        }

        // ==========================================
        // 8. إنشاء المشاريع وربطها بالموظفين (Projects)
        // ==========================================
        $projectsList = [
            [
                'client_idx' => 0,
                'name' => 'نظام ERP داخلي لإدارة الموارد',
                'desc' => 'تطوير نظام لتخطيط موارد المؤسسة يشمل الموارد البشرية، المالية، المبيعات وإدارة المخزون.',
                'budget' => 150000,
                'status' => 'in_progress',
                'start_sub' => 40, 'end_add' => 120
            ],
            [
                'client_idx' => 1,
                'name' => 'تطبيق جوال لتوصيل الطلبات',
                'desc' => 'تطبيق للهواتف الذكية (iOS & Android) يتيح للعملاء طلب المنتجات وتتبعها في الوقت الفعلي.',
                'budget' => 80000,
                'status' => 'completed',
                'start_sub' => 150, 'end_add' => -10
            ],
            [
                'client_idx' => 2,
                'name' => 'منصة التعليم الإلكتروني التفاعلية',
                'desc' => 'منصة لتقديم دورات تدريبية عن بعد مع نظام لاختبار الطلاب وإصدار الشهادات.',
                'budget' => 120000,
                'status' => 'in_progress',
                'start_sub' => 15, 'end_add' => 90
            ],
            [
                'client_idx' => 3,
                'name' => 'نظام إدارة ملفات المرضى وحجز المواعيد',
                'desc' => 'تطوير نظام سحابي آمن لحفظ السجلات الطبية للمرضى وإدارة مواعيد العيادات الخارجية.',
                'budget' => 200000,
                'status' => 'pending',
                'start_sub' => -5, 'end_add' => 180
            ],
            [
                'client_idx' => 4,
                'name' => 'نظام تتبع أسطول الشحنات المتقدم',
                'desc' => 'نظام يعتمد على الـ GPS لتتبع حركة الشاحنات وتحسين خطوط السير.',
                'budget' => 95000,
                'status' => 'in_progress',
                'start_sub' => 60, 'end_add' => 45
            ],
        ];

        $createdProjects = [];

        foreach ($projectsList as $projData) {
            $project = Project::create([
                'client_id' => $clients[$projData['client_idx']]->id,
                'name' => $projData['name'],
                'description' => $projData['desc'],
                'budget' => $projData['budget'],
                'start_date' => Carbon::now()->subDays($projData['start_sub']),
                'end_date' => Carbon::now()->addDays($projData['end_add']),
                'status' => $projData['status'],
            ]);

            $createdProjects[] = $project;

            $teamIds = Employee::whereIn('user_id', User::where('role_id', $roles['employee']->id)->pluck('id'))->pluck('id')->toArray();
            $project->employees()->attach($faker->randomElements($teamIds, rand(3, 5)));
            $project->employees()->attach($employeeIds[2]);

            // ==========================================
            // 9. إنشاء مهام (Tasks) لكل مشروع
            // ==========================================
            $taskTemplates = [
                'تصميم واجهات المستخدم (UI/UX)',
                'تحليل متطلبات النظام وقاعدة البيانات',
                'برمجة واجهات الـ API (الخلفية)',
                'برمجة الواجهات الأمامية (Frontend)',
                'تطوير تطبيق الجوال',
                'إعداد السيرفرات وبيئة العمل (DevOps)',
                'إجراء اختبارات الجودة (QA Testing)',
                'النشر والتشغيل الأولي',
            ];

            $assignedEmployees = $project->employees()->pluck('employees.id')->toArray();
            $projectTasks = [];

            foreach ($faker->randomElements($taskTemplates, rand(4, 7)) as $taskTitle) {
                $status = ($projData['status'] === 'completed') ? 'done' : $faker->randomElement(['todo', 'in_progress', 'review', 'done']);

                $task = Task::create([
                    'project_id' => $project->id,
                    'employee_id' => $faker->randomElement($assignedEmployees),
                    'title' => $taskTitle,
                    'description' => 'الرجاء إنجاز هذه المهمة وفقاً للمواصفات المتفق عليها في مستند المشروع.',
                    'start_date' => Carbon::parse($project->start_date)->addDays(rand(0, 10)),
                    'due_date' => Carbon::parse($project->start_date)->addDays(rand(5, 30)),
                    'status' => $status,
                ]);

                $projectTasks[] = $task;

                // ==========================================
                // 9أ. إنشاء تعليقات على المهام (Task Comments)
                // ==========================================
                if ($faker->boolean(70)) {
                    $commentUsers = [$employeeIds[2]];
                    if (count($assignedEmployees) > 0) {
                        $commentUsers[] = $faker->randomElement($assignedEmployees);
                    }

                    foreach ($commentUsers as $commentEmpId) {
                        $emp = Employee::find($commentEmpId);
                        if ($emp) {
                            TaskComment::create([
                                'task_id' => $task->id,
                                'user_id' => $emp->user_id,
                                'comment' => $faker->randomElement([
                                    'تم البدء في العمل على هذه المهمة.',
                                    'هناك بعض الملاحظات التي تحتاج مراجعة من مدير المشروع.',
                                    'تم الانتهاء من المرحلة الأولى.',
                                    'أحتاج مساعدة إضافية في هذا الجزء.',
                                    'العمل يسير وفق الخطة المحددة.',
                                    'تم رفع التحديثات على الفرع الخاص بهذه المهمة.',
                                ]),
                            ]);
                        }
                    }
                }

                // ==========================================
                // 9ب. إنشاء مرفقات المهام (Task Attachments)
                // ==========================================
                if ($faker->boolean(40)) {
                    $attachEmp = Employee::find($faker->randomElement($assignedEmployees));
                    if ($attachEmp) {
                        TaskAttachment::create([
                            'task_id' => $task->id,
                            'user_id' => $attachEmp->user_id,
                            'file_name' => $faker->randomElement(['design_mockup.pdf', 'api_docs.docx', 'test_report.xlsx', 'requirements_v2.pdf', 'architecture_diagram.png']),
                            'file_path' => 'attachments/task_' . $task->id . '/' . $faker->randomElement(['design_mockup.pdf', 'api_docs.docx', 'test_report.xlsx']),
                            'file_type' => $faker->randomElement(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/png']),
                            'file_size' => rand(50000, 5000000),
                        ]);
                    }
                }

                // ==========================================
                // 9ج. إنشاء تتبع الوقت (Time Entries)
                // ==========================================
                if ($status === 'in_progress' || $status === 'done') {
                    $numEntries = rand(1, 5);
                    for ($e = 0; $e < $numEntries; $e++) {
                        TimeEntry::create([
                            'task_id' => $task->id,
                            'employee_id' => $task->employee_id,
                            'date' => Carbon::now()->subDays(rand(0, 14)),
                            'hours' => $faker->randomFloat(2, 0.5, 8),
                            'description' => $faker->randomElement([
                                'عمل على تطوير الكود الأساسي',
                                'مراجعة وإصلاح الأخطاء',
                                'كتابة اختبارات الوحدة',
                                'تحديث التوثيق الفني',
                                'اجتماع مراجعة التقدم',
                            ]),
                        ]);
                    }
                }
            }

            // ==========================================
            // 10. إنشاء فواتير (Invoices) مع بنود وضريبة
            // ==========================================
            $invoiceStatuses = ['unpaid', 'paid', 'overdue'];
            $invStatus = ($projData['status'] === 'completed') ? 'paid' : $faker->randomElement($invoiceStatuses);
            $vatRate = 15.00;

            $invoice1 = Invoice::create([
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'invoice_number' => 'INV-2024-' . strtoupper($faker->unique()->bothify('####')),
                'amount' => 0,
                'vat_rate' => $vatRate,
                'vat_amount' => 0,
                'total_with_vat' => 0,
                'issue_date' => Carbon::parse($project->start_date)->addDays(2),
                'due_date' => Carbon::parse($project->start_date)->addDays(16),
                'status' => 'paid',
            ]);

            $firstPayment = $project->budget * 0.3;
            $item1Total = round($firstPayment * 0.6, 2);
            $item2Total = round($firstPayment * 0.4, 2);

            InvoiceItem::create([
                'invoice_id' => $invoice1->id,
                'description' => 'تصميم وتطوير واجهات المستخدم - المرحلة الأولى',
                'quantity' => 1,
                'unit_price' => $item1Total,
                'total' => $item1Total,
            ]);
            InvoiceItem::create([
                'invoice_id' => $invoice1->id,
                'description' => 'تحليل المتطلبات وتصميم قاعدة البيانات - المرحلة الأولى',
                'quantity' => 1,
                'unit_price' => $item2Total,
                'total' => $item2Total,
            ]);
            $invoice1->calculateTotals();

            $invoice2 = Invoice::create([
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'invoice_number' => 'INV-2024-' . strtoupper($faker->unique()->bothify('####')),
                'amount' => 0,
                'vat_rate' => $vatRate,
                'vat_amount' => 0,
                'total_with_vat' => 0,
                'issue_date' => Carbon::now()->subDays(rand(5, 20)),
                'due_date' => Carbon::now()->addDays(rand(5, 10)),
                'status' => $invStatus,
            ]);

            $secondPayment = $project->budget * 0.4;
            $item3Total = round($secondPayment * 0.5, 2);
            $item4Total = round($secondPayment * 0.3, 2);
            $item5Total = round($secondPayment * 0.2, 2);

            InvoiceItem::create([
                'invoice_id' => $invoice2->id,
                'description' => 'برمجة واجهات API الخلفية - المرحلة الثانية',
                'quantity' => 1,
                'unit_price' => $item3Total,
                'total' => $item3Total,
            ]);
            InvoiceItem::create([
                'invoice_id' => $invoice2->id,
                'description' => 'برمجة الواجهة الأمامية - المرحلة الثانية',
                'quantity' => 1,
                'unit_price' => $item4Total,
                'total' => $item4Total,
            ]);
            InvoiceItem::create([
                'invoice_id' => $invoice2->id,
                'description' => 'اختبارات الجودة والنشر التجريبي - المرحلة الثانية',
                'quantity' => 1,
                'unit_price' => $item5Total,
                'total' => $item5Total,
            ]);
            $invoice2->calculateTotals();
        }

        // ==========================================
        // 11. إنشاء المصروفات (Expenses) مع ربط المشاريع والموافقات
        // ==========================================
        $accountantUser = User::where('email', 'finance@techcompany.com')->first();
        $ceoUser = User::where('email', 'ceo@techcompany.com')->first();

        $realExpenses = [
            ['title' => 'إيجار مقر الشركة - الربع الأول', 'category' => 'إيجار مكتب', 'amount' => 45000, 'project' => null, 'status' => 'approved'],
            ['title' => 'تجديد اشتراكات سيرفرات AWS', 'category' => 'تراخيص برامج وسيرفرات', 'amount' => 5500, 'project' => 0, 'status' => 'approved'],
            ['title' => 'تجديد تراخيص JetBrains & Adobe', 'category' => 'تراخيص برامج وسيرفرات', 'amount' => 3200, 'project' => null, 'status' => 'approved'],
            ['title' => 'حملة تسويقية على منصة لينكد إن', 'category' => 'تسويق', 'amount' => 8000, 'project' => null, 'status' => 'approved'],
            ['title' => 'شراء أجهزة حواسيب محمولة للموظفين الجدد', 'category' => 'أصول ومعدات', 'amount' => 15000, 'project' => null, 'status' => 'approved'],
            ['title' => 'نثريات وضيافة للمكتب', 'category' => 'ضيافة', 'amount' => 1200, 'project' => null, 'status' => 'pending'],
            ['title' => 'رسوم استشارات قانونية', 'category' => 'استشارات', 'amount' => 4000, 'project' => null, 'status' => 'approved'],
            ['title' => 'مصاريف سفر لزيارة عميل مشروع التوصيل', 'category' => 'سفر وانتقالات', 'amount' => 3500, 'project' => 1, 'status' => 'approved'],
            ['title' => 'شراء域名 وأجهزة شبكية لمشروع التعليم', 'category' => 'أصول ومعدات', 'amount' => 6000, 'project' => 2, 'status' => 'pending'],
            ['title' => 'تكاليف استضافة سحابية لمشروع المستشفى', 'category' => 'تراخيص برامج وسيرفرات', 'amount' => 2800, 'project' => 3, 'status' => 'rejected'],
        ];

        foreach ($realExpenses as $exp) {
            Expense::create([
                'user_id' => $accountantUser->id,
                'project_id' => $exp['project'] !== null ? $createdProjects[$exp['project']]->id : null,
                'title' => $exp['title'],
                'category' => $exp['category'],
                'amount' => $exp['amount'],
                'expense_date' => Carbon::now()->subDays(rand(1, 60)),
                'status' => $exp['status'],
                'approved_by' => $exp['status'] === 'approved' ? $ceoUser->id : null,
            ]);
        }

        // ==========================================
        // 12. إنشاء التقارير (Reports)
        // ==========================================
        $realReports = [
            ['title' => 'تقرير الإنجاز الأسبوعي لتطبيق التوصيل', 'sender' => 2, 'receiver' => 0],
            ['title' => 'تقرير أداء السيرفرات واستخدام الموارد', 'sender' => 9, 'receiver' => 0],
            ['title' => 'تقرير الميزانية والمصروفات للربع الأول', 'sender' => 3, 'receiver' => 0],
            ['title' => 'احتياجات التوظيف لقسم تطوير الويب', 'sender' => 2, 'receiver' => 1],
            ['title' => 'نتائج اختبارات الجودة لمنصة التعليم', 'sender' => 8, 'receiver' => 2],
        ];

        foreach ($realReports as $rep) {
            Report::create([
                'sender_id' => $employeeIds[$rep['sender']],
                'receiver_id' => $employeeIds[$rep['receiver']],
                'title' => $rep['title'],
                'content' => 'مرفق لكم تفاصيل التقرير بناءً على البيانات المسجلة في النظام خلال الفترة الماضية. يرجى المراجعة والإفادة.',
                'feedback' => $faker->optional(0.5)->realText(100),
                'status' => $faker->randomElement(['unread', 'read', 'replied']),
            ]);
        }

        // ==========================================
        // 13. إنشاء الإجازات (Leaves) مع تحديث رصيد الإجازات
        // ==========================================
        $leaveTypes = ['Annual', 'Sick', 'Emergency'];
        $leaveStatuses = ['pending', 'approved_by_head', 'approved_by_hr', 'rejected'];

        for ($i = 1; $i <= 12; $i++) {
            $empId = $faker->randomElement($employeeIds);
            $startDate = Carbon::now()->addDays(rand(-30, 30));
            $endDate = (clone $startDate)->addDays(rand(1, 5));
            $leaveStatus = $faker->randomElement($leaveStatuses);
            $duration = $startDate->diffInDays($endDate) + 1;

            Leave::create([
                'employee_id' => $empId,
                'type' => $faker->randomElement($leaveTypes),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $faker->randomElement([
                    'أسباب عائلية',
                    'إجازة سنوية مخططة',
                    'ظروف صحية طارئة',
                    'مراجعة طبية',
                    'إجازة طارئة لظروف شخصية',
                ]),
                'status' => $leaveStatus,
            ]);

            if (in_array($leaveStatus, ['approved_by_head', 'approved_by_hr'])) {
                $emp = Employee::find($empId);
                if ($emp) {
                    $emp->increment('used_leave_days', $duration);
                }
            }
        }

        // ==========================================
        // 14. إنشاء مسيرات الرواتب (Payrolls) مع البدلات
        // ==========================================
        $months = [Carbon::now()->subMonth()->format('Y-m'), Carbon::now()->format('Y-m')];

        foreach ($months as $monthYear) {
            foreach ($employeeIds as $empId) {
                $emp = Employee::find($empId);
                $bonuses = $faker->randomElement([0, 0, 500, 1000]);
                $deductions = $faker->randomElement([0, 0, 0, 200, 500]);

                $housingAllowance = round($emp->salary * 0.25, 2);
                $transportAllowance = round($emp->salary * 0.10, 2);
                $phoneAllowance = $faker->randomElement([200, 300, 500]);
                $socialInsuranceRate = 9.75;
                $socialInsuranceAmount = round($emp->salary * ($socialInsuranceRate / 100), 2);
                $absenceDays = $faker->randomElement([0, 0, 0, 0, 1, 2]);
                $absenceDeduction = round(($emp->salary / 30) * $absenceDays, 2);

                $netSalary = Payroll::calculateNetSalary(
                    $emp->salary,
                    $bonuses,
                    $deductions,
                    $housingAllowance,
                    $transportAllowance,
                    $phoneAllowance,
                    $socialInsuranceRate,
                    $absenceDeduction
                );

                Payroll::create([
                    'employee_id' => $empId,
                    'month_year' => $monthYear,
                    'basic_salary' => $emp->salary,
                    'bonuses' => $bonuses,
                    'deductions' => $deductions,
                    'housing_allowance' => $housingAllowance,
                    'transport_allowance' => $transportAllowance,
                    'phone_allowance' => $phoneAllowance,
                    'social_insurance_rate' => $socialInsuranceRate,
                    'social_insurance_amount' => $socialInsuranceAmount,
                    'absence_days' => $absenceDays,
                    'absence_deduction' => $absenceDeduction,
                    'net_salary' => $netSalary,
                    'status' => ($monthYear === $months[0]) ? 'paid' : $faker->randomElement(['paid', 'unpaid']),
                ]);
            }
        }

        // ==========================================
        // 15. إنشاء سجلات الحضور (Attendance)
        // ==========================================
        foreach ($employeeIds as $empId) {
            for ($d = 0; $d < 20; $d++) {
                $date = Carbon::now()->subDays($d);

                if ($date->isWeekend()) {
                    continue;
                }

                $status = $faker->randomElement(['present', 'present', 'present', 'late', 'absent', 'half_day']);

                $checkIn = null;
                $checkOut = null;
                $hoursWorked = 0;
                $notes = null;

                if ($status === 'present') {
                    $checkIn = $date->copy()->setTime(8, rand(0, 30), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0);
                    $hoursWorked = round($checkIn->diffInMinutes($checkOut) / 60, 2);
                } elseif ($status === 'late') {
                    $checkIn = $date->copy()->setTime(9, rand(20, 59), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0);
                    $hoursWorked = round($checkIn->diffInMinutes($checkOut) / 60, 2);
                    $notes = 'تأخير في الحضور';
                } elseif ($status === 'half_day') {
                    $checkIn = $date->copy()->setTime(8, rand(0, 30), 0);
                    $checkOut = $date->copy()->setTime(13, rand(0, 0), 0);
                    $hoursWorked = round($checkIn->diffInMinutes($checkOut) / 60, 2);
                    $notes = 'دوام نصف يوم';
                }

                Attendance::create([
                    'employee_id' => $empId,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'hours_worked' => $hoursWorked,
                    'status' => $status,
                    'notes' => $notes,
                ]);
            }
        }

        // ==========================================
        // 16. إنشاء الدورات التدريبية (Trainings)
        // ==========================================
        $trainingsData = [
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel المتقدم',
                'description' => 'دورة تدريبية متقدمة في إطار عمل Laravel تشمل تصميم الـ API، إدارة الطوابير، والأنماط المتقدمة.',
                'trainer' => 'م. عبدالرحمن الشمري',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(5),
                'status' => 'ongoing',
                'location' => 'قاعة التدريب الرئيسية - الرياض',
                'max_participants' => 15,
            ],
            [
                'title' => 'أساسيات DevOps و Docker',
                'description' => 'دورة شاملة لتعلم أساسيات DevOps وإنشاء بيئات تطوير معزولة باستخدام Docker و Kubernetes.',
                'trainer' => 'م. فهد العتيبي',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(15),
                'status' => 'completed',
                'location' => 'عن بُعد (Zoom)',
                'max_participants' => 20,
            ],
            [
                'title' => 'تصميم واجهات المستخدم UX/UI',
                'description' => 'دورة في تصميم تجربة وواجهة المستخدم باستخدام Figma مع مشاريع عملية.',
                'trainer' => 'أ. نورة الحربي',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(25),
                'status' => 'upcoming',
                'location' => 'قاعة الابتكار - جدة',
                'max_participants' => 12,
            ],
            [
                'title' => 'إدارة المشاريع الاحترافية PMP',
                'description' => 'دورة تحضيرية لشهادة PMP تشمل إطار عمل إدارة المشاريع وأساليب Agile و Waterfall.',
                'trainer' => 'د. محمد القحطاني',
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(40),
                'status' => 'upcoming',
                'location' => 'فندق الريتز كارلتون - الرياض',
                'max_participants' => 25,
            ],
            [
                'title' => 'اختبار البرمجيات الآلي Selenium',
                'description' => 'دورة عملية في الاختبار الآلي باستخدام Selenium WebDriver مع Java.',
                'trainer' => 'م. سلطان الدوسري',
                'start_date' => Carbon::now()->subDays(45),
                'end_date' => Carbon::now()->subDays(30),
                'status' => 'completed',
                'location' => 'مركز التدريب التقني - الدمام',
                'max_participants' => 10,
            ],
        ];

        $createdTrainings = [];
        foreach ($trainingsData as $trainData) {
            $training = Training::create($trainData);
            $createdTrainings[] = $training;

            $numParticipants = min(rand(3, 8), count($employeeIds));
            $participantIds = $faker->randomElements($employeeIds, $numParticipants);

            foreach ($participantIds as $partEmpId) {
                $pivotStatus = 'enrolled';
                $completionDate = null;
                $certUrl = null;

                if ($trainData['status'] === 'completed') {
                    $pivotStatus = $faker->randomElement(['completed', 'certified']);
                    $completionDate = Carbon::parse($trainData['end_date'])->addDays(rand(0, 5));
                    if ($pivotStatus === 'certified') {
                        $certUrl = 'certificates/training_' . $training->id . '_emp_' . $partEmpId . '.pdf';
                    }
                }

                $training->employees()->attach($partEmpId, [
                    'status' => $pivotStatus,
                    'certificate_url' => $certUrl,
                    'completion_date' => $completionDate,
                ]);
            }
        }

        // ==========================================
        // 17. إنشاء الشهادات (Certificates)
        // ==========================================
        $certificatesData = [
            ['emp_idx' => 4, 'title' => 'AWS Certified Solutions Architect – Associate', 'issuer' => 'Amazon Web Services', 'months_ago' => 8],
            ['emp_idx' => 9, 'title' => 'Certified Kubernetes Administrator (CKA)', 'issuer' => 'Cloud Native Computing Foundation', 'months_ago' => 5],
            ['emp_idx' => 5, 'title' => 'Vue.js Certified Developer', 'issuer' => 'Vue School', 'months_ago' => 3],
            ['emp_idx' => 6, 'title' => 'Google Associate Android Developer', 'issuer' => 'Google', 'months_ago' => 10],
            ['emp_idx' => 8, 'title' => 'ISTQB Certified Tester Foundation Level', 'issuer' => 'ISTQB', 'months_ago' => 6],
            ['emp_idx' => 7, 'title' => 'Google UX Design Professional Certificate', 'issuer' => 'Google / Coursera', 'months_ago' => 4],
            ['emp_idx' => 2, 'title' => 'PMI Agile Certified Practitioner (PMI-ACP)', 'issuer' => 'Project Management Institute', 'months_ago' => 12],
            ['emp_idx' => 4, 'title' => 'Laravel Certified Developer', 'issuer' => 'Laravel', 'months_ago' => 2],
        ];

        foreach ($certificatesData as $certData) {
            $issueDate = Carbon::now()->subMonths($certData['months_ago']);
            Certificate::create([
                'employee_id' => $employeeIds[$certData['emp_idx']],
                'title' => $certData['title'],
                'issuer' => $certData['issuer'],
                'issue_date' => $issueDate,
                'expiry_date' => $faker->optional(0.4) ? $issueDate->copy()->addYears(2) : null,
                'certificate_url' => 'certificates/' . strtolower(str_replace([' ', '/', '–', '(', ')'], '_', $certData['title'])) . '.pdf',
            ]);
        }

        // ==========================================
        // 18. إنشاء خطط المسيرة المهنية (Career Plans)
        // ==========================================
        $careerPlansData = [
            ['emp_idx' => 4, 'current' => 'Senior Laravel Developer', 'target' => 'Tech Lead / مدير تقني', 'months' => 18, 'skills' => 'Leadership, System Architecture, Microservices, Code Review', 'status' => 'active'],
            ['emp_idx' => 5, 'current' => 'Frontend Vue.js Developer', 'target' => 'Senior Frontend Engineer', 'months' => 12, 'skills' => 'TypeScript, Nuxt.js, Performance Optimization, Testing', 'status' => 'active'],
            ['emp_idx' => 6, 'current' => 'Mobile App Developer (Flutter)', 'target' => 'Mobile Team Lead', 'months' => 24, 'skills' => 'iOS Native, Android Native, CI/CD Mobile, Team Management', 'status' => 'active'],
            ['emp_idx' => 8, 'current' => 'QA Engineer', 'target' => 'QA Manager', 'months' => 15, 'skills' => 'Test Strategy, Automation Frameworks, Performance Testing, Team Leadership', 'status' => 'draft'],
            ['emp_idx' => 9, 'current' => 'DevOps Engineer', 'target' => 'Senior DevOps / SRE', 'months' => 12, 'skills' => 'Kubernetes Advanced, Terraform, Monitoring, Incident Management', 'status' => 'active'],
            ['emp_idx' => 7, 'current' => 'UI/UX Designer', 'target' => 'Design Lead', 'months' => 18, 'skills' => 'Design Systems, User Research, Design Strategy, Team Leadership', 'status' => 'draft'],
        ];

        foreach ($careerPlansData as $planData) {
            CareerPlan::create([
                'employee_id' => $employeeIds[$planData['emp_idx']],
                'current_role' => $planData['current'],
                'target_role' => $planData['target'],
                'timeline_months' => $planData['months'],
                'required_skills' => $planData['skills'],
                'notes' => 'تم وضع الخطة بالتعاون مع المدير المباشر وقسم الموارد البشرية.',
                'status' => $planData['status'],
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
