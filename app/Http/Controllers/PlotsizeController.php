<?php

namespace App\Http\Controllers;

use App\Models\Plotsize;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlotsizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $project_id = $request->project_id;

        $query = Plotsize::with('project');

        // 🔎 Apply filter (optional)
        if ($project_id) {
            $query->where('project_id', $project_id);
        }
 
        // 📌 Order by: First Project Name, then Block Name
        // $query->join('projects', 'projects.id', '=', 'plotsizes.project_id')
        //     ->orderBy('projects.project_name', 'ASC')
        //     ->orderBy('sizes.title', 'ASC')
        //     ->select('sizes.*'); // important to avoid issues in pagination

        $sizes = $query->paginate(10);
        $sizes->appends($request->all());

        $projects = Project::all();

        return view('sizes.index', compact('sizes', 'projects', 'project_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = DB::table('projects')
                ->get();
        // return $projects;

        return view('sizes.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            // Validate
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'size_area' => 'required',
                'title' => [
                    'required',
                    'string',
                    Rule::unique('plotsizes')->where(function ($query) use ($request) {
                        return $query->where('project_id', $request->project_id);
                    }),
                ],
                'remarks' => 'nullable|string|max:255',
            ], [
                // 👇 Custom error message
                'title.unique' => 'This Size already exists for the selected Project.',
            ]);
            // return $validatedData;

            // Create record
            Plotsize::create($validatedData);

            return redirect()->route('sizes.index')
                ->with('success', 'Plot Size created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors automatically redirect back with errors and old input
            throw $e;
        } catch (\Exception $e) {
            // return $e;
            // Agar koi aur duplicate DB level se aaye
            return back()
                ->withErrors(['error' => 'Size already exists for this project (duplicate entry).'])
                ->withInput();
        }        

    }

    /**
     * Display the specified resource.
     */
    public function show(Plotsize $plotsize)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plotsize $size)
    {
        return view('sizes.edit', compact('size'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plotsize $size)
    {
        $validatedData = $request->validate([
            'title' => [
                'required',
                'string',
                Rule::unique('plotsizes')
                    ->where(function ($query) use ($size) {
                        return $query->where('project_id', $size->project_id);
                    })
                    ->ignore($size->id),
            ],
            'size_area' => 'required|numeric',
            'remarks' => 'nullable|string|max:255',
        ], [
            'title.unique' => 'This Size already exists for the selected Project.',
        ]);

        $size->update($validatedData);

        return redirect()
            ->route('sizes.index')
            ->with('success', 'Plot Size updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($plotsize)
    {
        // $plotsize->delete();
        // return $plotsize;
        try {
            $size = Plotsize::findOrFail($plotsize);
            // return $size;
            $size->delete();

            // return redirect()->route('sizes.index')->with('success', 'Size deleted successfully!');
            return redirect()->back()->with('success', 'Size deleted successfully!');
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Size deleted successfully!'
            // ]);
        } catch (\Exception $e) {
            // return redirect()->route('sizes.index')->with('error', 'Unable to delete size.It might have related plots.');
            return redirect()->back()->with('error', 'Unable to delete size.It might have related plots.');

            // return response()->json([
            //     'status' => 'error',
            //     'message' => 'Unable to delete size. It might have related plots.'
            // ], 500);
        }
        // try {
        
        //     $size = Plotsize::findOrFail($plotsize);
        //     $size->delete();

        //     return response()->json([
        //         'status'=> 'success',
        //         'message' => 'Size deleted successfully',
        //     ]);

        // }catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unable to delete size. It might have related plots.'
        //     ], 500);
        // }

    }
    /**
     * Show deleted sizes
     */
    public function trash(Request $request)
        {
            $project_id = $request->project_id;

            $query = Plotsize::onlyTrashed()->with('project');

            // Filter by project
            if ($project_id) {
                $query->where('project_id', $project_id);
            }

            $sizes = $query->latest()->paginate(10);

            $projects = Project::all();

            return view('sizes.trash', compact('sizes', 'projects', 'project_id'));
        }

        /**
         * Restore deleted size
         */
        public function restore($id)
        {
            try {

                $size = Plotsize::onlyTrashed()->findOrFail($id);

                $size->restore();

                return redirect()
                    ->route('sizes.trash')
                    ->with('success', 'Deleted Size restored successfully!');

            } catch (\Exception $e) {

                return redirect()
                    ->back()
                    ->with('error', 'Unable to restore size.');

            }
        }
        /**
         * Permanently delete size
         */
        public function forceDelete($id)
        {
            try {

                $size = Plotsize::onlyTrashed()->findOrFail($id);

                // Check related plots
                if ($size->plots()->withTrashed()->count() > 0) {

                    return redirect()
                        ->back()
                        ->with('error',
                            'This size is already used in plots and cannot be permanently deleted.');

                }

                $size->forceDelete();

                return redirect()
                    ->route('sizes.trash')
                    ->with('success',
                        'Size permanently deleted successfully!');

            } catch (\Exception $e) {

                return redirect()
                    ->back()
                    ->with('error',
                        'Unable to permanently delete size.');

            }
        }
        // public function forceDelete($id)
        // {
        //     try {

        //         $size = Plotsize::onlyTrashed()->findOrFail($id);

        //         $size->forceDelete();

        //         return redirect()
        //             ->route('sizes.trash')
        //             ->with('success', 'Size permanently deleted successfully!');

        //     } catch (\Exception $e) {

        //         return redirect()
        //             ->back()
        //             ->with('error', 'Unable to permanently delete size.');

        //     }
        // }

}
