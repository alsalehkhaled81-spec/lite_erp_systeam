<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = ['name'];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }
}
