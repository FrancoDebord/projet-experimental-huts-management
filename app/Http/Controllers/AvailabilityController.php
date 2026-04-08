<?php

namespace App\Http\Controllers;

use App\Models\Hut;
use App\Models\Site;
use App\Models\ProjectUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index()
    {
        $sites = Site::with('huts')->where('status', 'active')->orderBy('name')->get();
        return view('availability.index', compact('sites'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end'   => 'required|date|after_or_equal:date_start',
            'site_id'    => 'nullable|exists:exp_huts_sites,id',
        ]);

        $start   = $request->date_start;
        $end     = $request->date_end;
        $siteId  = $request->site_id;

        $query = Hut::with(['site', 'projectUsages.project'])
            ->orderBy('site_id')
            ->orderBy('number');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $huts = $query->get();

        $results = $huts->map(function ($hut) use ($start, $end) {
            $available = $hut->isAvailableForPeriod($start, $end);
            $reason    = $available ? null : $hut->getUnavailabilityReason($start, $end);

            return [
                'hut'       => $hut,
                'available' => $available,
                'reason'    => $reason,
            ];
        });

        $available   = $results->where('available', true)->count();
        $unavailable = $results->where('available', false)->count();

        $sites = Site::where('status', 'active')->orderBy('name')->get();

        return view('availability.index', compact(
            'sites', 'results', 'start', 'end', 'available', 'unavailable', 'siteId'
        ));
    }
}
