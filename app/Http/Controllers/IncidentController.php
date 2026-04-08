<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Hut;
use App\Models\ProProject;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['hut.site', 'project', 'reporter'])
            ->orderBy('incident_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('hut_id')) {
            $query->where('hut_id', $request->hut_id);
        }

        $incidents = $query->paginate(20);
        $huts      = Hut::with('site')->orderBy('name')->get();

        return view('incidents.index', compact('incidents', 'huts'));
    }

    public function create()
    {
        $huts     = Hut::with('site')->orderBy('name')->get();
        $projects = ProProject::orderBy('project_code')->get(['id', 'project_code', 'project_title']);
        return view('incidents.create', compact('huts', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hut_id'        => 'nullable|exists:exp_huts_huts,id',
            'project_id'    => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'incident_date' => 'required|date',
            'severity'      => 'required|in:low,medium,high,critical',
            'status'        => 'required|in:open,in_progress,resolved,closed',
        ]);

        $validated['reported_by'] = Auth::id();

        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $validated['resolved_by'] = Auth::id();
            $validated['resolved_at'] = now();
        }

        $incident = Incident::create($validated);

        // Notify relevant users
        NotificationService::notifyIncident($incident);

        return redirect()->route('incidents.index')
            ->with('success', 'Incident enregistré et notifications envoyées.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['hut.site', 'project', 'reporter', 'resolver']);
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $huts     = Hut::with('site')->orderBy('name')->get();
        $projects = ProProject::orderBy('project_code')->get(['id', 'project_code', 'project_title']);
        return view('incidents.edit', compact('incident', 'huts', 'projects'));
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'hut_id'           => 'nullable|exists:exp_huts_huts,id',
            'project_id'       => 'nullable|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'incident_date'    => 'required|date',
            'severity'         => 'required|in:low,medium,high,critical',
            'status'           => 'required|in:open,in_progress,resolved,closed',
            'resolution_notes' => 'nullable|string',
        ]);

        if (in_array($validated['status'], ['resolved', 'closed']) && !$incident->resolved_at) {
            $validated['resolved_by'] = Auth::id();
            $validated['resolved_at'] = now();
        }

        $incident->update($validated);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident mis à jour.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete(); // soft delete
        return redirect()->route('incidents.index')
            ->with('success', 'Incident supprimé (restaurable).');
    }

    public function forceDestroy(int $id)
    {
        $this->authorizeAdminOrFacility();
        Incident::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Incident supprimé définitivement.');
    }

    public function restore(int $id)
    {
        $this->authorizeAdminOrFacility();
        Incident::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Incident restauré.');
    }

    private function authorizeAdminOrFacility(): void
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) {
            abort(403, 'Action réservée aux administrateurs.');
        }
    }
}
