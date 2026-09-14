<?php

namespace App\Http\Controllers;

use App\Models\PlotSizeAssignment;
use App\Models\Project;
use App\Models\Block;
use App\Models\PropertyType;
use App\Models\Plotsize;
use Illuminate\Http\Request;

class PlotSizeAssignmentController extends Controller
{
    // public function index()
    // {
    //     $assignments = PlotSizeAssignment::with([
    //         'project',
    //         'block',
    //         'propertyType',
    //         'plotsize',
    //     ])
    //     ->latest()
    //     ->paginate(10);

    //     return view(
    //         'plot-size-assignments.index',
    //         compact('assignments')
    //     );
    // }
    public function index(Request $request)
    {
        $projects = Project::orderBy('project_name')->get();

        $propertyTypes = PropertyType::orderBy('name')->get();

        $assignments = PlotSizeAssignment::with([
            'project',
            'block',
            'propertyType',
            'plotsize',
        ])

        ->when(
            $request->project_id,
            function ($query) use ($request) {
                $query->where(
                    'project_id',
                    $request->project_id
                );
            }
        )

        ->when(
            $request->block_id,
            function ($query) use ($request) {
                $query->where(
                    'block_id',
                    $request->block_id
                );
            }
        )

        ->when(
            $request->property_type_id,
            function ($query) use ($request) {
                $query->where(
                    'property_type_id',
                    $request->property_type_id
                );
            }
        )

        ->when(
            $request->plotsize_id,
            function ($query) use ($request) {
                $query->where(
                    'plotsize_id',
                    $request->plotsize_id
                );
            }
        )

        ->latest()
        ->paginate(10)
        ->withQueryString();

        $blocks = collect();

        $sizes = collect();

        if ($request->project_id) {

            $blocks = Block::where(
                'project_id',
                $request->project_id
            )
            ->orderBy('block_name')
            ->get();

            $sizes = Plotsize::where(
                'project_id',
                $request->project_id
            )
            ->orderBy('title')
            ->get();
        }

        return view(
            'plot-size-assignments.index',
            compact(
                'assignments',
                'projects',
                'blocks',
                'propertyTypes',
                'sizes'
            )
        );
    }


    public function create()
    {
        $projects = Project::orderBy('project_name')->get();

        $propertyTypes = PropertyType::orderBy('name')->get();

        return view(
            'plot-size-assignments.create',
            compact(
                'projects',
                'propertyTypes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Blocks according to Project
    |--------------------------------------------------------------------------
    */

    public function getBlocks($project_id)
    {
        $blocks = Block::where('project_id', $project_id)
            ->orderBy('block_name')
            ->get([
                'id',
                'block_name'
            ]);

        return response()->json($blocks);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Sizes according to Project
    |--------------------------------------------------------------------------
    */

    public function getSizes($project_id)
    {
        $sizes = Plotsize::where('project_id', $project_id)
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'size_area'
            ]);

        return response()->json($sizes);
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id'
            ],

            'block_id' => [
                'required',
                'exists:blocks,id'
            ],

            'property_type_id' => [
                'required',
                'exists:property_types,id'
            ],

            'plotsize_id' => [
                'required',
                'exists:plotsizes,id'
            ],
        ]);


        // Check Block belongs to selected Project
        $blockBelongsToProject = Block::where('id', $validated['block_id'])
            ->where('project_id', $validated['project_id'])
            ->exists();

        if (! $blockBelongsToProject) {

            return back()
                ->withErrors([
                    'block_id' =>
                        'The selected Block does not belong to the selected Project.',
                ])
                ->withInput();
        }


        // Check Size belongs to selected Project
        $sizeBelongsToProject = Plotsize::where(
            'id',
            $validated['plotsize_id']
        )
        ->where('project_id', $validated['project_id'])
        ->exists();

        if (! $sizeBelongsToProject) {

            return back()
                ->withErrors([
                    'plotsize_id' =>
                        'The selected Size does not belong to the selected Project.',
                ])
                ->withInput();
        }


        // Prevent duplicate assignment
        $exists = PlotSizeAssignment::where(
            'project_id',
            $validated['project_id']
        )
        ->where('block_id', $validated['block_id'])
        ->where('property_type_id', $validated['property_type_id'])
        ->where('plotsize_id', $validated['plotsize_id'])
        ->exists();

        if ($exists) {

            return back()
                ->withErrors([
                    'plotsize_id' =>
                        'This Size is already assigned to the selected Project, Block and Property Type.',
                ])
                ->withInput();
        }


        PlotSizeAssignment::create($validated);

        return redirect()
            ->route('plot-size-assignments.index')
            ->with(
                'success',
                'Plot Size assigned successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(PlotSizeAssignment $plotSizeAssignment)
    {
        $projects = Project::orderBy('project_name')->get();

        $propertyTypes = PropertyType::orderBy('name')->get();

        // Current project's blocks
        $blocks = Block::where(
            'project_id',
            $plotSizeAssignment->project_id
        )
        ->orderBy('block_name')
        ->get();

        // Current project's sizes
        $sizes = Plotsize::where(
            'project_id',
            $plotSizeAssignment->project_id
        )
        ->orderBy('title')
        ->get();

        return view(
            'plot-size-assignments.edit',
            compact(
                'plotSizeAssignment',
                'projects',
                'blocks',
                'propertyTypes',
                'sizes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        PlotSizeAssignment $plotSizeAssignment
    ) {
        $validated = $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id'
            ],

            'block_id' => [
                'required',
                'exists:blocks,id'
            ],

            'property_type_id' => [
                'required',
                'exists:property_types,id'
            ],

            'plotsize_id' => [
                'required',
                'exists:plotsizes,id'
            ],
        ]);


        // Check Block belongs to selected Project
        $blockBelongsToProject = Block::where('id', $validated['block_id'])
            ->where('project_id', $validated['project_id'])
            ->exists();

        if (! $blockBelongsToProject) {

            return back()
                ->withErrors([
                    'block_id' =>
                        'The selected Block does not belong to the selected Project.',
                ])
                ->withInput();
        }


        // Check Size belongs to selected Project
        $sizeBelongsToProject = Plotsize::where(
            'id',
            $validated['plotsize_id']
        )
        ->where('project_id', $validated['project_id'])
        ->exists();

        if (! $sizeBelongsToProject) {

            return back()
                ->withErrors([
                    'plotsize_id' =>
                        'The selected Size does not belong to the selected Project.',
                ])
                ->withInput();
        }


        // Prevent duplicate assignment
        // Ignore the current assignment
        $exists = PlotSizeAssignment::where(
            'project_id',
            $validated['project_id']
        )
        ->where('block_id', $validated['block_id'])
        ->where('property_type_id', $validated['property_type_id'])
        ->where('plotsize_id', $validated['plotsize_id'])
        ->where('id', '!=', $plotSizeAssignment->id)
        ->exists();

        if ($exists) {

            return back()
                ->withErrors([
                    'plotsize_id' =>
                        'This Size is already assigned to the selected Project, Block and Property Type.',
                ])
                ->withInput();
        }


        $plotSizeAssignment->update($validated);

        return redirect()
            ->route('plot-size-assignments.index')
            ->with(
                'success',
                'Plot Size assignment updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        PlotSizeAssignment $plotSizeAssignment
    ) {
        $plotSizeAssignment->delete();

        return redirect()
            ->route('plot-size-assignments.index')
            ->with(
                'success',
                'Plot Size assignment deleted successfully.'
            );
    }
}







// <!-- <php

// namespace App\Http\Controllers;

// use App\Models\PlotSizeAssignment;
// use App\Models\Project;
// use App\Models\PropertyType;
// use App\Models\Block;
// use App\Models\Plotsize;
// use Illuminate\Http\Request;

// class PlotSizeAssignmentController extends Controller
// {
//     /**
//      * Display all Plot Size Assignments.
//      */
//     public function index(Request $request)
//     {
//         $query = PlotSizeAssignment::with([
//             'project',
//             'propertyType',
//             'block',
//             'plotsize',
//         ]);

//         // Project filter
//         if ($request->project_id) {
//             $query->where('project_id', $request->project_id);
//         }

//         // Block filter
//         if ($request->block_id) {
//             $query->where('block_id', $request->block_id);
//         }

//         // Property Type filter
//         if ($request->property_type_id) {
//             $query->where('property_type_id', $request->property_type_id);
//         }

//         $assignments = $query
//             ->latest()
//             ->paginate(10)
//             ->withQueryString();

//         $projects = Project::orderBy('project_name')->get();

//         $propertyTypes = PropertyType::orderBy('name')->get();

//         $blocks = Block::orderBy('block_name')->get();

//         return view('plot-size-assignments.index', compact(
//             'assignments',
//             'projects',
//             'propertyTypes',
//             'blocks'
//         ));
//     }


//     /**
//      * Show the form for creating a new assignment.
//      */
//     public function create()
//     {
//         $projects = Project::orderBy('project_name')->get();

//         $propertyTypes = PropertyType::orderBy('name')->get();

//         return view('plots.plot-size-assignments.create', compact(
//             'projects',
//             'propertyTypes'
//         ));
//     }


//     /**
//      * Store a new Plot Size Assignment.
//      */
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'project_id' => [
//                 'required',
//                 'exists:projects,id',
//             ],

//             'property_type_id' => [
//                 'required',
//                 'exists:property_types,id',
//             ],

//             'block_id' => [
//                 'required',
//                 'exists:blocks,id',
//             ],

//             'plotsize_id' => [
//                 'required',
//                 'exists:plotsizes,id',
//             ],
//         ]);


//         /*
//         |--------------------------------------------------------------------------
//         | Check Block belongs to selected Project
//         |--------------------------------------------------------------------------
//         */

//         $blockBelongsToProject = Block::where('id', $validated['block_id'])
//             ->where('project_id', $validated['project_id'])
//             ->exists();

//         if (! $blockBelongsToProject) {
//             return back()
//                 ->withErrors([
//                     'block_id' => 'The selected Block does not belong to the selected Project.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Check Plot Size belongs to selected Project
//         |--------------------------------------------------------------------------
//         */

//         $sizeBelongsToProject = Plotsize::where('id', $validated['plotsize_id'])
//             ->where('project_id', $validated['project_id'])
//             ->exists();

//         if (! $sizeBelongsToProject) {
//             return back()
//                 ->withErrors([
//                     'plotsize_id' => 'The selected Plot Size does not belong to the selected Project.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Check Duplicate Assignment
//         |--------------------------------------------------------------------------
//         */

//         $exists = PlotSizeAssignment::where('project_id', $validated['project_id'])
//             ->where('property_type_id', $validated['property_type_id'])
//             ->where('block_id', $validated['block_id'])
//             ->where('plotsize_id', $validated['plotsize_id'])
//             ->exists();

//         if ($exists) {
//             return back()
//                 ->withErrors([
//                     'plotsize_id' =>
//                         'This Plot Size is already assigned to the selected Project, Block and Property Type.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Create Assignment
//         |--------------------------------------------------------------------------
//         */

//         PlotSizeAssignment::create($validated);

//         return redirect()
//             ->route('plot-size-assignments.index')
//             ->with('success', 'Plot Size assigned successfully!');
//     }


//     /**
//      * Show the form for editing an assignment.
//      */
//     public function edit(PlotSizeAssignment $plotSizeAssignment)
//     {
//         $projects = Project::orderBy('project_name')->get();

//         $propertyTypes = PropertyType::orderBy('name')->get();

//         /*
//         |--------------------------------------------------------------------------
//         | Load Blocks of selected Project
//         |--------------------------------------------------------------------------
//         */

//         $blocks = Block::where(
//             'project_id',
//             $plotSizeAssignment->project_id
//         )
//         ->orderBy('block_name')
//         ->get();


//         /*
//         |--------------------------------------------------------------------------
//         | Load Plot Sizes of selected Project
//         |--------------------------------------------------------------------------
//         */

//         $sizes = Plotsize::where(
//             'project_id',
//             $plotSizeAssignment->project_id
//         )
//         ->orderBy('title')
//         ->get();


//         return view('plot-size-assignments.edit', compact(
//             'plotSizeAssignment',
//             'projects',
//             'propertyTypes',
//             'blocks',
//             'sizes'
//         ));
//     }


//     /**
//      * Update an existing assignment.
//      */
//     public function update(
//         Request $request,
//         PlotSizeAssignment $plotSizeAssignment
//     ) {
//         $validated = $request->validate([
//             'project_id' => [
//                 'required',
//                 'exists:projects,id',
//             ],

//             'property_type_id' => [
//                 'required',
//                 'exists:property_types,id',
//             ],

//             'block_id' => [
//                 'required',
//                 'exists:blocks,id',
//             ],

//             'plotsize_id' => [
//                 'required',
//                 'exists:plotsizes,id',
//             ],
//         ]);


//         /*
//         |--------------------------------------------------------------------------
//         | Check Block belongs to selected Project
//         |--------------------------------------------------------------------------
//         */

//         $blockBelongsToProject = Block::where('id', $validated['block_id'])
//             ->where('project_id', $validated['project_id'])
//             ->exists();

//         if (! $blockBelongsToProject) {
//             return back()
//                 ->withErrors([
//                     'block_id' => 'The selected Block does not belong to the selected Project.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Check Plot Size belongs to selected Project
//         |--------------------------------------------------------------------------
//         */

//         $sizeBelongsToProject = Plotsize::where('id', $validated['plotsize_id'])
//             ->where('project_id', $validated['project_id'])
//             ->exists();

//         if (! $sizeBelongsToProject) {
//             return back()
//                 ->withErrors([
//                     'plotsize_id' => 'The selected Plot Size does not belong to the selected Project.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Check Duplicate Assignment
//         |--------------------------------------------------------------------------
//         */

//         $exists = PlotSizeAssignment::where('project_id', $validated['project_id'])
//             ->where('property_type_id', $validated['property_type_id'])
//             ->where('block_id', $validated['block_id'])
//             ->where('plotsize_id', $validated['plotsize_id'])
//             ->where('id', '!=', $plotSizeAssignment->id)
//             ->exists();

//         if ($exists) {
//             return back()
//                 ->withErrors([
//                     'plotsize_id' =>
//                         'This Plot Size is already assigned to the selected Project, Block and Property Type.',
//                 ])
//                 ->withInput();
//         }


//         /*
//         |--------------------------------------------------------------------------
//         | Update Assignment
//         |--------------------------------------------------------------------------
//         */

//         $plotSizeAssignment->update($validated);

//         return redirect()
//             ->route('plot-size-assignments.index')
//             ->with('success', 'Plot Size assignment updated successfully!');
//     }

//     /**
//     * Get Blocks by Project.
//     */
//     public function getBlocks($project_id)
//     {
//         $blocks = Block::where('project_id', $project_id)
//             ->orderBy('block_name')
//             ->get(['id', 'block_name']);

//         return response()->json($blocks);
//     }


//     /**
//      * Get Plot Sizes by Project + Block + Property Type.
//      */
//     public function getSizes(Request $request)
//     {
//         $request->validate([
//             'project_id' => ['required', 'exists:projects,id'],
//             'block_id' => ['required', 'exists:blocks,id'],
//             'property_type_id' => ['required', 'exists:property_types,id'],
//         ]);

//         $sizes = Plotsize::where('project_id', $request->project_id)
//             ->whereHas('plotSizeAssignments', function ($query) use ($request) {
//                 $query->where('block_id', $request->block_id)
//                     ->where('property_type_id', $request->property_type_id);
//             })
//             ->orderBy('title')
//             ->get(['id', 'title', 'size_area']);

//         return response()->json($sizes);
//     }


//     /**
//      * Delete an assignment.
//      */
//     public function destroy(PlotSizeAssignment $plotSizeAssignment)
//     {
//         $plotSizeAssignment->delete();

//         return redirect()
//             ->route('plot-size-assignments.index')
//             ->with('success', 'Plot Size assignment deleted successfully!');
//     }


// } -->