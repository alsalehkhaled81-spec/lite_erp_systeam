<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Vacancy;

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

        $vacancies = Vacancy::withCount('applicants')
            ->where('status', 'open') // or 'active', we'll keep 'open' as it was
            ->latest()
            ->take(3)
            ->get();

        return view('landing', compact('stats', 'vacancies'));
    }

    public function vacancies()
    {
        $vacancies = Vacancy::withCount('applicants')
            ->where('status', 'open')
            ->latest()
            ->paginate(12);

        return view('vacancies', compact('vacancies'));
    }
}
