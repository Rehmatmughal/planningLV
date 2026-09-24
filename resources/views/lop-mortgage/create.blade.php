@extends('app')

@section('content')

<div class="container mt-4">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add LOP & Mortgage Status</h4>
        </div>

        <div class="card-body">

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif


            {{-- Success --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif


            <form method="POST"
                  action="{{ route('lop-mortgage.store') }}">

                @csrf

                <div class="row">

                    {{-- LEFT COLUMN --}}
                    <div class="col-md-6">

                        {{-- Project --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Project <span class="text-danger">*</span>
                            </label>

                            <select name="project_id"
                                    id="project"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select Project --
                                </option>

                                @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)

                                    <option value="{{ $project->id }}"
                                        {{ old('project_id') == $project->id ? 'selected' : '' }}>

                                        {{ $project->project_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Property Type --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Property Type <span class="text-danger">*</span>
                            </label>

                            <select name="property_type_id"
                                    id="propertytype"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select Property Type --
                                </option>

                                @foreach(\App\Models\PropertyType::orderBy('name')->get() as $propertyType)

                                    <option value="{{ $propertyType->id }}"
                                        {{ old('property_type_id') == $propertyType->id ? 'selected' : '' }}>

                                        {{ $propertyType->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Block --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Block <span class="text-danger">*</span>
                            </label>

                            <select name="block_id"
                                    id="block"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select Block --
                                </option>

                            </select>

                        </div>


                        {{-- Street --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Street
                            </label>

                            <select name="street_id"
                                    id="street"
                                    class="form-control">

                                <option value="">
                                    -- All Streets --
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- RIGHT COLUMN --}}
                    <div class="col-md-6">

                        {{-- Plot Number Search --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Plot Number <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input type="text"
                                       id="plot_number"
                                       class="form-control"
                                       placeholder="Enter plot number">

                                <button type="button"
                                        id="searchPlotBtn"
                                        class="btn btn-primary">

                                    <i class="fa fa-search"></i>
                                    Search

                                </button>

                            </div>

                            <small class="text-muted">
                                Select Project, Property Type and Block first.
                            </small>

                        </div>


                        {{-- Search Results --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Matching Plots
                            </label>

                            <select name="plot_id"
                                    id="plot_id"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Search plot first --
                                </option>

                            </select>

                        </div>


                        {{-- Selected Plot Information --}}
                        <div id="selectedPlotInfo"
                             class="alert alert-light border d-none">

                            <strong>Selected Plot</strong>

                            <div class="mt-2">

                                <div>
                                    <strong>Plot No:</strong>
                                    <span id="selectedPlotNumber">-</span>
                                </div>

                                <div>
                                    <strong>Street:</strong>
                                    <span id="selectedStreet">-</span>
                                </div>

                                <div>
                                    <strong>Size:</strong>
                                    <span id="selectedSize">-</span>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                {{-- STATUS SECTION --}}
                <div class="row">

                    {{-- LOP --}}
                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">
                                LOP Status <span class="text-danger">*</span>
                            </label>

                            <select name="lop_status"
                                    id="lop_status"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select LOP Status --
                                </option>

                                <option value="lop"
                                    {{ old('lop_status') === 'lop' ? 'selected' : '' }}>
                                    LOP
                                </option>

                                <option value="non_lop"
                                    {{ old('lop_status') === 'non_lop' ? 'selected' : '' }}>
                                    Non-LOP
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- Mortgage --}}
                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">
                                Mortgage <span class="text-danger">*</span>
                            </label>

                            <select name="is_mortgaged"
                                    id="is_mortgaged"
                                    class="form-control"
                                    required>

                                <option value="">
                                    -- Select Mortgage Status --
                                </option>

                                <option value="yes"
                                    {{ old('is_mortgaged') === 'yes' ? 'selected' : '' }}>
                                    Yes
                                </option>

                                <option value="no"
                                    {{ old('is_mortgaged') === 'no' ? 'selected' : '' }}>
                                    No
                                </option>

                            </select>

                            <small class="text-muted">
                                Mortgage YES is only allowed when LOP is LOP.
                            </small>

                        </div>

                    </div>


                    {{-- Remarks --}}
                    <div class="col-12">

                        <div class="mb-3">

                            <label class="form-label">
                                Remarks
                            </label>

                            <textarea name="remarks"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Enter remarks">{{ old('remarks') }}</textarea>

                        </div>

                    </div>

                </div>


                {{-- BUTTONS --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">

                    <a href="{{ route('lop-mortgage.index') }}"
                       class="btn btn-secondary">

                        <i class="fa fa-arrow-left"></i>
                        Back

                    </a>


                    <div class="d-flex gap-2">

                        <a href="{{ route('lop-mortgage.create') }}"
                           class="btn btn-outline-danger"
                           onclick="return confirm('Are you sure you want to reset all fields?')">

                            <i class="fa fa-refresh"></i>
                            Reset

                        </a>


                        <button type="submit"
                                class="btn btn-success px-4">

                            <i class="fa fa-save"></i>
                            Save LOP & Mortgage

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

    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    */

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Old Values
    |--------------------------------------------------------------------------
    */

    const oldProject =
        "{{ old('project_id') }}";

    const oldBlock =
        "{{ old('block_id') }}";

    const oldStreet =
        "{{ old('street_id') }}";

    const oldPlot =
        "{{ old('plot_id') }}";


    /*
    |--------------------------------------------------------------------------
    | Project Change
    |--------------------------------------------------------------------------
    */

    $('#project').on('change', function () {

        let projectID = $(this).val();


        // Reset Block
        $('#block').html(
            '<option value="">-- Select Block --</option>'
        );


        // Reset Street
        $('#street').html(
            '<option value="">-- All Streets --</option>'
        );


        // Reset Plot
        $('#plot_id').html(
            '<option value="">-- Search plot first --</option>'
        );


        $('#selectedPlotInfo').addClass('d-none');


        if (!projectID) {
            return;
        }


        $('#block').html(
            '<option value="">Loading blocks...</option>'
        );


        $.get(
            '/get-blocks/' + projectID,
            function (data) {

                $('#block').html(
                    '<option value="">-- Select Block --</option>'
                );


                $.each(data, function (i, item) {

                    $('#block').append(
                        '<option value="' +
                        item.id +
                        '">' +
                        item.block_name +
                        '</option>'
                    );

                });


                if (oldBlock) {

                    $('#block')
                        .val(oldBlock)
                        .trigger('change');

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Block Change
    |--------------------------------------------------------------------------
    */

    $('#block').on('change', function () {

        let blockID = $(this).val();


        $('#street').html(
            '<option value="">-- All Streets --</option>'
        );


        $('#plot_id').html(
            '<option value="">-- Search plot first --</option>'
        );


        $('#selectedPlotInfo').addClass('d-none');


        if (!blockID) {
            return;
        }


        $('#street').html(
            '<option value="">Loading streets...</option>'
        );


        $.get(
            '/get-streets/' + blockID,
            function (data) {

                $('#street').html(
                    '<option value="">-- All Streets --</option>'
                );


                $.each(data, function (i, item) {

                    $('#street').append(
                        '<option value="' +
                        item.id +
                        '">' +
                        item.street_name +
                        '</option>'
                    );

                });


                if (oldStreet) {

                    $('#street').val(oldStreet);

                }

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Search Plot
    |--------------------------------------------------------------------------
    */

    $('#searchPlotBtn').on('click', function () {

        let projectID =
            $('#project').val();

        let propertyTypeID =
            $('#propertytype').val();

        let blockID =
            $('#block').val();

        let streetID =
            $('#street').val();

        let plotNumber =
            $('#plot_number').val().trim();


        /*
        |--------------------------------------------------------------------------
        | Basic Validation
        |--------------------------------------------------------------------------
        */

        if (!projectID) {

            alert('Please select Project first.');

            return;

        }


        if (!propertyTypeID) {

            alert('Please select Property Type first.');

            return;

        }


        if (!blockID) {

            alert('Please select Block first.');

            return;

        }


        if (!plotNumber) {

            alert('Please enter Plot Number.');

            $('#plot_number').focus();

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        $('#plot_id').html(
            '<option value="">Searching plots...</option>'
        );


        $('#selectedPlotInfo').addClass('d-none');


        /*
        |--------------------------------------------------------------------------
        | AJAX Search
        |--------------------------------------------------------------------------
        */

        $.get(
            "{{ route('lop-mortgage.search-plots') }}",
            {
                project_id: projectID,
                property_type_id: propertyTypeID,
                block_id: blockID,
                street_id: streetID,
                plot_number: plotNumber
            },

            function (data) {

                $('#plot_id').html(
                    '<option value="">-- Select Matching Plot --</option>'
                );


                if (data.length === 0) {

                    $('#plot_id').html(
                        '<option value="">No matching plot found</option>'
                    );

                    return;

                }


                $.each(data, function (i, plot) {

                    let text =
                        plot.plot_number;

                    if (plot.street) {

                        text +=
                            ' - ' + plot.street;

                    }

                    if (plot.size) {

                        text +=
                            ' - ' + plot.size;

                    }


                    $('#plot_id').append(

                        '<option value="' +
                        plot.id +
                        '" ' +
                        'data-plot-number="' +
                        plot.plot_number +
                        '" ' +
                        'data-street="' +
                        (plot.street ?? '') +
                        '" ' +
                        'data-size="' +
                        (plot.size ?? '') +
                        '">' +

                        text +

                        '</option>'

                    );

                });


                /*
                |--------------------------------------------------------------------------
                | Restore Old Plot
                |--------------------------------------------------------------------------
                */

                if (oldPlot) {

                    $('#plot_id')
                        .val(oldPlot)
                        .trigger('change');

                }

            }
        )

        .fail(function (xhr) {

            $('#plot_id').html(
                '<option value="">Search failed</option>'
            );

            console.error(xhr);

            alert(
                'Plot search failed. Please try again.'
            );

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Plot Selected
    |--------------------------------------------------------------------------
    */

    $('#plot_id').on('change', function () {

        let selected =
            $(this).find(':selected');


        let plotNumber =
            selected.data('plot-number');

        let street =
            selected.data('street');

        let size =
            selected.data('size');


        if (!selected.val()) {

            $('#selectedPlotInfo')
                .addClass('d-none');

            return;

        }


        $('#selectedPlotNumber')
            .text(plotNumber || '-');

        $('#selectedStreet')
            .text(street || '-');

        $('#selectedSize')
            .text(size || '-');


        $('#selectedPlotInfo')
            .removeClass('d-none');

    });


    /*
    |--------------------------------------------------------------------------
    | Enter Key = Search
    |--------------------------------------------------------------------------
    */

    $('#plot_number').on('keypress', function (e) {

        if (e.which === 13) {

            e.preventDefault();

            $('#searchPlotBtn').click();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Initial Page Load
    |--------------------------------------------------------------------------
    */

    if (oldProject) {

        $('#project')
            .val(oldProject)
            .trigger('change');

    }

});

</script>

@endsection