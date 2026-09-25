<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Block;
use App\Models\Plot;
use App\Models\Owner;
use App\Models\PossessionCase;
use App\Models\PossessionCaseHistory;
use App\Models\AreaVariation;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. POSSESSION STATUS
        |--------------------------------------------------------------------------
        |
        | Current status of all active/non-deleted possession cases.
        |
        */

        $possessionStatus = [
            'received' => PossessionCase::where('current_status', 'received')->count(),

            'prepared' => PossessionCase::where('current_status', 'prepared')->count(),

            'signing' => PossessionCase::where(
                'current_status',
                'surveyor_signed'
            )->count(),

            'approval' => PossessionCase::where(
                'current_status',
                'approval'
            )->count(),

            'town_planner_signed' => PossessionCase::where(
                'current_status',
                'town_planner_signed'
            )->count(),

            'completed' => PossessionCase::where(
                'current_status',
                'completed'
            )->count(),

            'cancelled' => PossessionCase::where(
                'current_status',
                'cancelled'
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | 2. ATTENTION REQUIRED
        |--------------------------------------------------------------------------
        */

        // All cases which are still somewhere in the workflow
        // and are not completed/cancelled.
        $pendingPossessionCases = PossessionCase::whereIn(
            'current_status',
            [
                'received',
                'prepared',
                'surveyor_signed',
                'approval',
                'town_planner_signed',
            ]
        )->count();

        // Cases currently waiting at approval stage
        $awaitingApproval = PossessionCase::where(
            'current_status',
            'approval'
        )->count();

        // Area variations currently available in the system
        $areaVariationsCount = AreaVariation::count();

        // According to the current possession workflow,
        // completed cases are treated as ready for handover.
        $readyForHandover = PossessionCase::where(
            'current_status',
            'completed'
        )->count();

        $attention = [
            'pending_possession' => $pendingPossessionCases,
            'awaiting_approval' => $awaitingApproval,
            'area_variations' => $areaVariationsCount,
            'ready_for_handover' => $readyForHandover,
        ];


        /*
        |--------------------------------------------------------------------------
        | 3. AREA VARIATION STATUS
        |--------------------------------------------------------------------------
        |
        | workflow_status:
        | 1 = Pending Review
        | 2 = Ready for Print
        | 3 = Printed
        |
        */

        $areaVariationStatus = [
            'received_updated' => AreaVariation::count(),

            'ready_for_print' => AreaVariation::where(
                'workflow_status',
                2
            )->count(),

            'printed' => AreaVariation::where(
                'workflow_status',
                3
            )->count(),

            'pending' => AreaVariation::where(
                'workflow_status',
                1
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | 4. TODAY'S POSSESSION OVERVIEW
        |--------------------------------------------------------------------------
        |
        | We use possession_case_histories instead of possession_cases
        | because the dashboard should show today's workflow activity.
        |
        */

        $today = Carbon::today();

        $todayReceived = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'received')
            ->count();

        $todayPrepared = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'prepared')
            ->count();

        $todaySigning = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'surveyor_signed')
            ->count();

        $todaySentForApproval = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'approval')
            ->count();

        $todayApproval = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'town_planner_signed')
            ->count();

        $todayReadyForHandover = PossessionCaseHistory::whereDate(
            'created_at',
            $today
        )
            ->where('new_status', 'completed')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 5. TODAY'S AREA VARIATION OVERVIEW
        |--------------------------------------------------------------------------
        */

        $todayAreaReceivedUpdated = AreaVariation::whereDate(
            'created_at',
            $today
        )->count();

        $todayAreaPending = AreaVariation::where(
            'workflow_status',
            1
        )
            ->whereDate('updated_at', $today)
            ->count();

        $todayAreaReadyForPrint = AreaVariation::where(
            'workflow_status',
            2
        )
            ->whereDate('updated_at', $today)
            ->count();

        /*
        | Printed today:
        |
        | workflow_status changes are recorded by Spatie Activity Log.
        | We therefore check the activity log for an Area Variation
        | update where workflow_status became 3.
        */

        $todayAreaPrinted = Activity::where(
            'log_name',
            'area_variation'
        )
            ->where('event', 'updated')
            ->whereDate('created_at', $today)
            ->whereJsonContains(
                'properties->attributes->workflow_status',
                3
            )
            ->count();

        $todayOverview = [
            'possession' => [
                'received' => $todayReceived,
                'prepared' => $todayPrepared,
                'sent_for_approval' => $todaySentForApproval,
                'signing' => $todaySigning,
                'approval' => $todayApproval,
                'ready_for_handover' => $todayReadyForHandover,
            ],

            'area_variation' => [
                'received_updated' => $todayAreaReceivedUpdated,
                'printed' => $todayAreaPrinted,
                'ready_for_print' => $todayAreaReadyForPrint,
                'pending' => $todayAreaPending,
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | 6. PROJECT-WISE OVERVIEW
        |--------------------------------------------------------------------------
        */
        /*
        |--------------------------------------------------------------------------
        | 6. PROJECT-WISE OVERVIEW
        |--------------------------------------------------------------------------
        |
        | Possession:
        |   possession_sequence = 1
        |
        | Repossession:
        |   possession_sequence > 1
        |
        | Example:
        |   35    = Possession
        |   35-T1 = Repossession
        |   35-T2 = Repossession
        |
        | Every subsequent possession of the same plot is counted
        | as a repossession.
        |
        */

        $projects = Project::orderBy('project_name')->get();

        $projectOverview = $projects->map(function ($project) {

            /*
            |--------------------------------------------------------------------------
            | Possession
            |--------------------------------------------------------------------------
            |
            | First possession of a plot.
            | possession_sequence = 1
            |
            */

            $possession = PossessionCase::whereHas(
                'plot',
                function ($query) use ($project) {
                    $query->where('project_id', $project->id);
                }
            )
                ->where('possession_sequence', 1)
                ->count();


            /*
            |--------------------------------------------------------------------------
            | Repossession
            |--------------------------------------------------------------------------
            |
            | Any subsequent possession of the same plot.
            | possession_sequence > 1
            |
            */

            $repossession = PossessionCase::whereHas(
                'plot',
                function ($query) use ($project) {
                    $query->where('project_id', $project->id);
                }
            )
                ->where('possession_sequence', '>', 1)
                ->count();


            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            |
            | Total possession cases for this project.
            |
            */

            $total = $possession + $repossession;


            return [
                'project_name' => $project->project_name,
                'possession' => $possession,
                'repossession' => $repossession,
                'total' => $total,
            ];
        });

        // $projects = Project::orderBy('project_name')->get();

        // $projectOverview = $projects->map(function ($project) {

        //     /*
        //     | Blocks
        //     */
        //     $blocks = Block::where(
        //         'project_id',
        //         $project->id
        //     )->count();


        //     /*
        //     | Plots
        //     */
        //     $plots = Plot::where(
        //         'project_id',
        //         $project->id
        //     )->count();


        //     /*
        //     | Possession Cases
        //     */
        //     $possession = PossessionCase::whereHas(
        //         'plot',
        //         function ($query) use ($project) {
        //             $query->where(
        //                 'project_id',
        //                 $project->id
        //             );
        //         }
        //     )->count();


        //     /*
        //     | Owners
        //     |
        //     | Owners are connected to possession cases through:
        //     |
        //     | possession_cases
        //     |       ↓
        //     | possession_case_owners
        //     |       ↓
        //     | owners
        //     |
        //     | DISTINCT owner IDs are counted so the same owner
        //     | appearing in multiple possession cases is counted once.
        //     */
        //     $owners = DB::table('possession_case_owners')
        //         ->join(
        //             'possession_cases',
        //             'possession_cases.id',
        //             '=',
        //             'possession_case_owners.possession_case_id'
        //         )
        //         ->join(
        //             'plots',
        //             'plots.id',
        //             '=',
        //             'possession_cases.plot_id'
        //         )
        //         ->where(
        //             'plots.project_id',
        //             $project->id
        //         )
        //         ->whereNull('possession_cases.deleted_at')
        //         ->distinct('possession_case_owners.owner_id')
        //         ->count('possession_case_owners.owner_id');


        //     /*
        //     | Area Variations
        //     */
        //     $areaVariations = AreaVariation::whereHas(
        //         'plot',
        //         function ($query) use ($project) {
        //             $query->where(
        //                 'project_id',
        //                 $project->id
        //             );
        //         }
        //     )->count();


        //     return [
        //         'project_name' => $project->project_name,
        //         'blocks' => $blocks,
        //         'plots' => $plots,
        //         'owners' => $owners,
        //         'possession' => $possession,
        //         'area_variations' => $areaVariations,
        //     ];
        // });


        /*
        |--------------------------------------------------------------------------
        | 7. POSSESSION TREND - LAST 6 MONTHS
        |--------------------------------------------------------------------------
        |
        | We count "Case Received" history records month-wise.
        |
        */

        $trendLabels = [];
        $trendData = [];

        for ($i = 5; $i >= 0; $i--) {

            $month = Carbon::now()
                ->startOfMonth()
                ->subMonths($i);

            $trendLabels[] = $month->format('M');

            $count = PossessionCaseHistory::where(
                'new_status',
                'received'
            )
                ->whereYear(
                    'created_at',
                    $month->year
                )
                ->whereMonth(
                    'created_at',
                    $month->month
                )
                ->count();

            $trendData[] = $count;
        }


        /*
        |--------------------------------------------------------------------------
        | 8. RECENT ACTIVITY
        |--------------------------------------------------------------------------
        |
        | Possession workflow history + Area Variation activity log.
        |
        */

        $recentPossessionActivity = PossessionCaseHistory::with([
            'possessionCase.plot.project',
            'possessionCase.plot.block',
            'user',
        ])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($history) {

                $plotNumber = optional(
                    optional($history->possessionCase)->plot
                )->plot_number;

                return [
                    'type' => 'possession',
                    'title' => $history->action ?? 'Possession Activity',
                    'description' => $history->remarks
                        ?? (
                            $plotNumber
                                ? 'Plot ' . $plotNumber
                                : 'Possession case activity'
                        ),
                    'created_at' => $history->created_at,
                ];
            });


        $recentAreaActivity = Activity::where(
            'log_name',
            'area_variation'
        )
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($activity) {

                return [
                    'type' => 'area_variation',
                    'title' => $activity->description
                        ?? 'Area Variation Updated',

                    'description' => 'Area variation activity',

                    'created_at' => $activity->created_at,
                ];
            });


        $recentActivity = $recentPossessionActivity
            ->merge($recentAreaActivity)
            ->sortByDesc('created_at')
            ->take(4)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 9. RECENTLY ADDED
        |--------------------------------------------------------------------------
        |
        | Last 7 days.
        |
        */

        $last7Days = Carbon::now()->subDays(7);


        $recentlyAdded = [
            'owners' => Owner::where(
                'created_at',
                '>=',
                $last7Days
            )->count(),

            'possession_cases' => PossessionCase::where(
                'created_at',
                '>=',
                $last7Days
            )->count(),

            'area_variations' => AreaVariation::where(
                'created_at',
                '>=',
                $last7Days
            )->count(),

            'plot_updates' => Plot::where(
                'updated_at',
                '>=',
                $last7Days
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | 10. RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'possessionStatus',
            'attention',
            'areaVariationStatus',
            'todayOverview',
            'projectOverview',
            'trendLabels',
            'trendData',
            'recentActivity',
            'recentlyAdded'
        ));
    }
}
