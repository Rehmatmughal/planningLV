@extends('app')

@section('content')
<div class="container-fluid mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                Edit Possession Case
            </h3>
            <p class="text-muted mb-0">
                Possession #{{ $possessionCase->possession_no }}
            </p>
        </div>
        <div>
            <a href="{{ route('possession-cases.show', $possessionCase) }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
        </div>
    </div>
    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">

            <strong>Please fix the following errors:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    <form method="POST"
          action="{{ route('possession-cases.update', $possessionCase) }}">
        @csrf
        @method('PUT')


        {{-- ========================================================= --}}
        {{-- POSSESSION / PLOT INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    Possession & Plot Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Possession No --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Possession No.
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->possession_no }}"
                               readonly>

                    </div>


                    {{-- Project --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Project
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->project?->project_name ?? 'N/A' }}"
                               readonly>

                    </div>


                    {{-- Block --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Block
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->block?->block_name ?? 'N/A' }}"
                               readonly>

                    </div>


                    {{-- Property Type --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Property Type
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->propertyType?->name ?? 'N/A' }}"
                               readonly>

                    </div>


                    {{-- Street --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Street
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->street?->street_name ?? 'N/A' }}"
                               readonly>

                    </div>


                    {{-- Plot Number --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Plot No.
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->plot_number ?? 'N/A' }}"
                               readonly>

                    </div>


                    {{-- Plot Size --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Plot Size
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->size?->title ?? 'N/A' }}"
                               readonly>

                    </div>

                </div>


                {{-- Hidden plot_id --}}
                <input type="hidden"
                       name="plot_id"
                       value="{{ $possessionCase->plot_id }}">

                <div class="alert alert-info mt-4 mb-0">

                    <i class="bi bi-info-circle me-1"></i>

                    <strong>Plot cannot be changed.</strong>
                    This possession case is permanently linked to the above plot.

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- POSSESSION DETAILS --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    Possession Details
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Reference No --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Reference No.
                        </label>

                        <input type="text"
                               name="reference_no"
                               class="form-control"
                               value="{{ old('reference_no', $possessionCase->reference_no) }}">

                    </div>


                    {{-- Need Approval --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Need Approval
                        </label>

                        <div class="form-check mt-2">

                            <input type="checkbox"
                                   name="need_approval"
                                   value="1"
                                   class="form-check-input"
                                   id="needApproval"
                                   {{ old('need_approval', $possessionCase->need_approval) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="needApproval">
                                Approval Required
                            </label>

                        </div>

                    </div>


                    {{-- Current Holder Type --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Current Holder Type
                        </label>

                        <input type="text"
                               name="current_holder_type"
                               class="form-control"
                               value="{{ old('current_holder_type', $possessionCase->current_holder_type) }}">

                    </div>


                    {{-- Current Holder ID --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Current Holder ID
                        </label>

                        <input type="number"
                               name="current_holder_id"
                               class="form-control"
                               value="{{ old('current_holder_id', $possessionCase->current_holder_id) }}">

                    </div>


                    {{-- Current Holder Name --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Current Holder Name
                        </label>

                        <input type="text"
                               name="current_holder_name"
                               class="form-control"
                               value="{{ old('current_holder_name', $possessionCase->current_holder_name) }}">

                    </div>


                    {{-- Received At --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Received Date
                        </label>

                        <input type="datetime-local"
                               name="received_at"
                               class="form-control"
                               value="{{ old(
                                   'received_at',
                                   $possessionCase->received_at
                                       ? \Carbon\Carbon::parse($possessionCase->received_at)->format('Y-m-d\TH:i')
                                       : ''
                               ) }}">

                    </div>


                    {{-- Remarks --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  rows="2"
                                  class="form-control">{{ old('remarks', $possessionCase->remarks) }}</textarea>

                    </div>

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- OWNERS --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">
                    Owners
                </h5>

                <button type="button"
                        class="btn btn-sm btn-primary"
                        id="addOwner">

                    <i class="bi bi-plus-circle"></i>
                    Add Owner

                </button>

            </div>


            <div class="card-body">

                <div id="ownersContainer">

                    @foreach($possessionCase->owners as $index => $owner)

                        <div class="owner-row border rounded p-3 mb-3">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h6 class="fw-bold mb-0">
                                    Owner #{{ $index + 1 }}
                                </h6>

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger remove-owner">

                                    <i class="bi bi-trash"></i>
                                    Remove

                                </button>

                            </div>


                            <input type="hidden"
                                   name="owners[{{ $index }}][id]"
                                   value="{{ $owner->id }}">

                            <input type="hidden"
                                   name="owners[{{ $index }}][owner_id]"
                                   value="{{ $owner->id }}">


                            <div class="row g-3">

                                {{-- Owner Name --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Owner Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][owner_name]"
                                           class="form-control owner-name"
                                           value="{{ old("owners.$index.owner_name", $owner->owner_name) }}"
                                           required>

                                </div>


                                {{-- Relative Name --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        F/H/W Name
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][relative_name]"
                                           class="form-control relative-name"
                                           value="{{ old("owners.$index.relative_name", $owner->relative_name) }}">

                                </div>


                                {{-- CNIC --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        CNIC <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                            name="owners[{{ $index }}][cnic]"
                                            class="form-control owner-cnic"
                                            value="{{ old("owners.$index.cnic", $owner->cnic) }}"
                                            required>
                                        <button type="button"
                                                class="btn btn-outline-primary check-cnic">
                                            Check
                                        </button>
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        CNIC <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][cnic]"
                                           class="form-control owner-cnic"
                                           value="{{ old("owners.$index.cnic", $owner->cnic) }}"
                                           required>

                                </div> --}}


                                {{-- Contact --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Contact No.
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][contact_no]"
                                           class="form-control"
                                           value="{{ old("owners.$index.contact_no", $owner->contact_no) }}">

                                </div>


                                {{-- Address --}}
                                <div class="col-md-8">

                                    <label class="form-label fw-semibold">
                                        Address
                                    </label>

                                    <textarea name="owners[{{ $index }}][address]"
                                              class="form-control"
                                              rows="2">{{ old("owners.$index.address", $owner->address) }}</textarea>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- BUTTONS --}}
        {{-- ========================================================= --}}

        <div class="d-flex justify-content-end gap-2 mb-5">

            <a href="{{ route('possession-cases.show', $possessionCase) }}"
               class="btn btn-secondary">

                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-save"></i>
                Update Possession

            </button>

        </div>

    </form>

</div>



{{-- ============================================================= --}}
{{-- OWNER TEMPLATE --}}
{{-- ============================================================= --}}

<template id="ownerTemplate">

    <div class="owner-row border rounded p-3 mb-3">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h6 class="fw-bold mb-0 owner-number">
                New Owner
            </h6>

            <button type="button"
                    class="btn btn-sm btn-outline-danger remove-owner">

                <i class="bi bi-trash"></i>
                Remove

            </button>

        </div>


        <input type="hidden"
               class="owner-id-input"
               name="__NAME__[id]"
               value="">

        <input type="hidden"
               class="owner-owner-id-input"
               name="__NAME__[owner_id]"
               value="">


        <div class="row g-3">

            {{-- Owner Name --}}
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    Owner Name <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="__NAME__[owner_name]"
                       class="form-control owner-name"
                       required>

            </div>


            {{-- Relative Name --}}
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    F/H/W Name
                </label>

                <input type="text"
                       name="__NAME__[relative_name]"
                       class="form-control relative-name">

            </div>


            {{-- CNIC --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    CNIC <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="text"
                        name="__NAME__[cnic]"
                        class="form-control owner-cnic"
                        required>
                    <button type="button"
                            class="btn btn-outline-primary check-cnic">
                        Check
                    </button>
                </div>
            </div>

            {{-- <div class="col-md-4">

                <label class="form-label fw-semibold">
                    CNIC <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="__NAME__[cnic]"
                       class="form-control owner-cnic"
                       required>

            </div> --}}


            {{-- Contact --}}
            <div class="col-md-4">

                <label class="form-label fw-semibold">
                    Contact No.
                </label>

                <input type="text"
                       name="__NAME__[contact_no]"
                       class="form-control">

            </div>


            {{-- Address --}}
            <div class="col-md-8">

                <label class="form-label fw-semibold">
                    Address
                </label>

                <textarea name="__NAME__[address]"
                          class="form-control"
                          rows="2"></textarea>

            </div>

        </div>

    </div>

</template>




<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | PLOT SEARCH
    |--------------------------------------------------------------------------
    */

    const projectSelect =
        document.getElementById('project_id');

    const blockSelect =
        document.getElementById('block_id');

    const streetSelect =
        document.getElementById('street_id');

    const plotNumberInput =
        document.getElementById('plot_number');

    const searchPlotBtn =
        document.getElementById('searchPlotBtn');

    const plotResults =
        document.getElementById('plotResults');

    const plotSearchMessage =
        document.getElementById('plotSearchMessage');

    const plotSearchLoading =
        document.getElementById('plotSearchLoading');

    const plotIdInput =
        document.getElementById('plot_id');

    const selectedPlotBox =
        document.getElementById('selectedPlotBox');

    const selectedPlotText =
        document.getElementById('selectedPlotText');

    const changePlotBtn =
        document.getElementById('changePlotBtn');


    const oldBlockId =
        @json($possessionCase->plot?->block_id);

    const oldStreetId =
        @json($possessionCase->plot?->street_id);


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


    function loadBlocks(
        projectId,
        selectedBlockId = null
    ) {

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
            '{{ url("possession-cases/ajax/blocks") }}/' +
            projectId
        )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Unable to load blocks.'
                );

            }

            return response.json();

        })

        .then(blocks => {

            blockSelect.innerHTML =
                '<option value="">-- Select Block --</option>';


            blocks.forEach(block => {

                const option =
                    document.createElement('option');

                option.value =
                    block.id;

                option.textContent =
                    block.block_name;


                if (
                    selectedBlockId &&
                    String(selectedBlockId) ===
                    String(block.id)
                ) {

                    option.selected = true;

                }

                blockSelect.appendChild(option);

            });


            blockSelect.disabled = false;

            updateSearchButton();


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


    function loadStreets(
        blockId,
        selectedStreetId = null
    ) {

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
            '{{ url("possession-cases/ajax/streets") }}/' +
            blockId
        )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Unable to load streets.'
                );

            }

            return response.json();

        })

        .then(streets => {

            streetSelect.innerHTML =
                '<option value="">-- All Streets --</option>';


            streets.forEach(street => {

                const option =
                    document.createElement('option');

                option.value =
                    street.id;

                option.textContent =
                    street.street_name;


                if (
                    selectedStreetId &&
                    String(selectedStreetId) ===
                    String(street.id)
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


    projectSelect.addEventListener(
        'change',
        function () {

            plotIdInput.value = '';

            selectedPlotBox.style.display =
                'none';

            plotResults.innerHTML = '';

            loadBlocks(this.value);

        }
    );


    blockSelect.addEventListener(
        'change',
        function () {

            plotIdInput.value = '';

            selectedPlotBox.style.display =
                'none';

            plotResults.innerHTML = '';

            loadStreets(this.value);

            updateSearchButton();

        }
    );


    streetSelect.addEventListener(
        'change',
        function () {

            plotIdInput.value = '';

            selectedPlotBox.style.display =
                'none';

            plotResults.innerHTML = '';

        }
    );


    plotNumberInput.addEventListener(
        'input',
        function () {

            plotIdInput.value = '';

            selectedPlotBox.style.display =
                'none';

            updateSearchButton();

        }
    );


    searchPlotBtn.addEventListener(
        'click',
        function () {


            const projectId =
                projectSelect.value;

            const blockId =
                blockSelect.value;

            const streetId =
                streetSelect.value;

            const plotNumber =
                plotNumberInput.value.trim();


            if (
                !projectId ||
                !blockId ||
                !plotNumber
            ) {

                plotSearchMessage.innerHTML = `

                    <div class="alert alert-warning mb-0">

                        Please select Project, Block
                        and enter Plot No.

                    </div>

                `;

                return;

            }


            plotResults.innerHTML = '';

            plotSearchMessage.innerHTML = '';

            plotSearchLoading.style.display =
                'block';

            searchPlotBtn.disabled =
                true;


            const params =
                new URLSearchParams({

                    project_id: projectId,

                    block_id: blockId,

                    plot_number: plotNumber

                });


            if (streetId) {

                params.append(
                    'street_id',
                    streetId
                );

            }


            fetch(
                '{{ route("possession-cases.ajax.search-plots") }}?' +
                params.toString()
            )

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'Unable to search plots.'
                    );

                }

                return response.json();

            })

            .then(plots => {

                plotSearchLoading.style.display =
                    'none';

                searchPlotBtn.disabled =
                    false;

                plotResults.innerHTML =
                    '';


                if (plots.length === 0) {

                    plotSearchMessage.innerHTML = `

                        <div class="alert alert-warning">

                            No active plot found with Plot No

                            <strong>
                                ${escapeHtml(plotNumber)}
                            </strong>.

                        </div>

                    `;

                    return;

                }


                plotSearchMessage.innerHTML = `

                    <div class="alert alert-success">

                        ${plots.length}
                        matching plot(s) found.

                        Please select the correct plot.

                    </div>

                `;


                plots.forEach(plot => {

                    const result =
                        document.createElement('div');

                    result.className =
                        'card border mb-2 shadow-sm';


                    const sizeText =
                        plot.size_title
                            ? plot.size_title +
                              (
                                  plot.size_area
                                      ? ' (' +
                                        plot.size_area +
                                        ')'
                                      : ''
                              )
                            : 'N/A';


                    result.innerHTML = `

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-8">

                                    <h6 class="fw-bold mb-2">

                                        Plot
                                        ${escapeHtml(
                                            plot.plot_number
                                        )}

                                    </h6>


                                    <div class="small text-muted">

                                        <div>

                                            <strong>
                                                Project:
                                            </strong>

                                            ${escapeHtml(
                                                plot.project_name ??
                                                'N/A'
                                            )}

                                        </div>


                                        <div>

                                            <strong>
                                                Block:
                                            </strong>

                                            ${escapeHtml(
                                                plot.block_name ??
                                                'N/A'
                                            )}

                                        </div>


                                        <div>

                                            <strong>
                                                Street:
                                            </strong>

                                            ${escapeHtml(
                                                plot.street_name ??
                                                'No Street'
                                            )}

                                        </div>


                                        <div>

                                            <strong>
                                                Size:
                                            </strong>

                                            ${escapeHtml(
                                                sizeText
                                            )}

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

                plotSearchLoading.style.display =
                    'none';

                searchPlotBtn.disabled =
                    false;


                plotSearchMessage.innerHTML = `

                    <div class="alert alert-danger">

                        Unable to search plots.
                        Please try again.

                    </div>

                `;

            });

        }

    );


    plotResults.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.select-plot-btn'
                );


            if (!button) {
                return;
            }


            const plotId =
                button.dataset.plotId;


            const card =
                button.closest('.card');


            const plotNumber =
                card.querySelector('h6')
                    .textContent
                    .trim();


            const details =
                card.querySelector('.small')
                    .innerText
                    .trim();


            plotIdInput.value =
                plotId;


            selectedPlotText.innerHTML = `

                <strong>
                    ${escapeHtml(plotNumber)}
                </strong>

                <br>

                <small class="text-muted">

                    ${escapeHtml(details)}

                </small>

            `;


            selectedPlotBox.style.display =
                'block';


            plotResults.innerHTML =
                '';


            plotSearchMessage.innerHTML = `

                <div class="alert alert-success">

                    Plot selected successfully.

                </div>

            `;

        }
    );


    changePlotBtn.addEventListener(
        'click',
        function () {

            plotIdInput.value = '';

            selectedPlotBox.style.display =
                'none';

            plotResults.innerHTML =
                '';

            plotSearchMessage.innerHTML =
                '';

            plotNumberInput.focus();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load current plot hierarchy
    |--------------------------------------------------------------------------
    */

    if (projectSelect.value) {

        loadBlocks(
            projectSelect.value,
            oldBlockId
        );

    }


    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {

            return '';

        }


        return String(value)

            .replace(
                /&/g,
                '&amp;'
            )

            .replace(
                /</g,
                '&lt;'
            )

            .replace(
                />/g,
                '&gt;'
            )

            .replace(
                /"/g,
                '&quot;'
            )

            .replace(
                /'/g,
                '&#039;'
            );

    }



    /*
    |--------------------------------------------------------------------------
    | OWNERS
    |--------------------------------------------------------------------------
    */

    const container =
        document.getElementById(
            'ownersContainer'
        );


    const addOwnerButton =
        document.getElementById(
            'addOwner'
        );


    let ownerIndex =
        {{ $possessionCase->owners->count() }};


    /*
    |--------------------------------------------------------------------------
    | Add Owner
    |--------------------------------------------------------------------------
    */

    addOwnerButton.addEventListener(
        'click',
        function () {


            const row =
                document.createElement('div');


            row.className =
                'owner-row border rounded p-3 mb-3';


            row.innerHTML = `

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <strong>
                        New Owner
                    </strong>


                    <button type="button"
                            class="btn btn-danger btn-sm removeOwner">

                        Remove

                    </button>

                </div>


                <div class="row">


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Owner Name
                            <span class="text-danger">*</span>

                        </label>


                        <input type="text"
                               name="owners[${ownerIndex}][owner_name]"
                               class="form-control"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            CNIC
                            <span class="text-danger">*</span>

                        </label>


                        <div class="input-group">

                            <input type="text"
                                   name="owners[${ownerIndex}][cnic]"
                                   class="form-control owner-cnic"
                                   placeholder="xxxxx-xxxxxxx-x"
                                   required>


                            <button type="button"
                                    class="btn btn-outline-primary check-cnic">

                                Check

                            </button>

                        </div>


                        <small class="cnic-message mt-1 d-block"></small>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Contact No
                        </label>


                        <input type="text"
                               name="owners[${ownerIndex}][contact_no]"
                               class="form-control">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-bold">
                            Address
                        </label>


                        <textarea name="owners[${ownerIndex}][address]"
                                  class="form-control"
                                  rows="2"></textarea>

                    </div>

                </div>

            `;


            container.appendChild(row);

            ownerIndex++;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Remove Owner
    |--------------------------------------------------------------------------
    */

    container.addEventListener(
        'click',
        function (event) {


            if (
                !event.target.classList.contains(
                    'removeOwner'
                )
            ) {

                return;

            }


            const rows =
                container.querySelectorAll(
                    '.owner-row'
                );


            if (rows.length <= 1) {

                alert(
                    'At least one owner is required.'
                );

                return;

            }


            event.target
                .closest('.owner-row')
                .remove();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CNIC Check
    |--------------------------------------------------------------------------
    */

    container.addEventListener(
        'click',
        function (event) {


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
                row.querySelector(
                    '.owner-cnic'
                );


            const message =
                row.querySelector(
                    '.cnic-message'
                );


            const cnic =
                cnicInput.value.trim();


            if (!cnic) {

                message.className =
                    'cnic-message text-danger mt-1 d-block';

                message.textContent =
                    'Please enter CNIC first.';

                return;

            }


            button.disabled =
                true;

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


                    const nameInput =
                        row.querySelector(
                            '[name$="[owner_name]"]'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Existing owner found
                    |--------------------------------------------------------------------------
                    */

                    if (
                        nameInput.value.trim() &&
                        nameInput.value.trim().toLowerCase() !==
                        (owner.owner_name ?? '')
                            .trim()
                            .toLowerCase()
                    ) {

                        message.className =
                            'cnic-message text-danger mt-1 d-block';

                        message.innerHTML =
                            '⚠ This CNIC is already registered with the name <strong>' +
                            escapeHtml(owner.owner_name) +
                            '</strong>. Please verify the CNIC.';

                        return;

                    }


                    nameInput.value =
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


                    let ownerIdInput =
                        row.querySelector(
                            '.owner-id'
                        );


                    if (!ownerIdInput) {

                        ownerIdInput =
                            document.createElement(
                                'input'
                            );

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
                        '✓ Existing owner found. Details loaded.';

                }


                else {

                    message.className =
                        'cnic-message text-warning mt-1 d-block';

                    message.textContent =
                        'Owner not found. Please verify the CNIC or enter a new owner.';

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

                button.disabled =
                    false;

                button.textContent =
                    'Check';

            });

        }
    );

});

</script>

@endsection