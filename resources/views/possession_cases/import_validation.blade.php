@extends('app')

@section('content')

<div class="container-fluid mt-4">

    {{-- =========================================================
        HEADER
    ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">

                <i class="bi bi-shield-check me-2"></i>
                Possession Import Validation

            </h4>

            <small class="text-muted">

                Review validation results before importing
                historical possession records.

            </small>

        </div>

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back

        </a>

    </div>


    {{-- =========================================================
        FILE INFORMATION
    ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-dark text-white">

            <strong>
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                Import File
            </strong>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <strong>File:</strong>

                    <div>
                        {{ $validation['source_file'] ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <strong>Project:</strong>

                    <div>
                        {{ $validation['project_name'] ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <strong>Existing Owner Action:</strong>

                    <div>

                        @if(
                            ($validation['owner_action'] ?? '')
                            === 'update'
                        )

                            <span class="badge bg-primary">
                                Update Existing Owner
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Keep Existing Owner
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- test only --}}
                @if(($validation['error_count'] ?? 0) == 0)
                <form method="POST"
                    action="{{ route('possession-cases.import.execute') }}"
                    class="d-inline"
                    onsubmit="return confirm('Are you sure you want to import these historical possession records? This action will create possession and owner records in the database.');">
                    @csrf
                    <button type="submit"
                            class="btn btn-success">

                        <i class="bi bi-database-add me-1"></i>

                        Final Import
                        ({{ $validation['total_count'] ?? 0 }} Records)

                    </button>

                </form>

            @else

                <button type="button"
                        class="btn btn-secondary"
                        disabled>

                    <i class="bi bi-lock me-1"></i>

                    Final Import Disabled

                </button>

            @endif


    {{-- =========================================================
        SUMMARY CARDS
    ========================================================= --}}

    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm border-primary">

                <div class="card-body">

                    <div class="text-muted">
                        Total Rows
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $validation['total_count'] ?? 0 }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm border-success">

                <div class="card-body">

                    <div class="text-muted">
                        Valid
                    </div>

                    <h3 class="fw-bold text-success mb-0">
                        {{ $validation['valid_count'] ?? 0 }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm border-warning">

                <div class="card-body">

                    <div class="text-muted">
                        Warnings
                    </div>

                    <h3 class="fw-bold text-warning mb-0">
                        {{ $validation['warning_count'] ?? 0 }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card shadow-sm border-danger">

                <div class="card-body">

                    <div class="text-muted">
                        Errors
                    </div>

                    <h3 class="fw-bold text-danger mb-0">
                        {{ $validation['error_count'] ?? 0 }}
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        IMPORTANT MESSAGE
    ========================================================= --}}

    @if(
        ($validation['error_count'] ?? 0) > 0
    )

        <div class="alert alert-danger">

            <strong>
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Import cannot proceed yet.
            </strong>

            <div class="mt-1">

                Please correct the rows marked as
                <strong>Error</strong>
                before final import.

            </div>

        </div>

    @else

        <div class="alert alert-success">

            <strong>
                <i class="bi bi-check-circle-fill me-1"></i>
                Validation completed successfully.
            </strong>

            <div class="mt-1">

                No blocking errors were found.

                The valid rows are ready for the
                final import step.

            </div>

        </div>

    @endif


    {{-- =========================================================
        VALIDATION TABLE
    ========================================================= --}}

    <div class="card shadow-sm">

        <div class="card-header bg-light">

            <strong>
                Validation Details
            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                Row
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Possession No.
                            </th>

                            <th>
                                Sequence
                            </th>

                            <th>
                                Revision
                            </th>

                            <th>
                                Block
                            </th>

                            <th>
                                Plot
                            </th>

                            <th>
                                Owners
                            </th>

                            <th>
                                Details
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach(
                            $validation['rows'] ?? []
                            as $row
                        )

                            <tr>

                                <td>
                                    {{ $row['row_number'] }}
                                </td>


                                <td>

                                    @if(
                                        $row['status'] === 'valid'
                                    )

                                        <span class="badge bg-success">
                                            Valid
                                        </span>

                                    @elseif(
                                        $row['status'] === 'warning'
                                    )

                                        <span class="badge bg-warning text-dark">
                                            Warning
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Error
                                        </span>

                                    @endif

                                </td>


                                <td class="fw-bold">

                                    {{ $row['possession_no'] ?? '-' }}

                                </td>


                                <td>

                                    {{ $row['possession_sequence'] ?? '-' }}

                                </td>


                                <td>

                                    {{ $row['revision_no'] ?? '-' }}

                                </td>


                                <td>

                                    {{ $row['block_name'] ?? '-' }}

                                </td>


                                <td>

                                    {{ $row['plot_number'] ?? '-' }}

                                </td>


                                <td>

                                    {{ $row['owner_count'] ?? 0 }}

                                </td>


                                <td style="min-width: 350px;">

                                    @if(
                                        !empty($row['errors'])
                                    )

                                        <div class="mb-2">

                                            <strong class="text-danger">
                                                Errors:
                                            </strong>

                                            <ul class="mb-0">

                                                @foreach(
                                                    $row['errors']
                                                    as $error
                                                )

                                                    <li class="text-danger">
                                                        {{ $error }}
                                                    </li>

                                                @endforeach

                                            </ul>

                                        </div>

                                    @endif


                                    @if(
                                        !empty($row['warnings'])
                                    )

                                        <div>

                                            <strong class="text-warning">
                                                Warnings:
                                            </strong>

                                            <ul class="mb-0">

                                                @foreach(
                                                    $row['warnings']
                                                    as $warning
                                                )

                                                    <li class="text-warning">
                                                        {{ $warning }}
                                                    </li>

                                                @endforeach

                                            </ul>

                                        </div>

                                    @endif


                                    @if(
                                        empty($row['errors'])
                                        &&
                                        empty($row['warnings'])
                                    )

                                        <span class="text-success">

                                            <i class="bi bi-check-circle me-1"></i>
                                            Ready for import

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
        BOTTOM ACTIONS
    ========================================================= --}}

    <div class="d-flex justify-content-between mt-4">

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-x-circle me-1"></i>
            Cancel

        </a>


        @if(
            ($validation['error_count'] ?? 0) === 0
            &&
            ($validation['valid_count'] ?? 0) > 0
        )
            {{-- button --}}
            @if(($validation['error_count'] ?? 0) == 0)
                <form method="POST"
                    action="{{ route('possession-cases.import.execute') }}"
                    class="d-inline"
                    onsubmit="return confirm('Are you sure you want to import these historical possession records? This action will create possession and owner records in the database.');">
                    @csrf
                    <button type="submit"
                            class="btn btn-success">

                        <i class="bi bi-database-add me-1"></i>

                        Final Import
                        ({{ $validation['total_count'] ?? 0 }} Records)

                    </button>

                </form>

            @else

                <button type="button"
                        class="btn btn-secondary"
                        disabled>

                    <i class="bi bi-lock me-1"></i>

                    Final Import Disabled

                </button>

            @endif


            {{-- <button type="button"
                    class="btn btn-success"
                    disabled>

                <i class="bi bi-database-check me-1"></i>
                Final Import

            </button> --}}

            <small class="text-muted align-self-center ms-2">
                Final import will be enabled in the next step.
            </small>

        @endif

    </div>

</div>

@endsection