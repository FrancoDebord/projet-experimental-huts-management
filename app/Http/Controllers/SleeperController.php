<?php

namespace App\Http\Controllers;

use App\Models\Sleeper;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SleeperController extends Controller
{
    public function index()
    {
        $sleepers = Sleeper::withTrashed()
            ->with('site')
            ->orderBy('code')
            ->get();

        $isAdmin = in_array(Auth::user()->role, ['super_admin', 'facility_manager']);
        return view('sleepers.index', compact('sleepers', 'isAdmin'));
    }

    public function create()
    {
        $sites = Site::where('status', 'active')->orderBy('name')->get();
        return view('sleepers.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:191',
            'code'    => 'required|string|max:50|unique:exp_huts_sleepers,code',
            'site_id' => 'nullable|exists:exp_huts_sites,id',
            'gender'  => 'nullable|in:M,F',
            'active'  => 'boolean',
            'notes'   => 'nullable|string',
        ]);

        $validated['active'] = $request->boolean('active', true);
        Sleeper::create($validated);

        return redirect()->route('sleepers.index')
            ->with('success', "Dormeur « {$validated['name']} » créé.");
    }

    public function edit(Sleeper $sleeper)
    {
        $sites = Site::orderBy('name')->get();
        return view('sleepers.edit', compact('sleeper', 'sites'));
    }

    public function update(Request $request, Sleeper $sleeper)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:191',
            'code'    => 'required|string|max:50|unique:exp_huts_sleepers,code,' . $sleeper->id,
            'site_id' => 'nullable|exists:exp_huts_sites,id',
            'gender'  => 'nullable|in:M,F',
            'active'  => 'boolean',
            'notes'   => 'nullable|string',
        ]);

        $validated['active'] = $request->boolean('active', true);
        $sleeper->update($validated);

        return redirect()->route('sleepers.index')
            ->with('success', 'Dormeur mis à jour.');
    }

    public function destroy(Sleeper $sleeper)
    {
        $sleeper->delete();
        return back()->with('success', 'Dormeur désactivé.');
    }

    public function forceDestroy(int $id)
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) {
            abort(403);
        }
        Sleeper::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Dormeur supprimé définitivement.');
    }

    public function restore(int $id)
    {
        if (!in_array(Auth::user()->role, ['super_admin', 'facility_manager'])) {
            abort(403);
        }
        Sleeper::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Dormeur restauré.');
    }
}
