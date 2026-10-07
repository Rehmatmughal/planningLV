@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">Activity Logs</h2>


    <!-- Filters -->
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


    <!-- Activity Logs -->
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
                                | Plot context
                                |--------------------------------------------------------------------------
                                */

                                $plotContext =
                                    $properties['plot_context']
                                    ?? null;


                                /*
                                |--------------------------------------------------------------------------
                                | Possession context
                                |--------------------------------------------------------------------------
                                */

                                $possessionContext =
                                    $properties['possession_context']
                                    ?? null;


                                /*
                                |--------------------------------------------------------------------------
                                | Owner changes
                                |--------------------------------------------------------------------------
                                */

                                $ownerChanges =
                                    $properties['owner_changes']
                                    ?? null;

                            @endphp


                            <tr>


                                <!-- Date -->
                                <td>
                                    {{ $activity->created_at->format('d-m-Y H:i') }}
                                </td>


                                <!-- User -->
                                <td>
                                    {{ optional($activity->causer)->name ?? 'System' }}
                                </td>


                                <!-- Event -->
                                <td>
                                    {{ ucfirst($activity->event ?? 'Activity') }}
                                </td>


                                <!-- Model -->
                                <td>
                                    {{ class_basename($activity->subject_type ?? '') }}
                                </td>


                                <!-- Description -->
                                <td>
                                    {{ $activity->description }}
                                </td>


                                <!-- Changes -->
                                <td>

                                    {{-- ================================================= --}}
                                    {{-- Owner Changes                                     --}}
                                    {{-- ================================================= --}}

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


                                    {{-- ================================================= --}}
                                    {{-- Normal Model Changes                            --}}
                                    {{-- ================================================= --}}

                                    @elseif(count($attributes) > 0)


                                        @foreach($attributes as $key => $value)

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

                                            @endphp


                                            <div class="small mb-1">

                                                <strong>
                                                    {{ $displayKey }}:
                                                </strong>


                                                @if(array_key_exists($key, $old))

                                                    <span class="text-danger">

                                                        {{
                                                            is_array($oldValue)
                                                            ? json_encode($oldValue)
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
                                                        ? json_encode($value)
                                                        : (
                                                            $value === null ||
                                                            $value === ''
                                                            ? '—'
                                                            : $value
                                                        )
                                                    }}

                                                </span>

                                            </div>

                                        @endforeach


                                    {{-- ================================================= --}}
                                    {{-- Context only                                     --}}
                                    {{-- ================================================= --}}

                                    @elseif($plotContext || $possessionContext)


                                        <span class="text-muted">
                                            Context only
                                        </span>


                                    @else

                                        <span class="text-muted">
                                            No changes
                                        </span>

                                    @endif

                                </td>


                                <!-- View -->
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



{{-- ========================================================= --}}
{{-- MODALS                                                     --}}
{{-- IMPORTANT: Modals table ke bahar hain                       --}}
{{-- ========================================================= --}}

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
        | Plot context
        |--------------------------------------------------------------------------
        */

        $plotContext =
            $properties['plot_context']
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | Possession context
        |--------------------------------------------------------------------------
        */

        $possessionContext =
            $properties['possession_context']
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | Owner changes
        |--------------------------------------------------------------------------
        */

        $ownerChanges =
            $properties['owner_changes']
            ?? null;

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


                <!-- Header -->
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


                <!-- Body -->
                <div class="modal-body">


                    <!-- Basic Information -->

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



                    {{-- ================================================= --}}
                    {{-- Possession Information                           --}}
                    {{-- ================================================= --}}

                    @if($possessionContext)

                        <h6 class="mb-3">
                            Possession Information
                        </h6>


                        <div class="row">

                            @if(!empty($possessionContext['possession_no']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Possession No:
                                    </strong>

                                    {{ $possessionContext['possession_no'] }}

                                </div>

                            @endif


                            @if(!empty($possessionContext['reference_no']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Reference No:
                                    </strong>

                                    {{ $possessionContext['reference_no'] }}

                                </div>

                            @endif


                            @if(!empty($possessionContext['plot_number']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Plot Number:
                                    </strong>

                                    {{ $possessionContext['plot_number'] }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- ================================================= --}}
                    {{-- Plot Information                                --}}
                    {{-- ================================================= --}}

                    @if($plotContext)

                        <h6 class="mb-3">
                            Plot Information
                        </h6>


                        <div class="row">


                            @if(!empty($plotContext['project']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Project:
                                    </strong>

                                    {{ $plotContext['project'] }}

                                </div>

                            @endif


                            @if(!empty($plotContext['block']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Block:
                                    </strong>

                                    {{ $plotContext['block'] }}

                                </div>

                            @endif


                            @if(!empty($plotContext['street']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Street:
                                    </strong>

                                    {{ $plotContext['street'] }}

                                </div>

                            @endif


                            @if(!empty($plotContext['plot_number']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Plot Number:
                                    </strong>

                                    {{ $plotContext['plot_number'] }}

                                </div>

                            @endif


                            @if(!empty($plotContext['property_type']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Property Type:
                                    </strong>

                                    {{ $plotContext['property_type'] }}

                                </div>

                            @endif


                            @if(!empty($plotContext['size']))

                                <div class="col-md-6 mb-2">

                                    <strong>
                                        Size:
                                    </strong>

                                    {{ $plotContext['size'] }}

                                </div>

                            @endif

                        </div>


                        <hr>

                    @endif



                    {{-- ================================================= --}}
                    {{-- Owner Changes                                    --}}
                    {{-- ================================================= --}}

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


                        {{-- Attached Owners --}}

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



                        {{-- Detached Owners --}}

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



                    {{-- ================================================= --}}
                    {{-- Normal Changed Fields                            --}}
                    {{-- ================================================= --}}

                    @if(count($attributes) > 0)

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
                                        $attributes
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

                                        @endphp


                                        <tr>

                                            <td>

                                                <strong>
                                                    {{ $displayKey }}
                                                </strong>

                                            </td>


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



                    {{-- ================================================= --}}
                    {{-- No Changes                                      --}}
                    {{-- ================================================= --}}

                    @if(
                        count($attributes) === 0 &&
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


                    <!-- Raw Properties -->

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


                <!-- Footer -->

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