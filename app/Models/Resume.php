<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resume extends Model
{
    protected $fillable =['employee_id', 'file_path', 'resume_text'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
