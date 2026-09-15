{{-- @extends('layouts.app') --}}
@extends('app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Plot Size Assignments
            </h4>

            <p class="text-muted mb-0">
                Project, Block and Property Type ke sath assigned Sizes
            </p>
        </div>

        <a
            href="{{ route('plot-size-assignments.create') }}"
            class="btn btn-primary"
        >
            <i class="fa fa-plus"></i>
            Add Size Assignment
        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('plot-size-assignments.index') }}"
            >

                <div class="row g-3">

                    {{-- Project --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Project
                        </label>

                        <select
                            name="project_id"
                            id="project_id"
                            class="form-select"
                        >

                            <option value="">
                                All Projects
                            </option>

                            @foreach($projects as $project)

                                <option
                                    value="{{ $project->id }}"
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}
                                >
                                    {{ $project->project_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Block --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Block
                        </label>

                        <select
                            name="block_id"
                            id="block_id"
                            class="form-select"
                        >

                            <option value="">
                                All Blocks
                            </option>

                            @foreach($blocks as $block)

                                <option
                                    value="{{ $block->id }}"
                                    {{ request('block_id') == $block->id ? 'selected' : '' }}
                                >
                                    {{ $block->block_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Property Type --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Property Type
                        </label>

                        <select
                            name="property_type_id"
                            class="form-select"
                        >

                            <option value="">
                                All Property Types
                            </option>

                            @foreach($propertyTypes as $propertyType)

                                <option
                                    value="{{ $propertyType->id }}"
                                    {{ request('property_type_id') == $propertyType->id ? 'selected' : '' }}
                                >
                                    {{ $propertyType->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Size --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Size
                        </label>

                        <select
                            name="plotsize_id"
                            id="plotsize_id"
                            class="form-select"
                        >

                            <option value="">
                                All Sizes
                            </option>

                            @foreach($sizes as $size)

                                <option
                                    value="{{ $size->id }}"
                                    {{ request('plotsize_id') == $size->id ? 'selected' : '' }}
                                >
                                    {{ $size->title }}
                                    @if($size->size_area)
                                        - {{ $size->size_area }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="mt-3">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-filter"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route('plot-size-assignments.index') }}"
                        class="btn btn-secondary"
                    >
                        <i class="fa fa-refresh"></i>
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- Assignments Table --}}
    <div class="card shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th width="70">
                                #
                            </th>

                            <th>
                                Project
                            </th>

                            <th>
                                Block
                            </th>

                            <th>
                                Property Type
                            </th>

                            <th>
                                Size
                            </th>

                            <th>
                                Size Area
                            </th>

                            <th width="180">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($assignments as $assignment)

                            <tr>

                                {{-- Number --}}
                                <td>
                                    {{ $assignments->firstItem() + $loop->index }}
                                </td>


                                {{-- Project --}}
                                <td>
                                    {{ $assignment->project->project_name ?? '-' }}
                                </td>


                                {{-- Block --}}
                                <td>
                                    {{ $assignment->block->block_name ?? '-' }}
                                </td>


                                {{-- Property Type --}}
                                <td>
                                    {{ $assignment->propertyType->name ?? '-' }}
                                </td>


                                {{-- Size --}}
                                <td>
                                    {{ $assignment->plotsize->title ?? '-' }}
                                </td>


                                {{-- Size Area --}}
                                <td>
                                    {{ $assignment->plotsize->size_area ?? '-' }}
                                </td>


                                {{-- Actions --}}
                                <td>

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route(
                                            'plot-size-assignments.edit',
                                            $assignment
                                        ) }}"
                                        class="btn btn-sm btn-warning mb-1"
                                    >
                                        <i class="fa fa-edit"></i>
                                        Edit
                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        action="{{ route(
                                            'plot-size-assignments.destroy',
                                            $assignment
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this Size Assignment?'
                                        );"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger mb-1"
                                        >
                                            <i class="fa fa-trash"></i>
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >
                                    No Plot Size Assignments found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="mt-3">

                {{ $assignments->links() }}

            </div>

        </div>

    </div>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const projectSelect = document.getElementById('project_id');

    const blockSelect = document.getElementById('block_id');

    const sizeSelect = document.getElementById('plotsize_id');


    projectSelect.addEventListener('change', function () {

        const projectId = this.value;

        blockSelect.innerHTML =
            '<option value="">All Blocks</option>';

        sizeSelect.innerHTML =
            '<option value="">All Sizes</option>';


        if (!projectId) {
            return;
        }


        // Load Blocks
        fetch(
            "{{ url('/plot-size-assignments/blocks') }}/"
            + projectId
        )
        .then(response => response.json())
        .then(data => {

            data.forEach(function (block) {

                const option =
                    document.createElement('option');

                option.value = block.id;

                option.textContent = block.block_name;

                blockSelect.appendChild(option);

            });

        });


        // Load Sizes
        fetch(
            "{{ url('/plot-size-assignments/sizes') }}/"
            + projectId
        )
        .then(response => response.json())
        .then(data => {

            data.forEach(function (size) {

                const option =
                    document.createElement('option');

                option.value = size.id;

                option.textContent =
                    size.title +
                    (size.size_area
                        ? ' - ' + size.size_area
                        : '');

                sizeSelect.appendChild(option);

            });

        });

    });

});

</script>
@endsection
