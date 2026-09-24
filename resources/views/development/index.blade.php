@extends('app')

@section('content')

<div class="container-fluid">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Development Status
            </h4>

            <p class="text-muted mb-0">
                Manage development status of plots.
            </p>
        </div>



        
        <div>
            @can('development.view')
                <a href="{{ route('development.export-excel', request()->query()) }}"
                class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i>
                    Export Excel 
                </a>
            @endcan

            @can('development.create')
                <a href="{{ route('development.create') }}"
                   class="btn btn-primary">

                    <i class="bi bi-plus-circle me-1"></i>

                    Add Development Status

                </a>
            @endcan


        </div>

    </div>


    {{-- Filters --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Filters
            </strong>

        </div>


        <div class="card-body">

            <form method="GET"
                  action="{{ route('development.index') }}">

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
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}>

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
                                    {{ request('property_type_id') == $propertyType->id ? 'selected' : '' }}>

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

                        <label for="plot_number"
                               class="form-label">

                            Plot Number

                        </label>

                        <input type="text"
                               name="plot_number"
                               id="plot_number"
                               class="form-control"
                               value="{{ request('plot_number') }}"
                               placeholder="Enter plot number">

                    </div>


                    {{-- Overall Status --}}
                    <div class="col-md-3">

                        <label for="overall_status"
                               class="form-label">

                            Overall Status

                        </label>

                        <select name="overall_status"
                                id="overall_status"
                                class="form-select">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="developed"
                                {{ request('overall_status') === 'developed' ? 'selected' : '' }}>
                                Developed
                            </option>

                            <option value="under_development"
                                {{ request('overall_status') === 'under_development' ? 'selected' : '' }}>
                                Under Development
                            </option>

                            <option value="not_developed"
                                {{ request('overall_status') === 'not_developed' ? 'selected' : '' }}>
                                Not Developed
                            </option>

                        </select>

                    </div>


                    {{-- Sewer / Manholes --}}
                    <div class="col-md-3">

                        <label for="sewer_manholes"
                               class="form-label">

                            Sewer / Manholes

                        </label>

                        <select name="sewer_manholes"
                                id="sewer_manholes"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="constructed"
                                {{ request('sewer_manholes') === 'constructed' ? 'selected' : '' }}>
                                Constructed
                            </option>

                            <option value="not_constructed"
                                {{ request('sewer_manholes') === 'not_constructed' ? 'selected' : '' }}>
                                Not Constructed
                            </option>

                        </select>

                    </div>


                    {{-- Asphalt / TST --}}
                    <div class="col-md-3">

                        <label for="asphalt_tst"
                               class="form-label">

                            Asphalt / TST

                        </label>

                        <select name="asphalt_tst"
                                id="asphalt_tst"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="yes"
                                {{ request('asphalt_tst') === 'yes' ? 'selected' : '' }}>
                                Yes
                            </option>

                            <option value="no"
                                {{ request('asphalt_tst') === 'no' ? 'selected' : '' }}>
                                No
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="col-12">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-search me-1"></i>
                            Search

                        </button>


                        <a href="{{ route('development.index') }}"
                           class="btn btn-secondary">

                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Development Table --}}
    <div class="card shadow-sm">

        <div class="card-header bg-light">

            <strong>
                Development Records
            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Project</th>

                            <th>Block</th>

                            <th>Street</th>

                            <th>Plot No</th>

                            <th>Property Type</th>

                            <th>Size</th>

                            <th>Sewer / Manholes</th>

                            <th>Asphalt / TST</th>

                            <th>Overall Status</th>

                            <th>Remarks</th>

                            @canany(['development.edit', 'development.delete'])

                                <th>Actions</th>

                            @endcanany

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($plots as $index => $plot)

                            <tr>

                                <td>
                                    {{ $plots->firstItem() + $index }}
                                </td>


                                <td>
                                    {{ $plot->project?->project_name ?? '-' }}
                                </td>


                                <td>
                                    {{ $plot->block?->block_name ?? '-' }}
                                </td>


                                <td>
                                    {{ $plot->street?->street_name ?? '-' }}
                                </td>


                                <td>
                                    <strong>
                                        {{ $plot->plot_number }}
                                    </strong>
                                </td>


                                <td>
                                    {{ $plot->propertyType?->name ?? '-' }}
                                </td>


                                <td>
                                    {{ $plot->size?->title ?? '-' }}
                                </td>


                                <td>

                                    @if($plot->developmentStatus?->sewer_manholes === 'constructed')

                                        <span class="badge bg-success">
                                            Constructed
                                        </span>

                                    @elseif($plot->developmentStatus?->sewer_manholes === 'not_constructed')

                                        <span class="badge bg-danger">
                                            Not Constructed
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Not Set
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($plot->developmentStatus?->asphalt_tst === 'yes')

                                        <span class="badge bg-success">
                                            Yes
                                        </span>

                                    @elseif($plot->developmentStatus?->asphalt_tst === 'no')

                                        <span class="badge bg-danger">
                                            No
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Not Set
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($plot->developmentStatus?->overall_status === 'developed')

                                        <span class="badge bg-success">
                                            Developed
                                        </span>

                                    @elseif($plot->developmentStatus?->overall_status === 'under_development')

                                        <span class="badge bg-warning text-dark">
                                            Under Development
                                        </span>

                                    @elseif($plot->developmentStatus?->overall_status === 'not_developed')

                                        <span class="badge bg-danger">
                                            Not Developed
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Not Set
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $plot->developmentStatus?->remarks ?? '-' }}

                                </td>


                                @canany(['development.edit', 'development.delete'])

                                    <td class="text-nowrap">

                                        @can('development.edit')

                                            <a href="{{ route('development.edit', $plot) }}"
                                               class="btn btn-sm btn-warning">

                                                <i class="bi bi-pencil-square"></i>

                                            </a>

                                        @endcan


                                        @can('development.delete')

                                            <form action="{{ route('development.destroy', $plot) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this Development status?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        @endcan

                                    </td>

                                @endcanany

                            </tr>

                        @empty

                            <tr>

                                <td colspan="12"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                    No Development records found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($plots->hasPages())

            <div class="card-footer">

                {{ $plots->links() }}

            </div>

        @endif

    </div>

</div>

@endsection


@section('scripts')

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>

$(document).ready(function () {

    let selectedProject = "{{ request('project_id') }}";
    let selectedBlock = "{{ request('block_id') }}";
    let selectedStreet = "{{ request('street_id') }}";


    function loadFilterBlocks(projectId, selectedBlockId = '') {

        let blockDropdown = $('#filter_block');
        let streetDropdown = $('#filter_street');

        blockDropdown.html(
            '<option value="">Loading Blocks...</option>'
        );

        streetDropdown.html(
            '<option value="">All Streets</option>'
        );


        if (!projectId) {

            blockDropdown.html(
                '<option value="">All Blocks</option>'
            );

            return;
        }


        $.get(
            '/get-blocks/' + projectId,
            function (data) {

                blockDropdown.html(
                    '<option value="">All Blocks</option>'
                );


                $.each(data, function (key, block) {

                    blockDropdown.append(
                        '<option value="' +
                        block.id +
                        '">' +
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

            }
        ).fail(function () {

            blockDropdown.html(
                '<option value="">Unable to load blocks</option>'
            );

        });

    }


    function loadFilterStreets(blockId, selectedStreetId = '') {

        let streetDropdown = $('#filter_street');

        streetDropdown.html(
            '<option value="">Loading Streets...</option>'
        );


        if (!blockId) {

            streetDropdown.html(
                '<option value="">All Streets</option>'
            );

            return;
        }


        $.get(
            '/get-streets/' + blockId,
            function (data) {

                streetDropdown.html(
                    '<option value="">All Streets</option>'
                );


                $.each(data, function (key, street) {

                    streetDropdown.append(
                        '<option value="' +
                        street.id +
                        '">' +
                        street.street_name +
                        '</option>'
                    );

                });


                if (selectedStreetId) {

                    streetDropdown.val(
                        selectedStreetId
                    );

                }

            }
        ).fail(function () {

            streetDropdown.html(
                '<option value="">Unable to load streets</option>'
            );

        });

    }


    $('#filter_project').on('change', function () {

        let projectId = $(this).val();

        loadFilterBlocks(projectId);

    });


    $('#filter_block').on('change', function () {

        let blockId = $(this).val();

        loadFilterStreets(blockId);

    });


    if (selectedProject) {

        loadFilterBlocks(
            selectedProject,
            selectedBlock
        );

    }

});

</script>

@endsection
