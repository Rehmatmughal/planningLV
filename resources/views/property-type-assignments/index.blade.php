{{-- @extends('layouts.app') --}}
@extends('app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Property Type Assignments</h4>

            <p class="text-muted mb-0">
                Project aur Block ke sath assigned Property Types
            </p>
        </div>

        <a href="{{ route('property-type-assignments.create') }}"
           class="btn btn-primary">

            <i class="fa fa-plus"></i>
            Add Assignment

        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Filter --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>
                <i class="fa fa-filter"></i>
                Filter Assignments
            </strong>
        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('property-type-assignments.index') }}"
            >

                <div class="row g-3">

                    {{-- Project --}}
                    <div class="col-md-4">

                        <label for="project_id" class="form-label">
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
                    <div class="col-md-4">

                        <label for="block_id" class="form-label">
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
                    <div class="col-md-4">

                        <label for="property_type_id" class="form-label">
                            Property Type
                        </label>

                        <select
                            name="property_type_id"
                            id="property_type_id"
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


                    {{-- Buttons --}}
                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-search"></i>
                            Filter
                        </button>


                        <a
                            href="{{ route('property-type-assignments.index') }}"
                            class="btn btn-secondary"
                        >
                            <i class="fa fa-refresh"></i>
                            Reset
                        </a>

                    </div>

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

                            <th width="170">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($assignments as $assignment)

                            <tr>

                                <td>
                                    {{ $assignments->firstItem() + $loop->index }}
                                </td>


                                <td>
                                    {{ $assignment->project->project_name ?? '-' }}
                                </td>


                                <td>
                                    {{ $assignment->block->block_name ?? '-' }}
                                </td>


                                <td>
                                    {{ $assignment->propertyType->name ?? '-' }}
                                </td>


                                <td>

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route(
                                            'property-type-assignments.edit',
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
                                            'property-type-assignments.destroy',
                                            $assignment
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this assignment?'
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
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No Property Type Assignments found.
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


{{-- Dynamic Blocks --}}
<script>

document.getElementById('project_id').addEventListener('change', function () {

    let projectId = this.value;

    let blockSelect = document.getElementById('block_id');


    blockSelect.innerHTML =
        '<option value="">Loading Blocks...</option>';


    if (!projectId) {

        blockSelect.innerHTML =
            '<option value="">All Blocks</option>';

        return;
    }


    fetch(
        "{{ url('/property-type-assignments/blocks') }}/" + projectId
    )
    .then(response => response.json())
    .then(blocks => {

        blockSelect.innerHTML =
            '<option value="">All Blocks</option>';


        blocks.forEach(block => {

            let option = document.createElement('option');

            option.value = block.id;

            option.textContent = block.block_name;

            blockSelect.appendChild(option);

        });

    })
    .catch(error => {

        console.error(error);

        blockSelect.innerHTML =
            '<option value="">Unable to load blocks</option>';

    });

});

</script>

@endsection
