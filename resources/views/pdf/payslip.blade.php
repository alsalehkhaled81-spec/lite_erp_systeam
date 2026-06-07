<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <style>
        body { font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; direction: ltr; text-align: left; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header h2 { margin: 5px 0 0; font-size: 16px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #f0f0f0; font-weight: bold; }
        .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $company }}</h1>
            <h2>Payslip - {{ $payroll->month_year }}</h2>
        </div>

        <table>
            <tr>
                <th>Employee Name</th>
                <td>{{ $user->name }}</td>
                <th>Job Title</th>
                <td>{{ $employee->job_title }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
                <th>Hire Date</th>
                <td>{{ $employee->hire_date }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>Bonuses</td>
                    <td>{{ number_format($payroll->bonuses, 2) }}</td>
                </tr>
                <tr>
                    <td>Deductions</td>
                    <td>-{{ number_format($payroll->deductions, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Net Salary</td>
                    <td>{{ number_format($payroll->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p><strong>Status:</strong> {{ $payroll->status === 'paid' ? 'Paid' : 'Unpaid' }}</p>

        @if(!empty($ai_note))
            <p style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #4ade80; font-style: italic;">
                "{{ $ai_note }}"
            </p>
        @endif

        <div class="footer">
            Automatically generated on {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</body>
</html>
