<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
 
class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }
 
    /**
     * Store a newly created resource in storage.
     */
    // new sotre method without ajax
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|unique:projects,project_name',
            'project_remarks' => 'nullable|string',
        ]);

        Project::create([
            'project_name' => $request->project_name,
            'project_remarks' => $request->project_remarks,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
       
    }

    // old store method for ajax
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'project_name' => 'required|unique:projects,project_name',
    //         'remarks' => 'nullable|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    //     }

    //     $project = Project::create([
    //         'project_name' => $request->project_name,
    //         'remarks' => $request->remarks,
    //     ]);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Project created successfully!',
    //         'project' => $project
    //     ]);
    // }

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
    public function edit(Project $project)
    {
        // $project = Project::All()->findOrFail($id); // chatGPT says not good prictice 
        // $project = Project::findOrFail($id);

        return view('projects.edit', compact('project')); 
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            // 'code' => 'nullable|string|max:100',
            // 'location' => 'nullable|string|max:255',
            'project_remarks' => 'nullable|string',
            // 'status' => 'required|in:active,inactive',
        ]);

        $project->update($request->all());

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return back()->with('success', 'Project deleted successfully!');
        
        // $project->delete();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Project deleted successfully!'
        // ]);
    }
}
