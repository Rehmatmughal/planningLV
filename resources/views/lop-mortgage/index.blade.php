@extends('app')

@section('content')

<div class="container-fluid py-4">

{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
 
{{-- Error Message --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    {{-- <div>
        <h3 class="mb-1">
            LOP & Mortgage Status
        </h3>

        <p class="text-muted mb-0">
            Manage plot LOP and mortgage status
        </p>
    </div> --}}

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">
                LOP & Mortgage Status
            </h4>
            <p class="text-muted mb-0">
                Manage LOP and Mortgage status of plots.
            </p>
        </div>

        <div class="d-flex gap-2">
            {{-- @can('lop.excel')
                <a href="{{ route('lop-mortgage.export-excel', request()->query()) }}"
                class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i>
                    Export Excel
                </a>
            @endcan --}}

            {{-- @can('lop.create')
                <a href="{{ route('lop-mortgage.create') }}"
                class="btn btn-primary">
                    <i class="bi bi-plus-circle "></i>
                    Add LOP / Mortgage
                </a>
            @endcan --}}
        </div>
    </div>

    <div class="d-flex gap-2">
        @can('lop.excel')
            <a href="{{ route('lop-mortgage.export-excel', request()->query()) }}"
            class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i>
                Export Excel
            </a>
        @endcan

        @can('lop.create')
            <a href="{{ route('lop-mortgage.create') }}"
            class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Add LOP / Mortgage
            </a>
        @endcan
    </div>

        

</div>


{{-- Filters Card --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-white">
        <strong>
            <i class="bi bi-funnel me-1"></i>
            Filters
        </strong>
    </div>

    <div class="card-body">

        <form method="GET"
              action="{{ route('lop-mortgage.index') }}">

            <div class="row g-3">

                {{-- Project --}}
                <div class="col-md-3">

                    <label for="filter_project"
                           class="form-label">
                        Project
                    </label>

                    <select name="project_id"
                            id="filter_project"
                            class="form-select">

                        <option value="">
                            All Projects
                        </option>

                        @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)

                            <option value="{{ $project->id }}"
                                @selected(request('project_id') == $project->id)>

                                {{ $project->project_name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Property Type --}}
                <div class="col-md-3">

                    <label for="filter_property_type"
                           class="form-label">
                        Property Type
                    </label>

                    <select name="property_type_id"
                            id="filter_property_type"
                            class="form-select">

                        <option value="">
                            All Property Types
                        </option>

                        @foreach(\App\Models\PropertyType::orderBy('name')->get() as $propertyType)

                            <option value="{{ $propertyType->id }}"
                                @selected(request('property_type_id') == $propertyType->id)>

                                {{ $propertyType->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Block --}}
                <div class="col-md-3">

                    <label for="filter_block"
                           class="form-label">
                        Block
                    </label>

                    <select name="block_id"
                            id="filter_block"
                            class="form-select">

                        <option value="">
                            All Blocks
                        </option>

                    </select>

                </div>


                {{-- Street --}}
                <div class="col-md-3">

                    <label for="filter_street"
                           class="form-label">
                        Street
                    </label>

                    <select name="street_id"
                            id="filter_street"
                            class="form-select">

                        <option value="">
                            All Streets
                        </option>

                    </select>

                </div>


                {{-- Plot Number --}}
                <div class="col-md-3">

                    <label for="filter_plot_number"
                           class="form-label">
                        Plot No
                    </label>

                    <input type="text"
                           name="plot_number"
                           id="filter_plot_number"
                           value="{{ request('plot_number') }}"
                           class="form-control"
                           placeholder="Search plot number">

                </div>


                {{-- LOP --}}
                <div class="col-md-3">

                    <label for="filter_lop"
                           class="form-label">
                        LOP Status
                    </label>

                    <select name="lop_status"
                            id="filter_lop"
                            class="form-select">

                        <option value="">
                            All
                        </option>

                        <option value="lop"
                            @selected(request('lop_status') === 'lop')>
                            LOP
                        </option>

                        <option value="non_lop"
                            @selected(request('lop_status') === 'non_lop')>
                            Non-LOP
                        </option>

                    </select>

                </div>


                {{-- Mortgage --}}
                <div class="col-md-3">

                    <label for="filter_mortgage"
                           class="form-label">
                        Mortgage
                    </label>

                    <select name="is_mortgaged"
                            id="filter_mortgage"
                            class="form-select">

                        <option value="">
                            All
                        </option>

                        <option value="yes"
                            @selected(request('is_mortgaged') === 'yes')>
                            Yes
                        </option>

                        <option value="no"
                            @selected(request('is_mortgaged') === 'no')>
                            No
                        </option>

                    </select>

                </div>


                {{-- Buttons --}}
                <div class="col-md-3 d-flex align-items-end gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-search me-1"></i>
                        Search

                    </button>

                    <a href="{{ route('lop-mortgage.index') }}"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- Main Table --}}
<div class="card shadow-sm">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <strong>
            LOP & Mortgage Records
        </strong>

        <span class="badge bg-secondary">
            {{ $plots->total() }} Records
        </span>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th>
                            Project
                        </th>

                        <th>
                            Block
                        </th>

                        <th>
                            Street
                        </th>

                        <th>
                            Plot No
                        </th>

                        <th>
                            Property Type
                        </th>

                        <th>
                            Size
                        </th>

                        <th class="text-center">
                            LOP
                        </th>

                        <th class="text-center">
                            Mortgage
                        </th>

                        <th>
                            Remarks
                        </th>

                        @canany(['lop.edit', 'lop.delete'])

                            <th width="150"
                                class="text-center">
                                Actions
                            </th>

                        @endcanany

                    </tr>

                </thead>


                <tbody>

                    @forelse($plots as $index => $plot)

                        <tr>

                            {{-- Serial --}}
                            <td>
                                {{ $plots->firstItem() + $index }}
                            </td>


                            {{-- Project --}}
                            <td>
                                {{ $plot->project?->project_name ?? '-' }}
                            </td>


                            {{-- Block --}}
                            <td>
                                {{ $plot->block?->block_name ?? '-' }}
                            </td>


                            {{-- Street --}}
                            <td>
                                {{ $plot->street?->street_name ?? '-' }}
                            </td>


                            {{-- Plot --}}
                            <td>
                                <strong>
                                    {{ $plot->plot_number }}
                                </strong>
                            </td>


                            {{-- Property Type --}}
                            <td>
                                {{ $plot->propertyType?->name ?? '-' }}
                            </td>


                            {{-- Size --}}
                            <td>
                                {{ $plot->size?->size ?? '-' }}
                            </td>


                            {{-- LOP --}}
                            <td class="text-center">

                                @if($plot->lopStatus?->lop_status === 'lop')

                                    <span class="badge bg-success">
                                        LOP
                                    </span>

                                @elseif($plot->lopStatus?->lop_status === 'non_lop')

                                    <span class="badge bg-secondary">
                                        Non-LOP
                                    </span>

                                @else

                                    <span class="badge bg-light text-dark border">
                                        Not Set
                                    </span>

                                @endif

                            </td>


                            {{-- Mortgage --}}
                            <td class="text-center">

                                @if($plot->mortgageStatus?->is_mortgaged === 'yes')

                                    <span class="badge bg-warning text-dark">
                                        Yes
                                    </span>

                                @elseif($plot->mortgageStatus?->is_mortgaged === 'no')

                                    <span class="badge bg-secondary">
                                        No
                                    </span>

                                @else

                                    <span class="badge bg-light text-dark border">
                                        Not Set
                                    </span>

                                @endif

                            </td>


                            {{-- Remarks --}}
                            <td>
                                {{ $plot->lopStatus?->remarks
                                    ?? $plot->mortgageStatus?->remarks
                                    ?? '-' }}
                            </td>


                            {{-- Actions --}}
                            @canany(['lop.edit', 'lop.delete'])

                                <td class="text-center">

                                    @can('lop.edit')

                                        <a href="{{ route('lop-mortgage.edit', $plot) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                    @endcan


                                    @can('lop.delete')

                                        <form action="{{ route('lop-mortgage.destroy', $plot) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete LOP & Mortgage status for this plot?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    @endcan

                                </td>

                            @endcanany

                        </tr>

                    @empty

                        <tr>

                            <td colspan="11"
                                class="text-center py-5 text-muted">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No LOP / Mortgage records found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    @if($plots->hasPages())

        <div class="card-footer bg-white">

            {{ $plots->links() }}

        </div>

    @endif

</div>


</div>
@endsection

{{-- Dependent Filter Dropdowns --}}

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>

$(document).ready(function () {

    let selectedProject = "{{ request('project_id') }}";
    let selectedBlock = "{{ request('block_id') }}";
    let selectedStreet = "{{ request('street_id') }}";


    // ==========================================
    // Load Blocks
    // ==========================================

    function loadFilterBlocks(projectId, selectedBlockId = '') {

        let blockDropdown = $('#filter_block');
        let streetDropdown = $('#filter_street');

        blockDropdown.html('<option value="">Loading Blocks...</option>');
        streetDropdown.html('<option value="">All Streets</option>');

        if (!projectId) {

            blockDropdown.html('<option value="">All Blocks</option>');

            return;
        }


        $.get('/get-blocks/' + projectId, function (data) {

            blockDropdown.html('<option value="">All Blocks</option>');

            $.each(data, function (key, block) {

                blockDropdown.append(
                    '<option value="' + block.id + '">' +
                    block.block_name +
                    '</option>'
                );

            });


            if (selectedBlockId) {

                blockDropdown.val(selectedBlockId);

                loadFilterStreets(
                    selectedBlockId,
                    selectedStreet
                );

            }

        }).fail(function () {

            blockDropdown.html(
                '<option value="">Unable to load blocks</option>'
            );

        });

    }


    // ==========================================
    // Load Streets
    // ==========================================

    function loadFilterStreets(blockId, selectedStreetId = '') {

        let streetDropdown = $('#filter_street');

        streetDropdown.html('<option value="">Loading Streets...</option>');

        if (!blockId) {

            streetDropdown.html(
                '<option value="">All Streets</option>'
            );

            return;
        }


        $.get('/get-streets/' + blockId, function (data) {

            streetDropdown.html(
                '<option value="">All Streets</option>'
            );

            $.each(data, function (key, street) {

                streetDropdown.append(
                    '<option value="' + street.id + '">' +
                    street.street_name +
                    '</option>'
                );

            });


            if (selectedStreetId) {

                streetDropdown.val(selectedStreetId);

            }

        }).fail(function () {

            streetDropdown.html(
                '<option value="">Unable to load streets</option>'
            );

        });

    }


    // ==========================================
    // Project Changed
    // ==========================================

    $('#filter_project').on('change', function () {

        let projectId = $(this).val();

        loadFilterBlocks(projectId);

    });


    // ==========================================
    // Block Changed
    // ==========================================

    $('#filter_block').on('change', function () {

        let blockId = $(this).val();

        loadFilterStreets(blockId);

    });


    // ==========================================
    // Restore Filters After Search
    // ==========================================

    if (selectedProject) {

        loadFilterBlocks(
            selectedProject,
            selectedBlock
        );

    }

});

</script>

@endsection
