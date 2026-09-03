@extends('app')

@section('content')

<div class="container-fluid mt-4">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                📋 Possession Case #{{ $possessionCase->case_no }}
            </h4>

            <small class="text-muted">
                Possession case details and history
            </small>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('possession-cases.index') }}"
               class="btn btn-secondary">
                ← Back
            </a>

            <a href="{{ route('possession-cases.edit', $possessionCase) }}"
               class="btn btn-warning">
                ✏️ Edit
            </a>

        </div>

    </div>


    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
         CASE STATUS
    ========================================================== --}}

    @php

        $statusLabels = [

            'received' => 'Received',
            'prepared' => 'Prepared',
            'signed' => 'Signed',
            'approval' => 'Approval',
            'receive_back' => 'Receive Back',
            'handed_over' => 'Handed Over',
            'completed' => 'Completed',

        ];

        $statusClasses = [

            'received' => 'bg-primary',
            'prepared' => 'bg-info',
            'signed' => 'bg-warning text-dark',
            'approval' => 'bg-secondary',
            'receive_back' => 'bg-dark',
            'handed_over' => 'bg-success',
            'completed' => 'bg-success',

        ];

        $currentStatus =
            $statusLabels[$possessionCase->current_status]
            ?? ucfirst($possessionCase->current_status);

        $currentStatusClass =
            $statusClasses[$possessionCase->current_status]
            ?? 'bg-secondary';

    @endphp


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h5 class="fw-bold mb-2">
                        Current Status
                    </h5>

                    <span class="badge {{ $currentStatusClass }} fs-6 px-3 py-2">
                        {{ $currentStatus }}
                    </span>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    @if($possessionCase->is_active)

                        <span class="badge bg-success fs-6">
                            Active Case
                        </span>

                    @else

                        <span class="badge bg-secondary fs-6">
                            Inactive Case
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         STATUS WORKFLOW
    ========================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                🔄 Possession Workflow
            </strong>

        </div>

        <div class="card-body">

            <div class="row text-center g-2">

                @php

                    $workflow = [

                        'received' => 'Received',
                        'prepared' => 'Prepared',
                        'signed' => 'Signed',
                        'approval' => 'Approval',
                        'receive_back' => 'Receive Back',
                        'handed_over' => 'Handed Over',
                        'completed' => 'Completed',

                    ];

                    $statuses = array_keys($workflow);

                    $currentIndex =
                        array_search(
                            $possessionCase->current_status,
                            $statuses
                        );

                @endphp


                @foreach($workflow as $status => $label)

                    @php

                        $statusIndex =
                            array_search($status, $statuses);

                        if ($statusIndex < $currentIndex) {

                            $stepClass = 'bg-success text-white';

                        } elseif ($statusIndex == $currentIndex) {

                            $stepClass = 'bg-primary text-white';

                        } else {

                            $stepClass = 'bg-light text-muted border';

                        }

                    @endphp


                    <div class="col">

                        <div class="rounded p-2 {{ $stepClass }}">

                            <small class="fw-bold">
                                {{ $label }}
                            </small>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    <div class="row">


        {{-- =====================================================
             LEFT SIDE
        ====================================================== --}}

        <div class="col-lg-8">


            {{-- CASE INFORMATION --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">

                    <strong>
                        📋 Case Information
                    </strong>

                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-4">

                            <small class="text-muted">
                                Case No
                            </small>

                            <div class="fw-bold">
                                {{ $possessionCase->case_no }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Approval Required
                            </small>

                            <div>

                                @if($possessionCase->need_approval)

                                    <span class="badge bg-warning text-dark">
                                        Yes
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        No
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Active
                            </small>

                            <div>

                                @if($possessionCase->is_active)

                                    <span class="badge bg-success">
                                        Yes
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        No
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Received Date
                            </small>

                            <div>
                                {{ $possessionCase->received_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Prepared Date
                            </small>

                            <div>
                                {{ $possessionCase->prepared_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Signed Date
                            </small>

                            <div>
                                {{ $possessionCase->signed_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Approval Sent
                            </small>

                            <div>
                                {{ $possessionCase->approval_sent_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Received Back
                            </small>

                            <div>
                                {{ $possessionCase->received_back_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Handed Over
                            </small>

                            <div>
                                {{ $possessionCase->handed_over_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Completed
                            </small>

                            <div>
                                {{ $possessionCase->completed_at?->format('d-m-Y') ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Handed Over To
                            </small>

                            <div>
                                {{ $possessionCase->handed_over_to ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Created By
                            </small>

                            <div>
                                {{ $possessionCase->creator?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <small class="text-muted">
                                Remarks
                            </small>

                            <div class="border rounded p-2 bg-light">

                                {{ $possessionCase->remarks ?? 'No remarks' }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- OWNERS --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light d-flex justify-content-between">

                    <strong>
                        👥 Owners
                    </strong>

                    <span class="badge bg-secondary">
                        {{ $possessionCase->owners->count() }}
                    </span>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Name
                                    </th>

                                    <th>
                                        CNIC
                                    </th>

                                    <th>
                                        Contact
                                    </th>

                                    <th>
                                        Ownership %
                                    </th>

                                    <th>
                                        Address
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($possessionCase->owners as $owner)

                                    <tr>

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{ $owner->owner_name }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $owner->cnic ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $owner->contact_no ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $owner->ownership_percentage !== null
                                                ? $owner->ownership_percentage . '%'
                                                : '-' }}
                                        </td>

                                        <td>
                                            {{ $owner->address ?? '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6"
                                            class="text-center text-muted py-4">

                                            No owner information found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 UPDATE STATUS
            ================================================== --}}

            @if($possessionCase->is_active)

                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-light">

                        <strong>
                            🔄 Update Case Status
                        </strong>

                    </div>


                    <div class="card-body">

                        <form method="POST"
                              action="{{ route('possession-cases.update-status', $possessionCase) }}">

                            @csrf

                            @method('PATCH')


                            <div class="row g-3">


                                {{-- Status --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-bold">
                                        New Status
                                    </label>

                                    <select name="status"
                                            id="status"
                                            class="form-select"
                                            required>

                                        <option value="">
                                            -- Select Status --
                                        </option>

                                        @foreach($workflow as $status => $label)

                                            <option value="{{ $status }}"
                                                {{ $possessionCase->current_status == $status ? 'selected' : '' }}>

                                                {{ $label }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- Handed Over To --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Handed Over To
                                    </label>

                                    <input type="text"
                                           name="handed_over_to"
                                           id="handed_over_to"
                                           class="form-control"
                                           value="{{ $possessionCase->handed_over_to }}"
                                           placeholder="Person / Department">

                                </div>


                                {{-- Remarks --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Remarks
                                    </label>

                                    <input type="text"
                                           name="remarks"
                                           class="form-control"
                                           placeholder="Status remarks">

                                </div>


                                <div class="col-12">

                                    <button type="submit"
                                            class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to update the case status?');">

                                        🔄 Update Status

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            @endif


        </div>


        {{-- =====================================================
             RIGHT SIDE
        ====================================================== --}}

        <div class="col-lg-4">


            {{-- PLOT INFORMATION --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">

                    <strong>
                        🏠 Plot Information
                    </strong>

                </div>


                <div class="card-body">

                    @if($possessionCase->plot)

                        <div class="mb-3">

                            <small class="text-muted">
                                Plot Number
                            </small>

                            <div class="fs-5 fw-bold">
                                {{ $possessionCase->plot->plot_number }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Project
                            </small>

                            <div>
                                {{ $possessionCase->plot->project?->project_name ?? '-' }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Block
                            </small>

                            <div>
                                {{ $possessionCase->plot->block?->block_name ?? '-' }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Street
                            </small>

                            <div>
                                {{ $possessionCase->plot->street?->street_name ?? '-' }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Plot Size
                            </small>

                            <div>
                                {{ $possessionCase->plot->size?->title ?? '-' }}
                            </div>

                        </div>


                        <div>

                            <small class="text-muted">
                                Measured Area
                            </small>

                            <div>
                                {{ $possessionCase->plot->measured_plotarea ?? '-' }}
                            </div>

                        </div>

                    @else

                        <div class="text-danger">
                            Plot information not found.
                        </div>

                    @endif

                </div>

            </div>


            {{-- CURRENT HOLDER --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-light">

                    <strong>
                        👤 Current Holder
                    </strong>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Holder Type
                        </small>

                        <div>
                            {{ $possessionCase->current_holder_type ?? '-' }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Holder ID
                        </small>

                        <div>
                            {{ $possessionCase->current_holder_id ?? '-' }}
                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Holder Name
                        </small>

                        <div class="fw-bold">
                            {{ $possessionCase->current_holder_name ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- DELETE --}}
            <div class="card shadow-sm border-danger mb-4">

                <div class="card-body">

                    <h6 class="text-danger fw-bold">
                        Delete Case
                    </h6>

                    <p class="small text-muted">
                        The case will be soft deleted and removed from the active case list.
                    </p>

                    <form method="POST"
                          action="{{ route('possession-cases.destroy', $possessionCase) }}"
                          onsubmit="return confirm('Are you sure you want to delete this possession case?');">

                        @csrf

                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-outline-danger">

                            🗑 Delete Case

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         HISTORY
    ========================================================== --}}

    <div class="card shadow-sm mb-5">

        <div class="card-header bg-light">

            <strong>
                📜 Possession Case History
            </strong>

        </div>


        <div class="card-body">

            @forelse($possessionCase->histories->sortByDesc('created_at') as $history)

                <div class="border rounded p-3 mb-3">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="fw-bold mb-1">

                                {{ $history->action }}

                            </h6>

                            <small class="text-muted">

                                {{ $history->created_at->format('d-m-Y h:i A') }}

                                @if($history->user)
                                    — {{ $history->user->name }}
                                @endif

                            </small>

                        </div>


                        <div>

                            @if($history->old_status)

                                <span class="badge bg-secondary">

                                    {{ $statusLabels[$history->old_status]
                                        ?? ucfirst($history->old_status) }}

                                </span>

                                <span class="mx-1">
                                    →
                                </span>

                            @endif


                            @if($history->new_status)

                                <span class="badge bg-primary">

                                    {{ $statusLabels[$history->new_status]
                                        ?? ucfirst($history->new_status) }}

                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="row mt-3">

                        <div class="col-md-4">

                            <small class="text-muted">
                                Old Holder
                            </small>

                            <div>
                                {{ $history->old_holder ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                New Holder
                            </small>

                            <div>
                                {{ $history->new_holder ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Handed Over To
                            </small>

                            <div>
                                {{ $history->handed_over_to ?? '-' }}
                            </div>

                        </div>


                        @if($history->remarks)

                            <div class="col-12 mt-3">

                                <small class="text-muted">
                                    Remarks
                                </small>

                                <div class="bg-light border rounded p-2">

                                    {{ $history->remarks }}

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            @empty

                <div class="text-center text-muted py-4">

                    No history found.

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection

