<?php

namespace App\Http\Controllers;

use App\Models\DailyObservation;
use App\Models\Hut;
use App\Models\Site;
use App\Models\ProProject;
use App\Models\UsageSession;
use App\Models\ProjectUsage;
use App\Models\Sleeper;
use App\Models\SleeperAssignment;
use App\Models\StateChange;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    // Stages that do NOT allow new assignments
    const BLOCKED_STAGES = ['not_started', 'suspended', 'completed', 'archived', 'NA'];

    /** GET /assignments/create?project_id= */
    public function create(Request $request)
    {
        $projectId = $request->integer('project_id');
        $project   = $projectId ? ProProject::findOrFail($projectId) : null;

        if ($project && in_array($project->project_stage, self::BLOCKED_STAGES)) {
            return redirect()->route('projects.show', $project)
                ->with('error', "Impossible d'affecter des cases : le projet est « {$project->stage_label} ».");
        }

        $sites    = Site::where('status', 'active')->with('huts')->orderBy('name')->get();
        $projects = ProProject::where('project_stage', 'in progress')->orderBy('project_code')->get(['id', 'project_code', 'project_title']);
        $sleepers = Sleeper::active()->orderBy('code')->get();

        return view('assignments.create', compact('sites', 'projects', 'project', 'sleepers'));
    }

    /** POST /assignments — save everything */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|integer',
            'phase_name'   => 'nullable|string|max:255',
            'date_start'   => 'required|date',
            'date_end'     => 'required|date|after_or_equal:date_start',
            'notes'        => 'nullable|string',
            'hut_ids'      => 'required|array|min:1',
            'hut_ids.*'    => 'integer|exists:exp_huts_huts,id',
            // Sleeper assignments: sleepers[date][hut_id] = sleeper_id
            'sleepers'     => 'nullable|array',
        ]);

        $project = ProProject::findOrFail($validated['project_id']);
        if (in_array($project->project_stage, self::BLOCKED_STAGES)) {
            return back()->with('error', "Projet non éligible à l'affectation de cases.");
        }

        DB::transaction(function () use ($validated, $project) {
            // 1. Create session
            $session = UsageSession::create([
                'project_id'  => $validated['project_id'],
                'phase_name'  => $validated['phase_name'] ?? null,
                'date_start'  => $validated['date_start'],
                'date_end'    => $validated['date_end'],
                'notes'       => $validated['notes'] ?? null,
                'status'      => Carbon::today()->lte(Carbon::parse($validated['date_end'])) ? 'planned' : 'completed',
                'created_by'  => Auth::id(),
            ]);

            $today = Carbon::today()->toDateString();

            // 2. Create one ProjectUsage per hut
            foreach ($validated['hut_ids'] as $hutId) {
                // Check overlap
                $overlap = ProjectUsage::where('hut_id', $hutId)
                    ->where('date_start', '<=', $validated['date_end'])
                    ->where('date_end',   '>=', $validated['date_start'])
                    ->exists();

                if ($overlap) continue; // skip if already used

                ProjectUsage::create([
                    'session_id'  => $session->id,
                    'hut_id'      => $hutId,
                    'project_id'  => $validated['project_id'],
                    'phase_name'  => $validated['phase_name'] ?? null,
                    'date_start'  => $validated['date_start'],
                    'date_end'    => $validated['date_end'],
                    'notes'       => $validated['notes'] ?? null,
                    'created_by'  => Auth::id(),
                ]);

                // Update hut status if period is current
                if ($validated['date_start'] <= $today && $validated['date_end'] >= $today) {
                    $hut = Hut::find($hutId);
                    if ($hut && $hut->status === 'available') {
                        StateChange::create([
                            'hut_id'          => $hutId,
                            'previous_status' => $hut->status,
                            'new_status'      => 'in_use',
                            'reason'          => "Affectée au projet {$project->project_code}",
                            'changed_by'      => Auth::id(),
                            'changed_at'      => now(),
                        ]);
                        $hut->update(['status' => 'in_use']);
                    }
                }
            }

            // 3. Save sleeper assignments if provided
            if (!empty($validated['sleepers'])) {
                foreach ($validated['sleepers'] as $date => $hutSleepers) {
                    foreach ($hutSleepers as $hutId => $sleeperId) {
                        if (!$sleeperId) continue;
                        SleeperAssignment::updateOrCreate(
                            ['session_id' => $session->id, 'hut_id' => $hutId, 'assignment_date' => $date],
                            ['sleeper_id' => $sleeperId]
                        );
                    }
                }
            }

            // 4. Fire start notification if session starts today or already started
            if ($validated['date_start'] <= now()->toDateString()) {
                $session->update(['status' => 'active']);
                NotificationService::notifyActivityStart($session);
            }
        });

        return redirect()->route('projects.show', $validated['project_id'])
            ->with('success', 'Cases affectées avec succès. Notifications envoyées.');
    }

    /** GET /assignments/{session} — show session detail */
    public function show(UsageSession $assignment)
    {
        $assignment->load([
            'project', 'projectUsages.hut.site',
            'sleeperAssignments.sleeper', 'sleeperAssignments.hut',
            'dailyObservations.observer', 'dailyObservations.hut',
            'creator',
        ]);

        $sleepers = Sleeper::active()->orderBy('code')->get();
        $huts     = $assignment->projectUsages->map(fn($u) => $u->hut)->sortBy('number');

        return view('assignments.show', compact('assignment', 'sleepers', 'huts'));
    }

    /** PATCH /assignments/{session}/complete */
    public function complete(UsageSession $assignment)
    {
        $assignment->update(['status' => 'completed']);
        NotificationService::notifyActivityEnd($assignment);

        // Update huts status back to available if no other active usage
        foreach ($assignment->projectUsages as $usage) {
            $hut = $usage->hut;
            $hasOtherActive = ProjectUsage::where('hut_id', $hut->id)
                ->where('id', '!=', $usage->id)
                ->where('date_start', '<=', now()->toDateString())
                ->where('date_end',   '>=', now()->toDateString())
                ->exists();

            if (!$hasOtherActive && $hut->status === 'in_use') {
                StateChange::create([
                    'hut_id'          => $hut->id,
                    'previous_status' => 'in_use',
                    'new_status'      => 'available',
                    'reason'          => "Fin d'activité — {$assignment->project->project_code}",
                    'changed_by'      => Auth::id(),
                    'changed_at'      => now(),
                ]);
                $hut->update(['status' => 'available']);
            }
        }

        return back()->with('success', 'Activité marquée comme terminée. Notifications envoyées.');
    }

    /** GET /assignments/{session}/edit */
    public function edit(UsageSession $assignment)
    {
        $assignment->load(['project', 'projectUsages.hut.site']);
        $sites = Site::where('status', 'active')->with('huts')->orderBy('name')->get();
        return view('assignments.edit', compact('assignment', 'sites'));
    }

    /** PATCH /assignments/{session} */
    public function update(Request $request, UsageSession $assignment)
    {
        $validated = $request->validate([
            'phase_name' => 'nullable|string|max:255',
            'date_start' => 'required|date',
            'date_end'   => 'required|date|after_or_equal:date_start',
            'notes'      => 'nullable|string',
        ]);

        $assignment->update($validated);

        // Re-sync ProjectUsage dates
        $assignment->projectUsages()->update([
            'phase_name' => $validated['phase_name'] ?? null,
            'date_start' => $validated['date_start'],
            'date_end'   => $validated['date_end'],
            'notes'      => $validated['notes'] ?? null,
        ]);

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Session mise à jour.');
    }

    /** DELETE /assignments/{session} — soft delete */
    public function destroy(UsageSession $assignment)
    {
        $assignment->delete();
        return redirect()->route('projects.show', $assignment->project_id)
            ->with('success', 'Session annulée (restaurable par admin).');
    }

    /** POST /assignments/{id}/force-delete — hard delete (admin only) */
    public function forceDestroy(int $id)
    {
        $this->authorizeAdminOrFacility();
        UsageSession::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Session supprimée définitivement.');
    }

    /** POST /assignments/{id}/restore */
    public function restore(int $id)
    {
        $this->authorizeAdminOrFacility();
        UsageSession::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Session restaurée.');
    }

    /** POST /assignments/{session}/observations */
    public function addObservation(Request $request, UsageSession $assignment)
    {
        $validated = $request->validate([
            'observation_date' => 'required|date',
            'hut_id'           => 'nullable|exists:exp_huts_huts,id',
            'observation'      => 'required|string',
        ]);

        $validated['session_id']   = $assignment->id;
        $validated['observed_by']  = Auth::id();

        \App\Models\DailyObservation::create($validated);

        return back()->with('success', 'Observation enregistrée.');
    }

    /** DELETE /assignments/{session}/observations/{observation} */
    public function destroyObservation(UsageSession $assignment, DailyObservation $observation)
    {
        // Only the observer, or admin/facility manager can delete
        if ($observation->observed_by !== Auth::id()) {
            $this->authorizeAdminOrFacility();
        }
        $observation->delete();
        return back()->with('success', 'Observation supprimée.');
    }

    /** POST /assignments/{session}/sleepers — update sleeper matrix */
    public function updateSleepers(Request $request, UsageSession $assignment)
    {
        $validated = $request->validate([
            'sleepers'        => 'required|array',
            'sleepers.*.*'    => 'nullable|integer|exists:exp_huts_sleepers,id',
        ]);

        DB::transaction(function () use ($validated, $assignment) {
            foreach ($validated['sleepers'] as $date => $hutSleepers) {
                foreach ($hutSleepers as $hutId => $sleeperId) {
                    if (!$sleeperId) {
                        SleeperAssignment::where([
                            'session_id' => $assignment->id,
                            'hut_id' => $hutId,
                            'assignment_date' => $date,
                        ])->delete();
                        continue;
                    }
                    SleeperAssignment::updateOrCreate(
                        ['session_id' => $assignment->id, 'hut_id' => $hutId, 'assignment_date' => $date],
                        ['sleeper_id' => $sleeperId]
                    );
                }
            }
        });

        return back()->with('success', 'Planning des dormeurs mis à jour.');
    }

    private function authorizeAdminOrFacility(): void
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) {
            abort(403, 'Action réservée aux administrateurs.');
        }
    }
}
