<?php

namespace App\Http\Controllers;

use App\Models\ProProject;
use App\Models\UsageSession;
use App\Models\Incident;
use App\Models\StudyActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = ProProject::orderBy('project_code');

        // Par défaut : projets en cours uniquement. "all" = pas de filtre.
        $stage = $request->input('stage', 'in progress');
        if ($stage !== 'all') {
            $query->where('project_stage', $stage);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('project_code', 'like', "%{$request->search}%")
                  ->orWhere('project_title', 'like', "%{$request->search}%");
            });
        }

        $projects = $query->paginate(20);

        $projectsWithUsages = \App\Models\ProjectUsage::distinct()->pluck('project_id')->toArray();

        return view('projects.index', compact('projects', 'projectsWithUsages'));
    }

    public function show(ProProject $project)
    {
        // ── Sessions avec toutes les relations ─────────────────────────────
        $sessions = UsageSession::with([
            'projectUsages.hut.site',
            'sleeperAssignments.sleeper',
            'sleeperAssignments.hut',
            'dailyObservations.hut',
            'dailyObservations.observer',
            'creator',
        ])
        ->withTrashed()
        ->where('project_id', $project->id)
        ->orderByDesc('date_start')
        ->get();

        // ── Tous les huts utilisés dans ce projet ──────────────────────────
        $hutIds = $sessions
            ->flatMap(fn($s) => $s->projectUsages->pluck('hut_id'))
            ->unique()->values()->toArray();

        // ── Incidents liés au projet ou à ses huts ─────────────────────────
        $incidents = Incident::with(['hut.site', 'reporter'])
            ->where(function ($q) use ($project, $hutIds) {
                $q->where('project_id', $project->id);
                if ($hutIds) {
                    $q->orWhereIn('hut_id', $hutIds);
                }
            })
            ->orderBy('incident_date')
            ->get();

        // ── Activités "Experimental Huts" du projet ────────────────────────
        $expHutsActivities = StudyActivity::where('project_id', $project->id)
            ->where('study_activity_name', 'Experimental Huts')
            ->orderBy('estimated_activity_date')
            ->get();

        // ── Stats globales ────────────────────────────────────────────────
        $stats = [
            'sessions_count' => $sessions->count(),
            'huts_count'     => count($hutIds),
            'total_days'     => $sessions->sum('duration_in_days'),
            'active_count'   => $sessions->filter(fn($s) => $s->current_status === 'active')->count(),
        ];

        // ── JSON pour le modal "détail case" ──────────────────────────────
        // Structure : sessionsJson[session_id] = { huts: [ { id, number, site, dates:[{date, sleeper, obs, incidents}] } ] }
        $sessionsJson = $sessions->mapWithKeys(function ($session) use ($incidents) {

            $hutsList = $session->projectUsages->map(function ($usage) use ($session, $incidents) {
                $hut = $usage->hut;
                if (!$hut) return null;

                // Index sleeper assignments for this hut: date -> sleeper
                $sleeperByDate = $session->sleeperAssignments
                    ->where('hut_id', $hut->id)
                    ->keyBy(fn($a) => Carbon::parse($a->assignment_date)->format('Y-m-d'));

                // Index observations for this hut: date -> [texts]
                $obsByDate = $session->dailyObservations
                    ->filter(fn($o) => $o->hut_id == $hut->id || is_null($o->hut_id))
                    ->groupBy(fn($o) => $o->observation_date->format('Y-m-d'));

                // Index incidents for this hut: date -> [incidents]
                $incidentsByDate = $incidents
                    ->filter(fn($i) => $i->hut_id == $hut->id)
                    ->groupBy(fn($i) => $i->incident_date->format('Y-m-d'));

                $dateDetails = array_map(function ($date) use ($sleeperByDate, $obsByDate, $incidentsByDate) {
                    $assignment = $sleeperByDate[$date] ?? null;
                    $sleeper    = $assignment?->sleeper;

                    $obsTexts = ($obsByDate[$date] ?? collect())->pluck('observation')->toArray();

                    $dayIncidents = ($incidentsByDate[$date] ?? collect())->map(fn($i) => [
                        'id'             => $i->id,
                        'title'          => $i->title,
                        'severity_label' => $i->severity_label,
                        'severity_color' => $i->severity_color,
                        'status_label'   => $i->status_label,
                        'status_color'   => $i->status_color,
                    ])->values()->toArray();

                    return [
                        'date'        => $date,
                        'date_label'  => Carbon::parse($date)->translatedFormat('D d/m'),
                        'sleeper'     => $sleeper ? ['code' => $sleeper->code, 'name' => $sleeper->name, 'gender' => $sleeper->gender] : null,
                        'observations'=> $obsTexts,
                        'incidents'   => $dayIncidents,
                        'has_incident'=> !empty($dayIncidents),
                    ];
                }, $session->dates);

                return [
                    'id'           => $hut->id,
                    'number'       => $hut->number,
                    'site'         => $hut->site?->name ?? '—',
                    'status'       => $hut->status,
                    'status_label' => $hut->status_label,
                    'notes'        => $hut->notes,
                    'dates'        => array_values($dateDetails),
                ];
            })->filter()->sortBy('number')->values()->toArray();

            return [$session->id => ['huts' => $hutsList]];
        });

        return view('projects.show', compact(
            'project', 'sessions', 'expHutsActivities',
            'stats', 'incidents', 'sessionsJson'
        ));
    }
}
