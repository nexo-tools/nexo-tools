<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Aggregated, cookieless ecosystem metrics from beacon_events. Reads only the
 * local table — no external requests, no per-visitor detail. (AC-ADMIN-2..4)
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var array<string, array<string, mixed>> $registry */
        $registry = config('nexo-ecosystem.tools', []);

        $totalVisits = DB::table('beacon_events')->count();
        $totalUniques = DB::table('beacon_events')->distinct()->count('visitor_hash');

        // Visits + unique visitors per day (unique = distinct daily hash). (AC-ADMIN-2)
        $byDay = DB::table('beacon_events')
            ->selectRaw('day, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques')
            ->groupBy('day')->orderByDesc('day')->limit(30)->get();

        // Breakdown by emitting tool. (AC-ADMIN-2)
        $byOrigin = DB::table('beacon_events')
            ->selectRaw('origin, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques')
            ->groupBy('origin')->orderByDesc('visits')->get();

        // Top paths across the ecosystem. (AC-ADMIN-2)
        $topPaths = DB::table('beacon_events')
            ->selectRaw('origin, path, COUNT(*) as visits')
            ->groupBy('origin', 'path')->orderByDesc('visits')->limit(10)->get();

        // alvarocdev view: its own visits, plus which tool referred them. (AC-ADMIN-3)
        $alvaro = DB::table('beacon_events')->where('origin', 'alvarocdev');
        $alvaroVisits = (clone $alvaro)->count();
        $alvaroUniques = (clone $alvaro)->distinct()->count('visitor_hash');
        $alvaroRefs = (clone $alvaro)->whereNotNull('ref')
            ->selectRaw('ref, COUNT(*) as visits')
            ->groupBy('ref')->orderByDesc('visits')->get();

        // slug => display name for the view (registry names + alvarocdev).
        $names = ['alvarocdev' => 'alvarocdev'];
        foreach ($registry as $key => $tool) {
            $names[$key] = is_string($tool['name'] ?? null) ? $tool['name'] : $key;
        }

        return view('admin.dashboard', [
            'totalVisits' => $totalVisits,
            'totalUniques' => $totalUniques,
            'byDay' => $byDay,
            'byOrigin' => $byOrigin,
            'topPaths' => $topPaths,
            'alvaroVisits' => $alvaroVisits,
            'alvaroUniques' => $alvaroUniques,
            'alvaroRefs' => $alvaroRefs,
            'names' => $names,
        ]);
    }
}
