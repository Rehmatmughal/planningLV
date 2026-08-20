<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plot;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\DevelopmentStatus;
use App\Models\LopStatus;
use App\Models\MortgageStatus;
use App\Models\PossessionStatus;
use App\Models\AreaVariation;
use App\Models\PlotSize;
use App\Models\PlotCategoryType;
use App\Models\PlotCoordinate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
 
// for excel exports
use App\Exports\BlockPlotsExport;
use Maatwebsite\Excel\Facades\Excel;
 

class PlotController extends Controller
{
    // for export excel 
    public function exportBlockPlots(Request $request, Block $block)
    {
        return Excel::download(
            new BlockPlotsExport($block, $request),
            'block_' . $block->block_name . '_plots.xlsx'
        );
    }
 
    public function show($id)
    {
        $plot = Plot::with([
            'project',
            'block',
            'street',
            'size',
            'category',
            'lopStatus',
            'developmentStatus',
            'mortgageStatus',
            'coordinates',
        ])->findOrFail($id);

        // Area variation history (latest first)
        $areaVariations = AreaVariation::where('plot_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return view('plots.show', compact('plot', 'areaVariations'));
    }
    // plot detail print 
    public function print($id)
    {
        $plot = Plot::with([
            'project',
            'block',
            'street',
            'size',
            'category',
            'lopStatus',
            'developmentStatus',
            'areaVariations' // full history
        ])->findOrFail($id);

        return view('plots.print', compact('plot'));
    }

    // new method from chatgpt -- start --
    /**
     * --- GET LOP STATUS ---
     */
    public function getLop(Plot $plot)
    {
        return response()->json([
            'lop_status' => $plot->lopStatus->lop_status ?? null,
            'remarks'    => $plot->lopStatus->remarks ?? null,
        ]);
    }


    /**
     * --- UPDATE / CREATE LOP STATUS ---
     */
    public function updateLop(Request $r, Plot $plot)
    {
        $r->validate([
            'lop_status' => 'required|in:lop,non_lop,mortgaged',
            'remarks'    => 'nullable|string',
        ]);

        $saved = LopStatus::updateOrCreate(
            ['plot_id' => $plot->id],
            [
                'lop_status' => $r->lop_status,
                'remarks'    => $r->remarks,
            ]
        );

        return response()->json(['lop_status' => $saved->lop_status]);
    }


    /**
     * --- GET DEVELOPMENT STATUS ---
     */
    public function getDevelopment(Plot $plot)
    {
        return response()->json([
            'asphalt_tst'    => $plot->developmentStatus->asphalt_tst ?? null,
            'sewer_manholes' => $plot->developmentStatus->sewer_manholes ?? null,
            'remarks'        => $plot->developmentStatus->remarks ?? null,
        ]);
    }


    /**
     * --- UPDATE DEVELOPMENT STATUS ---
     */
    public function updateDevelopment(Request $r, Plot $plot)
    {
        // return $r;
        $r->validate([
            // 'asphalt_tst'    => 'required|in:complete,not_complete',
            // 'sewer_manholes' => 'required|in:complete,not_complete',
            'asphalt_tst'    => 'required|in:complete,not_complete',
            'sewer_manholes' => 'required|in:complete,not_complete',
            'remarks'        => 'nullable|string',
        ]);

        // Frontend → Database Mapping
        // Example: complete → yes, not_complete → no
        // old maping
        // Road mapping (frontend → DB)
        // new mapping
        $roadMap = [
            'complete'      => 'yes',
            'not_complete'  => 'no'
        ];

        // Sewer mapping (frontend → DB)
        $sewerMap = [
            'complete'      => 'constructed',
            'not_complete'  => 'not_constructed'
        ];

       
        // $sewerMap = [
        //     'constructed'      => 'constructed',
        //     'not_constructed'  => 'not_constructed'
        // ];


        // $map = [
        //     'complete' => 'yes',
        //     'not_complete' => 'no'
        // ];

        $saved = DevelopmentStatus::updateOrCreate(
            ['plot_id' => $plot->id],
            [
                'asphalt_tst'    => $roadMap[$r->asphalt_tst],
                'sewer_manholes' => $sewerMap[$r->sewer_manholes],
                // 'asphalt_tst'    => $map[$r->asphalt_tst],
                // 'sewer_manholes' => $map[$r->sewer_manholes],
                'remarks'        => $r->remarks,
            ]
        );
        // return $saved;
        
        return response()->json([
            'asphalt_tst'    => $saved->asphalt_tst,
            'sewer_manholes' => $saved->sewer_manholes,
            'remarks'        => $saved->remarks,
        ]);
    }

    // new method from chatgpt -- END --

    // 🔹 Index (List + Filters)
    // new filter index with plot, street and other ---
    public function index(Request $request)
    {
        $query = Plot::with([
            'project',
            'block',
            'street',
            // 'plotSize'
            'size',
            'category',
            'lopStatus',
            'developmentStatus',
            'latestAreavariation'
        ]);

        // 🔹 Project
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // 🔹 Block
        if ($request->block_id) {
            $query->where('block_id', $request->block_id);
        }

        // 🔹 Street
        if ($request->street_id) {
            $query->where('street_id', $request->street_id);
        }

        // 🔹 Size
        if ($request->size_id) {
            $query->where('size_id', $request->size_id);
        }

        // 🔹 Plot No
        if ($request->plot_no) {
            // $query->where('plot_number', 'LIKE', '%' . $request->plot_no . '%');
            $query->where('plot_number', $request->plot_no);
        }

        // 🔹 Universal Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('plot_number', 'LIKE', "%$search%")
                ->orWhereHas('block', fn($b) => $b->where('block_name', 'LIKE', "%$search%"))
                ->orWhereHas('street', fn($s) => $s->where('street_name', 'LIKE', "%$search%"))
                // ->orWhereHas('plotSize', fn($ps) => $ps->where('title', 'LIKE', "%$search%"))
                ->orWhereHas('size', fn($ps) => $ps->where('title', 'LIKE', "%$search%"))
                ->orWhereHas('category', fn($c) => $c->where('category_title', 'LIKE', "%$search%"));
            });
        }

        $plots = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();
        // $plots = $query->orderBy('id', 'desc')->simplePaginate(5)->withQueryString();        

        // for dynamic heading
        $currentStreet = null;
        if ($request->street_id) {
            $currentStreet = Street::with([
                'block',
                'project'
            ])->find($request->street_id);
        }
        return view('plots.index', [
            'plots' => $plots,
            'projects' => Project::all(),
            'blocks' => Block::all(),
            'streets' => Street::all(),
            'sizes' => PlotSize::all(),
            'currentStreet' => $currentStreet,
        ]);
    }


    // old filter with 2 items   
    // public function index(Request $request)
    // {
    //     $projects = Project::all();
    //     $sizes = PlotSize::all();
    //     $size_id = $request->size_id;

    //     // Filters
    //     $project_id = $request->input('project_id');
    //     $block_id = $request->input('block_id');
    //     $street_id = $request->input('street_id');

    //     // Base query
    //     // $query = Plot::with(['project', 'block', 'street']);
    //     // new query with plot, size etc
    //     // $query = Plot::with(['project', 'block', 'street', 'plotSize', 'category', 'lopStatus', 
    //     //                 'developmentStatus', 'latestAreavariation'
    //     // ]);
    //     // old query
    //     $query = Plot::with(['project', 'block', 'street', 
    //         'developmentStatus', 'lopStatus', 'mortgageStatus', 'possessionStatus','plotsize'
    //     ]);


    //     if ($project_id) $query->where('project_id', $project_id);
    //     if ($block_id) $query->where('block_id', $block_id);
    //     if ($street_id) $query->where('street_id', $street_id);

    //     // $plots = $query->latest()->paginate(10);
    //     $plots = $query->latest()->paginate(10);

    //     // Optional lists for filters
    //     $blocks = $project_id ? Block::where('project_id', $project_id)->get() : collect();
    //     $streets = $block_id ? Street::where('block_id', $block_id)->get() : collect();
    //     // for plot size
    //     if ($size_id) {
    //         $query->where('size_id', $size_id);
    //     }

    //     return view('plots.index', compact('plots', 'projects', 'blocks', 'streets', 'project_id', 'block_id', 'street_id','sizes'));
    // }

    // filter controller function
    public function filter(Request $request)
    {
        $query = Plot::with(['project', 'block', 'street', 'size']);

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->block_id) {
            $query->where('block_id', $request->block_id);
        }

        if ($request->street_id) {
            $query->where('street_id', $request->street_id);
        }

        if ($request->size_id) {
            $query->where('size_id', $request->size_id);
        }

        if ($request->lop_status) {
            $query->where('lop_status', $request->lop_status);
        }

        if ($request->development_status) {
            $query->where('development_status', $request->development_status);
        }

        $plots = $query->get();

        return view('plots.partials.table-data', compact('plots'))->render();
    }

    // ➕ Create Form
    public function create()
    {
        $projects = Project::all();
        $blocks = Block::all();
        $streets = Street::all();
        $sizes = Plotsize::all();
        $categories = PlotCategoryType::all();

        return view('plots.create', compact('projects', 'blocks', 'streets','sizes','categories'));
    }

    // ✅ Store New Plot -- NEW TRY START
    public function store(Request $request)
    {
        
        // Common validation rules
        $rules = [
            'numbering_type' => 'required|in:blockwise,streetwise',
            'project_id' => 'required|integer|exists:projects,id',
            'block_id' => 'required|integer|exists:blocks,id',
            'plot_number' => 'required|string',
            'street_id' => 'nullable|exists:streets,id',
            'size_id' => 'required',
            'category_id' => 'required|integer|exists:plot_category_types,id',
            'remarks' => 'nullable|string',
            ];

        // Conditional validation
        if ($request->numbering_type === 'blockwise') {
            $rules['block_id'] = 'required|integer';
            // 3 columns unique check
            $rules['plot_number'] .= '|unique:plots,plot_number,NULL,id,project_id,' . $request->project_id . ',block_id,' . $request->block_id;
        }

        if ($request->numbering_type === 'streetwise') {
            $rules['street_id'] = 'required|integer';
            // 4 columns unique check
            $rules['plot_number'] .= '|unique:plots,plot_number,NULL,id,project_id,' . $request->project_id . ',block_id,' . $request->block_id . ',street_id,' . $request->street_id;
        }

        $validatedData = $request->validate($rules);

        // return $validatedData;

        // save data
        $plot = Plot::create($validatedData);

        // return redirect()->back()->with('success', 'Plot saved successfully!');
        return redirect()->route('plots.index')->with('success', 'Plot saved successfully!');

    }

    // // ✅ AJAX: Edit (Fetch plot data)
    // public function edit($id)
    // {
    //     $plot = Plot::with(['project', 'block', 'street'])->findOrFail($id);

    //     return response()->json([
    //         'id' => $plot->id,
    //         'project_id' => $plot->project_id,
    //         'project_name' => $plot->project ? $plot->project->project_name : '',
    //         'block_id' => $plot->block_id,
    //         'block' => $plot->block ? ['block_name' => $plot->block->block_name] : null,
    //         'street_id' => $plot->street_id,
    //         'street' => $plot->street ? ['street_name' => $plot->street->street_name] : null,
    //         'plot_number' => $plot->plot_number,
    //         'size' => $plot->size,
    //         'remarks' => $plot->remarks,
    //         'numbering_type' => $plot->numbering_type,
    //     ]);
    // }

    // // ✅ AJAX: Update Plot
    // public function update(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'project_id' => 'required|exists:projects,id',
    //         'block_id' => 'required|exists:blocks,id',
    //         'plot_number' => 'required',
    //         'numbering_type' => 'required|in:blockwise,streetwise',
    //         'street_id' => 'nullable|exists:streets,id',
    //         'size' => 'nullable|string',
    //         'remarks' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $plot = Plot::findOrFail($id);

    //     // ✅ Unique check
    //     $exists = Plot::where('project_id', $request->project_id)
    //         ->where('block_id', $request->block_id)
    //         ->where('plot_number', $request->plot_number)
    //         ->when($request->numbering_type === 'streetwise', function ($q) use ($request) {
    //             $q->where('street_id', $request->street_id);
    //         })
    //         ->when($request->numbering_type === 'blockwise', function ($q) {
    //             $q->whereNull('street_id');
    //         })
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($exists) {
    //         return response()->json([
    //             'errors' => ['plot_number' => ['This plot number already exists for this block/street.']],
    //         ], 422);
    //     }

    //     $plot->update([
    //         'project_id' => $request->project_id,
    //         'block_id' => $request->block_id,
    //         'street_id' => $request->street_id,
    //         'plot_number' => $request->plot_number,
    //         'size' => $request->size,
    //         'remarks' => $request->remarks,
    //         'numbering_type' => $request->numbering_type,
    //     ]);

    //     return response()->json(['success' => true, 'message' => 'Plot updated successfully!']);
    // }
    // -- EDIT method with without model binding -- resource route
    // public function edit($id)
    // {
    //     $plot = Plot::with(['project', 'block', 'street', 'size'])->findOrFail($id);

    //     $projects = Project::orderBy('project_name')->get();
    //     $blocks   = Block::where('project_id', $plot->project_id)->orderBy('block_name')->get();
    //     $streets  = Street::where('project_id', $plot->project_id)->orderBy('street_name')->get();
    //     $sizes    = PlotSize::where('project_id', $plot->project_id)->orderBy('title')->get();
    //     $categories    = PlotCategoryType::orderBy('category_title')->get();

    //     return view('plots.edit', compact(
    //         'plot',
    //         'projects',
    //         'blocks',
    //         'streets',
    //         'sizes',
    //         'categories'
    //     ));
    // }
    public function edit(Plot $plot)
    {
        $plot->load(['project', 'block', 'street', 'size']);

        $projects = Project::orderBy('project_name')->get();
        $blocks   = Block::where('project_id', $plot->project_id)->orderBy('block_name')->get();
        $streets  = Street::where('project_id', $plot->project_id)->orderBy('street_name')->get();
        $sizes    = PlotSize::where('project_id', $plot->project_id)->orderBy('title')->get();
        $categories = PlotCategoryType::orderBy('category_title')->get();

        return view('plots.edit', compact(
            'plot',
            'projects',
            'blocks',
            'streets',
            'sizes',
            'categories'
        ));
    }
    public function update(Request $request, Plot $plot)
    {
        $rules = [
            'numbering_type' => 'required|in:blockwise,streetwise',
            'project_id'     => 'required|exists:projects,id',
            'block_id'       => 'required|exists:blocks,id',
            'street_id'      => 'required|exists:streets,id', // ALWAYS REQUIRED
            'plot_number'    => 'required|string',
            'size_id'        => 'required|exists:plotsizes,id',
            'category_id'    => 'required|exists:plot_category_types,id',
            'remarks'        => 'nullable|string',
        ];

        // UNIQUE LOGIC (same as store but IGNORE current plot)
        if ($request->numbering_type === 'blockwise') {

            $rules['plot_number'] .= '|unique:plots,plot_number,' . $plot->id .
                ',id,project_id,' . $request->project_id .
                ',block_id,' . $request->block_id;

        } else { // streetwise

            $rules['plot_number'] .= '|unique:plots,plot_number,' . $plot->id .
                ',id,project_id,' . $request->project_id .
                ',block_id,' . $request->block_id .
                ',street_id,' . $request->street_id;
        }

        $validated = $request->validate($rules);

        $plot->update($validated);

        return redirect()->route('plots.index')
            ->with('success', 'Plot updated successfully!');
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'project_id'     => 'required|exists:projects,id',
    //         'block_id'       => 'required|exists:blocks,id',
    //         'plot_number'    => 'required|string',
    //         'numbering_type' => 'required|in:blockwise,streetwise',
    //         'street_id'      => 'required|exists:streets,id',
    //         'size_id'        => 'nullable|exists:plotsizes,id',
    //         'category_id'    => 'required|exists:plot_category_types,id',
    //         'remarks'        => 'nullable|string',
    //     ]);

    //     $plot = Plot::findOrFail($id);

    //     // 🔒 Unique plot check
    //     $exists = Plot::where('project_id', $request->project_id)
    //         ->where('block_id', $request->block_id)
    //         ->where('plot_number', $request->plot_number)
    //         ->when($request->numbering_type === 'streetwise', function ($q) use ($request) {
    //             $q->where('street_id', $request->street_id);
    //         })
    //         ->when($request->numbering_type === 'blockwise', function ($q) {
    //             $q->whereNull('street_id');
    //         })
    //         ->where('id', '!=', $plot->id)
    //         ->exists();

    //     if ($exists) {
    //         return back()
    //             ->withInput()
    //             ->withErrors(['plot_number' => 'This plot number already exists for this block/street.']);
    //     }

    //     $plot->update([
    //         'project_id'     => $request->project_id,
    //         'block_id'       => $request->block_id,
    //         'street_id'      => $request->numbering_type === 'streetwise'
    //                             ? $request->street_id
    //                             : null,
    //         'plot_number'    => $request->plot_number,
    //         'numbering_type' => $request->numbering_type,
    //         'size_id'        => $request->size_id,   // ✅ FIX
    //         'remarks'        => $request->remarks,
    //     ]);

    //     return redirect()
    //         ->route('plots.index')
    //         ->with('success', 'Plot updated successfully.');
    // }



    // 🗑️ Delete plot
    public function destroy(Plot $plot)
    {
        $plot->delete();
        return back()->with('success', 'Plot deleted successfully!');
    }

    // 🔹 AJAX: Get blocks by project
    public function getBlocks($project_id)
    {
        $blocks = Block::where('project_id', $project_id)->get(['id', 'block_name']);
        return response()->json($blocks);
    }

    // 🔹 AJAX: Get sizes by project
    public function getSizes($project_id)
    {
        $sizes = PlotSize::where('project_id', $project_id)->get(['id', 'title']);
        return response()->json($sizes);
    }

    // 🔹 AJAX: Get streets by block
    public function getStreets($block_id)
    {
        $streets = Street::where('block_id', $block_id)->get(['id', 'street_name']);
        return response()->json($streets);
    }
    // view deleted plots
    public function deleted(Request $request)
    {
        $plots = Plot::onlyTrashed()
            ->with([
                'project',
                'block',
                'street',
                'size',
                'category'
            ])
            ->latest('deleted_at')
            ->paginate(10);

        return view('plots.deleted', compact('plots'));
    }

    public function deletedView($id)
    {
        $plot = Plot::onlyTrashed()
            ->with([
                'project',
                'block',
                'street',
                'size',
                'category',
                'areaVariations',
                'developmentStatus',
                'lopStatus',
                'mortgageStatus',
                'possessionStatus'
            ])
            ->findOrFail($id);

        return view('plots.deleted_show', compact('plot'));
    }

    public function restore($id)
    {
        $plot = Plot::onlyTrashed()->findOrFail($id);

        $plot->restore();

        return redirect()
            ->route('plots.deleted')
            ->with('success', 'Plot restored successfully.');
    }
    // force delete
    public function forceDelete($id)
    {
        $plot = Plot::onlyTrashed()->findOrFail($id);

        $plot->forceDelete();

        return redirect()
            ->route('plots.deleted')
            ->with('success', 'Plot permanently deleted.');
    }
    // public function indexByBlock(Block $block)
    // {
    //     $plots = Plot::with(['street', 'block'])
    //         ->where('block_id', $block->id)
    //         ->orderBy('plot_number')
    //         ->paginate(15);

    //     return view('plots.block_plots', compact('block', 'plots'));
    // }
    // index street by block shifted to street controller 
    public function indexByBlock(Request $request, Block $block)
    {
        // $plots = Plot::with(['street', 'plotsize'])
        $plots = Plot::with(['street', 'size'])
            ->where('block_id', $block->id)
            ->when($request->street_id, fn($q) =>
                $q->where('street_id', $request->street_id)
            )
            ->when($request->plot_number, fn($q) =>
                $q->where('plot_number', 'like', '%' . $request->plot_number . '%')
            )
            ->orderBy('plot_number')
            ->paginate(15);

        return view('plots.block_plots', compact('block', 'plots'));
    }
    
    // for google map retreive
    public function getMap(Plot $plot)
    {
        $plot->load(['coordinates', 'project', 'block', 'street']);

        $coordinate = $plot->coordinates;
        $project = optional($plot->project)->project_name;
        $block = optional($plot->block)->block_name;
        $street = optional($plot->street)->street_name;
        $plot_no = $plot->plot_number;
        
        return view('admin.googlemap.index', compact('coordinate', 'project', 'block', 'street', 'plot_no'));

    }

    // street wise plot list
    public function indexByStreet(Request $request, Street $street)
    {
        // $plots = Plot::with(['plotsize', 'block'])
        $plots = Plot::with(['size', 'block'])
        // $plots = Plot::with(['size', 'block'])
            ->where('street_id', $street->id)
            ->when($request->plot_number, fn ($q) =>
                $q->where('plot_number', 'like', '%' . $request->plot_number . '%')
            )
            ->when($request->block_id, fn ($q) =>
                $q->where('block_id', $request->block_id)
            )
            ->orderBy('plot_number')
            ->paginate(15);
        return view('plots.street_plots', compact('street', 'plots'));
    }

    public function blockwise(string $bid){
        $query = Plot::with([
            'project',
            'block',
            'street',
            // 'plotSize',
            'size',
            'category',
            'lopStatus',
            'developmentStatus',
            'latestAreavariation'
        ]);

            $query->where('project_id', $bid);
        
            $query->where('block_id', $bid);
            $plots = $query->get();
        return view('plots.plot', compact('plots'));
        // $plots = DB::table('plots')
        //             ->where('block_id', $bid)
        //             // ->paginate(10)
        //             ->get();
        
        // $block = DB::table('blocks')
        //             ->where('id', $bid)
        //             ->first();

        // $block_name = $block->block_name ?? 'Unknown Block';

        // $projectid = DB::table('blocks')
        //             ->where('id', $bid)
        //             ->first();
        // $project_id = $projectid->project_id;            

        // $project = DB::table('projects')
        //             ->where('id', $project_id)
        //             ->first();
        // $size = DB::table('plotsizes')
        //             ->get();
        // $project_name = $project->project_name ?? 'Unknown Project';

        // return view('plots.plot', compact('plots','project_name','block_name','size'));
    }
}
