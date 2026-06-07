<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTemplate extends Model
{
    protected $fillable = ['project_template_id', 'title', 'description', 'priority', 'estimated_hours', 'sort_order'];

    public function projectTemplate(): BelongsTo
    {
        return $this->belongsTo(ProjectTemplate::class);
    }
}
