<?php

namespace App\Http\Controllers;

use App\Models\Plot;
use App\Models\LopStatus;
use App\Models\MortgageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LopMortgageStatusController extends Controller
{
    /**
     * Display LOP + Mortgage status list.
     */
    public function index(Request $request)
    {
        $query = Plot::with([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'lopStatus',
            'mortgageStatus',
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

        // LOP filter
        if ($request->filled('lop_status')) {
            $query->whereHas('lopStatus', function ($q) use ($request) {
                $q->where('lop_status', $request->lop_status);
            });
        }

        // Mortgage filter
        if ($request->filled('is_mortgaged')) {
            $query->whereHas('mortgageStatus', function ($q) use ($request) {
                $q->where('is_mortgaged', $request->is_mortgaged);
            });
        }

        // Plot number search
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

        return view('lop-mortgage.index', compact('plots'));
    }

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
            ->where('project_id', $request->project_id)
            ->where('property_type_id', $request->property_type_id)
            ->where('block_id', $request->block_id)
            ->where('plot_number', 'like', '%' . $request->plot_number . '%');

        if ($request->filled('street_id')) {
            $query->where('street_id', $request->street_id);
        }

        $plots = $query
            ->orderBy('plot_number')
            ->limit(50)
            ->get();

        return response()->json($plots->map(function ($plot) {
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
        }));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('lop-mortgage.create');
    }

    /**
     * Store LOP + Mortgage status.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plot_id' => [
                'required',
                'exists:plots,id',
            ],

            'lop_status' => [
                'required',
                Rule::in(['lop', 'non_lop']),
            ],

            'is_mortgaged' => [
                'required',
                Rule::in(['yes', 'no']),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);
        $alreadyExists = LopStatus::where('plot_id', $validated['plot_id'])->exists()||MortgageStatus::where('plot_id', $validated['plot_id'])->exists();
        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'plot_id' => 'This plot already has LOP/Mortgage status. Please use Edit instead.',
                ])
                ->withInput();
        }

        /*
         * Mortgage YES is only allowed when
         * FINAL LOP status is "lop".
         */
        if (
            $validated['is_mortgaged'] === 'yes' &&
            $validated['lop_status'] !== 'lop'
        ) {
            return back()
                ->withErrors([
                    'is_mortgaged' =>
                        'Mortgage YES sirf un plots ke liye allowed hai jinka LOP status "lop" ho.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {

            LopStatus::updateOrCreate(
                [
                    'plot_id' => $validated['plot_id'],
                ],
                [
                    'lop_status' => $validated['lop_status'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );

            MortgageStatus::updateOrCreate(
                [
                    'plot_id' => $validated['plot_id'],
                ],
                [
                    'is_mortgaged' => $validated['is_mortgaged'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        });

        return redirect()
            ->route('lop-mortgage.index')
            ->with('success', 'LOP & Mortgage status saved successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(Plot $plot)
    {
        $plot->load([
            'project',
            'block',
            'street',
            'propertyType',
            'size',
            'lopStatus',
            'mortgageStatus',
        ]);

        return view('lop-mortgage.edit', compact('plot'));
    }

    /**
     * Update LOP + Mortgage status.
     */
    public function update(Request $request, Plot $plot)
    {
        $validated = $request->validate([
            'lop_status' => [
                'required',
                Rule::in(['lop', 'non_lop']),
            ],

            'is_mortgaged' => [
                'required',
                Rule::in(['yes', 'no']),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        /*
         * Check FINAL LOP status.
         *
         * This is important when user changes:
         *
         * LOP = lop
         * Mortgage = yes
         *
         * to:
         *
         * LOP = non_lop
         * Mortgage = yes
         *
         * The update must be rejected.
         */
        if (
            $validated['is_mortgaged'] === 'yes' &&
            $validated['lop_status'] !== 'lop'
        ) {
            return back()
                ->withErrors([
                    'is_mortgaged' =>
                        'Mortgage YES sirf un plots ke liye allowed hai jinka LOP status "lop" ho.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $plot) {

            LopStatus::updateOrCreate(
                [
                    'plot_id' => $plot->id,
                ],
                [
                    'lop_status' => $validated['lop_status'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );

            MortgageStatus::updateOrCreate(
                [
                    'plot_id' => $plot->id,
                ],
                [
                    'is_mortgaged' => $validated['is_mortgaged'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        });

        return redirect()
            ->route('lop-mortgage.index')
            ->with('success', 'LOP & Mortgage status updated successfully!');
    }

    /**
     * Delete LOP + Mortgage status.
     *
     * IMPORTANT:
     * This does NOT delete the Plot.
     */
    public function destroy(Plot $plot)
    {
        DB::transaction(function () use ($plot) {

            LopStatus::where('plot_id', $plot->id)->delete();

            MortgageStatus::where('plot_id', $plot->id)->delete();
        });

        return redirect()
            ->route('lop-mortgage.index')
            ->with('success', 'LOP & Mortgage status deleted successfully!');
    }
}