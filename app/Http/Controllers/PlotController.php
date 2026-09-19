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
use App\Models\PlotCategoryType;
use App\Models\PlotCoordinate;
use App\Models\PropertyType;
use App\Models\PlotSizeAssignment;
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

        $roadMap = [
            'complete'      => 'yes',
            'not_complete'  => 'no'
        ];

        // Sewer mapping (frontend → DB)
        $sewerMap = [
            'complete'      => 'constructed',
            'not_complete'  => 'not_constructed'
        ];

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
            'latestAreavariation',
            'propertyType'
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
 
        // 🔹 Property Type
        if ($request->property_type_id) {
            $query->where('property_type_id', $request->property_type_id);
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
                ->orWhereHas('category', fn($c) => $c->where('category_title', 'LIKE', "%$search%"))
                ->orWhereHas('propertyType', function ($pt) use ($search) {
                        $pt->where('name', 'LIKE', "%$search%");
                    });
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
            'sizes' => collect(),
            // 'sizes' => PlotSize::all(),
            'propertyTypes' => PropertyType::orderBy('name')->get(),
            'currentStreet' => $currentStreet,
        ]);
    }

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
        $projects = Project::orderBy('project_name')->get();

        $blocks = collect();

        $streets = collect();

        $sizes = collect();

        $categories = PlotCategoryType::orderBy('category_title')->get();

        $propertytypes = PropertyType::orderBy('name')->get();

        return view(
            'plots.create',
            compact(
                'projects',
                'blocks',
                'streets',
                'sizes',
                'categories',
                'propertytypes'
            )
        );
    }

    

    // ✅ Store New Plot -- NEW TRY START
    public function store(Request $request)
    {
        $rules = [
            'numbering_type' => 'required|in:blockwise,streetwise',
            'property_type_id' => 'required|integer|exists:property_types,id',
            'project_id' => 'required|integer|exists:projects,id',
            'block_id' => 'required|integer|exists:blocks,id',
            'plot_number' => 'required|string',
            'street_id' => 'nullable|exists:streets,id',
            // 'size_id' => 'required|integer',
            'size_id' => 'required|integer|exists:plotsizes,id',
            'category_id' => 'required|integer|exists:plot_category_types,id',
            'remarks' => 'nullable|string',
        ];

        if ($request->numbering_type === 'blockwise') {
            $rules['block_id'] = 'required|integer';

            $rules['plot_number'] .= '|unique:plots,plot_number,NULL,id,project_id,'
                . $request->project_id
                . ',property_type_id,' . $request->property_type_id
                . ',block_id,' . $request->block_id;
        }

        if ($request->numbering_type === 'streetwise') {
            $rules['street_id'] = 'required|integer';

            $rules['plot_number'] .= '|unique:plots,plot_number,NULL,id,project_id,'
                . $request->project_id
                . ',property_type_id,' . $request->property_type_id
                . ',block_id,' . $request->block_id
                . ',street_id,' . $request->street_id;
        }

        $validatedData = $request->validate($rules);

        $sizeAssigned = PlotSizeAssignment::where(
            'project_id',
            $validatedData['project_id']
        )
            ->where(
                'block_id',
                $validatedData['block_id']
            )
            ->where(
                'property_type_id',
                $validatedData['property_type_id']
            )
            ->where(
                'plotsize_id',
                $validatedData['size_id']
            )
            ->exists();

        if (! $sizeAssigned) {
            return back()
                ->withErrors([
                    'size_id' =>
                        'The selected Size is not assigned to this Project, Block and Property Type.',
                ])
                ->withInput();
        }
        $plot = Plot::create($validatedData);

        return redirect()
            ->route('plots.create')
            ->with('success', 'Plot saved successfully!')
            ->withInput([
                'project_id' => $validatedData['project_id'],
                'property_type_id' => $validatedData['property_type_id'],
                'block_id' => $validatedData['block_id'],
                'street_id' => $validatedData['street_id'] ?? null,
                'size_id' => $validatedData['size_id'],
                'category_id' => $validatedData['category_id'],
                'numbering_type' => $validatedData['numbering_type'],
                'remarks' => $validatedData['remarks'] ?? null,
                // 'plot_number' => '',
                'plot_number' => $validatedData['plot_number'] ?? null,
            ]);

    }

   
    public function edit(Plot $plot)
    {
        $plot->load([
            'project',
            'block',
            'street',
            'size'
        ]);

        $projects = Project::orderBy('project_name')->get();

        $blocks = Block::where(
            'project_id',
            $plot->project_id
        )
        ->orderBy('block_name')
        ->get();

        $streets = Street::where(
            'project_id',
            $plot->project_id
        )
        ->orderBy('street_name')
        ->get();

        $categories = PlotCategoryType::orderBy(
            'category_title'
        )->get();

        $propertytypes = PropertyType::orderBy(
            'name'
        )->get();

        $sizes = PlotSizeAssignment::where(
            'project_id',
            $plot->project_id
        )
        ->where(
            'block_id',
            $plot->block_id
        )
        ->where(
            'property_type_id',
            $plot->property_type_id
        )
        ->with('plotsize')
        ->get()
        ->map(function ($assignment) {
            return $assignment->plotsize;
        })
        ->filter();

        return view(
            'plots.edit',
            compact(
                'plot',
                'projects',
                'blocks',
                'streets',
                'sizes',
                'categories',
                'propertytypes'
            )
        );
    }
    


    public function update(Request $request, Plot $plot)
    {
        $rules = [
            'numbering_type'   => 'required|in:blockwise,streetwise',
            'project_id'       => 'required|exists:projects,id',
            'block_id'         => 'required|exists:blocks,id',
            'street_id'        => 'required|exists:streets,id',
            'plot_number'      => 'required|string',
            'property_type_id' => 'required|integer|exists:property_types,id',
            'size_id'          => 'required|integer|exists:plotsizes,id',
            'category_id'      => 'required|exists:plot_category_types,id',
            'remarks'          => 'nullable|string',
        ];

        // UNIQUE LOGIC
        // Current plot ko ignore karega
        if ($request->numbering_type === 'blockwise') {

            $rules['plot_number'] .= '|unique:plots,plot_number,' . $plot->id .
                ',id,project_id,' . $request->project_id .
                ',property_type_id,' . $request->property_type_id .
                ',block_id,' . $request->block_id;

        } else {

            $rules['plot_number'] .= '|unique:plots,plot_number,' . $plot->id .
                ',id,project_id,' . $request->project_id .
                ',property_type_id,' . $request->property_type_id .
                ',block_id,' . $request->block_id .
                ',street_id,' . $request->street_id;
        }

        $validated = $request->validate($rules);

        /*
        |--------------------------------------------------------------------------
        | Check Size Assignment
        |--------------------------------------------------------------------------
        | Selected size Project + Block + Property Type ke liye assigned honi chahiye.
        */

        $sizeAssigned = PlotSizeAssignment::where(
            'project_id',
            $validated['project_id']
        )
            ->where(
                'block_id',
                $validated['block_id']
            )
            ->where(
                'property_type_id',
                $validated['property_type_id']
            )
            ->where(
                'plotsize_id',
                $validated['size_id']
            )
            ->exists();

        if (! $sizeAssigned) {

            return back()
                ->withErrors([
                    'size_id' =>
                        'The selected Size is not assigned to this Project, Block and Property Type.',
                ])
                ->withInput();
        }

        // Update plot
        $plot->update($validated);

        return redirect()
            ->route('plots.index')
            ->with('success', 'Plot updated successfully!');
    }

    // 🗑️ Delete plot
    public function destroy(Plot $plot)
    {
        $plot->delete();
        return back()->with('success', 'Plot deleted successfully!');
    }

    // new ajax for plot size
    public function getAssignedSizes(
    $project_id,
    $block_id,
    $property_type_id
    ) {
        $sizes = PlotSizeAssignment::where('project_id', $project_id)
            ->where('block_id', $block_id)
            ->where('property_type_id', $property_type_id)
            ->with('plotsize')
            ->get()
            ->filter(function ($assignment) {
                return $assignment->plotsize !== null;
            })
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->plotsize->id,
                    'title' => $assignment->plotsize->title,
                    'size_area' => $assignment->plotsize->size_area,
                ];
            })
            ->values();

        return response()->json($sizes);
    }

    // 🔹 AJAX: Get blocks by project

    public function getBlocks($project_id)
    {
        $blocks = Block::where('project_id', $project_id)->get(['id', 'block_name']);
        return response()->json($blocks);
    }

    // 🔹 AJAX: Get sizes by project
    // public function getSizes($project_id)
    // {
    //     $sizes = PlotSize::where('project_id', $project_id)->get(['id', 'title']);
    //     return response()->json($sizes);
    // }

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

    }
}
