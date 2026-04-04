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
use App\Models\Invoice;
use App\Models\Expense;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // تهيئة Faker لتوليد أسماء وبيانات عربية
        $faker = Faker::create('ar_SA');

        // إيقاف فحص المفاتيح الأجنبية لتفريغ الجداول (مفيد عند عمل Seed أكثر من مرة)
        Schema::disableForeignKeyConstraints();

        // ==========================================
        // 1. إنشاء الأدوار (Roles)
        // ==========================================
        $rolesData = [['name' => 'super_admin', 'description' => 'المدير العام للنظام (صلاحيات كاملة)'],['name' => 'hr_manager', 'description' => 'مدير الموارد البشرية والتوظيف'],['name' => 'project_manager', 'description' => 'مدير المشاريع والمهام'],['name' => 'accountant', 'description' => 'المحاسب والمدير المالي'],
            ['name' => 'employee', 'description' => 'موظف عادي'],
        ];

        $roles =[];
        foreach ($rolesData as $data) {
            $roles[$data['name']] = Role::create($data);
        }

        // ==========================================
        // 2. إنشاء المهارات (Skills)
        // ==========================================
        $skillsList =[
            'Laravel', 'PHP', 'React', 'Vue.js', 'MySQL', 'Python',
            'UI/UX Design', 'Project Management', 'Agile/Scrum',
            'Accounting', 'Digital Marketing', 'SEO', 'Communication'
        ];

        foreach ($skillsList as $skill) {
            Skill::create(['name' => $skill]);
        }
        $allSkills = Skill::all();

        // ==========================================
        // 3. إنشاء حسابات مستخدمين أساسية (Users & Employees)
        // ==========================================
        $defaultPassword = Hash::make('password'); // كلمة المرور للجميع: password

        $keyUsers = [['name' => 'أحمد المدير', 'email' => 'admin@erp.com', 'role' => 'super_admin', 'job' => 'CEO', 'salary' => 15000],['name' => 'سارة الموارد', 'email' => 'hr@erp.com', 'role' => 'hr_manager', 'job' => 'HR Manager', 'salary' => 8000],['name' => 'عمر المشاريع', 'email' => 'pm@erp.com', 'role' => 'project_manager', 'job' => 'Project Manager', 'salary' => 10000],['name' => 'خالد المحاسب', 'email' => 'acc@erp.com', 'role' => 'accountant', 'job' => 'Senior Accountant', 'salary' => 7000],['name' => 'فاطمة المبرمجة', 'email' => 'emp1@erp.com', 'role' => 'employee', 'job' => 'Senior Laravel Developer', 'salary' => 6000],['name' => 'محمود المصمم', 'email' => 'emp2@erp.com', 'role' => 'employee', 'job' => 'UI/UX Designer', 'salary' => 5000],
        ];

        $employeeIds =[]; // لحفظ أرقام الموظفين لاستخدامها لاحقاً في المهام

        foreach ($keyUsers as $userData) {
            // إنشاء المستخدم
            $user = User::create([
                'role_id' => $roles[$userData['role']]->id,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $defaultPassword,
            ]);

            // إنشاء ملف موظف له
            $employee = Employee::create([
                'user_id' => $user->id,
                'job_title' => $userData['job'],
                'salary' => $userData['salary'],
                'status' => 'active',
                'hire_date' => Carbon::now()->subMonths(rand(2, 24)),
            ]);

            $employeeIds[] = $employee->id;

            // إنشاء سيرة ذاتية وهمية للذكاء الاصطناعي ليقرأها
            Resume::create([
                'employee_id' => $employee->id,
                'file_path' => 'resumes/dummy_resume_' . $employee->id . '.pdf',
                'resume_text' => $faker->realText(500) . " Experienced in " . $allSkills->random()->name . " and " . $allSkills->random()->name,
            ]);

            // ربط الموظف بـ 3 مهارات عشوائية
            $employee->skills()->attach($allSkills->random(3)->pluck('id'));
        }

        // ==========================================
        // 4. إنشاء العملاء (Clients)
        // ==========================================
        $clients =[];
        for ($i = 1; $i <= 5; $i++) {
            $clients[] = Client::create([
                'name' => $faker->name,
                'company_name' => $faker->company,
                'email' => $faker->unique()->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
            ]);
        }

        // ==========================================
        // 5. إنشاء المشاريع وربطها بالموظفين (Projects)
        // ==========================================
        $projectsList = [['name' => 'تطوير تطبيق متجر إلكتروني', 'budget' => 25000],['name' => 'تصميم هوية بصرية كاملة', 'budget' => 5000],['name' => 'نظام إدارة مطاعم (ERP)', 'budget' => 40000],['name' => 'حملة تسويق رقمي وإدارة حسابات', 'budget' => 12000],
        ];

        $projectStatuses =['pending', 'in_progress', 'completed'];

        foreach ($projectsList as $projData) {
            $project = Project::create([
                'client_id' => $faker->randomElement($clients)->id,
                'name' => $projData['name'],
                'description' => $faker->realText(200),
                'budget' => $projData['budget'],
                'start_date' => Carbon::now()->subDays(rand(10, 60)),
                'end_date' => Carbon::now()->addDays(rand(30, 100)),
                'status' => $faker->randomElement($projectStatuses),
            ]);

            // ربط من 2 إلى 4 موظفين عشوائيين بهذا المشروع
            $project->employees()->attach($faker->randomElements($employeeIds, rand(2, 4)));

            // ==========================================
            // 6. إنشاء مهام (Tasks) لكل مشروع
            // ==========================================
            $taskStatuses =['todo', 'in_progress', 'review', 'done'];

            for ($i = 1; $i <= 4; $i++) {
                Task::create([
                    'project_id' => $project->id,
                    'employee_id' => $faker->randomElement($employeeIds),
                    'title' => 'مهمة متعلقة بـ ' . $project->name . ' - الجزء ' . $i,
                    'description' => $faker->realText(100),
                    'due_date' => Carbon::now()->addDays(rand(1, 15)),
                    'status' => $faker->randomElement($taskStatuses),
                ]);
            }

            // ==========================================
            // 7. إنشاء فواتير (Invoices) لكل مشروع
            // ==========================================
            $invoiceStatuses = ['unpaid', 'paid', 'overdue'];

            Invoice::create([
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'invoice_number' => 'INV-' . strtoupper($faker->unique()->bothify('####-????')),
                'amount' => $project->budget / 2, // الفاتورة بنصف ميزانية المشروع مثلاً
                'issue_date' => Carbon::now()->subDays(rand(5, 20)),
                'due_date' => Carbon::now()->addDays(rand(5, 10)),
                'status' => $faker->randomElement($invoiceStatuses),
            ]);
        }

        // ==========================================
        // 8. إنشاء المصروفات (Expenses)
        // ==========================================
        $expenseCategories =['رواتب', 'إيجار مكتب', 'تراخيص برامج', 'تسويق', 'أدوات مكتبية', 'ضيافة'];
        $accountantUser = User::where('email', 'acc@erp.com')->first(); // لنسند المصاريف للمحاسب

        for ($i = 1; $i <= 10; $i++) {
            Expense::create([
                'user_id' => $accountantUser->id,
                'title' => 'مصروف ' . $faker->randomElement($expenseCategories),
                'category' => $faker->randomElement($expenseCategories),
                'amount' => rand(100, 5000),
                'expense_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        // إعادة تفعيل فحص المفاتيح الأجنبية
        Schema::enableForeignKeyConstraints();
    }
}
