<?php

namespace App\Http\Controllers;

use App\Models\PropertyTypeAssignment;
use App\Models\Project;
use App\Models\Block;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeAssignmentController extends Controller
{
    // public function index()
    // {
    //     $assignments = PropertyTypeAssignment::with([
    //         'project',
    //         'block',
    //         'propertyType',
    //     ])
    //     ->latest()
    //     ->paginate(10);

    //     return view(
    //         'property-type-assignments.index',
    //         compact('assignments')
    //     );
    // }

    public function index(Request $request)
    {
        $projects = Project::orderBy('project_name')->get();

        $propertyTypes = PropertyType::orderBy('name')->get();

        $blocks = collect();

        if ($request->filled('project_id')) {
            $blocks = Block::where('project_id', $request->project_id)
                ->orderBy('block_name')
                ->get();
        }

        $query = PropertyTypeAssignment::with([
            'project',
            'block',
            'propertyType',
        ]);

        // Project Filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Block Filter
        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Property Type Filter
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        $assignments = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'property-type-assignments.index',
            compact(
                'assignments',
                'projects',
                'blocks',
                'propertyTypes'
            )
        );
    }



    public function create()
    {
        $projects = Project::orderBy('project_name')->get();
        $propertyTypes = PropertyType::orderBy('name')->get();

        return view(
            'property-type-assignments.create',
            compact('projects', 'propertyTypes')
        );
    }


    public function getBlocks($project_id)
    {
        $blocks = Block::where('project_id', $project_id)
            ->orderBy('block_name')
            ->get(['id', 'block_name']);

        return response()->json($blocks);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'block_id' => ['required', 'exists:blocks,id'],
            'property_type_id' => ['required', 'exists:property_types,id'],
        ]);


        // Check that selected Block belongs to selected Project
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


        // Prevent duplicate assignment
        $exists = PropertyTypeAssignment::where(
            'project_id',
            $validated['project_id']
        )
        ->where('block_id', $validated['block_id'])
        ->where('property_type_id', $validated['property_type_id'])
        ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'property_type_id' =>
                        'This Property Type is already assigned to the selected Project and Block.',
                ])
                ->withInput();
        }


        PropertyTypeAssignment::create($validated);

        return redirect()
            ->route('property-type-assignments.index')
            ->with('success', 'Property Type assigned successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(PropertyTypeAssignment $propertyTypeAssignment)
    {
        $projects = Project::orderBy('project_name')->get();

        $propertyTypes = PropertyType::orderBy('name')->get();

        // Load blocks of currently selected project
        $blocks = Block::where(
            'project_id',
            $propertyTypeAssignment->project_id
        )
        ->orderBy('block_name')
        ->get();

        return view(
            'property-type-assignments.edit',
            compact(
                'propertyTypeAssignment',
                'projects',
                'blocks',
                'propertyTypes'
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
        PropertyTypeAssignment $propertyTypeAssignment
    ) {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'block_id' => ['required', 'exists:blocks,id'],
            'property_type_id' => ['required', 'exists:property_types,id'],
        ]);


        // Check that selected Block belongs to selected Project
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


        // Check duplicate assignment
        // Ignore the current record being edited
        $exists = PropertyTypeAssignment::where(
            'project_id',
            $validated['project_id']
        )
        ->where('block_id', $validated['block_id'])
        ->where('property_type_id', $validated['property_type_id'])
        ->where('id', '!=', $propertyTypeAssignment->id)
        ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'property_type_id' =>
                        'This Property Type is already assigned to the selected Project and Block.',
                ])
                ->withInput();
        }


        $propertyTypeAssignment->update($validated);

        return redirect()
            ->route('property-type-assignments.index')
            ->with('success', 'Property Type assignment updated successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(PropertyTypeAssignment $propertyTypeAssignment)
    {
        $propertyTypeAssignment->delete();

        return redirect()
            ->route('property-type-assignments.index')
            ->with('success', 'Property Type assignment deleted successfully.');
    }
}
