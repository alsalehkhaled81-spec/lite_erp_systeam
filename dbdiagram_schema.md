Table roles {
  id bigint [primary key, increment, not null]
  name varchar [not null]
  description text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table users {
  id bigint [primary key, increment, not null]
  role_id bigint [null]
  name varchar [not null]
  email varchar [not null]
  profile_photo_path varchar [null]
  is_approved tinyint [not null]
  email_verified_at timestamp [null]
  password varchar [not null]
  remember_token varchar [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table attendances {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  date date [not null]
  check_in datetime [null]
  check_out datetime [null]
  hours_worked decimal [not null]
  overtime_hours decimal [not null]
  status enum [not null]
  notes text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table career_plans {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  current_role varchar [not null]
  target_role varchar [not null]
  timeline_months int [not null]
  required_skills text [null]
  notes text [null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table certificates {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  title varchar [not null]
  issuer varchar [not null]
  issue_date date [not null]
  expiry_date date [null]
  certificate_url varchar [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table clients {
  id bigint [primary key, increment, not null]
  name varchar [not null]
  company_name varchar [null]
  email varchar [null]
  password varchar [null]
  profile_photo_path varchar [null]
  is_active tinyint [not null]
  remember_token varchar [null]
  phone varchar [null]
  address text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table departments {
  id bigint [primary key, increment, not null]
  name varchar [not null]
  head_id bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employees {
  id bigint [primary key, increment, not null]
  user_id bigint [not null]
  department_id bigint [null]
  vacancy_id bigint [null]
  job_title varchar [null]
  salary decimal [null]
  status enum [null]
  rejection_reason text [null]
  hire_date date [null]
  annual_leave_balance int [not null]
  used_leave_days int [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table employee_project {
  project_id bigint [not null]
  employee_id bigint [not null]
}

Table employee_skill {
  employee_id bigint [not null]
  skill_id bigint [not null]
}

Table employee_training {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  training_id bigint [not null]
  status enum [not null]
  certificate_url varchar [null]
  completion_date date [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table expenses {
  id bigint [primary key, increment, not null]
  user_id bigint [not null]
  title varchar [not null]
  category varchar [null]
  amount decimal [not null]
  expense_date date [null]
  receipt_url varchar [null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
  project_id bigint [null]
  approved_by bigint [null]
}

Table invoices {
  id bigint [primary key, increment, not null]
  client_id bigint [not null]
  project_id bigint [null]
  invoice_number varchar [not null]
  amount decimal [not null]
  vat_rate decimal [not null]
  vat_amount decimal [not null]
  total_with_vat decimal [not null]
  issue_date date [null]
  due_date date [null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table invoice_items {
  id bigint [primary key, increment, not null]
  invoice_id bigint [not null]
  description varchar [not null]
  quantity decimal [not null]
  unit_price decimal [not null]
  total decimal [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table leaves {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  type varchar [not null]
  start_date date [not null]
  end_date date [not null]
  reason text [not null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table notifications {
  id char [primary key, not null]
  type varchar [not null]
  notifiable_type varchar [not null]
  notifiable_id bigint [not null]
  data text [not null]
  read_at timestamp [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table payrolls {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  month_year varchar [not null]
  basic_salary decimal [not null]
  bonuses decimal [not null]
  deductions decimal [not null]
  housing_allowance decimal [not null]
  transport_allowance decimal [not null]
  phone_allowance decimal [not null]
  social_insurance_rate decimal [not null]
  social_insurance_amount decimal [not null]
  absence_days int [not null]
  absence_deduction decimal [not null]
  net_salary decimal [not null]
  status varchar [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table projects {
  id bigint [primary key, increment, not null]
  client_id bigint [null]
  name varchar [not null]
  description text [null]
  budget decimal [null]
  start_date date [null]
  end_date date [null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table project_templates {
  id bigint [primary key, increment, not null]
  name varchar [not null]
  description text [null]
  budget decimal [null]
  estimated_days int [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table reports {
  id bigint [primary key, increment, not null]
  sender_id bigint [not null]
  receiver_id bigint [not null]
  title varchar [not null]
  content text [not null]
  feedback text [null]
  status enum [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table resumes {
  id bigint [primary key, increment, not null]
  employee_id bigint [not null]
  file_path varchar [null]
  resume_text longtext [null]
  ai_score int [null]
  ai_summary longtext [null]
  ai_report longtext [null]
  ai_recommendation varchar [null]
  analyzed_at timestamp [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table skills {
  id bigint [primary key, increment, not null]
  name varchar [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table tasks {
  id bigint [primary key, increment, not null]
  project_id bigint [not null]
  employee_id bigint [null]
  title varchar [not null]
  description text [null]
  start_date date [null]
  due_date date [null]
  status enum [not null]
  priority enum [not null]
  sort_order int [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_attachments {
  id bigint [primary key, increment, not null]
  task_id bigint [not null]
  user_id bigint [not null]
  file_name varchar [not null]
  file_path varchar [not null]
  file_type varchar [null]
  file_size bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_comments {
  id bigint [primary key, increment, not null]
  task_id bigint [not null]
  user_id bigint [not null]
  comment text [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table task_templates {
  id bigint [primary key, increment, not null]
  project_template_id bigint [not null]
  title varchar [not null]
  description text [null]
  priority enum [not null]
  estimated_hours int [null]
  sort_order int [not null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table time_entries {
  id bigint [primary key, increment, not null]
  task_id bigint [not null]
  employee_id bigint [not null]
  date date [not null]
  hours decimal [not null]
  description text [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table trainings {
  id bigint [primary key, increment, not null]
  title varchar [not null]
  description text [null]
  trainer varchar [null]
  start_date date [not null]
  end_date date [not null]
  status enum [not null]
  location varchar [null]
  max_participants int [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table vacancies {
  id bigint [primary key, increment, not null]
  title varchar [not null]
  description longtext [null]
  requirements longtext [null]
  location varchar [null]
  employment_type varchar [not null]
  salary_min decimal [null]
  salary_max decimal [null]
  department_id bigint [null]
  status varchar [not null]
  positions_count int [not null]
  created_by bigint [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Ref: users.role_id > roles.id
Ref: attendances.employee_id > employees.id
Ref: career_plans.employee_id > employees.id
Ref: certificates.employee_id > employees.id
Ref: departments.head_id > employees.id
Ref: employees.department_id > departments.id
Ref: employees.user_id > users.id
Ref: employees.vacancy_id > vacancies.id
Ref: employee_project.employee_id > employees.id
Ref: employee_project.project_id > projects.id
Ref: employee_skill.employee_id > employees.id
Ref: employee_skill.skill_id > skills.id
Ref: employee_training.employee_id > employees.id
Ref: employee_training.training_id > trainings.id
Ref: expenses.approved_by > users.id
Ref: expenses.project_id > projects.id
Ref: expenses.user_id > users.id
Ref: invoices.client_id > clients.id
Ref: invoices.project_id > projects.id
Ref: invoice_items.invoice_id > invoices.id
Ref: leaves.employee_id > employees.id
Ref: payrolls.employee_id > employees.id
Ref: projects.client_id > clients.id
Ref: reports.receiver_id > employees.id
Ref: reports.sender_id > employees.id
Ref: resumes.employee_id > employees.id
Ref: tasks.employee_id > employees.id
Ref: tasks.project_id > projects.id
Ref: task_attachments.task_id > tasks.id
Ref: task_attachments.user_id > users.id
Ref: task_comments.task_id > tasks.id
Ref: task_comments.user_id > users.id
Ref: task_templates.project_template_id > project_templates.id
Ref: time_entries.employee_id > employees.id
Ref: time_entries.task_id > tasks.id
Ref: vacancies.created_by > users.id
Ref: vacancies.department_id > departments.id
