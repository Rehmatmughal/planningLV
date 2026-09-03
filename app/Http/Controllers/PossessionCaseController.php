<?php

namespace App\Http\Controllers;

use App\Models\PossessionCase;
use App\Models\PossessionCaseOwner;
use App\Models\PossessionCaseHistory;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PossessionCaseController extends Controller
{
    /**
     * Display possession cases.
     */
    public function index(Request $request)
    {
        $query = PossessionCase::with([
            'plot',
            'owners',
            'creator',
        ])->latest();

        // Search by case number
        if ($request->filled('case_no')) {
            $query->where('case_no', $request->case_no);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        // Filter active/inactive cases
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by owner name
        if ($request->filled('owner_name')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->owner_name . '%');
            });
        }

        // Search by CNIC
        if ($request->filled('cnic')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where('cnic', 'like', '%' . $request->cnic . '%');
            });
        }

        $possessionCases = $query->paginate(20)->withQueryString();

        return view('possession_cases.index', compact('possessionCases'));
    }


    /**
     * Show form for creating a new possession case.
     */
    public function create(Request $request)
    {
        $plots = Plot::with([
            'project',
            'block',
            'street',
            // 'plotSize',
            'size',
        ])->orderBy('plot_number')->get();

        $selectedPlot = null;

        if ($request->filled('plot_id')) {
            $selectedPlot = Plot::find($request->plot_id);
        }

        return view('possession_cases.create', compact(
            'plots',
            'selectedPlot'
        ));
    }


    /**
     * Store a new possession case.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plot_id' => [
                'required',
                'exists:plots,id',
            ],

            'case_no' => [
                'required',
                'integer',
                'min:1',
            ],

            'need_approval' => [
                'nullable',
                'boolean',
            ],

            'current_holder_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'current_holder_id' => [
                'nullable',
                'integer',
            ],

            'current_holder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_at' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'owners' => [
                'required',
                'array',
                'min:1',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                'nullable',
                'string',
                'max:30',
            ],

            'owners.*.address' => [
                'nullable',
                'string',
            ],

            'owners.*.contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'owners.*.ownership_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        // Make sure same case number is not already used for this plot
        $exists = PossessionCase::where('plot_id', $validated['plot_id'])
            ->where('case_no', $validated['case_no'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'case_no' => 'This case number already exists for the selected plot.',
                ]);
        }

        DB::transaction(function () use ($validated) {

            $case = PossessionCase::create([
                'plot_id' => $validated['plot_id'],
                'case_no' => $validated['case_no'],

                'need_approval' => $validated['need_approval'] ?? false,

                'current_status' => 'received',

                'current_holder_type' =>
                    $validated['current_holder_type'] ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id'] ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name'] ?? null,

                'received_at' =>
                    $validated['received_at'] ?? now()->toDateString(),

                'remarks' =>
                    $validated['remarks'] ?? null,

                'is_active' => true,

                'created_by' => Auth::id(),
            ]);

            // Save owners
            foreach ($validated['owners'] as $owner) {

                $case->owners()->create([
                    'owner_name' =>
                        $owner['owner_name'],

                    'cnic' =>
                        $owner['cnic'] ?? null,

                    'address' =>
                        $owner['address'] ?? null,

                    'contact_no' =>
                        $owner['contact_no'] ?? null,

                    'ownership_percentage' =>
                        $owner['ownership_percentage'] ?? null,
                ]);
            }

            // First history record
            $case->histories()->create([
                'plot_id' => $case->plot_id,

                'action' => 'Case Received',

                'old_status' => null,

                'new_status' => 'received',

                'old_holder' => null,

                'new_holder' =>
                    $case->current_holder_name,

                'handed_over_to' => null,

                'remarks' =>
                    'Possession case created.',

                'user_id' => Auth::id(),
            ]);
        });

        return redirect()
            ->route('possession-cases.index')
            ->with('success', 'Possession case created successfully.');
    }


    /**
     * Display a specific possession case.
     */
    public function show(PossessionCase $possessionCase)
    {
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            // 'plot.plotSize',
            'plot.size',
            'owners',
            'histories.user',
            'creator',
            'updater',
        ]);
 
        return view(
            'possession_cases.show',
            compact('possessionCase')
        );
    }


    /**
     * Show form for editing a possession case.
     */
    public function edit(PossessionCase $possessionCase)
    {
        $possessionCase->load('owners');

        $plots = Plot::with([
            'project',
            'block',
            'street',
            // 'plotSize',
            'size',
        ])->orderBy('plot_number')->get();

        return view(
            'possession_cases.edit',
            compact(
                'possessionCase',
                'plots'
            )
        );
    }


    /**
     * Update possession case.
     */
    public function update(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([
            'plot_id' => [
                'required',
                'exists:plots,id',
            ],

            'case_no' => [
                'required',
                'integer',
                'min:1',
            ],

            'need_approval' => [
                'nullable',
                'boolean',
            ],

            'current_holder_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'current_holder_id' => [
                'nullable',
                'integer',
            ],

            'current_holder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_at' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'owners' => [
                'required',
                'array',
                'min:1',
            ],

            'owners.*.id' => [
                'nullable',
                'integer',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                'nullable',
                'string',
                'max:30',
            ],

            'owners.*.address' => [
                'nullable',
                'string',
            ],

            'owners.*.contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'owners.*.ownership_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $exists = PossessionCase::where('plot_id', $validated['plot_id'])
            ->where('case_no', $validated['case_no'])
            ->where('id', '!=', $possessionCase->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'case_no' => 'This case number already exists for the selected plot.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            $possessionCase->update([
                'plot_id' => $validated['plot_id'],
                'case_no' => $validated['case_no'],

                'need_approval' =>
                    $validated['need_approval'] ?? false,

                'current_holder_type' =>
                    $validated['current_holder_type'] ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id'] ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name'] ?? null,

                'received_at' =>
                    $validated['received_at'] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'updated_by' => Auth::id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Owners
            |--------------------------------------------------------------------------
            */

            $existingOwnerIds = [];

            foreach ($validated['owners'] as $ownerData) {

                if (!empty($ownerData['id'])) {

                    $owner = $possessionCase->owners()
                        ->where('id', $ownerData['id'])
                        ->first();

                    if ($owner) {

                        $owner->update([
                            'owner_name' =>
                                $ownerData['owner_name'],

                            'cnic' =>
                                $ownerData['cnic'] ?? null,

                            'address' =>
                                $ownerData['address'] ?? null,

                            'contact_no' =>
                                $ownerData['contact_no'] ?? null,

                            'ownership_percentage' =>
                                $ownerData['ownership_percentage'] ?? null,
                        ]);

                        $existingOwnerIds[] = $owner->id;
                    }

                } else {

                    $newOwner = $possessionCase->owners()->create([
                        'owner_name' =>
                            $ownerData['owner_name'],

                        'cnic' =>
                            $ownerData['cnic'] ?? null,

                        'address' =>
                            $ownerData['address'] ?? null,

                        'contact_no' =>
                            $ownerData['contact_no'] ?? null,

                        'ownership_percentage' =>
                            $ownerData['ownership_percentage'] ?? null,
                    ]);

                    $existingOwnerIds[] = $newOwner->id;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Remove owners deleted from edit form
            |--------------------------------------------------------------------------
            */

            $possessionCase->owners()
                ->whereNotIn('id', $existingOwnerIds)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Update timestamp/user
            |--------------------------------------------------------------------------
            */

            $possessionCase->update([
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()
            ->route(
                'possession-cases.show',
                $possessionCase
            )
            ->with(
                'success',
                'Possession case updated successfully.'
            );
    }


    /**
     * Update case status.
     */
    public function updateStatus(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:received,prepared,signed,approval,receive_back,handed_over,completed',
            ],

            'handed_over_to' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            $oldStatus = $possessionCase->current_status;

            $newStatus = $validated['status'];

            // Do not create unnecessary history
            if ($oldStatus === $newStatus) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Date field according to status
            |--------------------------------------------------------------------------
            */

            $dateField = match ($newStatus) {

                'received' =>
                    'received_at',

                'prepared' =>
                    'prepared_at',

                'signed' =>
                    'signed_at',

                'approval' =>
                    'approval_sent_at',

                'receive_back' =>
                    'received_back_at',

                'handed_over' =>
                    'handed_over_at',

                'completed' =>
                    'completed_at',

                default => null,
            };

            $updateData = [
                'current_status' => $newStatus,
                'updated_by' => Auth::id(),
            ];

            if ($dateField) {
                $updateData[$dateField] = now()->toDateString();
            }

            if (!empty($validated['handed_over_to'])) {

                $updateData['handed_over_to'] =
                    $validated['handed_over_to'];
            }

            if (!empty($validated['remarks'])) {

                $updateData['remarks'] =
                    $validated['remarks'];
            }

            /*
            |--------------------------------------------------------------------------
            | Mark completed case inactive
            |--------------------------------------------------------------------------
            */

            if ($newStatus === 'completed') {
                $updateData['is_active'] = false;
            }

            $possessionCase->update($updateData);

            /*
            |--------------------------------------------------------------------------
            | History
            |--------------------------------------------------------------------------
            */

            $possessionCase->histories()->create([
                'plot_id' =>
                    $possessionCase->plot_id,

                'action' =>
                    ucfirst(str_replace('_', ' ', $newStatus)),

                'old_status' =>
                    $oldStatus,

                'new_status' =>
                    $newStatus,

                'old_holder' =>
                    $possessionCase->current_holder_name,

                'new_holder' =>
                    $possessionCase->current_holder_name,

                'handed_over_to' =>
                    $validated['handed_over_to'] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'user_id' =>
                    Auth::id(),
            ]);
        });

        return back()
            ->with(
                'success',
                'Possession case status updated successfully.'
            );
    }


    /**
     * Soft delete possession case.
     */
    public function destroy(PossessionCase $possessionCase)
    {
        $possessionCase->update([
            'is_active' => false,
            'updated_by' => Auth::id(),
        ]);

        $possessionCase->delete();

        return redirect()
            ->route('possession-cases.index')
            ->with(
                'success',
                'Possession case deleted successfully.'
            );
    }
}
