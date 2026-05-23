<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = ['user_id', 'department_id', 'job_title', 'salary', 'status', 'hire_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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
}
