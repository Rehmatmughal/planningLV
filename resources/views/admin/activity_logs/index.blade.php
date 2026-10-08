@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Activity Logs
    </h2>


    {{-- =========================================================
        FILTERS
    ========================================================== --}}

    <form method="GET" class="row mb-4">

        <div class="col-md-3">

            <label>User</label>

            <select name="user_id" class="form-control">

                <option value="">
                    All Users
                </option>

                @foreach($users as $user)

                    <option
                        value="{{ $user->id }}"
                        {{ request('user_id') == $user->id ? 'selected' : '' }}
                    >
                        {{ $user->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-3">

            <label>Model</label>

            <select name="model" class="form-control">

                <option value="">
                    All Models
                </option>

                @foreach($models as $model)

                    <option
                        value="{{ $model }}"
                        {{ request('model') == $model ? 'selected' : '' }}
                    >
                        {{ class_basename($model) }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-2">

            <label>From</label>

            <input
                type="date"
                name="from_date"
                value="{{ request('from_date') }}"
                class="form-control"
            >

        </div>


        <div class="col-md-2">

            <label>To</label>

            <input
                type="date"
                name="to_date"
                value="{{ request('to_date') }}"
                class="form-control"
            >

        </div>


        <div class="col-md-2 d-flex align-items-end">

            <button class="btn btn-primary w-100">
                Filter
            </button>

        </div>

    </form>



    {{-- =========================================================
        ACTIVITY LOGS
    ========================================================== --}}

    <div class="card">

        <div class="card-body">

            <a
                href="{{ route('activity.logs.export') }}"
                class="btn btn-success mb-3"
            >
                Export to Excel
            </a>


            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Description</th>
                            <th>Changes</th>
                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($activities as $activity)

                            @php

                                $properties =
                                    $activity->properties ?? collect();


                                /*
                                |--------------------------------------------------------------------------
                                | Normal model changes
                                |--------------------------------------------------------------------------
                                */

                                $attributes =
                                    $properties['attributes'] ?? [];

                                $old =
                                    $properties['old'] ?? [];


                                /*
                                |--------------------------------------------------------------------------
                                | Contexts
                                |--------------------------------------------------------------------------
                                */

                                $plotContext =
                                    $properties['plot_context']
                                    ?? null;

                                $possessionContext =
                                    $properties['possession_context']
                                    ?? null;

                                $ownerChanges =
                                    $properties['owner_changes']
                                    ?? null;

                                $ownerContext =
                                    $properties['owner_context']
                                    ?? null;

                                $areaVariationContext =
                                    $properties['area_variation_context']
                                    ?? null;


                                /*
                                |--------------------------------------------------------------------------
                                | Fields which should NOT be displayed
                                |--------------------------------------------------------------------------
                                */

                                $hiddenActivityFields = [

                                    'id',

                                    'plot_id',
                                    'project_id',
                                    'block_id',
                                    'street_id',
                                    'property_type_id',
                                    'size_id',

                                    'created_at',
                                    'updated_at',
                                    'deleted_at',

                                ];


                                /*
                                |--------------------------------------------------------------------------
                                | Area Variation workflow status
                                |--------------------------------------------------------------------------
                                */

                                $workflowStatusLabels = [

                                    1 => 'Pending',

                                    2 => 'Ready for Print',

                                    3 => 'Printed',

                                ];

                            @endphp


                            {{-- =====================================================
                                ONE ACTIVITY = ONE ROW
                            ====================================================== --}}

                            <tr>


                                {{-- =================================================
                                    DATE
                                ================================================== --}}

                                <td>

                                    {{ $activity->created_at->format('d-m-Y H:i') }}

                                </td>


                                {{-- =================================================
                                    USER
                                ================================================== --}}

                                <td>

                                    {{ optional($activity->causer)->name ?? 'System' }}

                                </td>


                                {{-- =================================================
                                    ACTION
                                ================================================== --}}

                                <td>

                                    {{ ucfirst($activity->event ?? 'Activity') }}

                                </td>


                                {{-- =================================================
                                    MODEL
                                ================================================== --}}

                                <td>

                                    {{ class_basename($activity->subject_type ?? '') }}

                                </td>


                                {{-- =================================================
                                    DESCRIPTION
                                ================================================== --}}

                                <td>

                                    {{ $activity->description }}

                                </td>


                                {{-- =================================================
                                    CHANGES
                                ================================================== --}}

                                <td>


                                    {{-- =========================================
                                        POSSESSION CONTEXT
                                    ========================================== --}}

                                    @if($possessionContext)

                                        <div class="small mb-2">

                                            @if(!empty($possessionContext['possession_no']))

                                                <div>

                                                    <strong>
                                                        Possession:
                                                    </strong>

                                                    {{ $possessionContext['possession_no'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['project_name']))

                                                <div>

                                                    <strong>
                                                        Project:
                                                    </strong>

                                                    {{ $possessionContext['project_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['block_name']))

                                                <div>

                                                    <strong>
                                                        Block:
                                                    </strong>

                                                    {{ $possessionContext['block_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['street_name']))

                                                <div>

                                                    <strong>
                                                        Street:
                                                    </strong>

                                                    {{ $possessionContext['street_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['plot_number']))

                                                <div>

                                                    <strong>
                                                        Plot:
                                                    </strong>

                                                    {{ $possessionContext['plot_number'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['property_type_name']))

                                                <div>

                                                    <strong>
                                                        Property Type:
                                                    </strong>

                                                    {{ $possessionContext['property_type_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($possessionContext['size_title']))

                                                <div>

                                                    <strong>
                                                        Size:
                                                    </strong>

                                                    {{ $possessionContext['size_title'] }}

                                                </div>

                                            @endif

                                        </div>

                                    @endif



                                    {{-- =========================================
                                        PLOT CONTEXT
                                    ========================================== --}}

                                    @if($plotContext)

                                        <div class="small mb-2">

                                            @if(!empty($plotContext['project_name']))

                                                <div>

                                                    <strong>
                                                        Project:
                                                    </strong>

                                                    {{ $plotContext['project_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($plotContext['block_name']))

                                                <div>

                                                    <strong>
                                                        Block:
                                                    </strong>

                                                    {{ $plotContext['block_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($plotContext['street_name']))

                                                <div>

                                                    <strong>
                                                        Street:
                                                    </strong>

                                                    {{ $plotContext['street_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($plotContext['plot_number']))

                                                <div>

                                                    <strong>
                                                        Plot:
                                                    </strong>

                                                    {{ $plotContext['plot_number'] }}

                                                </div>

                                            @endif


                                            @if(!empty($plotContext['property_type_name']))

                                                <div>

                                                    <strong>
                                                        Property Type:
                                                    </strong>

                                                    {{ $plotContext['property_type_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($plotContext['size_title']))

                                                <div>

                                                    <strong>
                                                        Size:
                                                    </strong>

                                                    {{ $plotContext['size_title'] }}

                                                </div>

                                            @endif

                                        </div>

                                    @endif



                                    {{-- =========================================
                                        AREA VARIATION CONTEXT
                                    ========================================== --}}

                                    @if($areaVariationContext)

                                        <div class="small mb-2">

                                            @if(!empty($areaVariationContext['project_name']))

                                                <div>

                                                    <strong>
                                                        Project:
                                                    </strong>

                                                    {{ $areaVariationContext['project_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($areaVariationContext['block_name']))

                                                <div>

                                                    <strong>
                                                        Block:
                                                    </strong>

                                                    {{ $areaVariationContext['block_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($areaVariationContext['street_name']))

                                                <div>

                                                    <strong>
                                                        Street:
                                                    </strong>

                                                    {{ $areaVariationContext['street_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($areaVariationContext['plot_number']))

                                                <div>

                                                    <strong>
                                                        Plot:
                                                    </strong>

                                                    {{ $areaVariationContext['plot_number'] }}

                                                </div>

                                            @endif


                                            @if(!empty($areaVariationContext['property_type_name']))

                                                <div>

                                                    <strong>
                                                        Property Type:
                                                    </strong>

                                                    {{ $areaVariationContext['property_type_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($areaVariationContext['size_title']))

                                                <div>

                                                    <strong>
                                                        Size:
                                                    </strong>

                                                    {{ $areaVariationContext['size_title'] }}

                                                </div>

                                            @endif

                                        </div>

                                    @endif



                                    {{-- =========================================
                                        OWNER CONTEXT
                                    ========================================== --}}

                                    @if($ownerContext)

                                        <div class="small mb-2">

                                            @if(!empty($ownerContext['owner_name']))

                                                <div>

                                                    <strong>
                                                        Owner:
                                                    </strong>

                                                    {{ $ownerContext['owner_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($ownerContext['relative_name']))

                                                <div>

                                                    <strong>
                                                        Relative Name:
                                                    </strong>

                                                    {{ $ownerContext['relative_name'] }}

                                                </div>

                                            @endif


                                            @if(!empty($ownerContext['cnic']))

                                                <div>

                                                    <strong>
                                                        CNIC:
                                                    </strong>

                                                    {{ $ownerContext['cnic'] }}

                                                </div>

                                            @endif

                                        </div>

                                    @endif



                                    {{-- =========================================
                                        OWNER CHANGES
                                    ========================================== --}}

                                    @if(
                                        $ownerChanges &&
                                        (
                                            !empty($ownerChanges['attached']) ||
                                            !empty($ownerChanges['detached'])
                                        )
                                    )


                                        {{-- Attached --}}

                                        @if(!empty($ownerChanges['attached']))

                                            <div class="small mb-2">

                                                <strong class="text-success">
                                                    Attached:
                                                </strong>


                                                @foreach(
                                                    $ownerChanges['attached']
                                                    as $owner
                                                )

                                                    <div class="mt-1">

                                                        <span class="text-success">

                                                            {{ $owner['owner_name'] ?? 'Unknown Owner' }}

                                                        </span>


                                                        @if(!empty($owner['cnic']))

                                                            <small class="text-muted">

                                                                — {{ $owner['cnic'] }}

                                                            </small>

                                                        @endif

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif



                                        {{-- Detached --}}

                                        @if(!empty($ownerChanges['detached']))

                                            <div class="small">

                                                <strong class="text-danger">
                                                    Detached:
                                                </strong>


                                                @foreach(
                                                    $ownerChanges['detached']
                                                    as $owner
                                                )

                                                    <div class="mt-1">

                                                        <span class="text-danger">

                                                            {{ $owner['owner_name'] ?? 'Unknown Owner' }}

                                                        </span>


                                                        @if(!empty($owner['cnic']))

                                                            <small class="text-muted">

                                                                — {{ $owner['cnic'] }}

                                                            </small>

                                                        @endif

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    @endif



                                    {{-- =========================================
                                        NORMAL MODEL CHANGES
                                    ========================================== --}}

                                    @if(count($attributes) > 0)

                                        @foreach($attributes as $key => $value)

                                            @if(!in_array($key, $hiddenActivityFields))

                                                @php

                                                    $oldValue =
                                                        $old[$key]
                                                        ?? null;


                                                    $displayKey =
                                                        ucwords(
                                                            str_replace(
                                                                ['_', '-'],
                                                                ' ',
                                                                $key
                                                            )
                                                        );


                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | Workflow Status
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    if (
                                                        $key === 'workflow_status' &&
                                                        $areaVariationContext
                                                    ) {

                                                        $displayKey =
                                                            'Workflow Status';


                                                        $value =
                                                            $workflowStatusLabels[$value]
                                                            ?? $value;


                                                        if (
                                                            array_key_exists(
                                                                $key,
                                                                $old
                                                            )
                                                        ) {

                                                            $oldValue =
                                                                $workflowStatusLabels[$oldValue]
                                                                ?? $oldValue;

                                                        }

                                                    }


                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | Measured By
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    if (
                                                        $key === 'measured_by' &&
                                                        $areaVariationContext
                                                    ) {

                                                        $displayKey =
                                                            'Measured By';


                                                        $value =
                                                            $areaVariationContext['measured_by_name']
                                                            ?? $value;

                                                    }


                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | Measured Date
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    if (
                                                        $key === 'measured_date'
                                                    ) {

                                                        $displayKey =
                                                            'Measured Date';


                                                        if (
                                                            !empty($value)
                                                        ) {

                                                            try {

                                                                $value =
                                                                    \Carbon\Carbon::parse(
                                                                        $value
                                                                    )->format('d-m-Y');

                                                            } catch (
                                                                \Throwable $e
                                                            ) {

                                                            }

                                                        }


                                                        if (
                                                            array_key_exists(
                                                                $key,
                                                                $old
                                                            ) &&
                                                            !empty($oldValue)
                                                        ) {

                                                            try {

                                                                $oldValue =
                                                                    \Carbon\Carbon::parse(
                                                                        $oldValue
                                                                    )->format('d-m-Y');

                                                            } catch (
                                                                \Throwable $e
                                                            ) {

                                                            }

                                                        }

                                                    }

                                                @endphp


                                                <div class="small mb-1">

                                                    <strong>
                                                        {{ $displayKey }}:
                                                    </strong>


                                                    @if(
                                                        array_key_exists(
                                                            $key,
                                                            $old
                                                        )
                                                    )

                                                        <span class="text-danger">

                                                            {{
                                                                is_array($oldValue)
                                                                ? json_encode(
                                                                    $oldValue
                                                                )
                                                                : (
                                                                    $oldValue === null ||
                                                                    $oldValue === ''
                                                                    ? '—'
                                                                    : $oldValue
                                                                )
                                                            }}

                                                        </span>


                                                        <span class="mx-1">
                                                            →
                                                        </span>

                                                    @endif


                                                    <span class="text-success">

                                                        {{
                                                            is_array($value)
                                                            ? json_encode(
                                                                $value
                                                            )
                                                            : (
                                                                $value === null ||
                                                                $value === ''
                                                                ? '—'
                                                                : $value
                                                            )
                                                        }}

                                                    </span>

                                                </div>

                                            @endif

                                        @endforeach

                                    @endif



                                    {{-- =========================================
                                        NOTHING TO SHOW
                                    ========================================== --}}

                                    @if(
                                        count($attributes) === 0 &&
                                        empty($plotContext) &&
                                        empty($possessionContext) &&
                                        empty($areaVariationContext) &&
                                        empty($ownerContext) &&
                                        (
                                            !$ownerChanges ||
                                            (
                                                empty($ownerChanges['attached']) &&
                                                empty($ownerChanges['detached'])
                                            )
                                        )
                                    )

                                        <span class="text-muted">
                                            No changes
                                        </span>

                                    @endif

                                </td>



                                {{-- =================================================
                                    VIEW BUTTON
                                ================================================== --}}

                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#logModal{{ $activity->id }}"
                                    >
                                        View
                                    </button>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center"
                                >
                                    No activity found
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{ $activities->withQueryString()->links() }}

        </div>

    </div>

</div>



{{-- =========================================================
    MODALS
    IMPORTANT:
    MODALS TABLE KE BAHAR HAIN
========================================================= --}}

@foreach($activities as $activity)

    @php

        $properties =
            $activity->properties ?? collect();


        /*
        |--------------------------------------------------------------------------
        | Normal changes
        |--------------------------------------------------------------------------
        */

        $attributes =
            $properties['attributes'] ?? [];

        $old =
            $properties['old'] ?? [];


        /*
        |--------------------------------------------------------------------------
        | Contexts
        |--------------------------------------------------------------------------
        */

        $plotContext =
            $properties['plot_context']
            ?? null;

        $possessionContext =
            $properties['possession_context']
            ?? null;

        $ownerChanges =
            $properties['owner_changes']
            ?? null;

        $ownerContext =
            $properties['owner_context']
            ?? null;

        $areaVariationContext =
            $properties['area_variation_context']
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | Hidden fields
        |--------------------------------------------------------------------------
        */

        $hiddenActivityFields = [

            'id',

            'plot_id',
            'project_id',
            'block_id',
            'street_id',
            'property_type_id',
            'size_id',

            'created_at',
            'updated_at',
            'deleted_at',

        ];


        /*
        |--------------------------------------------------------------------------
        | Workflow labels
        |--------------------------------------------------------------------------
        */

        $workflowStatusLabels = [

            1 => 'Pending',

            2 => 'Ready for Print',

            3 => 'Printed',

        ];


        /*
        |--------------------------------------------------------------------------
        | Plot context for modal
        |--------------------------------------------------------------------------
        |
        | Possession, Plot aur Area Variation tino ke liye
        | same Plot Information section use hoga.
        |
        */

        $modalPlotContext =
            $possessionContext
            ?? $plotContext
            ?? $areaVariationContext;

    @endphp


    <div
        class="modal fade"
        id="logModal{{ $activity->id }}"
        tabindex="-1"
        aria-labelledby="logModalLabel{{ $activity->id }}"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">


                {{-- =====================================================
                    HEADER
                ====================================================== --}}

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="logModalLabel{{ $activity->id }}"
                    >
                        Activity Detail
                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>



                {{-- =====================================================
                    BODY
                ====================================================== --}}

                <div class="modal-body">


                    {{-- =====================================================
                        BASIC ACTIVITY INFORMATION
                    ====================================================== --}}

                    <h6 class="mb-3">
                        Activity Information
                    </h6>


                    <div class="row mb-3">


                        <div class="col-md-6">

                            <strong>
                                User:
                            </strong>

                            {{ optional($activity->causer)->name ?? 'System' }}

                        </div>


                        <div class="col-md-6">

                            <strong>
                                Date/Time:
                            </strong>

                            {{ $activity->created_at->format('d-m-Y H:i:s') }}

                        </div>


                        <div class="col-md-6 mt-2">

                            <strong>
                                Action:
                            </strong>

                            {{ ucfirst($activity->event ?? 'Activity') }}

                        </div>


                        <div class="col-md-6 mt-2">

                            <strong>
                                Model:
                            </strong>

                            {{ class_basename($activity->subject_type ?? '') }}

                        </div>

                    </div>



                    <div class="mb-3">

                        <strong>
                            Description:
                        </strong>

                        <div class="mt-1">

                            {{ $activity->description }}

                        </div>

                    </div>



                    <hr>



                    {{-- =====================================================
                        POSSESSION INFORMATION
                    ====================================================== --}}

                    @if($possessionContext)

                        <h6 class="mb-3">
                            Possession Information
                        </h6>


                        <div class="row">


                            {{-- Possession No --}}

                            @if(!empty($possessionContext['possession_no']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Possession No:
                                    </strong>

                                    {{ $possessionContext['possession_no'] }}

                                </div>

                            @endif


                            {{-- Reference No --}}

                            @if(!empty($possessionContext['reference_no']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Reference No:
                                    </strong>

                                    {{ $possessionContext['reference_no'] }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- =====================================================
                        PLOT INFORMATION
                    ====================================================== --}}

                    @if($modalPlotContext)

                        <h6 class="mb-3">
                            Plot Information
                        </h6>


                        <div class="row">


                            {{-- Project --}}

                            @if(!empty($modalPlotContext['project_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Project:
                                    </strong>

                                    {{ $modalPlotContext['project_name'] }}

                                </div>

                            @endif


                            {{-- Block --}}

                            @if(!empty($modalPlotContext['block_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Block:
                                    </strong>

                                    {{ $modalPlotContext['block_name'] }}

                                </div>

                            @endif


                            {{-- Street --}}

                            @if(!empty($modalPlotContext['street_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Street:
                                    </strong>

                                    {{ $modalPlotContext['street_name'] }}

                                </div>

                            @endif


                            {{-- Plot Number --}}

                            @if(!empty($modalPlotContext['plot_number']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Plot Number:
                                    </strong>

                                    {{ $modalPlotContext['plot_number'] }}

                                </div>

                            @endif


                            {{-- Property Type --}}

                            @if(!empty($modalPlotContext['property_type_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Property Type:
                                    </strong>

                                    {{ $modalPlotContext['property_type_name'] }}

                                </div>

                            @endif


                            {{-- Plot Size --}}

                            @if(!empty($modalPlotContext['size_title']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Plot Size:
                                    </strong>

                                    {{ $modalPlotContext['size_title'] }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- =====================================================
                        AREA VARIATION INFORMATION
                    ====================================================== --}}

                    @if($areaVariationContext)

                        <h6 class="mb-3">
                            Area Variation Information
                        </h6>


                        <div class="row">


                            {{-- Previous Area --}}

                            @if(
                                array_key_exists(
                                    'previous_area',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Previous Area:
                                    </strong>

                                    {{ $attributes['previous_area'] ?? '—' }}

                                </div>

                            @endif


                            {{-- Measured Area --}}

                            @if(
                                array_key_exists(
                                    'measured_area',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Measured Area:
                                    </strong>

                                    {{ $attributes['measured_area'] ?? '—' }}

                                </div>

                            @endif


                            {{-- Measured By --}}

                            @if(
                                array_key_exists(
                                    'measured_by',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Measured By:
                                    </strong>

                                    {{ $areaVariationContext['measured_by_name'] ?? $attributes['measured_by'] ?? '—' }}

                                </div>

                            @endif


                            {{-- Measured Date --}}

                            @if(
                                array_key_exists(
                                    'measured_date',
                                    $attributes
                                )
                            )

                                @php

                                    $modalMeasuredDate =
                                        $attributes['measured_date']
                                        ?? null;

                                    if ($modalMeasuredDate) {

                                        try {

                                            $modalMeasuredDate =
                                                \Carbon\Carbon::parse(
                                                    $modalMeasuredDate
                                                )->format('d-m-Y');

                                        } catch (
                                            \Throwable $e
                                        ) {

                                        }

                                    }

                                @endphp


                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Measured Date:
                                    </strong>

                                    {{ $modalMeasuredDate ?: '—' }}

                                </div>

                            @endif


                            {{-- Road Status --}}

                            @if(
                                array_key_exists(
                                    'road_status_at_time',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Road Status At Time:
                                    </strong>

                                    {{ $attributes['road_status_at_time'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Sewer Status --}}

                            @if(
                                array_key_exists(
                                    'sewer_status_at_time',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Sewer Status At Time:
                                    </strong>

                                    {{ $attributes['sewer_status_at_time'] ?: '—' }}

                                </div>

                            @endif


                            {{-- LOP Status --}}

                            @if(
                                array_key_exists(
                                    'lop_status_at_time',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        LOP Status At Time:
                                    </strong>

                                    {{ $attributes['lop_status_at_time'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Overall Status --}}

                            @if(
                                array_key_exists(
                                    'overall_status_at_time',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Overall Status At Time:
                                    </strong>

                                    {{ $attributes['overall_status_at_time'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Mortgage Status --}}

                            @if(
                                array_key_exists(
                                    'mortgage_status_at_time',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Mortgage Status At Time:
                                    </strong>

                                    {{ $attributes['mortgage_status_at_time'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Possession Status --}}

                            @if(
                                array_key_exists(
                                    'possession_status',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Possession Status:
                                    </strong>

                                    {{ $attributes['possession_status'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Workflow Status --}}

                            @if(
                                array_key_exists(
                                    'workflow_status',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Workflow Status:
                                    </strong>

                                    {{
                                        $workflowStatusLabels[
                                            $attributes['workflow_status']
                                        ]
                                        ?? $attributes['workflow_status']
                                        ?? '—'
                                    }}

                                </div>

                            @endif


                            {{-- Remarks --}}

                            @if(
                                array_key_exists(
                                    'remarks',
                                    $attributes
                                )
                            )

                                <div class="col-md-12 mb-2">

                                    <strong>
                                        Remarks:
                                    </strong>

                                    {{ $attributes['remarks'] ?: '—' }}

                                </div>

                            @endif


                            {{-- Source --}}

                            @if(
                                array_key_exists(
                                    'source',
                                    $attributes
                                )
                            )

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Source:
                                    </strong>

                                    {{ $attributes['source'] ?: '—' }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- =====================================================
                        OWNER CONTEXT
                    ====================================================== --}}

                    @if($ownerContext)

                        <h6 class="mb-3">
                            Owner Information
                        </h6>


                        <div class="row">


                            @if(!empty($ownerContext['owner_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Owner:
                                    </strong>

                                    {{ $ownerContext['owner_name'] }}

                                </div>

                            @endif


                            @if(!empty($ownerContext['relative_name']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Relative Name:
                                    </strong>

                                    {{ $ownerContext['relative_name'] }}

                                </div>

                            @endif


                            @if(!empty($ownerContext['cnic']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        CNIC:
                                    </strong>

                                    {{ $ownerContext['cnic'] }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- =====================================================
                        OWNER CHANGES
                    ====================================================== --}}

                    @if(
                        $ownerChanges &&
                        (
                            !empty($ownerChanges['attached']) ||
                            !empty($ownerChanges['detached'])
                        )
                    )

                        <h6 class="mb-3">
                            Owner Changes
                        </h6>


                        {{-- =============================================
                            ATTACHED OWNERS
                        ============================================== --}}

                        @if(!empty($ownerChanges['attached']))

                            <div class="mb-4">

                                <h6 class="text-success">
                                    Attached Owners
                                </h6>


                                <div class="table-responsive">

                                    <table class="table table-bordered table-sm">

                                        <thead>

                                            <tr>

                                                <th>
                                                    Owner
                                                </th>

                                                <th>
                                                    Relative Name
                                                </th>

                                                <th>
                                                    CNIC
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach(
                                                $ownerChanges['attached']
                                                as $owner
                                            )

                                                <tr>

                                                    <td>
                                                        {{ $owner['owner_name'] ?? '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $owner['relative_name'] ?? '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $owner['cnic'] ?? '—' }}
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        @endif



                        {{-- =============================================
                            DETACHED OWNERS
                        ============================================== --}}

                        @if(!empty($ownerChanges['detached']))

                            <div class="mb-4">

                                <h6 class="text-danger">
                                    Detached Owners
                                </h6>


                                <div class="table-responsive">

                                    <table class="table table-bordered table-sm">

                                        <thead>

                                            <tr>

                                                <th>
                                                    Owner
                                                </th>

                                                <th>
                                                    Relative Name
                                                </th>

                                                <th>
                                                    CNIC
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            @foreach(
                                                $ownerChanges['detached']
                                                as $owner
                                            )

                                                <tr>

                                                    <td>
                                                        {{ $owner['owner_name'] ?? '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $owner['relative_name'] ?? '—' }}
                                                    </td>

                                                    <td>
                                                        {{ $owner['cnic'] ?? '—' }}
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        @endif

                    @endif



                    {{-- =====================================================
                        NORMAL CHANGED FIELDS
                    ====================================================== --}}

                    @php

                        $visibleAttributes = [];

                        foreach ($attributes as $key => $value) {

                            if (
                                !in_array(
                                    $key,
                                    $hiddenActivityFields
                                )
                            ) {

                                $visibleAttributes[$key] = $value;

                            }

                        }

                    @endphp


                    @if(count($visibleAttributes) > 0)

                        <h6 class="mb-3">
                            Changed Fields
                        </h6>


                        <div class="table-responsive">

                            <table class="table table-bordered table-sm">

                                <thead>

                                    <tr>

                                        <th>
                                            Field
                                        </th>

                                        <th>
                                            Old Value
                                        </th>

                                        <th>
                                            New Value
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach(
                                        $visibleAttributes
                                        as $key => $value
                                    )

                                        @php

                                            $oldValue =
                                                $old[$key]
                                                ?? null;


                                            $displayKey =
                                                ucwords(
                                                    str_replace(
                                                        ['_', '-'],
                                                        ' ',
                                                        $key
                                                    )
                                                );


                                            /*
                                            |--------------------------------------------------------------------------
                                            | Workflow Status
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $key === 'workflow_status' &&
                                                $areaVariationContext
                                            ) {

                                                $displayKey =
                                                    'Workflow Status';


                                                $value =
                                                    $workflowStatusLabels[$value]
                                                    ?? $value;


                                                if (
                                                    array_key_exists(
                                                        $key,
                                                        $old
                                                    )
                                                ) {

                                                    $oldValue =
                                                        $workflowStatusLabels[$oldValue]
                                                        ?? $oldValue;

                                                }

                                            }


                                            /*
                                            |--------------------------------------------------------------------------
                                            | Measured By
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $key === 'measured_by' &&
                                                $areaVariationContext
                                            ) {

                                                $displayKey =
                                                    'Measured By';


                                                $value =
                                                    $areaVariationContext['measured_by_name']
                                                    ?? $value;


                                                /*
                                                | If old measured_by is an ID,
                                                | try to show the old user's name.
                                                */

                                                if (
                                                    array_key_exists(
                                                        $key,
                                                        $old
                                                    ) &&
                                                    !empty($oldValue)
                                                ) {

                                                    try {

                                                        $oldUser =
                                                            \App\Models\User::find(
                                                                $oldValue
                                                            );

                                                        if ($oldUser) {

                                                            $oldValue =
                                                                $oldUser->name;

                                                        }

                                                    } catch (
                                                        \Throwable $e
                                                    ) {

                                                    }

                                                }

                                            }


                                            /*
                                            |--------------------------------------------------------------------------
                                            | Measured Date
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $key === 'measured_date'
                                            ) {

                                                $displayKey =
                                                    'Measured Date';


                                                if (!empty($value)) {

                                                    try {

                                                        $value =
                                                            \Carbon\Carbon::parse(
                                                                $value
                                                            )->format('d-m-Y');

                                                    } catch (
                                                        \Throwable $e
                                                    ) {

                                                    }

                                                }


                                                if (
                                                    array_key_exists(
                                                        $key,
                                                        $old
                                                    ) &&
                                                    !empty($oldValue)
                                                ) {

                                                    try {

                                                        $oldValue =
                                                            \Carbon\Carbon::parse(
                                                                $oldValue
                                                            )->format('d-m-Y');

                                                    } catch (
                                                        \Throwable $e
                                                    ) {

                                                    }

                                                }

                                            }

                                        @endphp


                                        <tr>


                                            {{-- Field --}}

                                            <td>

                                                <strong>
                                                    {{ $displayKey }}
                                                </strong>

                                            </td>


                                            {{-- Old Value --}}

                                            <td class="text-danger">

                                                @if(
                                                    array_key_exists(
                                                        $key,
                                                        $old
                                                    )
                                                )

                                                    {{
                                                        is_array($oldValue)
                                                        ? json_encode(
                                                            $oldValue,
                                                            JSON_PRETTY_PRINT
                                                        )
                                                        : (
                                                            $oldValue === null ||
                                                            $oldValue === ''
                                                            ? '—'
                                                            : $oldValue
                                                        )
                                                    }}

                                                @else

                                                    —

                                                @endif

                                            </td>


                                            {{-- New Value --}}

                                            <td class="text-success">

                                                {{
                                                    is_array($value)
                                                    ? json_encode(
                                                        $value,
                                                        JSON_PRETTY_PRINT
                                                    )
                                                    : (
                                                        $value === null ||
                                                        $value === ''
                                                        ? '—'
                                                        : $value
                                                    )
                                                }}

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @endif



                    {{-- =====================================================
                        NO CHANGES
                    ====================================================== --}}

                    @if(
                        count($visibleAttributes) === 0 &&
                        empty($plotContext) &&
                        empty($possessionContext) &&
                        empty($areaVariationContext) &&
                        empty($ownerContext) &&
                        (
                            !$ownerChanges ||
                            (
                                empty($ownerChanges['attached']) &&
                                empty($ownerChanges['detached'])
                            )
                        )
                    )

                        <div class="text-muted mb-3">

                            No field changes recorded.

                        </div>

                    @endif



                    <hr>



                    {{-- =====================================================
                        RAW PROPERTIES
                    ====================================================== --}}

                    <details>

                        <summary class="fw-bold">
                            Raw Properties
                        </summary>


                        <pre
                            class="bg-light p-3 mt-2"
                            style="white-space: pre-wrap; word-break: break-word;"
                        >{{ json_encode(
                            $properties,
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                        ) }}</pre>

                    </details>


                </div>



                {{-- =====================================================
                    FOOTER
                ====================================================== --}}

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>

                </div>


            </div>

        </div>

    </div>

@endforeach

@endsection