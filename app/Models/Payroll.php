<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = ['employee_id', 'month_year', 'basic_salary', 'bonuses', 'deductions', 'net_salary', 'status'];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function calculateNetSalary(float $basic, float $bonuses, float $deductions): float
    {
        return max(0, $basic + $bonuses - $deductions);
    }
}
