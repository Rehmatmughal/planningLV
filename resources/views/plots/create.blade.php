@extends('app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Plot</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="alert-area"></div>
            <form id="plotForm"
                method="POST"
                action="{{ route('plots.store') }}">
                @csrf
            {{-- <form id="plotForm">
                @csrf --}}
                <div class="row">
                    {{-- left column --}}
                    <div class="col-md-6">
                        {{-- Project --}}
                        <div class="mb-3">
                            <label>Project</label>
                            <select name="project_id" id="project" class="form-control" required>
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('project_id') == $p->id ? 'selected' : ''}}>
                                        {{ $p->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Block --}}
                        <div class="mb-3">
                            <label>Block</label>
                            <select name="block_id" id="block" class="form-control">
                                <option value="">-- Select Block --</option>
                            </select>
                        </div>

                        {{-- Street --}}
                        <div class="mb-3">
                            <label>Street</label>
                            <select name="street_id" id="street" class="form-control">
                                <option value="">-- Select Street --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Plot Number</label>
                            <input type="text" 
                                name="plot_number"
                                class="form-control"
                                value="{{old('plot_number')}}"
                                required>
                        </div>
                    </div>
                    {{-- right column --}}
                    <div class="col-md-6">
                        {{-- Property Type --}}
                        <div class="mb-3">
                            <label>Property Type</label>

                            <select name="property_type_id"
                                    id="propertytype"
                                    class="form-control"
                                    required>

                                <option value="">-- Select Property Type --</option>

                                @foreach($propertytypes as $propertytype)
                                    <option value="{{ $propertytype->id }}"
                                        {{ old('property_type_id') == $propertytype->id ? 'selected' : ''}}>
                                        {{ $propertytype->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Size</label>
                            <select name="size_id" id="plotsize" class="form-control">
                            <option value="">-- Select Size --</option>
                                @foreach($sizes as $s)
                                    <option value="{{ $s->id }}">{{ $s->title }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="size" class="form-control"> --}}
                        </div>
                        {{-- <div class="mb-3"> --}}
                            <div class="mb-3">
                                <label>Category of plot</label>

                                <select name="category_id" id="plotcategory" class="form-control" required>
                                    <option value="">-- Select Category --</option>

                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->id }} - {{ $category->category_title }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            {{-- <label>Category of plot</label> 
                            <select name="category_id" id="plotcategory" class="form-control">
                            <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->id }} - {{ $category->category_title }}</option>
                                @endforeach
                            </select> --}}
                            {{-- <input type="text" name="size" class="form-control"> --}}
                        {{-- </div> --}}

                        <div class="mb-3">
                            <label>Numbering Type</label>
                            <select name="numbering_type" class="form-control">
                                <option value="blockwise"
                                    {{ old('numbering_type', 'blockwise') == 'blockwise' ? 'selected' : '' }}>
                                    Blockwise
                                </option>
                                <option value="streetwise"
                                    {{ old('numbering_type') == 'streetwise' ? 'selected' : ''}}>
                                    Streetwise
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <textarea name="remarks" 
                                class="form-control">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Save Plot</button>
                </div> --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    {{-- Back Button --}}
                    <a href="{{ route('plots.index') }}"
                    class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i>
                        Back to Plots
                    </a>

                    <div class="d-flex gap-2">

                        {{-- Reset Button --}}
                        <a href="{{ route('plots.create') }}"
                        class="btn btn-outline-danger"
                        onclick="return confirm('Are you sure you want to reset all fields?')">
                            <i class="fa fa-refresh"></i>
                            Reset
                        </a>

                        {{-- Save Button --}}
                        <button type="submit"
                                class="btn btn-success px-4">
                            <i class="fa fa-save"></i>
                            Save Plot
                        </button>

                    </div>

                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const oldProject = "{{ old('project_id') }}";
    const oldBlock = "{{ old('block_id') }}";
    const oldStreet = "{{ old('street_id') }}";
    const oldSize = "{{ old('size_id') }}";
    const oldPropertyType = "{{ old('property_type_id') }}";


    function loadAssignedSizes() {

        let projectID = $('#project').val();
        let blockID = $('#block').val();
        let propertyTypeID = $('#propertytype').val();

        $('#plotsize').html(
            '<option value="">-- Select Size --</option>'
        );

        if (!projectID || !blockID || !propertyTypeID) {
            return;
        }

        $('#plotsize').html(
            '<option value="">Loading assigned sizes...</option>'
        );

        $.get(
            '/admin/get-assigned-sizes/'
            + projectID + '/'
            + blockID + '/'
            + propertyTypeID,

            function (data) {

                $('#plotsize').html(
                    '<option value="">-- Select Size --</option>'
                );

                if (data.length === 0) {

                    $('#plotsize').html(
                        '<option value="">No size assigned</option>'
                    );

                    return;
                }

                $.each(data, function (i, item) {

                    $('#plotsize').append(
                        '<option value="' + item.id + '">'
                        + item.title
                        + (item.size_area
                            ? ' - ' + item.size_area
                            : '')
                        + '</option>'
                    );

                });

                if (oldSize) {
                    $('#plotsize').val(oldSize);
                }

            }
        );
    }


    // Project change par Blocks load honge
    $('#project').on('change', function () {

        let projectID = $(this).val();

        $('#block').html(
            '<option value="">Loading blocks...</option>'
        );

        $('#street').html(
            '<option value="">-- Select Street --</option>'
        );

        $('#plotsize').html(
            '<option value="">-- Select Size --</option>'
        );

        if (!projectID) {

            $('#block').html(
                '<option value="">-- Select Block --</option>'
            );

            return;
        }

        $.get('/get-blocks/' + projectID, function (data) {

            $('#block').html(
                '<option value="">-- Select Block --</option>'
            );

            $.each(data, function (i, item) {

                $('#block').append(
                    '<option value="' + item.id + '">'
                    + item.block_name
                    + '</option>'
                );

            });

            if (oldBlock) {
                $('#block').val(oldBlock).trigger('change');
            }

        });

    });


    // Block change par Streets load hongi
    $('#block').on('change', function () {

        let blockID = $(this).val();

        $('#street').html(
            '<option value="">Loading streets...</option>'
        );

        $('#plotsize').html(
            '<option value="">-- Select Size --</option>'
        );

        if (!blockID) {

            $('#street').html(
                '<option value="">-- Select Street --</option>'
            );

            return;
        }

        $.get('/get-streets/' + blockID, function (data) {

            $('#street').html(
                '<option value="">-- Select Street --</option>'
            );

            $.each(data, function (i, item) {

                $('#street').append(
                    '<option value="' + item.id + '">'
                    + item.street_name
                    + '</option>'
                );

            });

            if (oldStreet) {
                $('#street').val(oldStreet);
            }

        });

        loadAssignedSizes();

    });


    // Property Type change par assigned Sizes load hongi
    $('#propertytype').on('change', function () {

        loadAssignedSizes();

    });


    // Page load par old values restore hongi
    if (oldProject) {

        $('#project').val(oldProject).trigger('change');

    }

});
</script>
@endsection