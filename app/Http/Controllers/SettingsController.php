<?php

namespace App\Http\Controllers;

use App\Models\UserPref;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $prefs = UserPref::forUser(Auth::id());
        $user  = Auth::user();
        return view('settings.index', compact('prefs', 'user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'push_enabled'          => 'boolean',
            'notify_incidents'      => 'boolean',
            'notify_activity_start' => 'boolean',
            'notify_activity_end'   => 'boolean',
        ]);

        $validated['push_enabled']          = $request->boolean('push_enabled');
        $validated['notify_incidents']      = $request->boolean('notify_incidents');
        $validated['notify_activity_start'] = $request->boolean('notify_activity_start');
        $validated['notify_activity_end']   = $request->boolean('notify_activity_end');

        UserPref::forUser(Auth::id())->update($validated);

        return back()->with('success', 'Préférences de notification enregistrées.');
    }
}
