<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'department_id',
        'status',
        'positions_count',
        'created_by',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Employee::class, 'vacancy_id');
    }

    public function pendingApplicants(): HasMany
    {
        return $this->hasMany(Employee::class, 'vacancy_id')->where('status', 'pending');
    }

    public function getApplicantsCountAttribute(): int
    {
        return $this->applicants()->count();
    }
}
