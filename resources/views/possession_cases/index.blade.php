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

                    {{-- Case No --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            Case No
                        </label>

                        <input type="number"
                               name="case_no"
                               class="form-control"
                               value="{{ request('case_no') }}"
                               placeholder="Case No">

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
                                Case No
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

                                    {{ $possessionCases->firstItem() + $loop->index }}

                                </td>


                                {{-- Case No
                                ------------------------------------------------ --}}
                                <td>

                                    <strong>
                                        {{ $case->case_no }}
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

                                        <a href="{{ route('possession-cases.create') }}"
                                           class="btn btn-primary">

                                            <i class="bi bi-plus-circle"></i>
                                            Create First Case

                                        </a>

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

@endsection
