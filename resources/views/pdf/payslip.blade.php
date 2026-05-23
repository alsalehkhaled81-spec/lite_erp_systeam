<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>قسيمة راتب</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header h2 { margin: 5px 0 0; font-size: 16px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: right; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #f0f0f0; font-weight: bold; }
        .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $company }}</h1>
            <h2>قسيمة راتب - {{ $payroll->month_year }}</h2>
        </div>

        <table>
            <tr>
                <th>اسم الموظف</th>
                <td>{{ $user->name }}</td>
                <th>المسمى الوظيفي</th>
                <td>{{ $employee->job_title }}</td>
            </tr>
            <tr>
                <th>البريد الإلكتروني</th>
                <td>{{ $user->email }}</td>
                <th>تاريخ التعيين</th>
                <td>{{ $employee->hire_date }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>البيان</th>
                    <th>المبلغ ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>الراتب الأساسي</td>
                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>المكافآت</td>
                    <td>{{ number_format($payroll->bonuses, 2) }}</td>
                </tr>
                <tr>
                    <td>الخصومات</td>
                    <td>-{{ number_format($payroll->deductions, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>صافي الراتب</td>
                    <td>{{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p><strong>الحالة:</strong> {{ $payroll->status === 'paid' ? 'مدفوعة' : 'غير مدفوعة' }}</p>

        <div class="footer">
            تم إنشاء هذه القسيمة تلقائياً بتاريخ {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</body>
</html>
