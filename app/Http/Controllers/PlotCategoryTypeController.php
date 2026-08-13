<?php

namespace App\Http\Controllers;

use App\Models\PlotCategoryType;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlotCategoryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index(Request $request)
    {
        
        // $project_id = $request->project_id;

        // $query = PlotCategoryType::with('project');

        // 🔎 Apply filter (optional)
        // if ($project_id) {
        //     $query->where('project_id', $project_id);
        // }
 
        // 📌 Order by: First Project Name, then Block Name
        // $query->join('projects', 'projects.id', '=', 'plotsizes.project_id')
        //     ->orderBy('projects.project_name', 'ASC')
        //     ->orderBy('sizes.title', 'ASC')
        //     ->select('sizes.*'); // important to avoid issues in pagination

        // $categories = $query->paginate(10);
        $categories = PlotCategoryType::latest()->paginate(10);
        // $categories->appends($request->all());

        // $projects = Project::all();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = DB::table('projects')
                ->get();
        // return $projects;

        return view('categories.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate
            $validatedData = $request->validate([
                // 'project_id' => 'required|exists:projects,id',
                // 'size_area' => 'required',
                'category_title' => 'required|unique:projects,project_name',
                // 'category_title' => [
                //     'required',
                //     'string',
                //     Rule::unique('categorytype')->where(function ($query) use ($request) {
                //         return $query->where('project_id', $request->project_id);
                //     }),
                // ],
                'remarks' => 'nullable|string|max:255',
            ], [
                // 👇 Custom error message
                'category_title.unique' => 'This Size already exists.',
            ]);
            // return $validatedData;

            // Create record
            PlotCategoryType::create($validatedData);

            return redirect()->route('categories.index')
                ->with('success', 'Plot Category created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors automatically redirect back with errors and old input
            throw $e;
        } catch (\Exception $e) {
            // return $e;
            // Agar koi aur duplicate DB level se aaye
            return back()
                ->withErrors(['error' => 'Category already exists for this project (duplicate entry).'])
                ->withInput();
        }      
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
    // EDIT FORM
    public function edit($id)
    {
        $category = PlotCategoryType::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_title' => 'required|string|max:255|unique:plot_category_types,category_title,' . $id,
            'remarks'        => 'nullable|string',
        ]);

        $category = PlotCategoryType::findOrFail($id);

        $category->update([
            'category_title' => $request->category_title,
            'remarks'        => $request->remarks,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

   
    public function destroy($id)
    {
    //     $id->delete();
    //     return back()->with('success', 'Category deleted successfully!');

            try {
            $plotcategory = PlotCategoryType::findOrFail($id);

            $plotcategory->delete();

            return redirect()->back()->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to delete size.It might have related plots.');
        }
    }
}
