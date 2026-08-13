<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Block;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// for exports into excel
use App\Exports\ProjectBlocksExport;
use Maatwebsite\Excel\Facades\Excel;


class BlockController extends Controller
{
    public function exportProjectBlocksExcel(Project $project)
    {
        return Excel::download(
            new ProjectBlocksExport($project->id),
            'project_'.$project->id.'_blocks.xlsx'
        );
    }
    
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request, Project $project = null)
    {

        // Project id GET se ya route se
        // case-1 (is mn agr value 0, '' ya koi b string hoto usko ye valid id manyga)
        // $project_id = $request->project_id ?? $project?->id;
        // case-2 (is mn agr value 0, '' ya koi b string hoto usko ye valid nae manta)
        $project_id = $request->project_id ?: $project?->id;

        $query = Block::with('project');
        // for future check for advance filter (next 3 lines)
        // $query->when($request->block_name, function ($q) use ($request) {
        //     $q->where('block_name', 'like', '%' . $request->block_name . '%');
        // });
        if ($project_id) {
            $query->where('project_id', $project_id);
        }

        $query->join('projects', 'projects.id', '=', 'blocks.project_id')
            ->orderBy('projects.project_name')
            ->orderBy('blocks.block_name')
            ->select('blocks.*');

        $blocks = $query->paginate(10);
        $blocks->appends($request->all());

        $projects = Project::orderBy('project_name')->get();

        return view('blocks.index', compact(
            'blocks',
            'projects',
            'project_id',
            'project'
        ));
    }

// old index without filter lock
    // public function index(Request $request)
    // {
    //     $project_id = $request->project_id;

    //     $query = Block::with('project');

    //     // 🔎 Apply filter (optional)
    //     if ($project_id) {
    //         $query->where('project_id', $project_id);
    //     }

    //     // 📌 Order by: First Project Name, then Block Name
    //     $query->join('projects', 'projects.id', '=', 'blocks.project_id')
    //         ->orderBy('projects.project_name', 'ASC')
    //         ->orderBy('blocks.block_name', 'ASC')
    //         ->select('blocks.*'); // important to avoid issues in pagination

    //     $blocks = $query->paginate(10);
    //     $blocks->appends($request->all());

    //     $projects = Project::all();

    //     return view('blocks.index', compact('blocks', 'projects', 'project_id'));
    // }

    // public function index(Request $request)
    // {
    //     $project_id = $request->project_id; // filter value

    //     // Base Query
    //     $query = Block::with('project')->latest();

    //     // If filter applied
    //     if ($project_id) {
    //         $query->where('project_id', $project_id);
    //     }

    //     $blocks = $query->paginate(10);

    //     // For filter dropdown
    //     $projects = Project::all();

    //     // Preserve filter in pagination
    //     $blocks->appends($request->all());

    //     return view('blocks.index', compact('blocks', 'projects', 'project_id'));
    // }

    // old index method -- start --
    // public function index()
    // {
    //     $blocks = Block::with('project')->latest()->paginate(10);
    //     $projects = Project::all();

    //     // return $blocks;

    //     return view('blocks.index', compact(['blocks', 'projects']));

    // }

    /**
     * Show the form for creating a new resource.
     */

    public function createpsproject(Request $request, $project_id = null)
{
        $projects = DB::table('projects')->get();

        return view('blocks.createpsproject', [
            'projects' => $projects,
            'selected_project_id' => $project_id
        ]);
    }


    public function create()
    {
        $projects = DB::table('projects')
                    ->get();
            // return $projects;

        return view('blocks.create', compact('projects'));
    }

    // for preselect project name ---
    //     public function createpsproject(string $pname)
    // {
    //     $projects = DB::table('projects')
    //                 ->get();
    //         // return $projects;
    //     $projectname = $pname;

    //     return view('blocks.create', compact('projects', 'projectname'));
    // }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            // Validate
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'block_name' => [
                    'required',
                    'string',
                    Rule::unique('blocks')->where(function ($query) use ($request) {
                        return $query->where('project_id', $request->project_id);
                    }),
                ],
                'remarks' => 'nullable|string|max:255',
            ], [
                // 👇 Custom error message
                'block_name.unique' => 'This Block already exists for the selected Project.',
            ]);

            // Create record
            Block::create($validatedData);

            return redirect()->route('blocks.index')
                ->with('success', 'Block created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors automatically redirect back with errors and old input
            throw $e;
        } catch (\Exception $e) {
            // Agar koi aur duplicate DB level se aaye
            return back()
                ->withErrors(['error' => 'Block already exists for this project (duplicate entry).'])
                ->withInput();
        }
    }


    // public function store(Request $request)
    // {
    //     // Validation
    //     $validatedData = $request->validate([
    //         'project_id' => 'required|exists:projects,id',
    //         'block_name' => [
    //             'required',
    //             'string',
    //             // custom rule to ensure unique combination
    //             Rule::unique('blocks')->where(function ($query) use ($request) {
    //                 return $query->where('project_id', $request->project_id);
    //             }),
    //         ],
    //         'remarks' => 'nullable|string|max:255',
    //     ], [
    //         // 👇 Custom error message
    //         'block_name.unique' => 'This Block already exists for the selected Project.',
    //     ]);

    //     // Create record
    //     $block = new Block();
    //     $block->project_id = $validatedData['project_id'];
    //     $block->block_name = $validatedData['block_name'];
    //     $block->remarks = $validatedData['remarks'] ?? null;
    //     $block->save();

    //     // Redirect with success message
    //     return redirect()->route('blocks.index')->with('success', 'Block created successfully.');
    // }


    // old store method with only block unique
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'project_id' => 'required|exists:projects,id',
    //         'block_name' => 'required|string|max:255|unique:blocks,block_name',
    //         'remarks' => 'nullable|string',
    //     ]);

    //     $block = Block::create($validated);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Block added successfully!',
    //         'data' => $block
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
    public function edit(Block $block)
    {
        $projects = Project::orderBy('project_name')->get();

        return view('blocks.edit', compact('block', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Block $block)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'block_name' => 'required|string|max:255',
            'remarks'    => 'nullable|string',
        ]);

        $block->update([
            'project_id' => $request->project_id,
            'block_name' => $request->block_name,
            'remarks'    => $request->remarks,
        ]);

        return redirect()
            ->route('blocks.index')
            ->with('success', 'Block updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // try {
            $block = Block::findOrFail($id);
            $block->delete();

            return back()->with('success', 'Block deleted successfully!');
// old json response
        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'Block deleted successfully!'
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unable to delete block. It might have related streets or plots.'
        //     ], 500);
        // }
    }
    // new function manual
    Public function addBlock()
    {
        $projects = Project::all();
        return view('addblock',compact('projects'));
    }
    
    public function saveBlock(Request $request)
    {
        $request->validate([
            'postproject' => 'required|exists:projects,id',
            'postblock' => 'required|unique:ggblocks,block_name',
            'postremarks' => 'nullable|string',
        ]);

        Block::create([
            'project_id' => $request->postproject,
            'block_name' => $request->postblock,
            'remarks' => $request->postremarks,
        ]);
        //  return response()->json([
        //     'status' => 'success',
        //     'message' => 'Block added successfully!',
        //     'data' => $block
        // ]);

        // return redirect()->back()->with('success', 'Block added successfully!');
    }
 
    public function showBlocks(){
        $blocks = DB::table('blocks')
                    ->join('projects','blocks.project_id','=','projects.id')
                    ->select('blocks.*','projects.project_name')
                    ->get();
        return view('pages.ghome', compact('blocks'));
        // return view('test',compact('grblock'));
    }

    public function projectwise($id)
    {
        $project = Project::findOrFail($id);

        $blocks = Block::where('project_id', $id)
                    ->orderBy('block_name', 'ASC')
                    ->get();

        return view('blocks.block', [
            'blocks' => $blocks,
            'project_name' => $project->project_name,
            'project_id' => $id
        ]);
    }

    public function getBlocksByProject($project_id)
    {
        $blocks = Block::where('project_id', $project_id)->get();

        return response()->json($blocks);
    }




    // public function projectwise($id)
    // {
    //     $project = Project::findOrFail($id);

    //     $blocks = Block::where('project_id', $id)
    //                 ->orderBy('block_name', 'ASC')
    //                 ->get();

    //     return view('blocks.block', [
    //         'blocks' => $blocks,
    //         'project_name' => $project->project_name,
    //         'project_id' => $id   // ← ADD THIS
    //     ]);
    // }


    // public function projectwise(string $proid){
    //     $blocks = DB::table('blocks')
    //                 ->where('project_id', $proid)
    //                 // ->paginate(10)
    //                 ->get();
    //     // $projects = Project::all();
    //     $project = DB::table('projects')
    //                 ->where('id', $proid)
    //                 ->first();

    //     $project_name = $project->project_name ?? 'Unknown Project';

    //     return view('blocks.block', compact('blocks','project_name','proid'));
    // }
}
