<?php

namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipPdfService
{
    public function generate(Payroll $payroll)
    {
        $payroll->load('employee.user');

        // Generate a short AI motivational note for the payslip
        $aiNote = null;
        try {
            $aiService = app(\App\Services\AiService::class);
            $prompt = "قم بكتابة رسالة شكر قصيرة جداً (سطر واحد) لموظف اسمه {$payroll->employee->user->name} بمناسبة استلام راتبه لشهر {$payroll->month_year}. كن ودوداً ومحفزاً.";
            
            $response = $aiService->chat([
                ['role' => 'system', 'content' => 'أنت مدير موارد بشرية لطيف يكتب رسائل شكر وتشجيع قصيرة جداً للموظفين.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $aiNote = $response['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            // Silently fail AI feature so PDF generation isn't interrupted
        }

        $data = [
            'payroll' => $payroll,
            'employee' => $payroll->employee,
            'user' => $payroll->employee->user,
            'company' => config('app.name', 'ERP-Lite'),
            'ai_note' => $aiNote,
        ];

        $pdf = Pdf::loadView('pdf.payslip', $data);

        return $pdf->download('payslip_' . $payroll->employee->user->name . '_' . $payroll->month_year . '.pdf');
    }
}
