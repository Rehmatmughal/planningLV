@extends('app')

@section('content')

<div class="container-fluid mt-4">


    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">

                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                Possession Import Preview

            </h4>

            <small class="text-muted">

                Review the uploaded file before importing historical
                possession records.

            </small>

        </div>


        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back

        </a>

    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
        ERROR MESSAGE
    ========================================================== --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please fix the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
        FILE INFORMATION
    ========================================================== --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                <i class="bi bi-info-circle me-1"></i>
                Import Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row">


                {{-- File --}}
                <div class="col-md-4 mb-3 mb-md-0">

                    <small class="text-muted d-block">
                        File
                    </small>

                    <strong>
                        {{ $import['original_name'] }}
                    </strong>

                </div>


                {{-- Total Rows --}}
                <div class="col-md-4 mb-3 mb-md-0">

                    <small class="text-muted d-block">
                        Total Data Rows
                    </small>

                    <strong>
                        {{ number_format($totalRows) }}
                    </strong>

                </div>


                {{-- Owner Action --}}
                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Existing Owner CNIC
                    </small>

                    <strong>

                        @if($import['owner_action'] === 'update')

                            Update existing owner

                        @else

                            Keep existing owner data

                        @endif

                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        PREVIEW INFORMATION
    ========================================================== --}}
    <div class="alert alert-warning">

        <i class="bi bi-exclamation-triangle me-1"></i>

        This is only a preview.
        <strong>No possession or owner records have been created yet.</strong>

        The actual validation and import will be performed in the next step.

    </div>


    {{-- =========================================================
        SPREADSHEET PREVIEW
    ========================================================== --}}
    <div class="card shadow-sm">

        <div class="card-header bg-light d-flex justify-content-between align-items-center">

            <strong>

                <i class="bi bi-table me-1"></i>
                First {{ count($previewRows) }} Rows

            </strong>


            <span class="badge bg-secondary">

                {{ count($headers) }} Columns

            </span>

        </div>


        <div class="card-body p-0">

            @if(!empty($headers))

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-sm mb-0">

                        <thead class="table-dark">

                            <tr>

                                <th style="width: 60px;">
                                    #
                                </th>

                                @foreach($headers as $header)

                                    <th>
                                        {{ $header ?: 'Unnamed Column' }}
                                    </th>

                                @endforeach

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($previewRows as $index => $row)

                                <tr>

                                    <td class="fw-bold">

                                        {{ $index + 1 }}

                                    </td>


                                    @foreach($row as $value)

                                        <td>

                                            {{ $value }}

                                        </td>

                                    @endforeach

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ count($headers) + 1 }}"
                                        class="text-center py-5 text-muted">

                                        <i class="bi bi-inbox fs-1"></i>

                                        <div class="mt-2">
                                            No data rows found.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center py-5 text-muted">

                    <i class="bi bi-file-earmark-x fs-1"></i>

                    <h5 class="mt-3">
                        No columns found
                    </h5>

                    <p class="mb-0">
                        The uploaded file does not appear to contain
                        a valid header row.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        BOTTOM ACTIONS
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mt-4">

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-x-circle me-1"></i>
            Cancel Import

        </a>


        @if(!empty($headers) && $totalRows > 0)

            {{-- <button type="button"
                    class="btn btn-success"
                    disabled>

                <i class="bi bi-check-circle me-1"></i>
                Validate & Import

            </button> --}}
            <form method="POST"
                action="{{ route('possession-cases.import.validate') }}">

                @csrf

                <button type="submit"
                        class="btn btn-primary">

                    <i class="bi bi-shield-check me-1"></i>
                    Validate File

                </button>

            </form>

        @endif

    </div>


</div>

@endsection