<?php

namespace App\Http\Controllers;

use App\Models\PossessionCase;
use App\Models\PossessionCaseOwner;
use App\Models\PossessionCaseHistory;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Owner;

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
        // Sirf projects load honge.
        // Tamam plots ek sath load nahi honge.
        $projects = Project::orderBy('project_name')
            ->get(['id', 'project_name']);

        $selectedPlot = null;

        // Agar kisi selected plot ke sath create page open hua ho
        // to us plot ki details load kar dein.
        $plotId = old('plot_id', $request->plot_id);

        if ($plotId) {
            $selectedPlot = Plot::with([
                'project',
                'block',
                'street',
                'size',
            ])->find($plotId);
        }

        return view('possession_cases.create', compact(
            'projects',
            'selectedPlot'
        ));
    }

    // public function create(Request $request)
    // {
        // old all plots
        // $plots = Plot::with([
        //     'project',
        //     'block',
        //     'street',
        //     // 'plotSize',
        //     'size',
        // ])->orderBy('plot_number')->get();

        // $selectedPlot = null;

        // if ($request->filled('plot_id')) {
        //     $selectedPlot = Plot::find($request->plot_id);
        // }

        // return view('possession_cases.create', compact(
        //     'plots',
        //     'selectedPlot'
        // ));
    // }
    /**
 * Get blocks according to selected project.
 */
public function getBlocks($projectId)
{
    $blocks = Block::where('project_id', $projectId)
        ->orderBy('block_name')
        ->get([
            'id',
            'block_name',
        ]);

    return response()->json($blocks);
}


/**
 * Get streets according to selected block.
 */
public function getStreets($blockId)
{
    $streets = Street::where('block_id', $blockId)
        ->orderBy('street_name')
        ->get([
            'id',
            'street_name',
        ]);

    return response()->json($streets);
}


/**
 * Search plots according to project, block,
 * optional street and plot number.
 */
    public function searchPlots(Request $request)
    {
        $request->validate([
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'block_id' => [
                'required',
                'integer',
                'exists:blocks,id',
            ],

            'street_id' => [
                'nullable',
                'integer',
            ],

            'plot_number' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $query = Plot::with([
            'project',
            'block',
            'street',
            'size',
        ])
            ->where('project_id', $request->project_id)
            ->where('block_id', $request->block_id)
            ->where('plot_number', $request->plot_number);

        // Street optional hai.
        // Agar street select ki gayi hai to us street ke plots hi search honge.
        if ($request->filled('street_id')) {
            $query->where('street_id', $request->street_id);
        }

        // Soft-deleted plots automatically nahi aayenge
        // kyun ke Plot model mein SoftDeletes use ho raha hai.
        $plots = $query
            ->orderBy('plot_number')
            ->limit(50)
            ->get();

        $results = $plots->map(function ($plot) {
            return [
                'id' => $plot->id,
                'plot_number' => $plot->plot_number,

                'project_name' => $plot->project?->project_name,

                'block_name' => $plot->block?->block_name,

                'street_name' => $plot->street?->street_name,

                'size_title' => $plot->size?->title,

                'size_area' => $plot->size?->size_area,
            ];
        });

        return response()->json($results);
    }


    /**
     * Store a new possession case.
     */
    // new store

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'plot_id' => [
            //     'required',
            //     'exists:plots,id',

            'plot_id' => [
                'required',
                'integer',
                Rule::exists('plots', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
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

            'owners.*.owner_id' => [
                'nullable',
                'integer',
                'exists:owners,id',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                // 'nullable',
                'required',
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

        ]);

        // Same case number for same plot should not exist
        $exists = PossessionCase::where('plot_id', $validated['plot_id'])
            ->where('case_no', $validated['case_no'])
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([
                    'case_no' =>
                        'This case number already exists for the selected plot.',
                ]);
        }


        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Create Possession Case
            |--------------------------------------------------------------------------
            */

            $case = PossessionCase::create([

                'plot_id' =>
                    $validated['plot_id'],

                'case_no' =>
                    $validated['case_no'],

                'need_approval' =>
                    $validated['need_approval'] ?? false,

                'current_status' =>
                    'received',

                'current_holder_type' =>
                    $validated['current_holder_type'] ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id'] ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name'] ?? null,

                'received_at' =>
                    $validated['received_at']
                        ?? now()->toDateString(),

                'remarks' =>
                    $validated['remarks'] ?? null,

                'is_active' =>
                    true,

                'created_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Save Owners
            |--------------------------------------------------------------------------
            */

            // new loop data for duplication check
            foreach ($validated['owners'] as $ownerData) {

                /*
                |--------------------------------------------------------------------------
                | Existing owner ID se owner find karein
                |--------------------------------------------------------------------------
                */

                $owner = null;

                if (!empty($ownerData['owner_id'])) {

                    $owner = Owner::find($ownerData['owner_id']);

                    if (!$owner) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' => 'Selected owner record was not found.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | CNIC se existing owner check karein
                |--------------------------------------------------------------------------
                */

                $cnic = trim($ownerData['cnic']);

                $ownerByCnic = Owner::where('cnic', $cnic)->first();

                /*
                |--------------------------------------------------------------------------
                | CNIC already kisi owner ke paas hai
                |--------------------------------------------------------------------------
                */

                if ($ownerByCnic) {

                    /*
                    | Agar owner_id bhi diya gaya hai lekin CNIC kisi
                    | different owner ka hai to error.
                    */

                    if ($owner && $owner->id !== $ownerByCnic->id) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "CNIC {$cnic} is already registered with another owner: {$ownerByCnic->owner_name}. Please verify the CNIC.",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Existing owner mil gaya.
                    | Naam compare karein.
                    |--------------------------------------------------------------------------
                    */

                    $enteredName = trim($ownerData['owner_name']);
                    $existingName = trim($ownerByCnic->owner_name);

                    if (strcasecmp($enteredName, $existingName) !== 0) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "This CNIC is already registered with the name '{$existingName}'. Please verify the CNIC and owner name.",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CNIC + Name dono match hain.
                    | Existing owner use hoga.
                    | Existing central record ko overwrite nahi karenge.
                    |--------------------------------------------------------------------------
                    */

                    $owner = $ownerByCnic;
                }

                /*
                |--------------------------------------------------------------------------
                | CNIC database mein nahi mila
                |--------------------------------------------------------------------------
                */

                else {

                    /*
                    | Agar owner_id diya gaya tha to usi owner ka CNIC
                    | update nahi karna bina verification ke.
                    | New CNIC hai to existing selected owner use kar sakte hain.
                    */

                    if ($owner) {

                        $owner->update([
                            'owner_name' => $ownerData['owner_name'],
                            'cnic' => $cnic,
                            'address' => $ownerData['address'],
                            'contact_no' => $ownerData['contact_no'],
                        ]);

                    } else {

                        $owner = Owner::create([
                            'owner_name' => $ownerData['owner_name'],
                            'cnic' => $cnic,
                            'address' => $ownerData['address'],
                            'contact_no' => $ownerData['contact_no'],
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Owner ko possession case ke sath attach karein
                |--------------------------------------------------------------------------
                */

                $case->owners()->attach(
                    $owner->id,
                    [
                        'address_snapshot' =>
                            $ownerData['address'] ?? $owner->address,
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | First History Record
            |--------------------------------------------------------------------------
            */

            $case->histories()->create([

                'plot_id' =>
                    $case->plot_id,

                'action' =>
                    'Case Received',

                'old_status' =>
                    null,

                'new_status' =>
                    'received',

                'old_holder' =>
                    null,

                'new_holder' =>
                    $case->current_holder_name,

                'handed_over_to' =>
                    null,

                'remarks' =>
                    'Possession case created.',

                'user_id' =>
                    Auth::id(),
            ]);
        });


        return redirect()
            ->route('possession-cases.index')
            ->with(
                'success',
                'Possession case created successfully.'
            );
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
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'owners',
        ]);

        // Sirf projects load honge.
        // Tamam plots ek sath load nahi honge.
        $projects = Project::orderBy('project_name')
            ->get([
                'id',
                'project_name',
            ]);

        return view(
            'possession_cases.edit',
            compact(
                'possessionCase',
                'projects'
            )
        );
    }

    // old edit
    // public function edit(PossessionCase $possessionCase)

    // {
    //     $possessionCase->load('owners');

    //     $plots = Plot::with([
    //         'project',
    //         'block',
    //         'street',
    //         // 'plotSize',
    //         'size',
    //     ])->orderBy('plot_number')->get();

    //     return view(
    //         'possession_cases.edit',
    //         compact(
    //             'possessionCase',
    //             'plots'
    //         )
    //     );
    // }

    /**
     * Update possession case.
     */

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
                'integer',
                Rule::exists('plots', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
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

            /*
            |--------------------------------------------------------------------------
            | Owners
            |--------------------------------------------------------------------------
            */

            'owners' => [
                'required',
                'array',
                'min:1',
            ],

            'owners.*.id' => [
                'nullable',
                'integer',
            ],

            'owners.*.owner_id' => [
                'nullable',
                'integer',
                'exists:owners,id',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                'required',
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | Check duplicate case number
        |--------------------------------------------------------------------------
        */

        $exists = PossessionCase::where('plot_id', $validated['plot_id'])
            ->where('case_no', $validated['case_no'])
            ->where('id', '!=', $possessionCase->id)
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([
                    'case_no' =>
                        'This case number already exists for the selected plot.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update everything inside transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update Possession Case
            |--------------------------------------------------------------------------
            */

            $possessionCase->update([

                'plot_id' =>
                    $validated['plot_id'],

                'case_no' =>
                    $validated['case_no'],

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

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Existing owners attached to this case
            |--------------------------------------------------------------------------
            */

            $oldOwnerIds = $possessionCase->owners()
                ->pluck('owners.id')
                ->toArray();


            /*
            |--------------------------------------------------------------------------
            | Owners that will remain attached
            |--------------------------------------------------------------------------
            */

            $existingOwnerIds = [];


            /*
            |--------------------------------------------------------------------------
            | Process Owners
            |--------------------------------------------------------------------------
            */

            foreach ($validated['owners'] as $ownerData) {

                $owner = null;


                /*
                |--------------------------------------------------------------------------
                | Owner ID
                |--------------------------------------------------------------------------
                |
                | Edit form se existing owner ka ID aa sakta hai.
                |
                */

                $ownerId =
                    $ownerData['owner_id']
                    ?? $ownerData['id']
                    ?? null;


                if ($ownerId) {

                    $owner = \App\Models\Owner::find($ownerId);

                    if (!$owner) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                'Selected owner record was not found.',
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CNIC
                |--------------------------------------------------------------------------
                */

                $cnic = trim($ownerData['cnic']);


                /*
                |--------------------------------------------------------------------------
                | Find owner by CNIC
                |--------------------------------------------------------------------------
                */

                $ownerByCnic = \App\Models\Owner::where(
                    'cnic',
                    $cnic
                )->first();


                /*
                |--------------------------------------------------------------------------
                | CNIC already exists
                |--------------------------------------------------------------------------
                */

                if ($ownerByCnic) {

                    /*
                    |--------------------------------------------------------------------------
                    | Selected owner ID aur CNIC kisi doosre owner ka hai
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $owner &&
                        $owner->id !== $ownerByCnic->id
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "CNIC {$cnic} is already registered with another owner: {$ownerByCnic->owner_name}. Please verify the CNIC.",
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Name safety check
                    |--------------------------------------------------------------------------
                    */

                    $enteredName =
                        trim($ownerData['owner_name']);

                    $existingName =
                        trim($ownerByCnic->owner_name);


                    if (
                        strcasecmp(
                            $enteredName,
                            $existingName
                        ) !== 0
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "This CNIC is already registered with the name '{$existingName}'. Please verify the CNIC and owner name.",
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Existing owner mil gaya
                    |--------------------------------------------------------------------------
                    */

                    $owner = $ownerByCnic;

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | CNIC does NOT exist
                    |--------------------------------------------------------------------------
                    */

                    if ($owner) {

                        /*
                        |--------------------------------------------------------------------------
                        | Existing selected owner - update it
                        |--------------------------------------------------------------------------
                        */

                        $owner->update([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address'] ?? null,

                            'contact_no' =>
                                $ownerData['contact_no'] ?? null,
                        ]);

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Completely new owner
                        |--------------------------------------------------------------------------
                        */

                        $owner = \App\Models\Owner::create([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address'] ?? null,

                            'contact_no' =>
                                $ownerData['contact_no'] ?? null,
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Update owner information
                |--------------------------------------------------------------------------
                |
                | Existing CNIC + same name:
                | central owner record ko use karenge.
                |
                | Yahan address/contact ko automatically overwrite nahi kar rahe.
                | Central owner changes Owner module se manage honge.
                |
                */

                /*
                |--------------------------------------------------------------------------
                | Attach owner to possession case
                |--------------------------------------------------------------------------
                */

                $possessionCase->owners()->syncWithoutDetaching([

                    $owner->id => [

                        'address_snapshot' =>
                            $ownerData['address']
                            ?? $owner->address,

                    ],

                ]);


                /*
                |--------------------------------------------------------------------------
                | Remember owner
                |--------------------------------------------------------------------------
                */

                $existingOwnerIds[] =
                    $owner->id;
            }


            /*
            |--------------------------------------------------------------------------
            | Remove owners deleted from edit form
            |--------------------------------------------------------------------------
            |
            | Sirf possession_case_owners se detach hoga.
            |
            | Central owners table ka record delete NAHI hoga.
            |
            */

            $ownersToDetach = array_diff(
                $oldOwnerIds,
                $existingOwnerIds
            );


            if (!empty($ownersToDetach)) {

                $possessionCase->owners()
                    ->detach($ownersToDetach);
            }


            /*
            |--------------------------------------------------------------------------
            | Update updater
            |--------------------------------------------------------------------------
            */

            $possessionCase->update([
                'updated_by' => Auth::id(),
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

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
                'in:received,prepared,surveyor_signed,approval,town_planner_signed,completed',
            ],

            // 'handed_over_to' => [
            //     'nullable',
            //     'string',
            //     'max:255',
            // ],
            
            'handed_over_to' => [
                $request->status === 'completed'
                    ? 'required'
                    : 'nullable',

                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Allowed workflow
        |--------------------------------------------------------------------------
        |
        | Without Approval:
        | Received
        |     ↓
        | Prepared
        |     ↓
        | Surveyor Signed
        |     ↓
        | Town Planner Signed
        |     ↓
        | Completed
        |
        | With Approval:
        | Received
        |     ↓
        | Prepared
        |     ↓
        | Surveyor Signed
        |     ↓
        | Approval
        |     ↓
        | Town Planner Signed
        |     ↓
        | Completed
        |
        */

        $currentStatus = $possessionCase->current_status;
        $newStatus = $validated['status'];


        /*
        |--------------------------------------------------------------------------
        | Determine next allowed status
        |--------------------------------------------------------------------------
        */

        if ($possessionCase->need_approval) {

            $allowedNextStatuses = [

                'received' => [
                    'prepared',
                ],

                'prepared' => [
                    'surveyor_signed',
                ],

                'surveyor_signed' => [
                    'approval',
                ],

                'approval' => [
                    'town_planner_signed',
                ],

                'town_planner_signed' => [
                    'completed',
                ],

                'completed' => [],

            ];

        } else {

            $allowedNextStatuses = [

                'received' => [
                    'prepared',
                ],

                'prepared' => [
                    'surveyor_signed',
                ],

                // 'surveyor_signed' => [
                //     'town_planner_signed',
                // ],

                'surveyor_signed' => [
                    'completed',
                ],

                'completed' => [],

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Check whether selected status is actually allowed
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            $newStatus,
            $allowedNextStatuses[$currentStatus] ?? []
        )) {

            return back()
                ->withErrors([
                    'status' =>
                        'Invalid status transition. Please follow the proper possession workflow.',
                ])
                ->withInput();
        }


        DB::transaction(function () use (
            $validated,
            $possessionCase,
            $currentStatus,
            $newStatus
        ) {
            $oldHolderName = $possessionCase->current_holder_name;
            $newHolderName = $validated['handed_over_to'] ?? null;
            /*
            |--------------------------------------------------------------------------
            | Holder Change During Handover
            |--------------------------------------------------------------------------
            */

            $oldHolderName = $possessionCase->current_holder_name;

            $newHolderName = $validated['handed_over_to'] ?? null;

            if (!empty($newHolderName)) {

                $updateData['current_holder_name'] = $newHolderName;

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

                'surveyor_signed' =>
                    'surveyor_signed_at',

                'approval' =>
                    'approval_sent_at',

                'town_planner_signed' =>
                    'town_planner_signed_at',

                'completed' =>
                    'completed_at',

                default =>
                    null,
            };


            /*
            |--------------------------------------------------------------------------
            | Prepare update data
            |--------------------------------------------------------------------------
            */

            $updateData = [

                'current_status' =>
                    $newStatus,

                'updated_by' =>
                    Auth::id(),

            ];


            /*
            |--------------------------------------------------------------------------
            | Save status date
            |--------------------------------------------------------------------------
            */

            if ($dateField) {

                $updateData[$dateField] =
                    now()->toDateString();
            }

            /*
            |--------------------------------------------------------------------------
            | Handed Over To / Current Holder
            |--------------------------------------------------------------------------
            */

            if (!empty($newHolderName)) {

                $updateData['handed_over_to'] = $newHolderName;

                $updateData['current_holder_name'] = $newHolderName;

            }
            // /*
            // |--------------------------------------------------------------------------
            // | Handed Over To
            // |--------------------------------------------------------------------------
            // */

            // if (!empty($validated['handed_over_to'])) {

            //     $updateData['handed_over_to'] =
            //         $validated['handed_over_to'];
            // }

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            if (!empty($validated['remarks'])) {

                $updateData['remarks'] =
                    $validated['remarks'];
            }

            /*
            |--------------------------------------------------------------------------
            | Completed
            |--------------------------------------------------------------------------
            |
            | Jab case Completed ho jaye to case inactive ho jayega.
            |
            */

            if ($newStatus === 'completed') {

                $updateData['is_active'] = false;
                $updateData['handed_over_at'] =
                    now()->toDateString();
            }

            /*
            |--------------------------------------------------------------------------
            | Update Possession Case
            |--------------------------------------------------------------------------
            */

            $possessionCase->update($updateData);


            /*
            |--------------------------------------------------------------------------
            | Create History
            |--------------------------------------------------------------------------
            */

            $actionLabels = [

                'received' =>
                    'Case Received',

                'prepared' =>
                    'Case Prepared',

                'surveyor_signed' =>
                    'Surveyor Signed',

                'approval' =>
                    'Approval Sent',

                'town_planner_signed' =>
                    'Town Planner Signed',

                'completed' =>
                    'Case Completed',

            ];


            $possessionCase->histories()->create([

                'plot_id' =>
                    $possessionCase->plot_id,

                'action' =>
                    $actionLabels[$newStatus]
                    ?? ucfirst(str_replace('_', ' ', $newStatus)),

                'old_status' =>
                    $currentStatus,

                'new_status' =>
                    $newStatus,

                'old_holder' => 
                    $oldHolderName,

                'new_holder' => $newHolderName
                    ?? $oldHolderName,

                'handed_over_to' => $newHolderName,
                    // 'old_holder' =>
                //     $possessionCase->current_holder_name,

                // 'new_holder' =>
                //     $possessionCase->current_holder_name,

                // 'handed_over_to' =>
                //     $validated['handed_over_to'] ?? null,

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
