<?php

namespace App\Exports;

use App\Models\Payroll;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollExport
{
    protected ?string $monthYear;

    public function __construct(?string $monthYear = null)
    {
        $this->monthYear = $monthYear;
    }

    public function download(): StreamedResponse
    {
        $filename = 'payrolls' . ($this->monthYear ? '_' . $this->monthYear : '') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                __('filament.fields.employee'),
                __('filament.fields.month'),
                __('filament.fields.basic_salary'),
                __('filament.fields.housing_allowance'),
                __('filament.fields.transport_allowance'),
                __('filament.fields.phone_allowance'),
                __('filament.fields.bonuses'),
                __('filament.fields.social_insurance_rate'),
                __('filament.fields.social_insurance_amount'),
                __('filament.fields.absence_days'),
                __('filament.fields.absence_deduction'),
                __('filament.fields.deductions'),
                __('filament.fields.net_salary'),
                __('filament.fields.status'),
            ]);

            $query = Payroll::with('employee.user');
            if ($this->monthYear) {
                $query->where('month_year', $this->monthYear);
            }

            foreach ($query->cursor() as $payroll) {
                fputcsv($file, [
                    $payroll->employee?->user?->name ?? '-',
                    $payroll->month_year,
                    $payroll->basic_salary,
                    $payroll->housing_allowance ?? 0,
                    $payroll->transport_allowance ?? 0,
                    $payroll->phone_allowance ?? 0,
                    $payroll->bonuses,
                    $payroll->social_insurance_rate ?? 0,
                    $payroll->social_insurance_amount ?? 0,
                    $payroll->absence_days ?? 0,
                    $payroll->absence_deduction ?? 0,
                    $payroll->deductions,
                    $payroll->net_salary,
                    $payroll->status === 'paid' ? __('filament.status.paid') : __('filament.status.unpaid'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}