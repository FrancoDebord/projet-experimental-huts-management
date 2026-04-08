<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Hut;
use App\Models\Incident;
use App\Models\ProjectUsage;
use App\Models\ProProject;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats
        $stats = [
            'sites_total'      => Site::count(),
            'sites_active'     => Site::where('status', 'active')->count(),
            'huts_total'       => Hut::count(),
            'huts_available'   => Hut::where('status', 'available')->count(),
            'huts_in_use'      => Hut::where('status', 'in_use')->count(),
            'huts_damaged'     => Hut::where('status', 'damaged')->count(),
            'incidents_open'   => Incident::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        // Active project usages (today is between start and end)
        $activeUsages = ProjectUsage::with(['hut.site', 'project'])
            ->where('date_start', '<=', $today)
            ->where('date_end', '>=', $today)
            ->orderBy('date_end')
            ->get();

        // Upcoming usages (next 30 days)
        $upcomingUsages = ProjectUsage::with(['hut.site', 'project'])
            ->where('date_start', '>', $today)
            ->where('date_start', '<=', $today->copy()->addDays(30))
            ->orderBy('date_start')
            ->get();

        // Recent incidents
        $recentIncidents = Incident::with(['hut.site', 'project'])
            ->orderBy('incident_date', 'desc')
            ->limit(5)
            ->get();

        // Chart data: huts by status
        $hutsByStatus = [
            'labels' => ['Disponible', 'En utilisation', 'Endommagée', 'Abandonnée'],
            'data'   => [
                Hut::where('status', 'available')->count(),
                Hut::where('status', 'in_use')->count(),
                Hut::where('status', 'damaged')->count(),
                Hut::where('status', 'abandoned')->count(),
            ],
            'colors' => ['#198754', '#CC0000', '#ffc107', '#6c757d'],
        ];

        // Chart data: usages per month (last 6 months)
        $usagesPerMonth = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $monthLabels[] = $month->translatedFormat('M Y');
            $usagesPerMonth[] = ProjectUsage::whereYear('date_start', $month->year)
                ->whereMonth('date_start', $month->month)
                ->count();
        }

        // Active projects using huts
        $activeProjectIds = ProjectUsage::where('date_start', '<=', $today)
            ->where('date_end', '>=', $today)
            ->distinct()
            ->pluck('project_id');

        $activeProjects = ProProject::whereIn('id', $activeProjectIds)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'activeUsages', 'upcomingUsages',
            'recentIncidents', 'hutsByStatus',
            'monthLabels', 'usagesPerMonth', 'activeProjects'
        ));
    }
}
