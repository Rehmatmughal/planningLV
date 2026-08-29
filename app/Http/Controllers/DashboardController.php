<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use Spatie\Activitylog\Models\Activity;


class DashboardController extends Controller
{
    public function index()
    {
        // Main Query with Filters
        $plotQuery = Plot::query();
            // ->when($request->project_id, function($q) use($request) { $q->where('project_id', $request->project_id); })
            // ->when($request->block_id, function($q) use($request) { $q->where('block_id', $request->block_id); });

        // 1. Statistics Cards Data
        $stats = [
            'total_plots' => (clone $plotQuery)->count(),
            
            // 'lop_clear' => (clone $plotQuery)->whereHas('lopStatus', function($q) {
            //     $q->where('lop_status', 'lop');
            // })->count(),

            // 'mortgaged' => (clone $plotQuery)->whereHas('mortgageStatus', function($q) {
            //     $q->where('is_mortgaged', 'yes');
            // })->count(),

            // 'fully_developed' => (clone $plotQuery)->whereHas('developmentStatus', function($q) {
            //     $q->where('sewer_manholes', 'constructed')->where('asphalt_tst', 'yes');
            // })->count(),
        ];
        // end of stats
        // $projects = Project::all();

        // for working summary view
        $totalActivities = Activity::count();

        $todayActivities = Activity::whereDate('created_at', today())->count();

        // $topUser = Activity::select('causer_id')
        //     ->selectRaw('count(*) as total')
        //     ->groupBy('causer_id')
        //     ->orderByDesc('total')
        //     ->with('causer')
        //     ->first();

        // $topUser = Activity::whereNotNull('causer_id')
        //     ->select('causer_id')
        //     ->selectRaw('count(*) as total')
        //     ->groupBy('causer_id')
        //     ->orderByDesc('total')
        //     ->with('causer')
        //     ->first();

        // test start
        // $activity = Activity::whereNotNull('causer_id')->latest()->first();

        // dd([
        //     'causer_id' => $activity->causer_id,
        //     'causer_type' => $activity->causer_type,
        //     'causer' => $activity->causer,
        // ]);
        // test end

        // working
        // $topUser = Activity::whereNotNull('causer_id')
        //     ->select('causer_id')
        //     ->selectRaw('count(*) as total')
        //     ->groupBy('causer_id')
        //     ->orderByDesc('total')
        //     ->first();

        // $topUserName = \App\Models\User::find($topUser?->causer_id)?->name ?? 'N/A';
        // working end
        $topUser = Activity::whereNotNull('causer_id')
            ->whereNotNull('causer_type')
            ->select('causer_id', 'causer_type')
            ->selectRaw('count(*) as total')
            ->groupBy('causer_id', 'causer_type')
            ->orderByDesc('total')
            ->with('causer')
            ->first();

        $topUserName = \App\Models\User::find($topUser?->causer_id)?->name ?? 'N/A';

        $todayTopUser = Activity::whereNotNull('causer_id')
            ->whereNotNull('causer_type')
            ->whereDate('created_at', today())
            ->select('causer_id', 'causer_type')
            ->selectRaw('count(*) as total')
            ->groupBy('causer_id', 'causer_type')
            ->orderByDesc('total')
            ->with('causer')
            ->first();

        //     // for working summary view end

        $totalProjects = Project::count();
        $totalBlocks = Block::count();
        $totalStreets = Street::count();
        $totalPlots = Plot::count();

        // Chart Data
        $plotsPerProject = Project::withCount('plots')->get(['project_name', 'plots_count']);

        // dd([
        //     'topUser' => $topUser,
        //     'causer_id' => $topUser?->causer_id,
        //     'causer' => $topUser?->causer,
        //     'name' => $topUser?->causer?->name,
        // ]);
        // dd(\App\Models\User::find(12));
        // dd($topUser->causer);
        // dd(Activity::latest()->first());

        // return view('dashboard', compact('stats', 'totalProjects', 'totalBlocks', 'totalStreets', 'totalPlots', 'plotsPerProject',
        // 'totalActivities',
        // 'todayActivities',
        // 'topUser'
        // ));
        return view('dashboard', compact(
            'stats',
            'totalProjects',
            'totalBlocks',
            'totalStreets',
            'totalPlots',
            'plotsPerProject',
            'totalActivities',
            'todayActivities',
            'topUser',
            'topUserName',
            'todayTopUser'
        ));
        
    }
    // public function app()
    // {
    //     $totalProjects = Project::count();
    //     $totalBlocks = Block::count();
    //     $totalStreets = Street::count();
    //     $totalPlots = Plot::count();

    //     // Chart Data
    //     $plotsPerProject = Project::withCount('plots')->get(['project_name', 'plots_count']);

    //     return view('app', compact('totalProjects', 'totalBlocks', 'totalStreets', 'totalPlots', 'plotsPerProject'));
    // }
}
