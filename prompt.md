🤖[Project Master Brief & Development Prompt for AI Agent]
📌 1. Project Overview (نظرة عامة على المشروع)
Name: ERP-Lite & Smart ATS System
Description: A comprehensive internal Enterprise Resource Planning (ERP) and Applicant Tracking System (ATS) tailored for SMEs. The system manages Human Resources, Projects, Tasks, Accounting, and Recruitment with integrated AI capabilities to assist in decision-making and resume parsing.
Core Philosophy: Security, Centralized Authentication, Multi-Panel Architecture, and strict Business Logic constraints (e.g., unsigned financial values).
🛠 2. Technology Stack (التقنيات المستخدمة)
Backend: Laravel 12 (PHP 8.2+)
Admin Panels & UI: FilamentPHP v3.3 (Multi-Panel Architecture)
Authentication: Custom Centralized Auth using Livewire 3 + Alpine.js + TailwindCSS (Bypassing default Filament Login).
Database: MySQL (Relational Schema).
AI Integration: OpenAI API (or similar) for NLP tasks.
🏗 3. Current Project State & Architecture (التقدم الحالي)
The project has successfully implemented the foundational core:
Multi-Panels (5 Panels): Admin, HR, Project Manager (PM), Accountant, Employee.
Centralized Auth: A Livewire login/register system. Registration is treated as a "Job Application" (ATS).
ATS Workflow: New registrations create an Employee record with a pending status. HR reviews the application, parses the uploaded resume, and can accept (changes to active) or reject (adds rejection_reason).
Resource Management: CRUD operations, Relation Managers, and Widgets (Charts & Stats) are built for Clients, Projects, Tasks, Invoices, Expenses, Employees, and Skills.
Strict Logic: Financial fields (budget, salary, amount) are protected at both frontend (minValue(0)) and database levels (unsignedDecimal).
🚀 4. New Development Requirements (التطويرات المطلوبة للتنفيذ)
Dear AI Agent, your task is to implement the following 7 new modules logically and seamlessly into the existing architecture:
Departments & Heads (الأقسام ورؤساء الأقسام):
Create a departments system where each department has a specific Head (Supervisor).
Link every Employee to a Department.
Update relationships so the Head can manage/view their department's employees.
Internal Reports System (نظام التقارير):
Allow employees to send daily/weekly reports to their Department Head or Super Admin.
Include statuses (unread, read, replied) and feedback capabilities.
Leaves & Attendance (نظام الإجازات والمغادرات):
Employees can request leaves (Annual, Sick, Emergency).
Workflow: Request -> Department Head Approval -> HR Final Approval.
Kanban Board for Tasks (لوحة كانبان للمهام):
Integrate a Kanban Board view for the TaskResource (using a Filament Kanban plugin or custom Livewire component).
Drag-and-drop functionality to change task statuses (todo, in_progress, review, done).
Real-time Notifications (الإشعارات الفورية):
Implement Filament's Database Notifications.
Triggers: New Task assigned, Leave request approved/rejected, Job application accepted/rejected, New report received.
Payroll & Payslips (مسيرات الرواتب):
Accountants can generate monthly payrolls for active employees.
Calculate: Basic Salary + Bonuses - Deductions/Unpaid Leaves = Net Salary.
Generate a PDF payslip for the employee.
AI Employee Evaluation (تقييم الموظف بالذكاء الاصطناعي):
An action button for HR/Admin: "AI Evaluation".
The AI will read the employee's completed tasks, delayed tasks, and reports, then generate a summarized performance review.
📊 5. Updated Database Schema (DBML Format)
AI Agent: Use this exact schema to generate Migrations and Eloquent Models and seeder.
code
Dbml
// ==========================================
// Enums
// ==========================================
Enum project_status { pending, in_progress, completed, canceled }
Enum task_status { todo, in_progress, review, done }
Enum invoice_status { unpaid, paid, overdue }
Enum employee_status { pending, active, on_leave, terminated, rejected }
Enum leave_status { pending, approved_by_head, approved_by_hr, rejected }
Enum report_status { unread, read, replied }

// ==========================================
// System & Auth
// ==========================================
Table roles {
  id bigint [pk, increment]
  name varchar [unique]
  description text
}

Table users {
  id bigint [pk, increment]
  role_id bigint
  name varchar
  email varchar [unique]
  password varchar
}

// ==========================================
// Organization & HR (New Features Included)
// ==========================================
Table departments {
  id bigint[pk, increment]
  name varchar
  head_id bigint [note: 'References employees.id (Head of Dept)']
}

Table employees {
  id bigint [pk, increment]
  user_id bigint [unique]
  department_id bigint [note: 'Can be null for new applicants']
  job_title varchar
  salary decimal(10,2) [note: 'unsigned']
  status employee_status[default: 'pending']
  rejection_reason text
  hire_date date
}

Table resumes {
  id bigint [pk, increment]
  employee_id bigint [unique]
  file_path varchar
  resume_text text [note: 'AI Parsed Text']
}

Table skills {
  id bigint [pk, increment]
  name varchar [unique]
}

Table employee_skill {
  employee_id bigint
  skill_id bigint
  indexes { (employee_id, skill_id) [pk] }
}

Table leaves {
  id bigint [pk, increment]
  employee_id bigint
  type varchar[note: 'Sick, Annual, Emergency']
  start_date date
  end_date date
  reason text
  status leave_status [default: 'pending']
}

Table payrolls {
  id bigint [pk, increment]
  employee_id bigint
  month_year varchar[note: 'e.g., 2026-05']
  basic_salary decimal(10,2) [note: 'unsigned']
  bonuses decimal(10,2) [note: 'unsigned']
  deductions decimal(10,2)[note: 'unsigned']
  net_salary decimal(10,2) [note: 'unsigned']
  status varchar [default: 'unpaid']
}

// ==========================================
// Operations & Communications
// ==========================================
Table reports {
  id bigint [pk, increment]
  sender_id bigint[note: 'employees.id']
  receiver_id bigint [note: 'employees.id']
  title varchar
  content text
  feedback text
  status report_status [default: 'unread']
  created_at timestamp
}

Table clients {
  id bigint [pk, increment]
  name varchar
  company_name varchar
  email varchar
  phone varchar
}

Table projects {
  id bigint [pk, increment]
  client_id bigint
  name varchar
  budget decimal(12,2)[note: 'unsigned']
  status project_status [default: 'pending']
}

Table employee_project {
  project_id bigint
  employee_id bigint
  indexes { (project_id, employee_id)[pk] }
}

Table tasks {
  id bigint [pk, increment]
  project_id bigint
  employee_id bigint
  title varchar
  status task_status [default: 'todo']
  due_date date
}

// ==========================================
// Finance
// ==========================================
Table invoices {
  id bigint [pk, increment]
  client_id bigint
  project_id bigint
  amount decimal(12,2) [note: 'unsigned']
  status invoice_status[default: 'unpaid']
}

Table expenses {
  id bigint [pk, increment]
  user_id bigint
  category varchar
  amount decimal(12,2) [note: 'unsigned']
}

// ==========================================
// Relationships
// ==========================================
Ref: users.role_id > roles.id
Ref: employees.user_id - users.id
Ref: employees.department_id > departments.id
Ref: departments.head_id > employees.id
Ref: resumes.employee_id - employees.id
Ref: employee_skill.employee_id > employees.id
Ref: employee_skill.skill_id > skills.id
Ref: leaves.employee_id > employees.id
Ref: payrolls.employee_id > employees.id
Ref: reports.sender_id > employees.id
Ref: reports.receiver_id > employees.id
Ref: projects.client_id > clients.id
Ref: employee_project.project_id > projects.id
Ref: employee_project.employee_id > employees.id
Ref: tasks.project_id > projects.id
Ref: tasks.employee_id > employees.id
Ref: invoices.client_id > clients.id
Ref: invoices.project_id > projects.id
Ref: expenses.user_id > users.id
🎯 6. Instructions for the AI Agent (تعليمات التنفيذ للذكاء الاصطناعي)
Step-by-Step Execution: Do not attempt to write the entire code at once. Ask the user which module to implement first (e.g., "Shall we start with Departments & Heads?").
Migrations First: When implementing a new module, always start by generating the Migration and updating the corresponding Model relationships.
Filament v3.3 Best Practices:
Ensure new resources are assigned to the correct Panels (using --panel=...).
Use Section, Grid, Filters, and RelationManagers extensively to maintain a professional UI.
Strictly use Filament's Native Database Notifications Notification::make()->title('...')->sendToDatabase($user);.
AI Logic Integration: When implementing the "AI Employee Evaluation", create a dedicated Action in Filament that gathers Data (Tasks + Reports) and formats a structured prompt to send to the OpenAI API, then displays the result in a Modal.
Kanban: Recommend and implement a stable Filament Kanban package (like mokhosh/filament-kanban) for the Task management requirement.
End of Prompt
