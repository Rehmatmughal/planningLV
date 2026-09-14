@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Assign Property Type</h4>

        <a href="{{ route('property-type-assignments.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

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

        <div class="card-header">
            <strong>Project, Block and Property Type</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('property-type-assignments.store') }}"
                  method="POST">

                @csrf

                {{-- Project --}}
                <div class="mb-3">

                    <label for="project" class="form-label">
                        Project <span class="text-danger">*</span>
                    </label>

                    <select name="project_id"
                            id="project"
                            class="form-select"
                            required>

                        <option value="">
                            -- Select Project --
                        </option>

                        @foreach($projects as $project)

                            <option value="{{ $project->id }}"
                                {{ old('project_id') == $project->id ? 'selected' : '' }}>

                                {{ $project->project_name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Block --}}
                <div class="mb-3">

                    <label for="block" class="form-label">
                        Block <span class="text-danger">*</span>
                    </label>

                    <select name="block_id"
                            id="block"
                            class="form-select"
                            required
                            disabled>

                        <option value="">
                            -- Select Project First --
                        </option>

                    </select>

                </div>


                {{-- Property Type --}}
                <div class="mb-3">

                    <label for="property_type" class="form-label">
                        Property Type <span class="text-danger">*</span>
                    </label>

                    <select name="property_type_id"
                            id="property_type"
                            class="form-select"
                            required>

                        <option value="">
                            -- Select Property Type --
                        </option>

                        @foreach($propertyTypes as $propertyType)

                            <option value="{{ $propertyType->id }}"
                                {{ old('property_type_id') == $propertyType->id ? 'selected' : '' }}>

                                {{ $propertyType->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <button type="submit"
                        class="btn btn-primary">
                    Save Assignment
                </button>

                <a href="{{ route('property-type-assignments.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const projectSelect = document.getElementById('project');
    const blockSelect = document.getElementById('block');

    projectSelect.addEventListener('change', function () {

        const projectId = this.value;

        blockSelect.innerHTML =
            '<option value="">-- Select Block --</option>';

        blockSelect.disabled = true;

        if (!projectId) {

            blockSelect.innerHTML =
                '<option value="">-- Select Project First --</option>';

            return;
        }

        blockSelect.innerHTML =
            '<option value="">Loading Blocks...</option>';

        fetch(
            "{{ url('/property-type-assignments/blocks') }}/" + projectId
        )
        .then(response => response.json())
        .then(data => {

            blockSelect.innerHTML =
                '<option value="">-- Select Block --</option>';

            data.forEach(block => {

                const option = document.createElement('option');

                option.value = block.id;
                option.textContent = block.block_name;

                blockSelect.appendChild(option);

            });

            blockSelect.disabled = false;

        })
        .catch(error => {

            console.error(error);

            blockSelect.innerHTML =
                '<option value="">Unable to load blocks</option>';

        });

    });

});

</script>

@endsection