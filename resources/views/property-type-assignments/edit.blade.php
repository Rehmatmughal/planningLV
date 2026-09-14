@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Edit Property Type Assignment</h4>
            <p class="text-muted mb-0">
                Update Project, Block or Property Type
            </p>
        </div>

        <a href="{{ route('property-type-assignments.index') }}"
           class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i>
            Back
        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm">

        <div class="card-body">

            <form
                action="{{ route('property-type-assignments.update', $propertyTypeAssignment) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                {{-- Project --}}
                <div class="mb-3">

                    <label for="project_id" class="form-label">
                        Project
                    </label>

                    <select
                        name="project_id"
                        id="project_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Project
                        </option>

                        @foreach($projects as $project)

                            <option
                                value="{{ $project->id }}"
                                {{ old(
                                    'project_id',
                                    $propertyTypeAssignment->project_id
                                ) == $project->id ? 'selected' : '' }}
                            >
                                {{ $project->project_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Block --}}
                <div class="mb-3">

                    <label for="block_id" class="form-label">
                        Block
                    </label>

                    <select
                        name="block_id"
                        id="block_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Block
                        </option>

                        @foreach($blocks as $block)

                            <option
                                value="{{ $block->id }}"
                                {{ old(
                                    'block_id',
                                    $propertyTypeAssignment->block_id
                                ) == $block->id ? 'selected' : '' }}
                            >
                                {{ $block->block_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Property Type --}}
                <div class="mb-4">

                    <label for="property_type_id" class="form-label">
                        Property Type
                    </label>

                    <select
                        name="property_type_id"
                        id="property_type_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Property Type
                        </option>

                        @foreach($propertyTypes as $propertyType)

                            <option
                                value="{{ $propertyType->id }}"
                                {{ old(
                                    'property_type_id',
                                    $propertyTypeAssignment->property_type_id
                                ) == $propertyType->id ? 'selected' : '' }}
                            >
                                {{ $propertyType->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-save"></i>
                        Update Assignment
                    </button>

                    <a
                        href="{{ route('property-type-assignments.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.getElementById('project_id').addEventListener('change', function () {

    let projectId = this.value;

    let blockSelect = document.getElementById('block_id');

    blockSelect.innerHTML =
        '<option value="">Loading Blocks...</option>';


    if (!projectId) {

        blockSelect.innerHTML =
            '<option value="">Select Block</option>';

        return;
    }


    fetch(
        "{{ url('/property-type-assignments/blocks') }}/" + projectId
    )
    .then(response => response.json())
    .then(blocks => {

        blockSelect.innerHTML =
            '<option value="">Select Block</option>';


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