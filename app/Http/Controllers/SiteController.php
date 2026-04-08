<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount('huts')
            ->with('huts')
            ->orderBy('name')
            ->get();

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:exp_huts_sites,name',
            'village'   => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:255',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status'    => 'required|in:active,abandoned',
            'notes'     => 'nullable|string',
            'image'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')
                ->store('exp_huts/sites', 'public');
        }

        unset($validated['image']);
        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', "Site « {$validated['name']} » créé avec succès.");
    }

    public function show(Site $site)
    {
        $site->load(['huts.projectUsages.project', 'huts.incidents']);
        return view('sites.show', compact('site'));
    }

    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:exp_huts_sites,name,' . $site->id,
            'village'   => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:255',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status'    => 'required|in:active,abandoned',
            'notes'     => 'nullable|string',
            'image'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($site->image_path) {
                Storage::disk('public')->delete($site->image_path);
            }
            $validated['image_path'] = $request->file('image')
                ->store('exp_huts/sites', 'public');
        }

        unset($validated['image']);
        $site->update($validated);

        return redirect()->route('sites.show', $site)
            ->with('success', 'Site mis à jour avec succès.');
    }

    public function destroy(Site $site)
    {
        $site->delete(); // soft delete
        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé (restaurable).');
    }

    public function forceDestroy(int $id)
    {
        $this->requireAdmin();
        $site = Site::withTrashed()->findOrFail($id);
        if ($site->image_path) Storage::disk('public')->delete($site->image_path);
        $site->forceDelete();
        return redirect()->route('sites.index')->with('success', 'Site supprimé définitivement.');
    }

    public function restore(int $id)
    {
        $this->requireAdmin();
        Site::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Site restauré.');
    }

    private function requireAdmin(): void
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) abort(403);
    }
}
