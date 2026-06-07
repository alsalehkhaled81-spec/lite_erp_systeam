<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerPlan extends Model
{
    protected $fillable = ['employee_id', 'current_role', 'target_role', 'timeline_months', 'required_skills', 'notes', 'status'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
