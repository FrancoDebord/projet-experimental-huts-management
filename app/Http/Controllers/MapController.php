<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Hut;
use App\Models\ProjectUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $sites = Site::with('huts')->get();
        return view('maps.index', compact('sites'));
    }

    public function data(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $sites = Site::with('huts')->get();

        $sitesData = $sites->map(function ($site) use ($today) {
            return [
                'id'        => $site->id,
                'name'      => $site->name,
                'village'   => $site->village,
                'city'      => $site->city,
                'status'    => $site->status,
                'lat'       => $site->latitude,
                'lng'       => $site->longitude,
                'huts_count'=> $site->huts->count(),
                'huts'      => $site->huts->map(function ($hut) use ($today) {
                    // Check current usage
                    $usage = ProjectUsage::with('project')
                        ->where('hut_id', $hut->id)
                        ->where('date_start', '<=', $today)
                        ->where('date_end', '>=', $today)
                        ->first();

                    return [
                        'id'        => $hut->id,
                        'name'      => $hut->name,
                        'number'    => $hut->number,
                        'status'    => $hut->status,
                        'lat'       => $hut->latitude,
                        'lng'       => $hut->longitude,
                        'project'   => $usage?->project?->project_code,
                        'phase'     => $usage?->phase_name,
                        'url'       => route('huts.show', $hut->id),
                    ];
                }),
            ];
        });

        return response()->json($sitesData);
    }
}
