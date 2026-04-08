<?php

namespace App\Http\Controllers;

use App\Models\ProProject;
use App\Models\ProjectUsage;
use App\Models\StudyActivity;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = ProProject::orderBy('project_code');

        if ($request->filled('stage')) {
            $query->where('project_stage', $request->stage);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('project_code', 'like', "%{$request->search}%")
                  ->orWhere('project_title', 'like', "%{$request->search}%");
            });
        }

        $projects = $query->paginate(20);

        // Get projects that have exp huts usages recorded
        $projectsWithUsages = ProjectUsage::distinct()->pluck('project_id')->toArray();

        return view('projects.index', compact('projects', 'projectsWithUsages'));
    }

    public function show(ProProject $project)
    {
        $usages = ProjectUsage::with('hut.site')
            ->where('project_id', $project->id)
            ->orderBy('date_start')
            ->get();

        $expHutsActivities = StudyActivity::where('project_id', $project->id)
            ->where('study_activity_name', 'Experimental Huts')
            ->orderBy('estimated_activity_date')
            ->get();

        // Group usages by phase
        $usagesByPhase = $usages->groupBy('phase_name');

        // Stats for this project
        $stats = [
            'huts_count'    => $usages->unique('hut_id')->count(),
            'phases_count'  => $usages->whereNotNull('phase_name')->unique('phase_name')->count(),
            'total_days'    => $usages->sum('duration_in_days'),
            'active_usages' => $usages->filter(fn($u) => $u->status === 'active')->count(),
        ];

        return view('projects.show', compact(
            'project', 'usages', 'expHutsActivities', 'usagesByPhase', 'stats'
        ));
    }
}
