<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'department_id', 'vacancy_id', 'job_title', 'salary', 'status', 'hire_date', 'annual_leave_balance', 'used_leave_days'];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function headOfDepartment(): HasOne
    {
        return $this->hasOne(Department::class, 'head_id');
    }

    public function resume(): HasOne
    {
        return $this->hasOne(Resume::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function sentReports(): HasMany
    {
        return $this->hasMany(Report::class, 'sender_id');
    }

    public function receivedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'receiver_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function careerPlans(): HasMany
    {
        return $this->hasMany(CareerPlan::class);
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'employee_training')
            ->withPivot(['status', 'certificate_url', 'completion_date'])
            ->withTimestamps();
    }

    public function getRemainingLeaveBalanceAttribute(): int
    {
        return $this->annual_leave_balance - $this->used_leave_days;
    }

    public function scopeEligibleDepartmentHead($query, ?int $currentHeadId = null)
    {
        return $query
            ->with('user')
            ->whereDoesntHave('user.role', fn ($q) => $q->where('name', 'super_admin'))
            ->where(function ($q) use ($currentHeadId) {
                $q->whereDoesntHave('headOfDepartment')
                    ->when($currentHeadId, fn ($q2) => $q2->orWhere('employees.id', $currentHeadId));
            });
    }
}
