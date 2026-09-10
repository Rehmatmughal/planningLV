@extends('app')

@section('content')

<div class="container-fluid mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                ➕ New Possession Case
            </h4>

            <small class="text-muted">
                Create a new possession case and add owner information
            </small>
        </div>

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-secondary">
            ← Back to Cases
        </a>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following errors:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('possession-cases.store') }}">

        @csrf


        {{-- =========================================================
             CASE INFORMATION
        ========================================================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">

                <strong>
                    📋 Case Information
                </strong>

            </div>


            <div class="card-body">

                {{-- =================================================
                     PLOT SEARCH
                ================================================== --}}

                <div class="border rounded p-3 mb-4 bg-light">

                    <h6 class="fw-bold mb-3">
                        🔎 Find Plot
                    </h6>


                    <div class="row g-3">

                        {{-- Project --}}
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Project <span class="text-danger">*</span>
                            </label>

                            <select id="project_id"
                                    class="form-select">

                                <option value="">
                                    -- Select Project --
                                </option>

                                @foreach($projects as $project)

                                    <option value="{{ $project->id }}"
                                        {{ old('project_id', $selectedPlot?->project_id) == $project->id ? 'selected' : '' }}>

                                        {{ $project->project_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Block --}}
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Block <span class="text-danger">*</span>
                            </label>

                            <select id="block_id"
                                    class="form-select"
                                    disabled>

                                <option value="">
                                    -- Select Block --
                                </option>

                            </select>

                        </div>


                        {{-- Street --}}
                        <div class="col-md-4">

                            <label class="form-label fw-bold">
                                Street
                            </label>

                            <select id="street_id"
                                    class="form-select"
                                    disabled>

                                <option value="">
                                    -- All Streets --
                                </option>

                            </select>

                            <small class="text-muted">
                                Optional. Leave blank to search all streets.
                            </small>

                        </div>


                        {{-- Plot Number --}}
                        <div class="col-md-8">

                            <label class="form-label fw-bold">
                                Plot No <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="plot_number"
                                   class="form-control"
                                   placeholder="Enter plot number, e.g. 123">

                        </div>


                        {{-- Search Button --}}
                        <div class="col-md-4 d-flex align-items-end">

                            <button type="button"
                                    id="searchPlotBtn"
                                    class="btn btn-primary w-100"
                                    disabled>

                                🔎 Search Plot

                            </button>

                        </div>

                    </div>


                    {{-- Loading --}}
                    <div id="plotSearchLoading"
                         class="text-muted mt-3"
                         style="display:none;">

                        <span class="spinner-border spinner-border-sm"></span>
                        Searching plots...

                    </div>


                    {{-- Search Message --}}
                    <div id="plotSearchMessage"
                         class="mt-3">
                    </div>


                    {{-- Search Results --}}
                    <div id="plotResults"
                         class="mt-3">
                    </div>

                </div>


                {{-- =================================================
                     SELECTED PLOT
                ================================================== --}}

                <div id="selectedPlotBox"
                     class="alert alert-success"
                     style="{{ $selectedPlot ? '' : 'display:none;' }}">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                ✓ Selected Plot
                            </strong>

                            <div id="selectedPlotText"
                                 class="mt-1">

                                @if($selectedPlot)

                                    Plot {{ $selectedPlot->plot_number }}

                                    @if($selectedPlot->project)
                                        - {{ $selectedPlot->project->project_name }}
                                    @endif

                                    @if($selectedPlot->block)
                                        - {{ $selectedPlot->block->block_name }}
                                    @endif

                                    @if($selectedPlot->street)
                                        - {{ $selectedPlot->street->street_name }}
                                    @endif

                                    @if($selectedPlot->size)
                                        - {{ $selectedPlot->size->title }}
                                    @endif

                                @endif

                            </div>

                        </div>


                        <button type="button"
                                id="changePlotBtn"
                                class="btn btn-sm btn-outline-danger">

                            Change Plot

                        </button>

                    </div>

                </div>


                {{-- Hidden selected plot ID --}}
                <input type="hidden"
                       name="plot_id"
                       id="plot_id"
                       value="{{ old('plot_id', $selectedPlot?->id) }}">


                <div class="row g-3">


                    {{-- Case No --}}
                    <div class="col-md-3">

                        <label class="form-label fw-bold">
                            Case No <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="case_no"
                               class="form-control @error('case_no') is-invalid @enderror"
                               value="{{ old('case_no') }}"
                               min="1"
                               placeholder="Enter case number"
                               required>

                        @error('case_no')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Approval --}}
                    <div class="col-md-3">

                        <label class="form-label fw-bold">
                            Need Approval?
                        </label>

                        <select name="need_approval"
                                class="form-select">

                            <option value="0"
                                {{ old('need_approval', '0') == '0' ? 'selected' : '' }}>
                                No
                            </option>

                            <option value="1"
                                {{ old('need_approval') == '1' ? 'selected' : '' }}>
                                Yes
                            </option>

                        </select>

                    </div>


                    {{-- Current Holder Type --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Current Holder Type
                        </label>

                        <input type="text"
                               name="current_holder_type"
                               class="form-control"
                               value="{{ old('current_holder_type') }}"
                               placeholder="e.g. Staff / Officer / Department">

                    </div>


                    {{-- Current Holder ID --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Current Holder ID
                        </label>

                        <input type="number"
                               name="current_holder_id"
                               class="form-control"
                               value="{{ old('current_holder_id') }}"
                               placeholder="Optional">

                    </div>


                    {{-- Current Holder Name --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Current Holder Name
                        </label>

                        <input type="text"
                               name="current_holder_name"
                               class="form-control"
                               value="{{ old('current_holder_name') }}"
                               placeholder="Current holder">

                    </div>


                    {{-- Received Date --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Received Date
                        </label>

                        <input type="date"
                               name="received_at"
                               class="form-control"
                               value="{{ old('received_at', date('Y-m-d')) }}">

                    </div>


                    {{-- Remarks --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Any remarks about this case">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             OWNERS
        ========================================================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light d-flex justify-content-between align-items-center">

                <strong>
                    👤 Owner Information
                </strong>

                <button type="button"
                        class="btn btn-sm btn-success"
                        id="addOwnerBtn">

                    + Add Owner

                </button>

            </div>


            <div class="card-body">

                <div id="ownersContainer">


                    {{-- Existing old input after validation error --}}
                    @if(old('owners'))

                        @foreach(old('owners') as $index => $owner)

                            <div class="owner-row border rounded p-3 mb-3">

                                <div class="d-flex justify-content-between mb-3">

                                    <strong>
                                        Owner {{ $index + 1 }}
                                    </strong>

                                    @if($index > 0)

                                        <button type="button"
                                                class="btn btn-sm btn-danger remove-owner">

                                            Remove

                                        </button>

                                    @endif

                                </div>


                                <div class="row g-3">

                                    {{-- Owner Name --}}
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Owner Name <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               name="owners[{{ $index }}][owner_name]"
                                               class="form-control"
                                               value="{{ $owner['owner_name'] ?? '' }}"
                                               required>

                                    </div>


                                    {{-- CNIC --}}
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            CNIC
                                        </label>

                                        <div class="input-group">

                                            <input type="text"
                                                   name="owners[{{ $index }}][cnic]"
                                                   class="form-control owner-cnic"
                                                   value="{{ $owner['cnic'] ?? '' }}"
                                                   placeholder="xxxxx-xxxxxxx-x">

                                            <button type="button"
                                                    class="btn btn-outline-primary check-cnic">

                                                Check

                                            </button>

                                        </div>

                                        <small class="cnic-message mt-1 d-block"></small>

                                    </div>


                                    {{-- Contact --}}
                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Contact No
                                        </label>

                                        <input type="text"
                                               name="owners[{{ $index }}][contact_no]"
                                               class="form-control"
                                               value="{{ $owner['contact_no'] ?? '' }}">

                                    </div>


                                    {{-- Address --}}
                                    <div class="col-md-8">

                                        <label class="form-label">
                                            Address
                                        </label>

                                        <textarea name="owners[{{ $index }}][address]"
                                                  class="form-control"
                                                  rows="2">{{ $owner['address'] ?? '' }}</textarea>

                                    </div>



                                </div>

                            </div>

                        @endforeach

                    @else

                        {{-- First Owner --}}
                        <div class="owner-row border rounded p-3 mb-3">

                            <div class="d-flex justify-content-between mb-3">

                                <strong>
                                    Owner 1
                                </strong>

                            </div>


                            <div class="row g-3">

                                {{-- Owner Name --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Owner Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="owners[0][owner_name]"
                                           class="form-control"
                                           required>

                                </div>


                                {{-- CNIC --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        CNIC
                                    </label>

                                    <div class="input-group">

                                        <input type="text"
                                               name="owners[0][cnic]"
                                               class="form-control owner-cnic"
                                               placeholder="xxxxx-xxxxxxx-x">

                                        <button type="button"
                                                class="btn btn-outline-primary check-cnic">

                                            Check

                                        </button>

                                    </div>

                                    <small class="cnic-message mt-1 d-block"></small>

                                </div>


                                {{-- Contact --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Contact No
                                    </label>

                                    <input type="text"
                                           name="owners[0][contact_no]"
                                           class="form-control">

                                </div>


                                {{-- Address --}}
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea name="owners[0][address]"
                                              class="form-control"
                                              rows="2"></textarea>

                                </div>




                            </div>

                        </div>

                    @endif

                </div>


                <div class="alert alert-info mb-0">

                    <small>

                        <strong>Note:</strong>

                        If a plot has multiple owners, use
                        <strong>+ Add Owner</strong>
                        to add additional owners.

                    </small>

                </div>

            </div>

        </div>


        {{-- =========================================================
             BUTTONS
        ========================================================== --}}

        <div class="d-flex justify-content-end gap-2 mb-5">

            <a href="{{ route('possession-cases.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary">

                💾 Save Possession Case

            </button>

        </div>

    </form>

</div>


{{-- =========================================================
     JAVASCRIPT
     PLOT SEARCH + OWNERS + CNIC
========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | PLOT SEARCH
    |--------------------------------------------------------------------------
    */

    const projectSelect = document.getElementById('project_id');
    const blockSelect = document.getElementById('block_id');
    const streetSelect = document.getElementById('street_id');
    const plotNumberInput = document.getElementById('plot_number');
    const searchPlotBtn = document.getElementById('searchPlotBtn');

    const plotResults = document.getElementById('plotResults');
    const plotSearchMessage = document.getElementById('plotSearchMessage');
    const plotSearchLoading = document.getElementById('plotSearchLoading');

    const plotIdInput = document.getElementById('plot_id');
    const selectedPlotBox = document.getElementById('selectedPlotBox');
    const selectedPlotText = document.getElementById('selectedPlotText');
    const changePlotBtn = document.getElementById('changePlotBtn');


    const oldBlockId = @json($selectedPlot?->block_id);
    const oldStreetId = @json($selectedPlot?->street_id);


    /*
    |--------------------------------------------------------------------------
    | Enable / Disable Search Button
    |--------------------------------------------------------------------------
    */

    function updateSearchButton() {

        if (
            projectSelect.value &&
            blockSelect.value &&
            plotNumberInput.value.trim() !== ''
        ) {

            searchPlotBtn.disabled = false;

        } else {

            searchPlotBtn.disabled = true;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Load Blocks
    |--------------------------------------------------------------------------
    */

    function loadBlocks(projectId, selectedBlockId = null) {

        blockSelect.innerHTML =
            '<option value="">-- Loading Blocks --</option>';

        blockSelect.disabled = true;

        streetSelect.innerHTML =
            '<option value="">-- All Streets --</option>';

        streetSelect.disabled = true;

        updateSearchButton();


        if (!projectId) {

            blockSelect.innerHTML =
                '<option value="">-- Select Block --</option>';

            return;

        }


        fetch(
            '{{ url("possession-cases/ajax/blocks") }}/' + projectId
        )

        .then(response => {

            if (!response.ok) {
                throw new Error('Unable to load blocks.');
            }

            return response.json();

        })

        .then(blocks => {

            blockSelect.innerHTML =
                '<option value="">-- Select Block --</option>';


            blocks.forEach(block => {

                const option =
                    document.createElement('option');

                option.value = block.id;
                option.textContent = block.block_name;

                if (
                    selectedBlockId &&
                    String(selectedBlockId) === String(block.id)
                ) {

                    option.selected = true;

                }

                blockSelect.appendChild(option);

            });


            blockSelect.disabled = false;

            updateSearchButton();


            // If an old/selected plot exists
            if (selectedBlockId) {

                loadStreets(
                    selectedBlockId,
                    oldStreetId
                );

            }

        })

        .catch(error => {

            console.error(error);

            blockSelect.innerHTML =
                '<option value="">Unable to load blocks</option>';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Load Streets
    |--------------------------------------------------------------------------
    */

    function loadStreets(blockId, selectedStreetId = null) {

        streetSelect.innerHTML =
            '<option value="">-- Loading Streets --</option>';

        streetSelect.disabled = true;

        updateSearchButton();


        if (!blockId) {

            streetSelect.innerHTML =
                '<option value="">-- All Streets --</option>';

            return;

        }


        fetch(
            '{{ url("possession-cases/ajax/streets") }}/' + blockId
        )

        .then(response => {

            if (!response.ok) {
                throw new Error('Unable to load streets.');
            }

            return response.json();

        })

        .then(streets => {

            streetSelect.innerHTML =
                '<option value="">-- All Streets --</option>';


            streets.forEach(street => {

                const option =
                    document.createElement('option');

                option.value = street.id;
                option.textContent = street.street_name;

                if (
                    selectedStreetId &&
                    String(selectedStreetId) === String(street.id)
                ) {

                    option.selected = true;

                }

                streetSelect.appendChild(option);

            });


            streetSelect.disabled = false;

            updateSearchButton();

        })

        .catch(error => {

            console.error(error);

            streetSelect.innerHTML =
                '<option value="">Unable to load streets</option>';

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Project Changed
    |--------------------------------------------------------------------------
    */

    projectSelect.addEventListener('change', function () {

        const projectId = this.value;

        // New selection means old plot is no longer valid
        plotIdInput.value = '';

        selectedPlotBox.style.display = 'none';

        plotResults.innerHTML = '';

        loadBlocks(projectId);

    });


    /*
    |--------------------------------------------------------------------------
    | Block Changed
    |--------------------------------------------------------------------------
    */

    blockSelect.addEventListener('change', function () {

        const blockId = this.value;

        plotIdInput.value = '';

        selectedPlotBox.style.display = 'none';

        plotResults.innerHTML = '';

        loadStreets(blockId);

        updateSearchButton();

    });


    /*
    |--------------------------------------------------------------------------
    | Street Changed
    |--------------------------------------------------------------------------
    */

    streetSelect.addEventListener('change', function () {

        plotIdInput.value = '';

        selectedPlotBox.style.display = 'none';

        plotResults.innerHTML = '';

    });


    /*
    |--------------------------------------------------------------------------
    | Plot Number Changed
    |--------------------------------------------------------------------------
    */

    plotNumberInput.addEventListener('input', function () {

        plotIdInput.value = '';

        selectedPlotBox.style.display = 'none';

        updateSearchButton();

    });


    /*
    |--------------------------------------------------------------------------
    | Search Plot
    |--------------------------------------------------------------------------
    */

    searchPlotBtn.addEventListener('click', function () {

        const projectId = projectSelect.value;
        const blockId = blockSelect.value;
        const streetId = streetSelect.value;
        const plotNumber = plotNumberInput.value.trim();


        if (!projectId || !blockId || !plotNumber) {

            plotSearchMessage.innerHTML = `
                <div class="alert alert-warning mb-0">
                    Please select Project, Block and enter Plot No.
                </div>
            `;

            return;

        }


        plotResults.innerHTML = '';

        plotSearchMessage.innerHTML = '';

        plotSearchLoading.style.display = 'block';

        searchPlotBtn.disabled = true;


        const params = new URLSearchParams({

            project_id: projectId,
            block_id: blockId,
            plot_number: plotNumber

        });


        if (streetId) {

            params.append('street_id', streetId);

        }


        fetch(
            '{{ route("possession-cases.ajax.search-plots") }}?' +
            params.toString()
        )

        .then(response => {

            if (!response.ok) {
                throw new Error('Unable to search plots.');
            }

            return response.json();

        })

        .then(plots => {

            plotSearchLoading.style.display = 'none';

            searchPlotBtn.disabled = false;

            plotResults.innerHTML = '';


            if (plots.length === 0) {

                plotSearchMessage.innerHTML = `
                    <div class="alert alert-warning">
                        No active plot found with Plot No
                        <strong>${escapeHtml(plotNumber)}</strong>.
                    </div>
                `;

                return;

            }


            plotSearchMessage.innerHTML = `
                <div class="alert alert-success">
                    ${plots.length} matching plot(s) found.
                    Please select the correct plot.
                </div>
            `;


            plots.forEach(plot => {

                const result = document.createElement('div');

                result.className =
                    'card border mb-2 shadow-sm';


                const sizeText =
                    plot.size_title
                        ? plot.size_title +
                          (
                              plot.size_area
                                  ? ' (' + plot.size_area + ')'
                                  : ''
                          )
                        : 'N/A';


                result.innerHTML = `

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-md-8">

                                <h6 class="fw-bold mb-2">

                                    Plot ${escapeHtml(
                                        plot.plot_number
                                    )}

                                </h6>


                                <div class="small text-muted">

                                    <div>
                                        <strong>Project:</strong>
                                        ${escapeHtml(
                                            plot.project_name ?? 'N/A'
                                        )}
                                    </div>

                                    <div>
                                        <strong>Block:</strong>
                                        ${escapeHtml(
                                            plot.block_name ?? 'N/A'
                                        )}
                                    </div>

                                    <div>
                                        <strong>Street:</strong>
                                        ${escapeHtml(
                                            plot.street_name ?? 'No Street'
                                        )}
                                    </div>

                                    <div>
                                        <strong>Size:</strong>
                                        ${escapeHtml(sizeText)}
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-4 text-md-end mt-3 mt-md-0">

                                <button type="button"
                                        class="btn btn-success select-plot-btn"
                                        data-plot-id="${plot.id}">

                                    ✓ Select This Plot

                                </button>

                            </div>

                        </div>

                    </div>

                `;


                plotResults.appendChild(result);

            });

        })

        .catch(error => {

            console.error(error);

            plotSearchLoading.style.display = 'none';

            searchPlotBtn.disabled = false;


            plotSearchMessage.innerHTML = `
                <div class="alert alert-danger">
                    Unable to search plots. Please try again.
                </div>
            `;

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Select Plot
    |--------------------------------------------------------------------------
    */

    plotResults.addEventListener('click', function (event) {

        const button =
            event.target.closest('.select-plot-btn');


        if (!button) {
            return;
        }


        const plotId =
            button.dataset.plotId;


        const card =
            button.closest('.card');


        const plotNumber =
            card.querySelector('h6').textContent.trim();


        const details =
            card.querySelector('.small').innerText.trim();


        plotIdInput.value = plotId;


        selectedPlotText.innerHTML = `
            <strong>${escapeHtml(plotNumber)}</strong>
            <br>
            <small class="text-muted">
                ${escapeHtml(details)}
            </small>
        `;


        selectedPlotBox.style.display = 'block';


        plotResults.innerHTML = '';

        plotSearchMessage.innerHTML = `
            <div class="alert alert-success">
                Plot selected successfully.
            </div>
        `;

    });


    /*
    |--------------------------------------------------------------------------
    | Change Plot
    |--------------------------------------------------------------------------
    */

    changePlotBtn.addEventListener('click', function () {

        plotIdInput.value = '';

        selectedPlotBox.style.display = 'none';

        plotResults.innerHTML = '';

        plotSearchMessage.innerHTML = '';

        plotNumberInput.focus();

    });


    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    if (projectSelect.value) {

        loadBlocks(
            projectSelect.value,
            oldBlockId
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /*
    |--------------------------------------------------------------------------
    | OWNERS
    |--------------------------------------------------------------------------
    */

    const container =
        document.getElementById('ownersContainer');

    const addButton =
        document.getElementById('addOwnerBtn');

    let ownerIndex =
        {{ old('owners') ? count(old('owners')) : 1 }};


    /*
    |--------------------------------------------------------------------------
    | Add Owner
    |--------------------------------------------------------------------------
    */

    addButton.addEventListener('click', function () {

        const ownerNumber =
            ownerIndex + 1;


        const ownerHtml = `

            <div class="owner-row border rounded p-3 mb-3">

                <div class="d-flex justify-content-between mb-3">

                    <strong>
                        Owner ${ownerNumber}
                    </strong>

                    <button type="button"
                            class="btn btn-sm btn-danger remove-owner">

                        Remove

                    </button>

                </div>


                <div class="row g-3">


                    <div class="col-md-4">

                        <label class="form-label">
                            Owner Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="owners[${ownerIndex}][owner_name]"
                               class="form-control"
                               required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            CNIC
                        </label>

                        <div class="input-group">

                            <input type="text"
                                   name="owners[${ownerIndex}][cnic]"
                                   class="form-control owner-cnic"
                                   placeholder="xxxxx-xxxxxxx-x">

                            <button type="button"
                                    class="btn btn-outline-primary check-cnic">

                                Check

                            </button>

                        </div>

                        <small class="cnic-message mt-1 d-block"></small>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Contact No
                        </label>

                        <input type="text"
                               name="owners[${ownerIndex}][contact_no]"
                               class="form-control">
                    </div>
                    <div class="col-md-8">

                        <label class="form-label">
                            Address
                        </label>
                        <textarea name="owners[${ownerIndex}][address]"
                                  class="form-control"
                                  rows="2"></textarea>

                    </div>

                </div>

            </div>
        `;


        container.insertAdjacentHTML(
            'beforeend',
            ownerHtml
        );


        ownerIndex++;

    });


    /*
    |--------------------------------------------------------------------------
    | Remove Owner
    |--------------------------------------------------------------------------
    */

    container.addEventListener('click', function (event) {

        if (
            !event.target.classList.contains(
                'remove-owner'
            )
        ) {
            return;
        }


        const rows =
            container.querySelectorAll('.owner-row');


        if (rows.length <= 1) {

            alert(
                'At least one owner is required.'
            );

            return;

        }


        event.target
            .closest('.owner-row')
            .remove();

    });


    /*
    |--------------------------------------------------------------------------
    | Check CNIC
    |--------------------------------------------------------------------------
    */

    container.addEventListener('click', function (event) {

        if (
            !event.target.classList.contains(
                'check-cnic'
            )
        ) {
            return;
        }


        const button =
            event.target;

        const row =
            button.closest('.owner-row');

        const cnicInput =
            row.querySelector('.owner-cnic');

        const message =
            row.querySelector('.cnic-message');

        const cnic =
            cnicInput.value.trim();


        if (!cnic) {

            message.className =
                'cnic-message text-danger mt-1 d-block';

            message.textContent =
                'Please enter CNIC first.';

            return;

        }


        button.disabled = true;

        button.textContent =
            'Checking...';


        message.className =
            'cnic-message text-muted mt-1 d-block';

        message.textContent =
            'Checking owner database...';


        fetch(
            '{{ route("owners.findByCnic") }}?cnic=' +
            encodeURIComponent(cnic)
        )

        .then(response =>
            response.json()
        )

        .then(data => {

            if (data.found) {

                const owner =
                    data.owner;


                /*
                |------------------------------------------------------
                | Fill existing owner data
                |------------------------------------------------------
                */

                row.querySelector(
                    '[name$="[owner_name]"]'
                ).value =
                    owner.owner_name ?? '';


                row.querySelector(
                    '[name$="[cnic]"]'
                ).value =
                    owner.cnic ?? '';


                row.querySelector(
                    '[name$="[address]"]'
                ).value =
                    owner.address ?? '';


                row.querySelector(
                    '[name$="[contact_no]"]'
                ).value =
                    owner.contact_no ?? '';


                /*
                |------------------------------------------------------
                | Store owner ID
                |------------------------------------------------------
                */

                let ownerIdInput =
                    row.querySelector('.owner-id');


                if (!ownerIdInput) {

                    ownerIdInput =
                        document.createElement('input');

                    ownerIdInput.type =
                        'hidden';

                    ownerIdInput.name =
                        row.querySelector(
                            '.owner-cnic'
                        ).name.replace(
                            '[cnic]',
                            '[owner_id]'
                        );

                    ownerIdInput.className =
                        'owner-id';

                    row.appendChild(
                        ownerIdInput
                    );

                }


                ownerIdInput.value =
                    owner.id;


                message.className =
                    'cnic-message text-success mt-1 d-block';

                message.innerHTML =
                    '✓ Owner found. Existing owner details loaded.';

            } else {

                /*
                |------------------------------------------------------
                | New Owner
                |------------------------------------------------------
                */

                const ownerIdInput =
                    row.querySelector(
                        '.owner-id'
                    );


                if (ownerIdInput) {
                    ownerIdInput.remove();
                }


                message.className =
                    'cnic-message text-warning mt-1 d-block';

                message.textContent =
                    'Owner not found. You can enter new owner details.';

            }

        })

        .catch(error => {

            console.error(error);


            message.className =
                'cnic-message text-danger mt-1 d-block';

            message.textContent =
                'Unable to check CNIC. Please try again.';

        })

        .finally(() => {

            button.disabled = false;

            button.textContent =
                'Check';

        });

    });

});

</script>

@endsection
