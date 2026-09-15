{{-- @extends('layouts.app') --}}
@extends('app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Assign Plot Size</h4>

        <a href="{{ route('plot-size-assignments.index') }}"
           class="btn btn-secondary">
            Back
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
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Plot Size Assignment</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('plot-size-assignments.store') }}"
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

                    @error('project_id')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

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

                    @error('block_id')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Property Type --}}
                <div class="mb-3">

                    <label for="property_type" class="form-label">
                        Property Type <span class="text-danger">*</span>
                    </label>

                    <select name="property_type_id"
                            id="property_type"
                            class="form-select"
                            required
                            disabled>

                        <option value="">
                            -- Select Block First --
                        </option>

                        @foreach($propertyTypes as $propertyType)

                            <option value="{{ $propertyType->id }}">
                                {{ $propertyType->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('property_type_id')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Plot Size --}}
                <div class="mb-3">

                    <label for="plotsize" class="form-label">
                        Plot Size <span class="text-danger">*</span>
                    </label>

                    <select name="plotsize_id"
                            id="plotsize"
                            class="form-select"
                            required
                            disabled>

                        <option value="">
                            -- Select Property Type First --
                        </option>

                    </select>

                    @error('plotsize_id')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mt-4">

                    <button type="submit"
                            class="btn btn-primary">

                        Save Assignment

                    </button>

                    <a href="{{ route('plot-size-assignments.index') }}"
                       class="btn btn-secondary">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const projectSelect = document.getElementById('project');
    const blockSelect = document.getElementById('block');
    const propertyTypeSelect = document.getElementById('property_type');
    const sizeSelect = document.getElementById('plotsize');


    /*
    |--------------------------------------------------------------------------
    | Project Change
    |--------------------------------------------------------------------------
    */

    projectSelect.addEventListener('change', function () {

        const projectId = this.value;


        // Reset Block
        blockSelect.innerHTML =
            '<option value="">-- Select Block --</option>';

        blockSelect.disabled = true;


        // Reset Property Type
        propertyTypeSelect.value = '';
        propertyTypeSelect.disabled = true;


        // Reset Size
        sizeSelect.innerHTML =
            '<option value="">-- Select Property Type First --</option>';

        sizeSelect.disabled = true;


        if (!projectId) {
            blockSelect.innerHTML =
                '<option value="">-- Select Project First --</option>';

            return;
        }


        blockSelect.innerHTML =
            '<option value="">Loading Blocks...</option>';


        fetch(
            "{{ url('/admin/plot-size-assignments/blocks') }}/" + projectId
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


    /*
    |--------------------------------------------------------------------------
    | Block Change
    |--------------------------------------------------------------------------
    */

    blockSelect.addEventListener('change', function () {

        const blockId = this.value;


        // Reset Size
        sizeSelect.innerHTML =
            '<option value="">-- Select Property Type First --</option>';

        sizeSelect.disabled = true;


        if (!blockId) {

            propertyTypeSelect.value = '';
            propertyTypeSelect.disabled = true;

            return;
        }


        // Property Type can now be selected
        propertyTypeSelect.disabled = false;

    });


    /*
    |--------------------------------------------------------------------------
    | Property Type Change
    |--------------------------------------------------------------------------
    */

    propertyTypeSelect.addEventListener('change', function () {

        const projectId = projectSelect.value;
        const blockId = blockSelect.value;
        const propertyTypeId = this.value;


        sizeSelect.innerHTML =
            '<option value="">-- Select Size --</option>';

        sizeSelect.disabled = true;


        if (!projectId || !blockId || !propertyTypeId) {

            return;
        }


        sizeSelect.innerHTML =
            '<option value="">Loading Sizes...</option>';


        const url =
            "{{ route('plot-size-assignments.sizes') }}" +
            "?project_id=" + encodeURIComponent(projectId) +
            "&block_id=" + encodeURIComponent(blockId) +
            "&property_type_id=" + encodeURIComponent(propertyTypeId);


        fetch(url)
            .then(response => response.json())
            .then(data => {

                sizeSelect.innerHTML =
                    '<option value="">-- Select Size --</option>';


                if (data.length === 0) {

                    sizeSelect.innerHTML =
                        '<option value="">No size assigned for this combination</option>';

                    sizeSelect.disabled = true;

                    return;
                }


                data.forEach(size => {

                    const option = document.createElement('option');

                    option.value = size.id;

                    option.textContent =
                        size.title + ' (' + size.size_area + ' sqyd)';

                    sizeSelect.appendChild(option);

                });


                sizeSelect.disabled = false;

            })
            .catch(error => {

                console.error(error);

                sizeSelect.innerHTML =
                    '<option value="">Unable to load sizes</option>';

            });

    });

});

</script>

@endsection