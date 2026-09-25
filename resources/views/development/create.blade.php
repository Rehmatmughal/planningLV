@extends('app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            {{-- <h4 class="mb-1">
                Add Development Status
            </h4>

            <p class="text-muted mb-0">
                Add development status for a plot.
            </p> --}}
            <h4 class="mb-1">
                Development Status
            </h4>

            <p class="text-muted mb-0">
                Select a plot to create or update its development status.
            </p>

        </div>

        <a href="{{ route('development.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back

        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

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


    {{-- Create Form --}}
    <form action="{{ route('development.store') }}"
          method="POST">

        @csrf


        {{-- Plot Search Card --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">

                <strong>
                    <i class="bi bi-search me-1"></i>
                    Select Plot
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    {{-- Project --}}
                    <div class="col-md-3">

                        <label for="project_id"
                               class="form-label">

                            Project
                            <span class="text-danger">*</span>

                        </label>

                        <select id="project_id"
                                class="form-select">

                            <option value="">
                                Select Project
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
                    <div class="col-md-3">

                        <label for="property_type_id"
                               class="form-label">

                            Property Type
                            <span class="text-danger">*</span>

                        </label>

                        <select id="property_type_id"
                                class="form-select">

                            <option value="">
                                Select Property Type
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
                    <div class="col-md-3">

                        <label for="block_id"
                               class="form-label">

                            Block
                            <span class="text-danger">*</span>

                        </label>

                        <select id="block_id"
                                class="form-select">

                            <option value="">
                                Select Project First
                            </option>

                        </select>

                    </div>


                    {{-- Street --}}
                    <div class="col-md-3">

                        <label for="street_id"
                               class="form-label">

                            Street

                        </label>

                        <select id="street_id"
                                class="form-select">

                            <option value="">
                                All Streets
                            </option>

                        </select>

                    </div>


                    {{-- Plot Number --}}
                    <div class="col-md-6">

                        <label for="plot_number"
                               class="form-label">

                            Plot Number
                            <span class="text-danger">*</span>

                        </label>

                        <div class="input-group">

                            <input type="text"
                                   id="plot_number"
                                   class="form-control"
                                   value="{{ old('plot_number') }}"
                                   placeholder="Enter plot number">

                            <button type="button"
                                    id="searchPlotsBtn"
                                    class="btn btn-primary">

                                <i class="bi bi-search me-1"></i>
                                Search

                            </button>

                        </div>

                        <small class="text-muted">
                            Enter full or partial plot number.
                        </small>

                    </div>


                    {{-- Matching Plots --}}
                    <div class="col-md-6">

                        <label for="plot_id"
                               class="form-label">

                            Matching Plots
                            <span class="text-danger">*</span>

                        </label>

                        <select name="plot_id"
                                id="plot_id"
                                class="form-select">

                            <option value="">
                                Search plots first
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Search Message --}}
                <div id="searchMessage"
                     class="mt-3"
                     style="display:none;">
                </div>


                {{-- Selected Plot Information --}}
                <div id="selectedPlotInfo"
                     class="alert alert-info mt-3"
                     style="display:none;">

                    <div class="row">

                        <div class="col-md-3">
                            <strong>Plot No:</strong>
                            <span id="infoPlotNumber">-</span>
                        </div>

                        <div class="col-md-3">
                            <strong>Project:</strong>
                            <span id="infoProject">-</span>
                        </div>

                        <div class="col-md-3">
                            <strong>Block:</strong>
                            <span id="infoBlock">-</span>
                        </div>

                        <div class="col-md-3">
                            <strong>Street:</strong>
                            <span id="infoStreet">-</span>
                        </div>

                        <div class="col-md-3 mt-2">
                            <strong>Property Type:</strong>
                            <span id="infoPropertyType">-</span>
                        </div>

                        <div class="col-md-3 mt-2">
                            <strong>Size:</strong>
                            <span id="infoSize">-</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Development Status Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>
                    <i class="bi bi-building me-1"></i>
                    Development Status
                </strong>

                <span id="statusMode"
                    class="badge bg-secondary">
                    Select a Plot
                </span>

            </div>


            {{-- <div class="card-header bg-light">

                <strong>
                    <i class="bi bi-building me-1"></i>
                    Development Status
                </strong>

            </div> --}}


            <div class="card-body">

                <div class="row g-3">

                    {{-- Sewer / Manholes --}}
                    <div class="col-md-4">

                        <label for="sewer_manholes"
                               class="form-label">

                            Sewer / Manholes
                            <span class="text-danger">*</span>

                        </label>

                        <select name="sewer_manholes"
                                id="sewer_manholes"
                                class="form-select"
                                required>

                            <option value="">
                                Select Status
                            </option>

                            <option value="constructed"
                                {{ old('sewer_manholes') === 'constructed' ? 'selected' : '' }}>

                                Constructed

                            </option>

                            <option value="not_constructed"
                                {{ old('sewer_manholes') === 'not_constructed' ? 'selected' : '' }}>

                                Not Constructed

                            </option>

                        </select>

                    </div>


                    {{-- Asphalt / TST --}}
                    <div class="col-md-4">

                        <label for="asphalt_tst"
                               class="form-label">

                            Asphalt / TST
                            <span class="text-danger">*</span>

                        </label>

                        <select name="asphalt_tst"
                                id="asphalt_tst"
                                class="form-select"
                                required>

                            <option value="">
                                Select Status
                            </option>

                            <option value="yes"
                                {{ old('asphalt_tst') === 'yes' ? 'selected' : '' }}>

                                Yes

                            </option>

                            <option value="no"
                                {{ old('asphalt_tst') === 'no' ? 'selected' : '' }}>

                                No

                            </option>

                        </select>

                    </div>


                    {{-- Overall Status --}}
                    <div class="col-md-4">

                        <label for="overall_status"
                               class="form-label">

                            Overall Development Status
                            <span class="text-danger">*</span>

                        </label>

                        <select name="overall_status"
                                id="overall_status"
                                class="form-select"
                                required>

                            <option value="">
                                Select Status
                            </option>

                            <option value="developed"
                                {{ old('overall_status') === 'developed' ? 'selected' : '' }}>

                                Developed

                            </option>

                            <option value="under_development"
                                {{ old('overall_status') === 'under_development' ? 'selected' : '' }}>

                                Under Development

                            </option>

                            <option value="not_developed"
                                {{ old('overall_status') === 'not_developed' ? 'selected' : '' }}>

                                Not Developed

                            </option>

                        </select>

                    </div>


                    {{-- Remarks --}}
                    <div class="col-12">

                        <label for="remarks"
                               class="form-label">

                            Remarks

                        </label>

                        <textarea name="remarks"
                                  id="remarks"
                                  rows="4"
                                  class="form-control"
                                  placeholder="Enter remarks if required">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- Form Buttons --}}
        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('development.index') }}"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back

            </a>

            <button type="reset"
                    class="btn btn-warning">

                <i class="bi bi-arrow-counterclockwise me-1"></i>
                Reset

            </button>

            <button type="submit"
                    id="saveDevelopmentBtn"
                    class="btn btn-success">

                <i class="bi bi-check-circle me-1"></i>
                Save Development Status

            </button>

            {{-- <button type="submit"
                    class="btn btn-success">

                <i class="bi bi-check-circle me-1"></i>
                Save Development Status

            </button> --}}

        </div>

    </form>

</div>
@endsection

@section('scripts')

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>

$(document).ready(function () {

    let oldProject = "{{ old('project_id') }}";
    let oldBlock = "{{ old('block_id') }}";
    let oldStreet = "{{ old('street_id') }}";
    let oldPlotId = "{{ old('plot_id') }}";
    let oldPlotNumber = "{{ old('plot_number') }}";


    /*
    |--------------------------------------------------------------------------
    | Load Blocks
    |--------------------------------------------------------------------------
    */

    function loadBlocks(projectId, selectedBlockId = '') {

        let blockDropdown = $('#block_id');
        let streetDropdown = $('#street_id');

        blockDropdown.html(
            '<option value="">Loading Blocks...</option>'
        );

        streetDropdown.html(
            '<option value="">All Streets</option>'
        );


        if (!projectId) {

            blockDropdown.html(
                '<option value="">Select Project First</option>'
            );

            return;
        }


        $.get(
            '/get-blocks/' + projectId,
            function (data) {

                blockDropdown.html(
                    '<option value="">Select Block</option>'
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

                    loadStreets(
                        selectedBlockId,
                        oldStreet
                    );

                }

            }
        ).fail(function () {

            blockDropdown.html(
                '<option value="">Unable to load blocks</option>'
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Load Streets
    |--------------------------------------------------------------------------
    */

    function loadStreets(blockId, selectedStreetId = '') {

        let streetDropdown = $('#street_id');

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


    /*
    |--------------------------------------------------------------------------
    | Project Change
    |--------------------------------------------------------------------------
    */

    $('#project_id').on('change', function () {

        let projectId = $(this).val();

        loadBlocks(projectId);

        $('#plot_id').html(
            '<option value="">Search plots first</option>'
        );

        $('#selectedPlotInfo').hide();

        clearDevelopmentStatus();

        $('#statusMode')
            .removeClass('bg-success bg-primary bg-warning')
            .addClass('bg-secondary')
            .text('Select a Plot');

    });


    /*
    |--------------------------------------------------------------------------
    | Property Type Change
    |--------------------------------------------------------------------------
    */

    $('#property_type_id').on('change', function () {

        $('#plot_id').html(
            '<option value="">Search plots first</option>'
        );

        $('#selectedPlotInfo').hide();

        clearDevelopmentStatus();

        $('#statusMode')
            .removeClass('bg-success bg-primary bg-warning')
            .addClass('bg-secondary')
            .text('Select a Plot');

    });


    /*
    |--------------------------------------------------------------------------
    | Block Change
    |--------------------------------------------------------------------------
    */

    $('#block_id').on('change', function () {

        let blockId = $(this).val();

        loadStreets(blockId);

        $('#plot_id').html(
            '<option value="">Search plots first</option>'
        );

        $('#selectedPlotInfo').hide();

        clearDevelopmentStatus();

        $('#statusMode')
            .removeClass('bg-success bg-primary bg-warning')
            .addClass('bg-secondary')
            .text('Select a Plot');

    });


    /*
    |--------------------------------------------------------------------------
    | Search Plots
    |--------------------------------------------------------------------------
    */

    $('#searchPlotsBtn').on('click', function () {

        let projectId = $('#project_id').val();
        let propertyTypeId = $('#property_type_id').val();
        let blockId = $('#block_id').val();
        let streetId = $('#street_id').val();
        let plotNumber = $('#plot_number').val().trim();


        $('#searchMessage')
            .hide()
            .removeClass('alert alert-danger alert-success')
            .html('');


        $('#plot_id').html(
            '<option value="">Searching...</option>'
        );

        $('#selectedPlotInfo').hide();

        clearDevelopmentStatus();

        $('#statusMode')
            .removeClass('bg-success bg-primary bg-warning')
            .addClass('bg-secondary')
            .text('Select a Plot');


        if (!projectId) {

            showSearchError(
                'Please select a Project.'
            );

            return;
        }


        if (!propertyTypeId) {

            showSearchError(
                'Please select a Property Type.'
            );

            return;
        }


        if (!blockId) {

            showSearchError(
                'Please select a Block.'
            );

            return;
        }


        if (!plotNumber) {

            showSearchError(
                'Please enter a Plot Number.'
            );

            return;
        }


        $.ajax({

            url: "{{ route('development.search-plots') }}",

            type: "GET",

            data: {

                project_id: projectId,
                property_type_id: propertyTypeId,
                block_id: blockId,
                street_id: streetId,
                plot_number: plotNumber

            },

            success: function (data) {

                $('#plot_id').html(
                    '<option value="">Select Matching Plot</option>'
                );


                if (!data.length) {

                    $('#plot_id').html(
                        '<option value="">No matching plots found</option>'
                    );

                    showSearchError(
                        'No matching plots found.'
                    );

                    return;
                }


                $.each(data, function (key, plot) {

                    let status =
                        plot.development_status || {};

                    let statusExists =
                        status.exists === true ? '1' : '0';


                    $('#plot_id').append(

                        '<option value="' +
                        plot.id +
                        '"' +

                        ' data-plot-number="' +
                        escapeHtml(
                            plot.plot_number ?? ''
                        ) +
                        '"' +

                        ' data-project="' +
                        escapeHtml(
                            plot.project ?? ''
                        ) +
                        '"' +

                        ' data-block="' +
                        escapeHtml(
                            plot.block ?? ''
                        ) +
                        '"' +

                        ' data-street="' +
                        escapeHtml(
                            plot.street ?? ''
                        ) +
                        '"' +

                        ' data-property-type="' +
                        escapeHtml(
                            plot.property_type ?? ''
                        ) +
                        '"' +

                        ' data-size="' +
                        escapeHtml(
                            plot.size ?? ''
                        ) +
                        '"' +

                        ' data-status-exists="' +
                        statusExists +
                        '"' +

                        ' data-sewer-manholes="' +
                        escapeHtml(
                            status.sewer_manholes ?? ''
                        ) +
                        '"' +

                        ' data-asphalt-tst="' +
                        escapeHtml(
                            status.asphalt_tst ?? ''
                        ) +
                        '"' +

                        ' data-overall-status="' +
                        escapeHtml(
                            status.overall_status ?? ''
                        ) +
                        '"' +

                        ' data-remarks="' +
                        escapeHtml(
                            status.remarks ?? ''
                        ) +
                        '"' +

                        '>' +

                        escapeHtml(
                            plot.plot_number ?? ''
                        ) +

                        ' — ' +

                        escapeHtml(
                            plot.street ?? 'No Street'
                        ) +

                        ' — ' +

                        escapeHtml(
                            plot.size ?? 'No Size'
                        ) +

                        '</option>'
                    );

                });


                showSearchSuccess(
                    data.length +
                    ' matching plot(s) found.'
                );


                /*
                |--------------------------------------------------------------------------
                | Restore Old Plot After Validation Error
                |--------------------------------------------------------------------------
                */

                if (oldPlotId) {

                    $('#plot_id').val(oldPlotId);

                    $('#plot_id').trigger('change');

                }

            },

            error: function (xhr) {

                $('#plot_id').html(
                    '<option value="">Unable to search plots</option>'
                );


                if (xhr.responseJSON?.message) {

                    showSearchError(
                        xhr.responseJSON.message
                    );

                } else {

                    showSearchError(
                        'Something went wrong while searching plots.'
                    );

                }

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Plot Selection
    |--------------------------------------------------------------------------
    */

    $('#plot_id').on('change', function () {

        let selected = $(this).find(':selected');


        /*
        |--------------------------------------------------------------------------
        | No Plot Selected
        |--------------------------------------------------------------------------
        */

        if (!selected.val()) {

            $('#selectedPlotInfo').hide();

            clearDevelopmentStatus();

            $('#statusMode')
                .removeClass(
                    'bg-success bg-primary bg-warning'
                )
                .addClass('bg-secondary')
                .text('Select a Plot');


            $('#saveDevelopmentBtn')
                .html(
                    '<i class="bi bi-check-circle me-1"></i>' +
                    'Save Development Status'
                );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Plot Information
        |--------------------------------------------------------------------------
        */

        $('#infoPlotNumber').text(
            selected.attr('data-plot-number') || '-'
        );

        $('#infoProject').text(
            selected.attr('data-project') || '-'
        );

        $('#infoBlock').text(
            selected.attr('data-block') || '-'
        );

        $('#infoStreet').text(
            selected.attr('data-street') || '-'
        );

        $('#infoPropertyType').text(
            selected.attr('data-property-type') || '-'
        );

        $('#infoSize').text(
            selected.attr('data-size') || '-'
        );

        $('#selectedPlotInfo').show();


        /*
        |--------------------------------------------------------------------------
        | Check Existing Development Status
        |--------------------------------------------------------------------------
        */

        let statusExists =
            selected.attr('data-status-exists') === '1';


        /*
        |--------------------------------------------------------------------------
        | Existing Status -> Update Mode
        |--------------------------------------------------------------------------
        */

        if (statusExists) {

            $('#sewer_manholes').val(
                selected.attr('data-sewer-manholes') || ''
            );

            $('#asphalt_tst').val(
                selected.attr('data-asphalt-tst') || ''
            );

            $('#overall_status').val(
                selected.attr('data-overall-status') || ''
            );

            $('#remarks').val(
                selected.attr('data-remarks') || ''
            );


            $('#statusMode')
                .removeClass(
                    'bg-secondary bg-success bg-warning'
                )
                .addClass('bg-primary')
                .text('Existing Status — Update');


            $('#saveDevelopmentBtn')
                .html(
                    '<i class="bi bi-pencil-square me-1"></i>' +
                    'Update Development Status'
                );

        }

        /*
        |--------------------------------------------------------------------------
        | No Status -> Create Mode
        |--------------------------------------------------------------------------
        */

        else {

            clearDevelopmentStatus();


            $('#statusMode')
                .removeClass(
                    'bg-secondary bg-primary bg-warning'
                )
                .addClass('bg-success')
                .text('New Status — Create');


            $('#saveDevelopmentBtn')
                .html(
                    '<i class="bi bi-check-circle me-1"></i>' +
                    'Create Development Status'
                );
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Enter Key Search
    |--------------------------------------------------------------------------
    */

    $('#plot_number').on('keypress', function (e) {

        if (e.which === 13) {

            e.preventDefault();

            $('#searchPlotsBtn').click();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Clear Development Status
    |--------------------------------------------------------------------------
    */

    function clearDevelopmentStatus() {

        $('#sewer_manholes').val('');

        $('#asphalt_tst').val('');

        $('#overall_status').val('');

        $('#remarks').val('');

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return $('<div>')
            .text(value ?? '')
            .html();

    }


    /*
    |--------------------------------------------------------------------------
    | Search Error
    |--------------------------------------------------------------------------
    */

    function showSearchError(message) {

        $('#searchMessage')
            .removeClass('alert-success')
            .addClass('alert alert-danger')
            .html(
                '<i class="bi bi-exclamation-triangle me-1"></i>' +
                message
            )
            .show();

    }


    /*
    |--------------------------------------------------------------------------
    | Search Success
    |--------------------------------------------------------------------------
    */

    function showSearchSuccess(message) {

        $('#searchMessage')
            .removeClass('alert-danger')
            .addClass('alert alert-success')
            .html(
                '<i class="bi bi-check-circle me-1"></i>' +
                message
            )
            .show();

    }


    /*
    |--------------------------------------------------------------------------
    | Restore Old Values After Validation Error
    |--------------------------------------------------------------------------
    */

    if (oldProject) {

        loadBlocks(
            oldProject,
            oldBlock
        );

    }


    if (oldPlotNumber) {

        $('#plot_number').val(oldPlotNumber);

    }

});

</script>

@endsection

