<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Exports\ActivityLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class ActivityLogController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Export Activity Logs
    |--------------------------------------------------------------------------
    */

    public function export()
    {
        return Excel::download(
            new ActivityLogsExport,
            'activity_logs.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Activity Log Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Activity::with([
            'causer',
            'subject',
        ])
        ->latest();


        /*
        |--------------------------------------------------------------------------
        | Filter By User
        |--------------------------------------------------------------------------
        */

        if ($request->user_id) {

            $query->where(
                'causer_id',
                $request->user_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter By Model
        |--------------------------------------------------------------------------
        */

        if ($request->model) {

            $query->where(
                'subject_type',
                $request->model
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter By Date
        |--------------------------------------------------------------------------
        */

        if (
            $request->from_date &&
            $request->to_date
        ) {

            $query->whereBetween(
                'created_at',
                [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59',
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $activities = $query->paginate(20);


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $users = User::orderBy(
            'name'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Models
        |--------------------------------------------------------------------------
        */

        $models = Activity::select(
                'subject_type'
            )
            ->whereNotNull(
                'subject_type'
            )
            ->distinct()
            ->orderBy(
                'subject_type'
            )
            ->pluck(
                'subject_type'
            );


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.activity_logs.index',
            compact(
                'activities',
                'users',
                'models'
            )
        );
    }
}

// <!-- 
// <php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Spatie\Activitylog\Models\Activity;
// use App\Models\User;
// use App\Exports\ActivityLogsExport;
// use Maatwebsite\Excel\Facades\Excel;


// class ActivityLogController extends Controller
// {
//     public function export()
//     {
//         return Excel::download(new ActivityLogsExport, 'activity_logs.xlsx');
//     }

//     public function index(Request $request)
//     {
//         $query = Activity::with([
//             'causer',
//             'subject',
//         ])
//             ->latest();
//         // $query = Activity::with('causer')
//         //     ->latest();

//         // Filter by User
//         if ($request->user_id) {
//             $query->where('causer_id', $request->user_id);
//         }

//         // Filter by Model
//         if ($request->model) {
//             $query->where('subject_type', $request->model);
//         }

//         // Filter by Date Range
//         if ($request->from_date && $request->to_date) {
//             $query->whereBetween('created_at', [
//                 $request->from_date . ' 00:00:00',
//                 $request->to_date . ' 23:59:59'
//             ]);
//         }

//         $activities = $query->paginate(20);

//         $users = User::all();

//         $models = Activity::select('subject_type')
//             ->distinct()
//             ->pluck('subject_type');

//         return view('admin.activity_logs.index', compact('activities', 'users', 'models'));
//     }
// } -->
