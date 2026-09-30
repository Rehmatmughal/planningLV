@extends('app')

@section('content')

<div class="container-fluid mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                📋 Possession Cases
            </h4>

            <small class="text-muted">
                Manage possession cases and their current status
            </small>
        </div>
        <button type="button"
                class="btn btn-success"
                data-bs-toggle="modal"
                data-bs-target="#uploadPossessionModal">

            <i class="bi bi-upload"></i>
            Upload Possession

        </button>


        <a href="{{ route('possession-cases.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            New Possession Case
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Filters --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                🔎 Search / Filter
            </strong>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('possession-cases.index') }}">

                <div class="row g-3">

                    {{-- Possession No --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            Possession No
                        </label>

                        <input type="number"
                               name="possession_no"
                               class="form-control"
                               value="{{ request('possession_no') }}"
                               placeholder="Possession No">

                    </div>


                    {{-- Owner --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Owner Name
                        </label>

                        <input type="text"
                               name="owner_name"
                               class="form-control"
                               value="{{ request('owner_name') }}"
                               placeholder="Owner name">

                    </div>


                    {{-- CNIC --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            CNIC
                        </label>

                        <input type="text"
                               name="cnic"
                               class="form-control"
                               value="{{ request('cnic') }}"
                               placeholder="CNIC">

                    </div>


                    {{-- Status --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="received"
                                {{ request('status') == 'received' ? 'selected' : '' }}>
                                Received
                            </option>

                            <option value="prepared"
                                {{ request('status') == 'prepared' ? 'selected' : '' }}>
                                Prepared
                            </option>

                            <option value="signed"
                                {{ request('status') == 'signed' ? 'selected' : '' }}>
                                Signed
                            </option>

                            <option value="approval"
                                {{ request('status') == 'approval' ? 'selected' : '' }}>
                                Approval
                            </option>

                            <option value="receive_back"
                                {{ request('status') == 'receive_back' ? 'selected' : '' }}>
                                Receive Back
                            </option>

                            <option value="handed_over"
                                {{ request('status') == 'handed_over' ? 'selected' : '' }}>
                                Handed Over
                            </option>

                            <option value="completed"
                                {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="col-md-2 d-flex align-items-end gap-2">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                        <a href="{{ route('possession-cases.index') }}"
                           class="btn btn-secondary">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Cases Table --}}
    <div class="card shadow-sm">

        <div class="card-header bg-light d-flex justify-content-between">

            <strong>
                Possession Cases
            </strong>

            <span class="badge bg-secondary">
                {{ $possessionCases->total() }}
                Cases
            </span>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-bordered mb-0 align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Property type - Possession No
                            </th>

                            <th>
                                Plot
                            </th>

                            <th>
                                Owner(s)
                            </th>

                            <th>
                                CNIC
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Received
                            </th>

                            <th>
                                Active
                            </th>

                            <th width="180">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($possessionCases as $case)

                            <tr>

                                {{-- #
                                ------------------------------------------------ --}}
                                <td>
                                    {{$case->plot->latestAreavariation->measured_area ?? '-' }} -

                                    {{ $possessionCases->firstItem() + $loop->index }}

                                </td>

                                {{-- Case No
                                ------------------------------------------------ --}}
                                <td>

                                    <strong>
                                        {{-- {{ $case->case_no }} --}}
                                        {{ $case->plot->propertyType->name ?? '-' }} - {{ $case->possession_no ?? '-' }}
                                        
                                    </strong>

                                </td>

                                {{-- Plot
                                ------------------------------------------------ --}}
                                <td>

                                    @if($case->plot)

                                        <strong>
                                            {{ $case->plot->plot_number }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            @if($case->plot->project)
                                                {{ $case->plot->project->project_name }}
                                            @endif

                                            @if($case->plot->block)
                                                -
                                                {{ $case->plot->block->block_name }}
                                            @endif

                                        </small>

                                    @else

                                        <span class="text-danger">
                                            Plot not found
                                        </span>

                                    @endif

                                </td>


                                {{-- Owners
                                ------------------------------------------------ --}}
                                <td>

                                    @forelse($case->owners as $owner)

                                        <div>
                                            {{ $owner->owner_name }}
                                        </div>

                                    @empty

                                        <span class="text-muted">
                                            No owner
                                        </span>

                                    @endforelse

                                </td>


                                {{-- CNIC
                                ------------------------------------------------ --}}
                                <td>

                                    @forelse($case->owners as $owner)

                                        <div>
                                            {{ $owner->cnic ?? '-' }}
                                        </div>

                                    @empty

                                        -
                                    @endforelse

                                </td>


                                {{-- Status
                                ------------------------------------------------ --}}
                                <td>

                                    @php

                                        $statusClasses = [

                                            'received' =>
                                                'bg-primary',

                                            'prepared' =>
                                                'bg-info',

                                            'signed' =>
                                                'bg-warning text-dark',

                                            'approval' =>
                                                'bg-secondary',

                                            'receive_back' =>
                                                'bg-dark',

                                            'handed_over' =>
                                                'bg-success',

                                            'completed' =>
                                                'bg-success',

                                        ];

                                        $statusLabels = [

                                            'received' =>
                                                'Received',

                                            'prepared' =>
                                                'Prepared',

                                            'signed' =>
                                                'Signed',

                                            'approval' =>
                                                'Approval',

                                            'receive_back' =>
                                                'Receive Back',

                                            'handed_over' =>
                                                'Handed Over',

                                            'completed' =>
                                                'Completed',

                                        ];

                                    @endphp


                                    <span class="badge {{ $statusClasses[$case->current_status] ?? 'bg-secondary' }}">

                                        {{ $statusLabels[$case->current_status] ?? ucfirst($case->current_status) }}

                                    </span>

                                </td>


                                {{-- Received Date
                                ------------------------------------------------ --}}
                                <td>

                                    @if($case->received_at)

                                        {{ $case->received_at->format('d-m-Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- Active
                                ------------------------------------------------ --}}
                                <td>

                                    @if($case->is_active)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions
                                ------------------------------------------------ --}}
                                <td>

                                    <div class="d-flex gap-1">

                                        {{-- View --}}
                                        <a href="{{ route('possession-cases.show', $case) }}"
                                           class="btn btn-sm btn-info text-white"
                                           title="View">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route('possession-cases.edit', $case) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('possession-cases.destroy', $case) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this possession case?');">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-inbox fs-1"></i>

                                        <h5 class="mt-2">
                                            No possession cases found
                                        </h5>

                                        <p class="mb-3">
                                            There are currently no possession cases matching your search.
                                        </p>

                                        <div class="d-flex gap-2">

                                            {{-- Upload Historical Possession --}}
                                            <button type="button"
                                                    class="btn btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#uploadPossessionModal">

                                                <i class="bi bi-upload"></i>
                                                Upload Possession

                                            </button>


                                            {{-- New Possession --}}
                                            <a href="{{ route('possession-cases.create') }}"
                                            class="btn btn-primary">

                                                <i class="bi bi-plus-circle"></i>
                                                New Possession Case

                                            </a>

                                        </div>

                                        {{-- <a href="{{ route('possession-cases.create') }}"
                                           class="btn btn-primary">

                                            <i class="bi bi-plus-circle"></i>
                                            Create First Case

                                        </a> --}}

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($possessionCases->hasPages())

            <div class="card-footer">

                {{ $possessionCases->links() }}

            </div>

        @endif

    </div>

</div>
{{-- =========================================================
    UPLOAD HISTORICAL POSSESSION MODAL
========================================================= --}}
<div class="modal fade"
     id="uploadPossessionModal"
     tabindex="-1"
     aria-labelledby="uploadPossessionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">


            {{-- =====================================================
                MODAL HEADER
            ====================================================== --}}
            <div class="modal-header bg-success text-white">

                <div>
                    <h5 class="modal-title mb-1"
                        id="uploadPossessionModalLabel">

                        <i class="bi bi-cloud-upload me-2"></i>
                        Upload Historical Possession

                    </h5>

                    <small class="opacity-75">
                        Import old possession records from CSV or Excel
                    </small>
                </div>


                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            {{-- =====================================================
                MODAL BODY
            ====================================================== --}}
            <div class="modal-body">


                {{-- Information --}}
                <div class="alert alert-info">

                    <div class="d-flex">

                        <div class="me-3">
                            <i class="bi bi-info-circle-fill fs-4"></i>
                        </div>

                        <div>

                            <strong>Historical Possession Import</strong>

                            <p class="mb-0 mt-1">

                                Possession records will be imported from the
                                selected file. Owner records will be matched
                                using <strong>CNIC</strong>.

                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    IMPORTANT RULES
                ================================================== --}}
                <div class="card border-0 bg-light mb-4">

                    <div class="card-body">

                        <h6 class="fw-bold mb-3">

                            <i class="bi bi-shield-check me-1"></i>
                            Import Rules

                        </h6>


                        <ul class="mb-0">

                            <li class="mb-2">
                                <strong>CNIC</strong> will be used to find
                                the owner in the Owners table.
                            </li>

                            <li class="mb-2">
                                If the CNIC already exists, the existing
                                owner record can be updated from the file.
                            </li>

                            <li class="mb-2">
                                If the CNIC does not exist, a new owner
                                record will be created.
                            </li>

                            <li class="mb-2">
                                Multiple owners can be attached to the
                                same possession.
                            </li>

                            <li class="mb-2">
                                Historical possession status will be
                                imported as <strong>Completed</strong>.
                            </li>

                            <li>
                                Invalid or missing CNIC records will be
                                shown as errors before final import.
                            </li>

                        </ul>

                    </div>

                </div>

                {{-- =================================================
                    FILE UPLOAD FORM
                ================================================== --}}
                <form method="POST"
                      action="{{ route('possession-cases.import') }}"
                      enctype="multipart/form-data"
                      id="possessionImportForm">

                    @csrf

                    <div class="mb-3">

                        <label for="possession_import_project"
                            class="form-label fw-bold">

                            <i class="bi bi-building me-1"></i>
                            Project

                        </label>

                        <select name="project_id"
                                id="possession_import_project"
                                class="form-select"
                                required>

                            <option value="">
                                -- Select Project --
                            </option>

                            @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)

                                <option value="{{ $project->id }}">
                                    {{ $project->project_name }}
                                </option>

                            @endforeach

                        </select>

                        <div class="form-text">
                            The uploaded CSV does not contain a Project column.
                            Please select the project to which these historical possessions belong.
                        </div>

                    </div>
                    {{-- File --}}
                    <div class="mb-3">

                        <label for="possession_import_file"
                               class="form-label fw-bold">

                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                            Select Possession File

                        </label>


                        <input type="file"
                               name="file"
                               id="possession_import_file"
                               class="form-control"
                               accept=".csv,.xlsx,.xls"
                               required>


                        <div class="form-text">

                            Allowed files:
                            <strong>CSV, XLSX, XLS</strong>

                        </div>

                    </div>


                    {{-- =================================================
                        EXISTING OWNER DATA OPTION
                    ================================================== --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold">

                            <i class="bi bi-person-check me-1"></i>
                            Existing Owner CNIC Found

                        </label>


                        <select name="owner_action"
                                class="form-select"
                                required>

                            <option value="update" selected>

                                Update existing owner with file data

                            </option>

                            <option value="keep">

                                Keep existing owner data

                            </option>

                        </select>


                        <div class="form-text">

                            CNIC will always be used for matching.
                            This option decides what to do when that
                            CNIC already exists in the Owners table.

                        </div>

                    </div>


                    {{-- =================================================
                        WARNING
                    ================================================== --}}
                    <div class="alert alert-warning mb-0">

                        <div class="d-flex">

                            <div class="me-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>

                            <div>

                                <strong>Please review the file first.</strong>

                                <div class="mt-1">

                                    The system will validate the file
                                    before creating historical possession
                                    records.

                                </div>

                            </div>

                        </div>

                    </div>


                </form>

            </div>


            {{-- =====================================================
                MODAL FOOTER
            ====================================================== --}}
            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    <i class="bi bi-x-circle me-1"></i>
                    Cancel

                </button>


                <button type="submit"
                        form="possessionImportForm"
                        class="btn btn-success">

                    <i class="bi bi-cloud-upload me-1"></i>
                    Upload & Validate

                </button>

            </div>


        </div>

    </div>

</div>

@endsection
