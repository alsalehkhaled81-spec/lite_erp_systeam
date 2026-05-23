<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Panel Brand Names
    |--------------------------------------------------------------------------
    */
    'brand' => [
        'admin' => 'Admin Panel',
        'pm' => 'Project Management',
        'hr' => 'Human Resources',
        'employee' => 'Employee Portal',
        'accountant' => 'Accountant Panel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation & Model Labels
    |--------------------------------------------------------------------------
    */
    'nav' => [
        'employees' => 'Employees',
        'departments' => 'Departments',
        'leaves' => 'Leaves',
        'leave_requests' => 'Leave Requests',
        'payrolls' => 'Payrolls',
        'projects' => 'Projects',
        'tasks' => 'Tasks',
        'clients' => 'Clients',
        'invoices' => 'Invoices',
        'expenses' => 'Expenses',
        'reports' => 'Reports',
        'users' => 'Users',
        'roles' => 'Roles',
        'skills' => 'Skills',
        'resumes' => 'Resumes',
        'kanban_board' => 'Kanban Board',
        'my_tasks_kanban' => 'My Tasks',
    ],

    'model' => [
        'employee' => 'Employee',
        'employees' => 'Employees',
        'department' => 'Department',
        'departments' => 'Departments',
        'leave' => 'Leave',
        'leaves' => 'Leaves',
        'leave_request' => 'Leave Request',
        'leave_requests' => 'Leave Requests',
        'payroll' => 'Payroll',
        'payrolls' => 'Payrolls',
        'project' => 'Project',
        'projects' => 'Projects',
        'task' => 'Task',
        'tasks' => 'Tasks',
        'client' => 'Client',
        'clients' => 'Clients',
        'invoice' => 'Invoice',
        'invoices' => 'Invoices',
        'expense' => 'Expense',
        'expenses' => 'Expenses',
        'report' => 'Report',
        'reports' => 'Reports',
        'user' => 'User',
        'users' => 'Users',
        'role' => 'Role',
        'roles' => 'Roles',
        'skill' => 'Skill',
        'skills' => 'Skills',
        'resume' => 'Resume',
        'resumes' => 'Resumes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Sections
    |--------------------------------------------------------------------------
    */
    'sections' => [
        'employee_data' => 'Employment Data',
        'leave_data' => 'Leave Details',
        'payroll_data' => 'Payroll Details',
        'project_details' => 'Project Details',
        'financial_temporal' => 'Financial & Timeline',
        'task_details' => 'Task Details',
        'task_assignment' => 'Task Assignment',
        'client_basic_data' => 'Client Basic Data',
        'contact_info' => 'Contact Information',
        'invoice_data' => 'Invoice Details',
        'associations' => 'Associations',
        'expense_data' => 'Expense Details',
        'report_data' => 'Report Details',
        'user_data' => 'User Data',
        'role_data' => 'Role Data',
        'skill_data' => 'Skill',
        'update_task_status' => 'Update Task Status',
        'new_expense' => 'Register New Expense',
        'leave_request' => 'Leave Request',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Field Labels
    |--------------------------------------------------------------------------
    */
    'fields' => [
        'user_account' => 'User Account',
        'department' => 'Department',
        'job_title' => 'Job Title',
        'salary' => 'Salary',
        'employee_status' => 'Employee Status',
        'hire_date' => 'Hire Date',
        'name' => 'Name',
        'department_name' => 'Department Name',
        'department_head' => 'Department Head',
        'employee' => 'Employee',
        'responsible_employee' => 'Responsible Employee',
        'leave_type' => 'Leave Type',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'reason' => 'Reason',
        'status' => 'Status',
        'month' => 'Month',
        'month_example' => 'Example: 2026-05',
        'basic_salary' => 'Basic Salary',
        'bonuses' => 'Bonuses',
        'deductions' => 'Deductions',
        'net_salary' => 'Net Salary',
        'client' => 'Client',
        'project_name' => 'Project Name',
        'project_description' => 'Project Description',
        'budget' => 'Budget',
        'project_status' => 'Project Status',
        'project_optional' => 'Project (Optional)',
        'task_title' => 'Task Title',
        'task_description' => 'Description',
        'due_date' => 'Due Date',
        'task_status' => 'Task Status',
        'change_status' => 'Change Status',
        'client_name' => 'Client Name',
        'client_name_rep' => 'Client / Representative Name',
        'company_name' => 'Company Name',
        'company_name_optional' => 'Company Name (if any)',
        'email' => 'Email',
        'phone' => 'Phone Number',
        'address' => 'Address',
        'invoice_number' => 'Invoice Number',
        'amount' => 'Amount',
        'issue_date' => 'Issue Date',
        'due_date_invoice' => 'Due Date',
        'expense_title' => 'Expense Title',
        'category' => 'Category',
        'expense_date' => 'Expense Date',
        'receipt_image' => 'Receipt / Invoice Image',
        'registered_by' => 'Registered By (Accountant)',
        'sender' => 'Sender',
        'receiver' => 'Receiver',
        'report_title' => 'Report Title',
        'content' => 'Content',
        'feedback' => 'Feedback',
        'full_name' => 'Full Name',
        'password' => 'Password',
        'role_permission' => 'Role (Permission)',
        'role_name' => 'Role Name',
        'role_description' => 'Description',
        'skill_name' => 'Skill Name',
        'statement' => 'Statement',
        'classification' => 'Classification',
        'employees_count' => 'Employees Count',
        'duration_days' => 'Duration (Days)',
        'created_at' => 'Created At',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Column Labels
    |--------------------------------------------------------------------------
    */
    'columns' => [
        'employee_name' => 'Employee',
        'department' => 'Department',
        'job_title' => 'Job Title',
        'salary' => 'Salary',
        'status' => 'Status',
        'hire_date' => 'Hire Date',
        'type' => 'Type',
        'start_date' => 'Start',
        'end_date' => 'End',
        'duration_days' => 'Days',
        'month' => 'Month',
        'basic_salary' => 'Basic Salary',
        'bonuses' => 'Bonuses',
        'deductions' => 'Deductions',
        'net_salary' => 'Net Salary',
        'project_name' => 'Project Name',
        'client' => 'Client',
        'budget' => 'Budget',
        'task' => 'Task',
        'project' => 'Project',
        'due_date' => 'Due Date',
        'name' => 'Name',
        'company' => 'Company',
        'email' => 'Email',
        'phone' => 'Phone',
        'invoice_number' => 'Invoice No.',
        'amount' => 'Amount',
        'issue_date' => 'Issue Date',
        'title' => 'Title',
        'category' => 'Category',
        'registered_by' => 'Registered By',
        'expense_date' => 'Date',
        'sender' => 'Sender',
        'receiver' => 'Receiver',
        'report_title' => 'Title',
        'sent_date' => 'Sent Date',
        'department_name' => 'Department',
        'department_head' => 'Department Head',
        'employees_count' => 'Employees',
        'role_name' => 'Role Name',
        'role_description' => 'Description',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    */
    'status' => [
        'active' => 'Active',
        'on_leave' => 'On Leave',
        'terminated' => 'Terminated',

        'pending' => 'Pending',
        'approved_by_head' => 'Dept. Head Approved',
        'approved_by_hr' => 'HR Approved',
        'rejected' => 'Rejected',

        'paid' => 'Paid',
        'unpaid' => 'Unpaid',
        'overdue' => 'Overdue',

        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Under Review',
        'done' => 'Completed',

        'completed' => 'Completed',
        'canceled' => 'Canceled',

        'unread' => 'Unread',
        'read' => 'Read',
        'replied' => 'Replied',
    ],

    /*
    |--------------------------------------------------------------------------
    | Leave Type Options
    |--------------------------------------------------------------------------
    */
    'leave_type' => [
        'Sick' => 'Sick',
        'Annual' => 'Annual',
        'Emergency' => 'Emergency',
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Category Options
    |--------------------------------------------------------------------------
    */
    'expense_category' => [
        'salaries' => 'Salaries',
        'operations' => 'Operational Expenses',
        'tools' => 'Tools & Software',
        'marketing' => 'Marketing & Advertising',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filter Labels
    |--------------------------------------------------------------------------
    */
    'filters' => [
        'filter_by_status' => 'Filter by Status',
        'filter_by_project' => 'Filter by Project',
        'filter_by_category' => 'Filter by Category',
        'filter_by_role' => 'Filter by Role',
        'project_status' => 'Project Status',
        'task_status' => 'Task Status',
        'employee_status' => 'Employee Status',
        'leave_status' => 'Leave Status',
        'leave_type' => 'Leave Type',
        'payroll_status' => 'Payroll Status',
        'invoice_status' => 'Invoice Status',
        'report_status' => 'Report Status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Action Labels
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'ai_evaluate' => 'AI Evaluation',
        'ai_evaluate_heading' => 'AI Employee Performance Evaluation',
        'close' => 'Close',
        'approve_final' => 'Final Approval',
        'reject' => 'Reject',
        'download_payslip' => 'Download Payslip',
        'create_invoice' => 'Issue Invoice',
        'add_employee' => 'Add Employee to Project',
        'remove_from_project' => 'Remove from Project',
        'add_skill' => 'Add Skill to Employee',
        'create_skill' => 'Create New Skill',
        'remove' => 'Remove',
        'new_task' => 'New Task',
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Labels
    |--------------------------------------------------------------------------
    */
    'widgets' => [
        'total_employees' => 'Total Employees',
        'total_employees_desc' => 'Registered in System',
        'active_projects' => 'Active Projects',
        'active_projects_desc' => 'Projects in Progress',
        'total_revenue' => 'Total Revenue Collected',
        'total_revenue_desc' => 'Paid Invoices Only',
        'project_status' => 'Project Status',
        'tasks_stats' => 'Task Statistics',
        'total_projects' => 'Total Projects',
        'total_projects_desc' => 'All Registered Projects',
        'tasks_in_progress' => 'In Progress Tasks',
        'tasks_in_progress_desc' => 'Currently Running Tasks',
        'tasks_done' => 'Completed Tasks',
        'tasks_done_desc' => 'Successfully Completed',
        'active_employees' => 'Active',
        'active_employees_desc' => 'Active Employees',
        'on_leave_employees' => 'On Leave',
        'on_leave_employees_desc' => 'Employees on Leave',
        'resumes_uploaded' => 'Uploaded Resumes (AI)',
        'resumes_uploaded_desc' => 'Analyzed by AI',
        'employees_distribution' => 'Employee Status Distribution',
        'my_total_tasks' => 'My Total Tasks',
        'my_total_tasks_desc' => 'All Assigned Tasks',
        'my_in_progress' => 'In Progress',
        'my_in_progress_desc' => 'Currently Running',
        'my_done' => 'Completed',
        'my_done_desc' => 'Successfully Completed',
        'total_income' => 'Revenue (Paid Invoices)',
        'total_income_desc' => 'Total Collected Invoices',
        'total_expenses' => 'Expenses',
        'total_expenses_desc' => 'Total Expenses',
        'net_profit' => 'Net Profit',
        'excellent_profit' => 'Excellent Profit',
        'loss' => 'Loss',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kanban Labels
    |--------------------------------------------------------------------------
    */
    'kanban' => [
        'no_tasks' => 'No tasks',
        'ai_evaluation' => 'AI Evaluation',
    ],

    'relation' => [
        'project_tasks' => 'Project Tasks',
        'project_team' => 'Project Team',
        'employee_skills' => 'Employee Skills',
        'client_invoices' => 'Client Invoices',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Labels
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'login_title' => 'Sign In',
        'login_subtitle' => 'Enter your credentials to access the dashboard',
        'create_employee_account' => 'Create Employee Account',
        'join_team' => 'Join our team today',
        'email' => 'Email Address',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot Password?',
        'send_reset_link' => 'Send Reset Link',
        'reset_password_title' => 'Reset Password',
        'reset_password_subtitle' => 'Enter your new password below',
        'new_password' => 'New Password',
        'save_password' => 'Save New Password',
        'login_button' => 'Sign In',
        'no_account' => 'Don\'t have an account?',
        'register_link' => 'Register now',
        'already_have_account' => 'Already have an account?',
        'login' => 'Log in',
        'forgot_password_desc' => 'Enter your email and we will send you a reset link',
        'back_to_login' => 'Back to login',
        'enter_full_name' => 'Enter your full name',
        'choose_role' => 'Choose your role',
        'register_account' => 'Create Account',
        'app_title' => 'Central ERP System',
        'job_application' => 'Job Application',
        'job_title_applied' => 'Applied Job Title',
        'expected_salary' => 'Expected Salary ($)',
        'resume_file' => 'Resume (PDF, Word)',
        'submit_application' => 'Submit Application',
        'uploading' => 'Uploading...',
    ],
];