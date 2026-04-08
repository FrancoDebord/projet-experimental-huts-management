<?php

namespace App\Http\Controllers;

use App\Models\Hut;
use App\Models\Site;
use App\Models\StateChange;
use App\Models\ProjectUsage;
use App\Models\ProProject;
use App\Models\StudyActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HutController extends Controller
{
    public function index(Request $request)
    {
        $query = Hut::with('site')
            ->orderBy('site_id')
            ->orderBy('number');

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $huts  = $query->get();
        $sites = Site::orderBy('name')->get();

        return view('huts.index', compact('huts', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->orderBy('name')->get();
        return view('huts.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id'   => 'required|exists:exp_huts_sites,id',
            'number'    => 'required|integer|min:1',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status'    => 'required|in:available,in_use,damaged,abandoned',
            'notes'     => 'nullable|string',
            'image'     => 'nullable|image|max:2048',
        ]);

        // Check uniqueness
        if (Hut::where('site_id', $validated['site_id'])
            ->where('number', $validated['number'])
            ->exists()
        ) {
            return back()->withErrors(['number' => 'Ce numéro de case existe déjà pour ce site.'])
                ->withInput();
        }

        $site = Site::findOrFail($validated['site_id']);
        $validated['name'] = "{$site->name} Case {$validated['number']}";

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('exp_huts/huts', 'public');
        }

        unset($validated['image']);
        $hut = Hut::create($validated);

        return redirect()->route('huts.show', $hut)
            ->with('success', "Case « {$hut->name} » créée avec succès.");
    }

    public function show(Hut $hut)
    {
        $hut->load([
            'site',
            'projectUsages.project',
            'stateChanges.changedBy',
            'incidents.reporter',
        ]);

        $projects = ProProject::orderBy('project_code')->get(['id', 'project_code', 'project_title']);
        $expHutsActivities = StudyActivity::where('study_activity_name', 'Experimental Huts')
            ->with('project')
            ->orderBy('estimated_activity_date', 'desc')
            ->limit(50)
            ->get();

        return view('huts.show', compact('hut', 'projects', 'expHutsActivities'));
    }

    public function edit(Hut $hut)
    {
        $sites = Site::orderBy('name')->get();
        return view('huts.edit', compact('hut', 'sites'));
    }

    public function update(Request $request, Hut $hut)
    {
        $validated = $request->validate([
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes'     => 'nullable|string',
            'image'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($hut->image_path) {
                Storage::disk('public')->delete($hut->image_path);
            }
            $validated['image_path'] = $request->file('image')
                ->store('exp_huts/huts', 'public');
        }

        unset($validated['image']);
        $hut->update($validated);

        return redirect()->route('huts.show', $hut)
            ->with('success', 'Case mise à jour avec succès.');
    }

    public function updateStatus(Request $request, Hut $hut)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,in_use,damaged,abandoned',
            'reason' => 'nullable|string|max:500',
            'notes'  => 'nullable|string',
        ]);

        $previousStatus = $hut->status;

        if ($previousStatus !== $validated['status']) {
            StateChange::create([
                'hut_id'          => $hut->id,
                'previous_status' => $previousStatus,
                'new_status'      => $validated['status'],
                'reason'          => $validated['reason'] ?? null,
                'changed_by'      => Auth::id(),
                'changed_at'      => now(),
                'notes'           => $validated['notes'] ?? null,
            ]);

            $hut->update(['status' => $validated['status']]);
        }

        return back()->with('success', 'État de la case mis à jour.');
    }

    public function addUsage(Request $request, Hut $hut)
    {
        $validated = $request->validate([
            'project_id'        => 'required|integer',
            'study_activity_id' => 'nullable|integer',
            'phase_name'        => 'nullable|string|max:255',
            'date_start'        => 'required|date',
            'date_end'          => 'required|date|after_or_equal:date_start',
            'notes'             => 'nullable|string',
        ]);

        // Check for overlapping usages
        $overlap = ProjectUsage::where('hut_id', $hut->id)
            ->where('date_start', '<=', $validated['date_end'])
            ->where('date_end', '>=', $validated['date_start'])
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'date_start' => 'La case est déjà utilisée pendant cette période.',
            ])->withInput();
        }

        $validated['hut_id']     = $hut->id;
        $validated['created_by'] = Auth::id();

        ProjectUsage::create($validated);

        // Auto-set hut status to in_use if currently available
        if ($hut->status === 'available') {
            $hut->update(['status' => 'in_use']);
        }

        return back()->with('success', 'Utilisation enregistrée avec succès.');
    }

    public function removeUsage(Hut $hut, ProjectUsage $usage)
    {
        if ($usage->hut_id !== $hut->id) {
            abort(403);
        }
        $usage->delete();
        return back()->with('success', 'Utilisation supprimée.');
    }

    public function destroy(Hut $hut)
    {
        $hut->delete(); // soft delete
        return redirect()->route('huts.index')
            ->with('success', 'Case supprimée (restaurable).');
    }

    public function forceDestroy(int $id)
    {
        $this->requireAdmin();
        $hut = Hut::withTrashed()->findOrFail($id);
        if ($hut->image_path) Storage::disk('public')->delete($hut->image_path);
        $hut->forceDelete();
        return redirect()->route('huts.index')->with('success', 'Case supprimée définitivement.');
    }

    public function restore(int $id)
    {
        $this->requireAdmin();
        Hut::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Case restaurée.');
    }

    private function requireAdmin(): void
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) abort(403);
    }
}
