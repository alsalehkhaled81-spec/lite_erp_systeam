<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'employees' => Employee::where('status', 'active')->count(),
            'projects' => Project::count(),
            'tasks_completed' => Task::where('status', 'done')->count(),
            'clients' => Client::count(),
        ];

        return view('landing', compact('stats'));
    }
}
