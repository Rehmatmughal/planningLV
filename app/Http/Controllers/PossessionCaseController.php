<?php

namespace App\Http\Controllers;

use App\Models\PossessionCase;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Owner;
use App\Models\PropertyType;


class PossessionCaseController extends Controller
{
    /**
     * Display possession cases.
     */
    public function index(Request $request)
    {
        $query = PossessionCase::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
            'creator',
            // test
            'plot.latestAreavariation',
        ])->latest();

        /*
        |--------------------------------------------------------------------------
        | Search by possession number
        |--------------------------------------------------------------------------
        */
        if ($request->filled('possession_no')) {
            $query->where(
                'possession_no',
                'like',
                '%' . $request->possession_no . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search by reference number
        |--------------------------------------------------------------------------
        */
        if ($request->filled('reference_no')) {
            $query->where(
                'reference_no',
                'like',
                '%' . $request->reference_no . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by status
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where(
                'current_status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter active/inactive
        |--------------------------------------------------------------------------
        */
        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                $request->is_active
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search by owner name
        |--------------------------------------------------------------------------
        */
        if ($request->filled('owner_name')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where(
                    'owner_name',
                    'like',
                    '%' . $request->owner_name . '%'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Search by CNIC
        |--------------------------------------------------------------------------
        */
        if ($request->filled('cnic')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where(
                    'cnic',
                    'like',
                    '%' . $request->cnic . '%'
                );
            });
        }

        $possessionCases = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'possession_cases.index',
            compact('possessionCases')
        );
    }


    /**
     * Show form for creating a new possession case.
     */
    public function create(Request $request)
    {
        $projects = Project::select('id', 'project_name')
            ->orderBy('project_name')
            ->get();

        $propertyTypes = PropertyType::orderBy('name')
            ->get();

        $selectedPlot = null;

        if ($request->filled('plot_id')) {

            $selectedPlot = Plot::with([
                'project',
                'block',
                'street',
                'size',
                'propertyType',
                // test
                'latestAreavariation',
            ])->find($request->plot_id);
        }

        return view('possession_cases.create', compact(
            'projects',
            'propertyTypes',
            'selectedPlot'
        ));
    }

    /**
     * Get blocks according to selected project and property type.
     */
    public function getBlocks(Request $request, $projectId)
    {
        $propertyTypeId = $request->property_type_id;

        $query = Block::where('project_id', $projectId);

        /*
        |--------------------------------------------------------------------------
        | Property Type Selected
        |--------------------------------------------------------------------------
        | Sirf woh blocks show honge jin mein selected
        | property type ke plots mojood hain.
        |--------------------------------------------------------------------------
        */

        if ($propertyTypeId) {

            $blockIds = Plot::where('project_id', $projectId)
                ->where('property_type_id', $propertyTypeId)
                ->pluck('block_id')
                ->unique();

            $query->whereIn('id', $blockIds);
        }

        return $query
            ->orderBy('block_name')
            ->get([
                'id',
                'block_name',
            ]);
    }

    public function getPossessionPreview($plotId)
    {
        $plot = Plot::findOrFail($plotId);

        /*
        |--------------------------------------------------------------------------
        | Find latest possession for this plot
        |--------------------------------------------------------------------------
        | withTrashed() is important because cancelled/deleted old
        | possession numbers must remain reserved.
        |--------------------------------------------------------------------------
        */

        $previousCase = PossessionCase::withTrashed()
            ->where('plot_id', $plot->id)
            ->orderByDesc('possession_sequence')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Next Sequence
        |--------------------------------------------------------------------------
        */

        $nextSequence = ($previousCase?->possession_sequence ?? 0) + 1;

        /*
        |--------------------------------------------------------------------------
        | Base Possession Number
        |--------------------------------------------------------------------------
        */

        if ($previousCase) {

            $basePossessionNo = $this->getBasePossessionNumber(
                $previousCase->possession_no
            );

        } else {

            $basePossessionNo = $this->generateBasePossessionNumber($plot);
        }

        /*
        |--------------------------------------------------------------------------
        | Final Possession Number
        |--------------------------------------------------------------------------
        */

        if ($nextSequence === 1) {

            $possessionNo = $basePossessionNo;

            $caseType = 'Possession';

        } else {

            $possessionNo =
                $basePossessionNo . '-T' . ($nextSequence - 1);

            $caseType = 'Re-Possession';
        }

        return response()->json([
            'possession_no' => $possessionNo,
            'case_type' => $caseType,
            'possession_sequence' => $nextSequence,
        ]);
    }

    /**
     * Get streets according to selected block.
     */
    public function getStreets($blockId)
    {
        $streets = Street::where(
                'block_id',
                $blockId
            )
            ->orderBy('street_name')
            ->get([
                'id',
                'street_name',
            ]);

        return response()->json($streets);
    }


    /**
     * Search plots.
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
                'exists:streets,id',
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
            'propertyType',
        ])
            ->where(
                'project_id',
                $request->project_id
            )
            ->where(
                'block_id',
                $request->block_id
            )
            ->where(
                'plot_number',
                $request->plot_number
            );

        /*
        |--------------------------------------------------------------------------
        | Optional Street
        |--------------------------------------------------------------------------
        */
        if ($request->filled('street_id')) {
            $query->where(
                'street_id',
                $request->street_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Soft deleted plots automatically excluded
        |--------------------------------------------------------------------------
        */
        $plots = $query
            ->orderBy('plot_number')
            ->limit(50)
            ->get();

        $results = $plots->map(function ($plot) {

            /*
            |--------------------------------------------------------------------------
            | Property Type Name
            |--------------------------------------------------------------------------
            | Agar PropertyType model mein name hai to name.
            | Agar title hai to title.
            */
            $propertyTypeName =
                $plot->propertyType?->name
                ?? $plot->propertyType?->title
                ?? $plot->propertyType?->property_type
                ?? '-';

            return [
                'id' => $plot->id,

                'plot_number' =>
                    $plot->plot_number,

                'project_name' =>
                    $plot->project?->project_name,

                'block_name' =>
                    $plot->block?->block_name,

                'street_name' =>
                    $plot->street?->street_name,

                'size_title' =>
                    $plot->size?->title,

                'size_area' =>
                    $plot->size?->size_area,

                'property_type_name' =>
                    $propertyTypeName,
            ];
        });

        return response()->json($results);
    }


    /**
     * Generate next base possession number.
     *
     * Numbering project + property type ke hisaab se hogi.
     *
     * Example:
     * 250
     * 251
     * 252
     */

    private function generateBasePossessionNumber(Plot $plot): string
    {
        /*
        |--------------------------------------------------------------------------
        | Lock Project
        |--------------------------------------------------------------------------
        |
        | Same Project + Property Type mein agar 2 different plots par
        | simultaneously new possession create ho rahi ho,
        | to dono ko same MAX number milne se prevent karta hai.
        |
        */
        Project::whereKey($plot->project_id)
            ->lockForUpdate()
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Find Existing Base Possession Numbers
        |--------------------------------------------------------------------------
        |
        | withTrashed() is liye use ho raha hai taake soft-deleted
        | possession cases ke numbers bhi dobara reuse na hon.
        |
        */
        $query = PossessionCase::withTrashed()
            ->whereNotNull('possession_no')
            ->whereRaw(
                "possession_no REGEXP '^[0-9]+$'"
            )
            ->whereHas('plot', function ($q) use ($plot) {

                /*
                | Soft-deleted plot ke possession records bhi
                | numbering mein count honge.
                */
                $q->withTrashed()
                    ->where(
                        'project_id',
                        $plot->project_id
                    );

                /*
                | Property Type ke hisaab se separate numbering.
                */
                if ($plot->property_type_id !== null) {

                    $q->where(
                        'property_type_id',
                        $plot->property_type_id
                    );

                } else {

                    $q->whereNull(
                        'property_type_id'
                    );
                }
            });


        /*
        |--------------------------------------------------------------------------
        | Get Highest Existing Base Number
        |--------------------------------------------------------------------------
        |
        | Example:
        | 1, 2, 3, 7, 9
        |
        | MAX = 9
        | Next = 10
        |
        | Gaps reuse nahi honge.
        |
        */
        $maxNumber = $query->max(
            DB::raw(
                'CAST(possession_no AS UNSIGNED)'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Next Number
        |--------------------------------------------------------------------------
        */
        return (string) (
            ((int) $maxNumber) + 1
        );
    }


    /**
     * Extract base possession number.
     *
     * 250       -> 250
     * 250-T1    -> 250
     * 250-T2    -> 250
     */
    private function getBasePossessionNumber(
        ?string $possessionNo
    ): ?string {

        if (!$possessionNo) {

            return null;

        }

        return preg_replace(
            '/-T\d+$/i',
            '',
            trim($possessionNo)
        );
    }

    // private function getBasePossessionNumber(
    //     ?string $possessionNo
    // ): ?string {

    //     if (!$possessionNo) {
    //         return null;
    //     }

    //     return preg_replace(
    //         '/-T\d+$/i',
    //         '',
    //         trim($possessionNo)
    //     );
    // }


    /**
     * Store a new possession case.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Plot
            |--------------------------------------------------------------------------
            */
            'plot_id' => [
                'required',
                'integer',
                Rule::exists('plots', 'id')
                    ->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Reference Number
            |--------------------------------------------------------------------------
            */
            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */
            'need_approval' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Current Holder
            |--------------------------------------------------------------------------
            */
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

            /*
            |--------------------------------------------------------------------------
            | Received Date
            |--------------------------------------------------------------------------
            */
            'received_at' => [
                'nullable',
                'date',
            ],

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */
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

            'owners.*.relative_name' => [
                'nullable',
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
        | Transaction
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Lock Plot
            |--------------------------------------------------------------------------
            |
            | Same plot par agar simultaneously possession create ho
            | to sequence control mein rahe.
            |
            */
            $plot = Plot::whereKey(
                $validated['plot_id']
            )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Find Previous Possession
            |--------------------------------------------------------------------------
            */

            // soft delete k sath wala code is mn soft delete ka possession no b reserve he ho ga resue nae hoga 
            // yani previouse possession find krty howay softdelete kia howa possession b find hoga
            $previousCase = PossessionCase::withTrashed()
                ->where(
                    'plot_id',
                    $plot->id
                )
                ->orderBy(
                    'possession_sequence',
                    'desc'
                )
                ->lockForUpdate()
                ->first();

            $nextSequence =
                // soft delete ko b sath mn find kryga awr uska number b dekhy ga
                ((int) PossessionCase::withTrashed()
                    ->where(
                // soft delete ko find nae krny ka code 
                // ((int) PossessionCase::where(
                    'plot_id',
                    $plot->id
                )->max('possession_sequence')) + 1;


            /*
            |--------------------------------------------------------------------------
            | Base Possession Number
            |--------------------------------------------------------------------------
            */
            $basePossessionNo = null;

            if ($previousCase) {
                $basePossessionNo =
                    $this->getBasePossessionNumber(
                        $previousCase->possession_no
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Agar previous case ka base number available nahi
            |--------------------------------------------------------------------------
            */
            if (
                !$basePossessionNo ||
                !ctype_digit($basePossessionNo)
            ) {
                $basePossessionNo =
                    $this->generateBasePossessionNumber(
                        $plot
                    );
            }


            if ($nextSequence === 1) {

                $possessionNo =
                    $basePossessionNo;

            } else {

                $possessionNo =
                    $basePossessionNo
                    . '-T'
                    . ($nextSequence - 1);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Possession Case
            |--------------------------------------------------------------------------
            */
            $case = PossessionCase::create([

                'plot_id' =>
                    $plot->id,

                'possession_no' =>
                    $possessionNo,

                'reference_no' =>
                    $validated['reference_no']
                    ?? null,

                'possession_sequence' =>
                    $nextSequence,

                /*
                | Initial creation always revision 0.
                */
                'revision_no' =>
                    0,

                'need_approval' =>
                    $validated['need_approval']
                    ?? false,

                'current_status' =>
                    'received',

                'current_holder_type' =>
                    $validated['current_holder_type']
                    ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id']
                    ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name']
                    ?? null,

                'received_at' =>
                    $validated['received_at']
                    ?? now()->toDateString(),

                'remarks' =>
                    $validated['remarks']
                    ?? null,

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
            foreach (
                $validated['owners']
                as $ownerData
            ) {

                $owner = null;


                /*
                |--------------------------------------------------------------------------
                | Owner ID
                |--------------------------------------------------------------------------
                */
                if (!empty($ownerData['owner_id'])) {

                    $owner = Owner::find(
                        $ownerData['owner_id']
                    );

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
                $cnic = trim(
                    $ownerData['cnic']
                );


                /*
                |--------------------------------------------------------------------------
                | Search owner by CNIC
                |--------------------------------------------------------------------------
                */
                $ownerByCnic = Owner::where(
                    'cnic',
                    $cnic
                )->first();


                /*
                |--------------------------------------------------------------------------
                | Existing CNIC
                |--------------------------------------------------------------------------
                */
                if ($ownerByCnic) {

                    /*
                    | Selected owner ID different hai
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
                        trim(
                            $ownerData['owner_name']
                        );

                    $existingName =
                        trim(
                            $ownerByCnic->owner_name
                        );

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
                    | Existing owner use karein
                    |--------------------------------------------------------------------------
                    */
                    $owner =
                        $ownerByCnic;
                }


                /*
                |--------------------------------------------------------------------------
                | New CNIC
                |--------------------------------------------------------------------------
                */
                else {

                    if ($owner) {

                        /*
                        |--------------------------------------------------------------------------
                        | Existing selected owner
                        |--------------------------------------------------------------------------
                        */
                        $owner->update([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Completely new owner
                        |--------------------------------------------------------------------------
                        */
                        $owner = Owner::create([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Attach Owner
                |--------------------------------------------------------------------------
                */
                $case->owners()->attach(
                    $owner->id,
                    [
                        'address_snapshot' =>
                            $ownerData['address']
                            ?? $owner->address,
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
                    'Possession case created. Possession No: '
                    . $case->possession_no,

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
    public function show(
        PossessionCase $possessionCase
    ) {
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
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
     * Show edit form.
     */
    public function edit(
        PossessionCase $possessionCase
    ) {
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
        ]);

        $projects = Project::orderBy(
                'project_name'
            )
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
                Rule::exists('plots', 'id')
                    ->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
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

            'owners.*.relative_name' => [
                'nullable',
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
        | Plot change prevent
        |--------------------------------------------------------------------------
        |
        | Possession number plot ke sath linked hai.
        | Is liye existing case ko doosre plot par move nahi karenge.
        */
        if (
            (int) $validated['plot_id']
            !== (int) $possessionCase->plot_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'plot_id' =>
                        'An existing possession case cannot be moved to another plot.',
                ]);
        }


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

                'reference_no' =>
                    $validated['reference_no']
                    ?? null,

                'need_approval' =>
                    $validated['need_approval']
                    ?? false,

                'current_holder_type' =>
                    $validated['current_holder_type']
                    ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id']
                    ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name']
                    ?? null,

                'received_at' =>
                    $validated['received_at']
                    ?? null,

                'remarks' =>
                    $validated['remarks']
                    ?? null,

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Existing owners
            |--------------------------------------------------------------------------
            */
            $oldOwnerIds =
                $possessionCase
                    ->owners()
                    ->pluck('owners.id')
                    ->toArray();


            $existingOwnerIds = [];


            /*
            |--------------------------------------------------------------------------
            | Process Owners
            |--------------------------------------------------------------------------
            */
            foreach (
                $validated['owners']
                as $ownerData
            ) {

                $owner = null;


                $ownerId =
                    $ownerData['owner_id']
                    ?? $ownerData['id']
                    ?? null;


                if ($ownerId) {

                    $owner = Owner::find(
                        $ownerId
                    );

                    if (!$owner) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                'Selected owner record was not found.',
                        ]);
                    }
                }


                $cnic =
                    trim(
                        $ownerData['cnic']
                    );


                $ownerByCnic =
                    Owner::where(
                        'cnic',
                        $cnic
                    )->first();


                /*
                |--------------------------------------------------------------------------
                | Existing CNIC
                |--------------------------------------------------------------------------
                */
                if ($ownerByCnic) {

                    if (
                        $owner &&
                        $owner->id
                            !== $ownerByCnic->id
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "CNIC {$cnic} is already registered with another owner: {$ownerByCnic->owner_name}. Please verify the CNIC.",
                        ]);
                    }


                    $enteredName =
                        trim(
                            $ownerData['owner_name']
                        );

                    $existingName =
                        trim(
                            $ownerByCnic->owner_name
                        );


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


                    $owner =
                        $ownerByCnic;

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | New CNIC
                    |--------------------------------------------------------------------------
                    */
                    if ($owner) {

                        $owner->update([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);

                    } else {

                        $owner =
                            Owner::create([

                                'owner_name' =>
                                    $ownerData['owner_name'],

                                'relative_name' =>
                                    $ownerData['relative_name']
                                    ?? null,

                                'cnic' =>
                                    $cnic,

                                'address' =>
                                    $ownerData['address']
                                    ?? null,

                                'contact_no' =>
                                    $ownerData['contact_no']
                                    ?? null,
                            ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Attach / update pivot
                |--------------------------------------------------------------------------
                */
                $possessionCase
                    ->owners()
                    ->syncWithoutDetaching([

                        $owner->id => [

                            'address_snapshot' =>
                                $ownerData['address']
                                ?? $owner->address,
                        ],
                    ]);


                $existingOwnerIds[] =
                    $owner->id;
            }


            /*
            |--------------------------------------------------------------------------
            | Detach removed owners
            |--------------------------------------------------------------------------
            */
            $ownersToDetach =
                array_diff(
                    $oldOwnerIds,
                    $existingOwnerIds
                );


            if (!empty($ownersToDetach)) {

                $possessionCase
                    ->owners()
                    ->detach(
                        $ownersToDetach
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Updater
            |--------------------------------------------------------------------------
            */
            $possessionCase->update([
                'updated_by' =>
                    Auth::id(),
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
                'in:received,prepared,surveyor_signed,approval,town_planner_signed,completed',
            ],

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
        | Cancelled case cannot continue workflow
        |--------------------------------------------------------------------------
        */
        if (
            $possessionCase->current_status
            === 'cancelled'
        ) {

            return back()
                ->withErrors([
                    'status' =>
                        'Cancelled possession case cannot continue through the normal workflow.',
                ]);
        }


        $currentStatus =
            $possessionCase->current_status;

        $newStatus =
            $validated['status'];


        /*
        |--------------------------------------------------------------------------
        | Allowed Workflow
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

                'surveyor_signed' => [
                    'completed',
                ],

                'completed' => [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Validate transition
        |--------------------------------------------------------------------------
        */
        if (
            !in_array(
                $newStatus,
                $allowedNextStatuses[$currentStatus]
                ?? []
            )
        ) {

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

            /*
            |--------------------------------------------------------------------------
            | Holder
            |--------------------------------------------------------------------------
            */
            $oldHolderName =
                $possessionCase
                    ->current_holder_name;

            $newHolderName =
                $validated['handed_over_to']
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | Date Field
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
            | Prepare Update Data FIRST
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
            | Save Date
            |--------------------------------------------------------------------------
            */
            if ($dateField) {

                $updateData[$dateField] =
                    now()->toDateString();
            }


            /*
            |--------------------------------------------------------------------------
            | Handover
            |--------------------------------------------------------------------------
            */
            if (!empty($newHolderName)) {

                $updateData[
                    'handed_over_to'
                ] = $newHolderName;

                $updateData[
                    'current_holder_name'
                ] = $newHolderName;
            }


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
            */
            if ($newStatus === 'completed') {

                $updateData['is_active'] =
                    false;

                $updateData['handed_over_at'] =
                    now()->toDateString();
            }


            /*
            |--------------------------------------------------------------------------
            | Update Case
            |--------------------------------------------------------------------------
            */
            $possessionCase->update(
                $updateData
            );


            /*
            |--------------------------------------------------------------------------
            | History
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


            $possessionCase
                ->histories()
                ->create([

                    'plot_id' =>
                        $possessionCase->plot_id,

                    'action' =>
                        $actionLabels[$newStatus]
                        ?? ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $newStatus
                            )
                        ),

                    'old_status' =>
                        $currentStatus,

                    'new_status' =>
                        $newStatus,

                    'old_holder' =>
                        $oldHolderName,

                    'new_holder' =>
                        $newHolderName
                        ?? $oldHolderName,

                    'handed_over_to' =>
                        $newHolderName,

                    'remarks' =>
                        $validated['remarks']
                        ?? null,

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
     * Cancel possession case.
     *
     * Cancelled possession delete nahi hoti.
     * Sirf status cancelled aur inactive hota hai.
     */
    public function cancel(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([

            'cancellation_reason' => [
                'required',
                'string',
                'min:3',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Already cancelled
        |--------------------------------------------------------------------------
        */

        if ($possessionCase->current_status === 'cancelled') {

            return back()
                ->withErrors([
                    'cancellation_reason' =>
                        'This possession case is already cancelled.',
                ]);
        }

        if ($possessionCase->current_status !== 'completed') {

            return back()
                ->withErrors([
                    'cancellation_reason' =>
                        'Only completed possession cases can be cancelled.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            $oldStatus =
                $possessionCase->current_status;

            $oldHolder =
                $possessionCase->current_holder_name;


            /*
            |--------------------------------------------------------------------------
            | Cancel Case
            |--------------------------------------------------------------------------
            */
            $possessionCase->update([

                'current_status' =>
                    'cancelled',

                'is_active' =>
                    false,

                'cancelled_at' =>
                    now()->toDateString(),

                'cancelled_by' =>
                    Auth::id(),

                'cancellation_reason' =>
                    $validated[
                        'cancellation_reason'
                    ],

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | History
            |--------------------------------------------------------------------------
            */
            $possessionCase
                ->histories()
                ->create([

                    'plot_id' =>
                        $possessionCase->plot_id,

                    'action' =>
                        'Possession Cancelled',

                    'old_status' =>
                        $oldStatus,

                    'new_status' =>
                        'cancelled',

                    'old_holder' =>
                        $oldHolder,

                    'new_holder' =>
                        $oldHolder,

                    'handed_over_to' =>
                        null,

                    'remarks' =>
                        $validated[
                            'cancellation_reason'
                        ],

                    'user_id' =>
                        Auth::id(),
                ]);
        });


        return back()
            ->with(
                'success',
                'Possession case cancelled successfully.'
            );
    }


    /**
     * Soft delete possession case.
     */
    public function destroy(
        PossessionCase $possessionCase
    ) {
        $possessionCase->update([

            'is_active' =>
                false,

            'updated_by' =>
                Auth::id(),
        ]);

        $possessionCase->delete();

        return redirect()
            ->route(
                'possession-cases.index'
            )
            ->with(
                'success',
                'Possession case deleted successfully.'
            );
    }
}