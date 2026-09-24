<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentStatus;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DevelopmentExport;


class DevelopmentStatusController extends Controller
{
    /**
     * Development status listing
     */
    public function index(Request $request)
    {
        $query = Plot::with([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'developmentStatus',
        ]);

        // Project filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Block filter
        if ($request->filled('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Street filter
        if ($request->filled('street_id')) {
            $query->where('street_id', $request->street_id);
        }

        // Property Type filter
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        // Development status filter
        if ($request->filled('overall_status')) {
            $query->whereHas('developmentStatus', function ($q) use ($request) {
                $q->where(
                    'overall_status',
                    $request->overall_status
                );
            });
        }

        // Sewer / Manholes filter
        if ($request->filled('sewer_manholes')) {
            $query->whereHas('developmentStatus', function ($q) use ($request) {
                $q->where(
                    'sewer_manholes',
                    $request->sewer_manholes
                );
            });
        }

        // Asphalt / TST filter
        if ($request->filled('asphalt_tst')) {
            $query->whereHas('developmentStatus', function ($q) use ($request) {
                $q->where(
                    'asphalt_tst',
                    $request->asphalt_tst
                );
            });
        }

        // Plot number filter
        if ($request->filled('plot_number')) {
            $query->where(
                'plot_number',
                'like',
                '%' . $request->plot_number . '%'
            );
        }

        $plots = $query
            ->orderBy('project_id')
            ->orderBy('block_id')
            ->orderBy('plot_number')
            ->paginate(20)
            ->withQueryString();

        return view('development.index', compact('plots'));
    }


    /**
     * Development create page
     */
    public function create()
    {
        return view('development.create');
    }


    /**
     * Store new Development status
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plot_id' => [
                'required',
                'exists:plots,id',
            ],

            'sewer_manholes' => [
                'required',
                Rule::in([
                    'constructed',
                    'not_constructed',
                ]),
            ],

            'asphalt_tst' => [
                'required',
                Rule::in([
                    'yes',
                    'no',
                ]),
            ],

            'overall_status' => [
                'required',
                Rule::in([
                    'developed',
                    'under_development',
                    'not_developed',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);


        // Ek plot ka sirf ek Development Status allowed hai.
        $alreadyExists = DevelopmentStatus::where(
            'plot_id',
            $validated['plot_id']
        )->exists();

        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'plot_id' =>
                        'This plot already has a Development status. Please use Edit instead.',
                ])
                ->withInput();
        }


        DevelopmentStatus::create([
            'plot_id' => $validated['plot_id'],
            'sewer_manholes' => $validated['sewer_manholes'],
            'asphalt_tst' => $validated['asphalt_tst'],
            'overall_status' => $validated['overall_status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);


        return redirect()
            ->route('development.index')
            ->with(
                'success',
                'Development status saved successfully!'
            );
    }


    /**
     * Edit Development status
     */
    public function edit(Plot $plot)
    {
        $plot->load([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'developmentStatus',
        ]);

        return view('development.edit', compact('plot'));
    }


    /**
     * Update Development status
     */
    public function update(Request $request, Plot $plot)
    {
        $validated = $request->validate([
            'sewer_manholes' => [
                'required',
                Rule::in([
                    'constructed',
                    'not_constructed',
                ]),
            ],

            'asphalt_tst' => [
                'required',
                Rule::in([
                    'yes',
                    'no',
                ]),
            ],

            'overall_status' => [
                'required',
                Rule::in([
                    'developed',
                    'under_development',
                    'not_developed',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);


        DevelopmentStatus::updateOrCreate(
            [
                'plot_id' => $plot->id,
            ],
            [
                'sewer_manholes' => $validated['sewer_manholes'],
                'asphalt_tst' => $validated['asphalt_tst'],
                'overall_status' => $validated['overall_status'],
                'remarks' => $validated['remarks'] ?? null,
            ]
        );


        return redirect()
            ->route('development.index')
            ->with(
                'success',
                'Development status updated successfully!'
            );
    }


    /**
     * Delete Development status
     */
    public function destroy(Plot $plot)
    {
        DevelopmentStatus::where(
            'plot_id',
            $plot->id
        )->delete();


        return redirect()
            ->route('development.index')
            ->with(
                'success',
                'Development status deleted successfully!'
            );
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'project_id' => $request->project_id,
            'block_id' => $request->block_id,
            'street_id' => $request->street_id,
            'property_type_id' => $request->property_type_id,
            'overall_status' => $request->overall_status,
            'sewer_manholes' => $request->sewer_manholes,
            'asphalt_tst' => $request->asphalt_tst,
            'plot_number' => $request->plot_number,
        ];

        return Excel::download(
            new DevelopmentExport($filters),
            'development_status.xlsx'
        );
    }

    /**
     * Search plots for Development create page
     */
    public function searchPlots(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'property_type_id' => 'required|exists:property_types,id',
            'block_id' => 'required|exists:blocks,id',
            'street_id' => 'nullable|exists:streets,id',
            'plot_number' => 'required|string|max:100',
        ]);


        $query = Plot::with([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
        ])
            ->where(
                'project_id',
                $request->project_id
            )
            ->where(
                'property_type_id',
                $request->property_type_id
            )
            ->where(
                'block_id',
                $request->block_id
            )
            ->where(
                'plot_number',
                'like',
                '%' . $request->plot_number . '%'
            );


        if ($request->filled('street_id')) {
            $query->where(
                'street_id',
                $request->street_id
            );
        }


        $plots = $query
            ->orderBy('plot_number')
            ->limit(50)
            ->get();


        return response()->json(
            $plots->map(function ($plot) {
                return [
                    'id' => $plot->id,
                    'plot_number' => $plot->plot_number,
                    'project' => $plot->project?->project_name,
                    'block' => $plot->block?->block_name,
                    'street' => $plot->street?->street_name,
                    'property_type' => $plot->propertyType?->name,
                    'size' => $plot->size?->title,
                    'size_area' => $plot->size?->size_area,
                ];
            })
        );
    }


    /**
     * Existing method used by old Area Variation / Plot pages.
     *
     * IMPORTANT:
     * Is method ko abhi remove nahi karna.
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'plot_id' => 'required',
            'sewer_manholes' => 'required',
            'asphalt_tst' => 'required',
            'overall_status' => 'required',
            'remarks' => 'nullable',
        ]);


        DevelopmentStatus::updateOrCreate(
            ['plot_id' => $request->plot_id],
            [
                'sewer_manholes' => $request->sewer_manholes,
                'asphalt_tst' => $request->asphalt_tst,
                'overall_status' => $request->overall_status,
                'remarks' => $request->remarks,
            ]
        );


        return back()->with(
            'success',
            'Development status saved!'
        );
    }
}

// <!-- <php

// namespace App\Http\Controllers;

// use App\Models\DevelopmentStatus;
// use Illuminate\Http\Request;

// class DevelopmentStatusController extends Controller
// {
//     public function storeOrUpdate(Request $request)
//     {
//         $request->validate([
//             'plot_id' => 'required',
//             'sewer_manholes' => 'required',
//             'asphalt_tst' => 'required',
//             'overall_status' => 'required',
//             'remarks' => 'nullable'
//         ]);

//         DevelopmentStatus::updateOrCreate(
//             ['plot_id' => $request->plot_id],
//             [
//                 'sewer_manholes' => $request->sewer_manholes,
//                 'asphalt_tst' => $request->asphalt_tst,
//                 'overall_status' => $request->overall_status,
//                 'remarks' => $request->remarks
//             ]
//         );

//         return back()->with('success', 'Development status saved!');
//     }
// } -->
