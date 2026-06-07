<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectTemplate extends Model
{
    protected $fillable = ['name', 'description', 'budget', 'estimated_days'];

    public function taskTemplates(): HasMany
    {
        return $this->hasMany(TaskTemplate::class);
    }

    public function createProject(array $overrides = []): Project
    {
        $project = Project::create(array_merge([
            'name' => $this->name,
            'description' => $this->description,
            'budget' => $this->budget,
            'status' => 'pending',
        ], $overrides));

        foreach ($this->taskTemplates as $taskTemplate) {
            $project->tasks()->create([
                'title' => $taskTemplate->title,
                'description' => $taskTemplate->description,
                'priority' => $taskTemplate->priority,
                'status' => 'todo',
            ]);
        }

        return $project;
    }
}
