<?php

namespace App\Http\Controllers;

use App\Models\AreaVariation;
use App\Models\Plot;
use App\Models\Plotsize;
use App\Models\DevelopmentStatus;
use App\Models\LopStatus;
use App\Models\MortgageStatus;
use App\Models\PossessionStatus;
use Illuminate\Http\Request;
// for import area variations
use Carbon\Carbon;
use App\Models\Project;
use App\Models\Block;
use Illuminate\Support\Facades\Log;
// for excel import
// use App\Exports\PlotAreaVariationExport;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\PlotVariationTemplateExport;
// use App\Exports\PlotVariationExport;
use App\Exports\PlotVariationExporttemplate;
// for new excel import
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Spatie\Activitylog\Models\Activity;


class AreaVariationController extends Controller
{
    public function activityLogs()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(20);

        return view('admin.activity_logs', compact('logs'));
    }

    public function exportExcel($id)
    {
        $variation = AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.lopStatus',
            'plot.mortgageStatus',
            'plot.developmentStatus'
        ])->findOrFail($id);

        $plot = $variation->plot;

        $filename =
            $plot->project->project_name."_".
            $plot->block->block_name."_".
            $plot->street->street_name."_".
            $plot->plot_number."_".
            $variation->measured_date.".xlsx";

            // dd($variation);

        // ✅ template load karo
        $spreadsheet = IOFactory::load(
            storage_path('app/templates/template.xlsx')
        );

        $sheet = $spreadsheet->getActiveSheet();

        // ✅ yahan apna data fill karo
        $sheet->setCellValue('L2', $plot->project->project_name);   // Project name
        $sheet->setCellValue('M2', $plot->block->block_name);       // block name

        $sheet->setCellValue('O2', $plot->street->street_name);     // street name
        $sheet->setCellValue('N2', $plot->plot_number);             // plot no

        $sheet->setCellValue('P2', $plot->developmentStatus->overall_status);   // development status
        $sheet->setCellValue('Q2', $variation->road_status_at_time);   // asphalt status
        // dd($variation);


        $sheet->setCellValue('R2', $variation->sewer_status_at_time);   // sewer status
        $sheet->setCellValue('S2', $variation->lop_status_at_time);  //  LOP status


        $sheet->setCellValue('T2', $plot->possessionStatus->possession_status);  // possession status
        $sheet->setCellValue('U2', $variation->measured_area);  // plot area


        $sheet->setCellValue('V2', $plot->size->title);  // size of plot
        // $sheet->setCellValue('W2', $variation->measured_date);  // date

        $sheet->setCellValue('W2', \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($variation->measured_date)));
        $sheet->getStyle('W2')
            ->getNumberFormat()
            ->setFormatCode('dd-mm-yyyy');

        $sheet->setCellValue('X2', $plot->category->category_title);                // category of plot
        $sheet->setCellValue('Y2', $variation->measured_by ?? 'N/A');

        $sheet->setCellValue('Z2', $variation->created_at ?? 'N/A');
        $sheet->setCellValue('AA2', $variation->updated_at ?? 'N/A');

        // $sheet->setCellValue('B8', $variation->measured_date);
        // $sheet->setCellValue('D8', $variation->measured_area);

        // ✅ download response
        return new StreamedResponse(function() use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"report.xlsx\"",
            "Cache-Control" => "max-age=0",
        ]);
    }
    
    // for imports area variations ---**** for date format check /** */
    private function parseDate($date)
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

// for import data
    public function importAreaVariations()
    {
        // for remove time limits
        set_time_limit(0); // unlimited execution time
        ini_set('memory_limit', '-1');
        
        $file = storage_path('app/Area-variations.csv');

        if (!file_exists($file)) {
            return 'CSV file not found';
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $index => $row) {
            try {
                $data = array_combine($header, $row);

                // 🔹 Project
                $project = Project::where('project_name', trim($data['Project']))->first();
                if (!$project) {
                    throw new \Exception('Project not found');
                }

                // 🔹 Block
                $block = Block::where('block_name', trim($data['Block']))
                    ->where('project_id', $project->id)
                    ->first();

                if (!$block) {
                    throw new \Exception('Block not found');
                }

                // 🔹 Plot
                $plot = Plot::where('plot_number', trim($data['Plot']))
                    ->where('block_id', $block->id)
                    ->first();

                if (!$plot) {
                    throw new \Exception('Plot not found');
                }

                // 🔹 Previous Area
                $previousArea = AreaVariation::where('plot_id', $plot->id)
                    ->latest('measured_date')
                    ->value('measured_area') ?? 0;

                // 🔹 Save Area Variation
                AreaVariation::create([
                    'plot_id'            => $plot->id,
                    'previous_area'      => $previousArea,
                    'measured_area'      => (float) $data['measured_area'],
                    'measured_date'   => $this->parseDate($data['created_at']),
                    'remarks'            => $data['Remarks'] ?? null,

                    // 🧊 SNAPSHOT FIELDS
                    'lop_status_at_time' =>
                        strtolower($data['LOP_status']) == 'lop'
                            ? 'lop'
                            : 'non_lop',
 
                    'road_status_at_time' =>
                        strtolower($data['asphalttstroad_status']) == 'completed'
                            ? 'complete'
                            : 'not_complete',

                    // 'sewer_status_at_time' =>
                    //     str_contains(strtolower($data['sewer_status']), 'constructed')
                    //         ? 'constructed'
                    //         : 'not_constructed',
                                        // 'sewer_status_at_time' =>
                        'sewer_status_at_time' =>
                            strtolower($data['sewer_status']) === 'mh not constructed'
                                ? 'not_constructed'
                                : (strtolower($data['sewer_status']) === 'constructed'
                                    ? 'constructed'
                                    : 'not_constructed'),


                ]);

            } catch (\Throwable $e) {
                Log::error('AreaVariation Import Failed', [
                    'row'    => $index + 2,
                    'reason' => $e->getMessage(),
                    'data'   => $row,
                ]);
            }
        }

        return 'Area Variation Import Completed';
    }

    // end imports area variations 

    public function index(Request $request)
    {
        $query = AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            // 'plot.plotsize',
            'plot.size',
            'plot.developmentStatus',
            'plot.lopStatus',
            'plot.mortgageStatus',
            'plot.possessionStatus'
        ]);

        // 📅 Date range
        if ($request->filled('from_date')) {
            $query->whereDate('measured_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('measured_date', '<=', $request->to_date);
        }

        // 🏢 Project
        if ($request->filled('project_id')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        // 🧱 Block
        if ($request->filled('block_id')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
        }

        // 🔢 Plot number
        if ($request->filled('plot_number')) {
            $query->whereHas('plot', function ($q) use ($request) {
                $q->where('plot_number', 'like', '%' . $request->plot_number . '%');
            });
        }

        $areaVariations = $query->latest()->paginate(5)->withQueryString();

        // if(request('page') == 3 ){
        //     // dd($areaVariations->items());
        //     foreach($areaVariations as $variation){
        //         dump([
        //             'variation_id' => $variation->id,
        //             'plot_id' => $variation->plot_id,
        //             'plot_relation' => $variation->plot,
        //         ]);
        //     }
        //     dd('check completed');
        // }

        return view('plots.area_variations.index', [
            'areaVariations' => $areaVariations,
            'projects' => Project::orderBy('project_name')->get(),
            'blocks' => Block::orderBy('block_name')->get(),
        ]);
    }

    public function edit($id)
    {
        $av = AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.developmentStatus',
            'plot.lopStatus',
            'plot.mortgageStatus',
            'plot.possessionStatus'
        ])->findOrFail($id);

        return view('plots.area_variations.edit', compact('av'));
    }
    public function create(Plot $plot)
    {
        $latestArea = $plot->latestAreavariation?->measured_area ?? $plot->size;

        return view('plots.area_variations.create', compact('plot', 'latestArea'));
    }
    public function createnew($plot_id)
    {
        $plot = Plot::with([
            'project',
            'block',
            'street',
            'developmentStatus', 
            'lopStatus',
            'mortgageStatus',
            'possessionStatus',
            // 'plotsize',
            'size',
            'latestAreavariation'
        ])->findOrFail($plot_id);

        // Dummy AreaVariation object (edit jaisa structure)
        $av = new AreaVariation();

        $av->plot = $plot;

        // previous area set kar lo
        $av->previous_area = $plot->latestAreavariation
            ? $plot->latestAreavariation->measured_area
            : $plot->plotsize?->size_area;

        // default values (optional but recommended)
        $av->measured_date = now();
        $av->measured_by = auth()->user()->name;

        return view('plots.area_variations.createnew', compact('av'));

    }

    // new store method
    // public function store(Request $request, Plot $plot)
    // {
    //     $request->validate([
    //         'measured_area' => 'required|numeric',
    //         'measured_by'   => 'nullable|string',
    //         'measured_date' => 'required|date',
    //         'remarks'       => 'nullable|string',
    //     ]);

    //     // 🔹 Current Development Status
    //     $dev = $plot->developmentStatus;
    //     $lop = $plot->lopStatus;

    //     $area = AreaVariation::create([
    //         'plot_id'        => $plot->id,
    //         'previous_area'  => $plot->latestAreavariation->measured_area ?? null,
    //         'measured_area'  => $request->measured_area,
    //         'measured_by'    => $request->measured_by,
    //         'measured_date'  => $request->measured_date,
    //         'remarks'        => $request->remarks,

    //         // 🧊 SNAPSHOT FIELDS
    //         'road_status_at_time'  => $dev?->asphalt_tst == 'yes'
    //                                         ? 'complete'
    //                                         : 'not_complete',

    //         'sewer_status_at_time' => $dev?->sewer_manholes ?? 'not_constructed',

    //         'lop_status_at_time'   => $lop?->lop_status ?? null,
    //     ]);

    //     return response()->json($area);
    // }

    // old but working
    // public function store(Request $request)
    // {
    //     // return $request;
    //     $request->validate([
    //         'plot_id'           => 'required|exists:plots,id',
    //         'previous_area'     => 'required|numeric',
    //         'measured_area'     => 'required|numeric',
    //         'measured_date'  => 'required|date',
    //         'measured_by'       => 'nullable|string',
    //     ]);
    //     // return $request;

    //     $plot = Plot::with(['developmentStatus', 'lopStatus'])->findOrFail($request->plot_id);
    //     // return $plot;

    //     $dev = $plot->developmentStatus;   // may be null
    //     $lop = $plot->lopStatus;           // may be null
    //     // return $dev;
    //     // return $lop;


    //     AreaVariation::create([
    //         'plot_id'          => $plot->id,
    //     //     'previous_area'    => $request->previous_area,
    //     //     'measured_area'    => $request->measured_area,
    //         'previous_area' => (float) $request->previous_area,
    //         'measured_area' => (float) $request->measured_area,
    //         'measured_date' => $request->measured_date,
    //         'measured_by'      => $request->measured_by ?? 'system',

    //         // 🧊 SNAPSHOT FIELDS (FIXED & SAFE)
    //         'road_status_at_time'  => $dev?->asphalt_tst === 'yes'
    //                                     ? 'complete'
    //                                     : 'not_complete',

    //         'sewer_status_at_time' => $dev?->sewer_manholes === 'constructed'
    //                                     ? 'constructed'
    //                                     : 'not_constructed',

    //         'lop_status_at_time'   => $lop?->lop_status, // lop | non_lop | mortgaged | null
    //     ]);

    //     return redirect()
    //         ->route('plots.show', $plot->id)
    //         ->with('success', 'Area variation added successfully');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'plot_id'           => 'required|exists:plots,id',
            // 'previous_area'     => 'required|numeric',
            'measured_area'     => 'required|numeric',
            'measured_date'     => 'required|date',
            'measured_by'       => 'nullable|string',

            // 👇 ADD THESE (same as update)
            'sewer_manholes' => 'nullable|in:constructed,not_constructed',
            'asphalt_tst' => 'nullable|in:yes,no',
            'overall_status' => 'nullable|in:developed,under_development,not_developed',

            'lop_status' => 'nullable|in:lop,non_lop',
            'is_mortgaged' => 'nullable|in:yes,no',
            'possession_status' => 'nullable|in:possessionable,non_lop_possessionable,under_development_possessionable,not_possessionable',
        ]);

        // new add for current user name
        $request->merge([
            'measured_by' => auth()->user()->name
        ]);

        $plot = Plot::with(['developmentStatus', 'lopStatus'])->findOrFail($request->plot_id);

        // ✅ Step 1: AreaVariation save
        $av = AreaVariation::create([
            'plot_id'        => $plot->id,
            'previous_area'  => (float) $request->previous_area,
            'measured_area'  => (float) $request->measured_area,
            'measured_date'  => $request->measured_date,
            'measured_by'    => $request->measured_by ?? 'system',

            // snapshot
            'road_status_at_time'  => $request->asphalt_tst === 'yes' ? 'complete' : 'not_complete',
            'sewer_status_at_time' => $request->sewer_manholes === 'constructed' ? 'constructed' : 'not_constructed',
            'overall_status_at_time' => $request->overall_status,
            'lop_status_at_time'   => $request->lop_status,
        ]);

        $plotId = $plot->id;


        // ✅ Step 2: Development Status update
        if ($request->sewer_manholes || $request->asphalt_tst || $request->overall_status) {
            DevelopmentStatus::updateOrCreate(
                ['plot_id' => $plotId],
                [
                    'sewer_manholes' => $request->sewer_manholes ?? DevelopmentStatus::where('plot_id',$plotId)->value('sewer_manholes'),
                    'asphalt_tst'    => $request->asphalt_tst ?? DevelopmentStatus::where('plot_id',$plotId)->value('asphalt_tst'),
                    'overall_status' => $request->overall_status ?? DevelopmentStatus::where('plot_id',$plotId)->value('overall_status'),
                ]
            );
        }

        // ✅ Step 3: LOP
        if ($request->lop_status) {
            LopStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['lop_status' => $request->lop_status]
            );
        }

        // ✅ Step 4: Mortgage
        if ($request->is_mortgaged) {
            MortgageStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['is_mortgaged' => $request->is_mortgaged]
            );
        }

        // ✅ Step 5: Possession
        if ($request->possession_status) {
            PossessionStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['possession_status' => $request->possession_status]
            );
        }

        return redirect()
            ->route('plots.show', $plot->id)
            ->with('success', 'Area variation added successfully + statuses updated');
    }
        // new after filter eleminate -- start --
    // public function store(Request $r)
    // {
    //     $r->validate([
    //         'plot_id'        => 'required|exists:plots,id',
    //         'measured_area'  => 'required|numeric',
    //         'measured_date'  => 'required|date',
    //         // 'measured_area'  => 'nullable|numeric',
    //         // 'measured_date'  => 'nullable|date',
    //     ]);

    //     $variation = AreaVariation::create([
    //         'plot_id'        => $r->plot_id,
    //         'measured_area' => $r->previous_area,
    //         // 'measured_area'  => $r->measured_area,
    //         // 'measured_date'  => $r->measured_date,
    //         'source'         => 'draftsman', // default
    //     ]);

    //     return back()->with('success', 'Area variation added.');

    //     // return response()->json([
    //     //     'measured_area' => $variation->measured_area,
    //     //     // 'measured_area' => $r->previous_area
    //     // ]);
    // }
    public function storearea(Request $r)
    {
        $r->validate([
            'plot_id'        => 'required|exists:plots,id',
            'measured_area'  => 'required|numeric',
            // 'measured_date'  => 'required|date',
            // 'measured_area'  => 'nullable|numeric',
            // 'measured_date'  => 'nullable|date',
        ]);

        $variation = AreaVariation::create([
            'plot_id'        => $r->plot_id,
            // 'measured_area' => $r->previous_area,
            'measured_area'  => $r->measured_area,
            // 'measured_date'  => $r->measured_date,
            'source'         => 'draftsman', // default
        ]);

        // return back()->with('success', 'Area variation added.');

        return response()->json([
            'measured_area' => $variation->measured_area,
            // 'measured_area' => $r->previous_area
        ]);
    }
    // new after filter eleminate -- end --

    // // new store method
    
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'plot_id' => 'required|exists:plots,id',
    //         'measured_area' => 'required|numeric',
    //         'measured_date' => 'nullable|date',
    //         'measured_by' => 'nullable|string|max:255',
    //         'remarks' => 'nullable|string',
    //         'source' => 'nullable|string' // optional
    //     ]);

    //     $data['source'] = $data['source'] ?? 'survey';

    //     AreaVariation::create($data);

    //     return back()->with('success','Area variation saved.');
    // }

    // store new measurement
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'plot_id' => 'required|exists:plots,id',
    //         // 'measured_area' => 'required|numeric',
    //         // 'measured_date' => 'nullable|date',
    //         // 'measured_by' => 'nullable|string|max:255',
    //         // 'remarks' => 'nullable|string',
    //     ]);

    //     AreaVariation::create($data);

    //     return back()->with('success', 'Area variation saved.');
    // }

    // update an area variation and optionally update statuses
    public function update(Request $request, $id)
    {
        $av = AreaVariation::findOrFail($id);

        $data = $request->validate([
            'measured_area' => 'required|numeric',
            'measured_date' => 'nullable|date',
            'measured_by' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',

            // optional status updates (coming from modal)
            'sewer_manholes' => 'nullable|in:constructed,not_constructed',
            'asphalt_tst' => 'nullable|in:yes,no',
            'overall_status' => 'nullable|in:developed,under_development,not_developed',

            'lop_status' => 'nullable|in:lop,non_lop',
            'is_mortgaged' => 'nullable|in:yes,no',
            'possession_status' => 'nullable|in:possessionable,non_lop_possessionable,under_development_possessionable,not_possessionable',
        ]);

        // update area variation
        $av->update([
            'measured_area' => $data['measured_area'],
            'measured_date' => $data['measured_date'] ?? $av->measured_date,
            'measured_by' => $data['measured_by'] ?? $av->measured_by,
            'remarks' => $data['remarks'] ?? $av->remarks,
            // save status in areavariation table
            'sewer_status_at_time' => $data['sewer_manholes'] ?? $av->sewer_manholes,
            // 'road_status_at_time' => $data['asphalt_tst'] ?? $av->asphalt_tst,
            'road_status_at_time' => isset($data['asphalt_tst'])
            ? ($data['asphalt_tst'] === 'yes' ? 'complete' : 'not_complete')
            : $av->road_status_at_time,
            'lop_status_at_time' => $data['lop_status'] ?? $av->lop_status,
        ]);

        $plotId = $av->plot_id;

        // If development fields provided, updateOrCreate development_statuses
        if (isset($data['sewer_manholes']) || isset($data['asphalt_tst']) || isset($data['overall_status'])) {
            DevelopmentStatus::updateOrCreate(
                ['plot_id' => $plotId],
                [
                    'sewer_manholes' => $data['sewer_manholes'] ?? DevelopmentStatus::where('plot_id',$plotId)->value('sewer_manholes'),
                    'asphalt_tst' => $data['asphalt_tst'] ?? DevelopmentStatus::where('plot_id',$plotId)->value('asphalt_tst'),
                    'overall_status' => $data['overall_status'] ?? DevelopmentStatus::where('plot_id',$plotId)->value('overall_status'),
                ]
            );
        }

        // LOP
        if (isset($data['lop_status'])) {
            LopStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['lop_status' => $data['lop_status']]
            );
        }

        // Mortgage
        if (isset($data['is_mortgaged'])) {
            MortgageStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['is_mortgaged' => $data['is_mortgaged']]
            );
        }

        // Possession
        if (isset($data['possession_status'])) {
            PossessionStatus::updateOrCreate(
                ['plot_id' => $plotId],
                ['possession_status' => $data['possession_status']]
            );
        }
        // option-1
        // return back()->with('success', 'Area variation and related statuses updated.');
        // option-2
        return redirect()
            ->route('area_variations.index')
            ->with('success', 'Area variation updated successfully.');
        // option - 3
        // return redirect()
        //     ->route('plots.show', $av->plot_id)
        //     ->with('success', 'Area variation updated successfully.');
    }

    // delete
    public function destroy($id)
    {
        $av = AreaVariation::findOrFail($id);
        $av->delete();
        return back()->with('success', 'Area variation deleted.');
    }

    // print single area variation (or print report)
    public function print($id)
    {
        $av = AreaVariation::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.developmentStatus',
            'plot.lopStatus',
            'plot.mortgageStatus',
            'plot.possessionStatus'
        ])->findOrFail($id);

        // render a print-friendly view
        return view('plots.area_variations.print_single', compact('av'));
    }
    // ---- old functins  ----

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'plot_id' => 'required',
    //         // 'measured_area' => 'required|numeric',
    //         // 'measured_date' => 'required|date',
    //         // 'measured_by' => 'nullable|string',
    //         // 'remarks' => 'nullable|string'
    //     ]);

    //     AreaVariation::create([
    //         'plot_id' => $request->plot_id,
    //         'measured_area' => 0,
    //         // 'measured_area' => $request->measured_area,
    //         // 'measured_by' => $request->measured_by,
    //         // 'measured_date' => $request->measured_date,
    //         // 'remarks' => $request->remarks,
    //     ]);

    //     return back()->with('success', 'Area variation added successfully!');
    // }
    // public function print(Plot $plot)
    // {
    //     $variations = $plot->areaVariations()->latest()->get();
    //     return view('plots.area_variations.print', compact('plot', 'variations'));
    // }
    // public function show()
    // {
    //     $areaVariations = AreaVariation::with([
    //             'plot.project',
    //             'plot.block',
    //             'plot.street',
    //             'plot.developmentStatus',
    //             'plot.lopStatus',
    //             'plot.mortgageStatus',
    //             'plot.possessionStatus'
    //         ])
    //         ->get();
                
    //         // return $areaVariations;
    //     return view('plots.area_variations.index', compact('areaVariations'));
    // }
}
