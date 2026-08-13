<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Street;
use App\Models\Project;
use App\Models\Block;
// use App\Http\Controllers\Rule;
use Illuminate\Validation\Rule;

class StreetController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request, Block $block = null)
    {
        // $project_id = $request->project_id;
        // $block_id   = $request->block_id ?? $block?->id;

        $block_id = $request->block_id ?? $block?->id;

        $project_id = $request->project_id ?? $block?->project_id;
        
        
        $streets = Street::with(['project','block'])
            ->when($project_id, function ($q) use ($project_id) {
                $q->where('project_id', $project_id);
            })
            ->when($block_id, function ($q) use ($block_id) {
                $q->where('block_id', $block_id);
            })
            ->latest()
            ->paginate(15);

        // $streetOptions = Street::when($block_id, function ($q) use ($block_id) {
        //     $q->where('block_id', $block_id);
        // })
        // ->orderBy('street_name')
        // ->get();

        $projects = Project::orderBy('project_name')->get();

        // $blocks = Block::when($project_id, function ($q) use ($project_id) {
        //     $q->where('project_id', $project_id);
        // })
        // ->orderBy('block_name')
        // ->get();
        $blocks = Block::when($project_id, function ($q) use ($project_id) {
                $q->where('project_id', $project_id);
            })
            ->orderBy('block_name')
            ->get();

        return view('streets.index', compact(
            'streets',
            'projects',
            'blocks',
            // 'streetOptions',
            'project_id',
            'block_id',
            'block'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $blocks = Block::all();
        // $streets = Street::all();
        return view('streets.create', compact('projects', 'blocks'));

    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(Request $request)
    
    {
        
        $rules = [
            'project_id' => 'required|integer|exists:projects,id',
            'block_id'   => 'required|integer|exists:blocks,id',
            'street_name' => [
                'required',
                Rule::unique('streets')->where(function ($query) use ($request) {
                    return $query
                        ->where('project_id', $request->project_id)
                        ->where('block_id', $request->block_id);
                }),
            ],
            // 'numbering_type' => 'required|in:blockwise,streetwise',
            'remarks' => 'nullable|string',
        ];

        // ✔ Validation only once
        $validatedData = $request->validate($rules);

        // ✔ Save data
        Street::create($validatedData);

        return redirect()
            ->route('streets.index')
            ->with('success', 'Street saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Street $street)
    {
        $projects = Project::orderBy('project_name')->get();

        // Sirf us project ke blocks jahan ye street hai
        $blocks = Block::where('project_id', $street->project_id)
                        ->orderBy('block_name')
                        ->get();

        return view('streets.edit', compact('street', 'projects', 'blocks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Street $street)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'block_id'   => 'required|exists:blocks,id',
            'street_name' => 'required|string|max:255',
            'remarks'    => 'nullable|string',
        ]);

        $street->update([
            'project_id' => $request->project_id,
            'block_id'   => $request->block_id,
            'street_name' => $request->street_name,
            'remarks'    => $request->remarks,
        ]);

        return redirect()
            ->route('streets.index')
            ->with('success', 'Street updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
        public function destroy(Street $street)
    {
        $street->delete();
        return back()->with('success', 'Street deleted successfully!');
    }

}
