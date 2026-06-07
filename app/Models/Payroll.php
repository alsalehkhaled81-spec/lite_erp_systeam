<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 'month_year', 'basic_salary', 'bonuses', 'deductions',
        'housing_allowance', 'transport_allowance', 'phone_allowance',
        'social_insurance_rate', 'social_insurance_amount',
        'absence_days', 'absence_deduction',
        'net_salary', 'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'phone_allowance' => 'decimal:2',
        'social_insurance_rate' => 'decimal:2',
        'social_insurance_amount' => 'decimal:2',
        'absence_days' => 'integer',
        'absence_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function calculateNetSalary(
        float $basic,
        float $bonuses,
        float $deductions,
        float $housing = 0,
        float $transport = 0,
        float $phone = 0,
        float $insuranceRate = 0,
        float $absenceDeduction = 0
    ): float {
        $allowances = $housing + $transport + $phone;
        $insuranceAmount = $basic * ($insuranceRate / 100);
        return max(0, $basic + $bonuses + $allowances - $deductions - $insuranceAmount - $absenceDeduction);
    }
}
