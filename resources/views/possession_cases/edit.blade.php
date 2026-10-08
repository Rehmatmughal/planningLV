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
                Update possession case information and owner details.
            </p>
        </div>

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

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


    {{-- Main Form --}}
    <form method="POST"
          action="{{ route('possession-cases.update', $possessionCase) }}">

        @csrf
        @method('PUT')


        {{-- =========================================================
            POSSESSION INFORMATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    Possession Information
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


                    {{-- Reference No --}}
                    <div class="col-md-4">

                        <label for="reference_no"
                               class="form-label fw-semibold">
                            Reference No.
                        </label>

                        <input type="text"
                               name="reference_no"
                               id="reference_no"
                               class="form-control"
                               value="{{ old('reference_no', $possessionCase->reference_no) }}">

                    </div>


                    {{-- Need Approval --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold d-block">
                            Need Approval
                        </label>

                        <input type="hidden"
                               name="need_approval"
                               value="0">

                        <div class="form-check mt-2">

                            <input type="checkbox"
                                   name="need_approval"
                                   id="need_approval"
                                   value="1"
                                   class="form-check-input"
                                   {{ old('need_approval', $possessionCase->need_approval) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="need_approval">
                                Approval Required
                            </label>

                        </div>

                    </div>

                </div>

            </div>
        </div>



        {{-- =========================================================
            PLOT INFORMATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    Plot Information
                </h5>
            </div>

            <div class="card-body">

                {{-- Hidden plot ID --}}
                <input type="hidden"
                       name="plot_id"
                       value="{{ $possessionCase->plot_id }}">

                <div class="row g-3">

                    {{-- Project --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Project
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->project?->project_name }}"
                               readonly>

                    </div>


                    {{-- Block --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Block
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->block?->block_name }}"
                               readonly>

                    </div>


                    {{-- Street --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Street
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->street?->street_name }}"
                               readonly>

                    </div>


                    {{-- Plot Number --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Plot No.
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->plot_number }}"
                               readonly>

                    </div>


                    {{-- Property Type --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Property Type
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->propertyType?->name }}"
                               readonly>

                    </div>


                    {{-- Plot Size --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Plot Size
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->size?->name }}"
                               readonly>

                    </div>


                    {{-- Numbering Type --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Numbering Type
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $possessionCase->plot?->numbering_type }}"
                               readonly>

                    </div>

                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle"></i>
                    Plot information is fixed for an existing possession case and cannot be changed.
                </div>

            </div>
        </div>



        {{-- =========================================================
            OWNER INFORMATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-success text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        Owner Information
                    </h5>

                    <button type="button"
                            class="btn btn-light btn-sm"
                            id="addOwner">

                        <i class="bi bi-plus-circle"></i>
                        Add Owner

                    </button>

                </div>

            </div>


            <div class="card-body">

                <div id="ownersContainer">

                    @foreach ($possessionCase->owners as $index => $owner)

                        <div class="owner-row border rounded p-3 mb-3 bg-light">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h6 class="fw-bold mb-0">
                                    Owner {{ $index + 1 }}
                                </h6>

                                <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-owner">

                                    <i class="bi bi-trash"></i>
                                    Remove

                                </button>

                            </div>


                            {{-- Hidden IDs --}}
                            <input type="hidden"
                                   name="owners[{{ $index }}][id]"
                                   value="{{ $owner->id }}">

                            <input type="hidden"
                                   name="owners[{{ $index }}][owner_id]"
                                   class="owner-id"
                                   value="{{ $owner->id }}">


                            <div class="row g-3">

                                {{-- Owner Name --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Owner Name
                                        <span class="text-danger">*</span>
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
                                        CNIC
                                        <span class="text-danger">*</span>
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

                                    <small class="cnic-message mt-1 d-block"></small>

                                </div>


                                {{-- Contact --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Contact No.
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][contact_no]"
                                           class="form-control owner-contact"
                                           value="{{ old("owners.$index.contact_no", $owner->contact_no) }}">

                                </div>


                                {{-- Address --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Address
                                    </label>

                                    <textarea name="owners[{{ $index }}][address]"
                                              class="form-control owner-address"
                                              rows="2">{{ old("owners.$index.address", $owner->pivot?->address_snapshot ?? $owner->address) }}</textarea>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                @if ($possessionCase->owners->count() === 0)

                    <div class="alert alert-warning mb-0">
                        No owner is currently attached to this possession case.
                        Please add at least one owner.
                    </div>

                @endif

            </div>
        </div>



        {{-- =========================================================
            OTHER INFORMATION
        ========================================================== --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    Other Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

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
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Received Date
                        </label>

                        <input type="date"
                               name="received_at"
                               class="form-control"
                               value="{{ old('received_at', $possessionCase->received_at ? \Carbon\Carbon::parse($possessionCase->received_at)->format('Y-m-d') : '') }}">

                    </div>


                    {{-- Remarks --}}
                    <div class="col-md-8">

                        <label class="form-label fw-semibold">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="3">{{ old('remarks', $possessionCase->remarks) }}</textarea>

                    </div>

                </div>

            </div>
        </div>



        {{-- =========================================================
            FORM BUTTONS
        ========================================================== --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a href="{{ route('possession-cases.show', $possessionCase) }}"
               class="btn btn-secondary">

                <i class="bi bi-x-circle"></i>
                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-save"></i>
                Update Possession Case

            </button>

        </div>

    </form>

</div>



{{-- =============================================================
    JAVASCRIPT
============================================================= --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const ownersContainer = document.getElementById('ownersContainer');
    const addOwnerButton = document.getElementById('addOwner');

    let ownerIndex = {{ $possessionCase->owners->count() }};


    /*
    |--------------------------------------------------------------------------
    | Add Owner
    |--------------------------------------------------------------------------
    */

    addOwnerButton.addEventListener('click', function () {

        const ownerNumber = ownerIndex + 1;

        const html = `
            <div class="owner-row border rounded p-3 mb-3 bg-light">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h6 class="fw-bold mb-0">
                        Owner ${ownerNumber}
                    </h6>

                    <button type="button"
                            class="btn btn-outline-danger btn-sm remove-owner">

                        <i class="bi bi-trash"></i>
                        Remove

                    </button>

                </div>

                <input type="hidden"
                       name="owners[${ownerIndex}][id]"
                       value="">

                <input type="hidden"
                       name="owners[${ownerIndex}][owner_id]"
                       class="owner-id"
                       value="">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Owner Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="owners[${ownerIndex}][owner_name]"
                               class="form-control owner-name"
                               required>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            F/H/W Name
                        </label>

                        <input type="text"
                               name="owners[${ownerIndex}][relative_name]"
                               class="form-control relative-name">

                    </div>

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            CNIC
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <input type="text"
                                   name="owners[${ownerIndex}][cnic]"
                                   class="form-control owner-cnic"
                                   required>

                            <button type="button"
                                    class="btn btn-outline-primary check-cnic">

                                Check

                            </button>

                        </div>

                        <small class="cnic-message mt-1 d-block"></small>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Contact No.
                        </label>

                        <input type="text"
                               name="owners[${ownerIndex}][contact_no]"
                               class="form-control owner-contact">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            Address
                        </label>

                        <textarea name="owners[${ownerIndex}][address]"
                                  class="form-control owner-address"
                                  rows="2"></textarea>

                    </div>

                </div>

            </div>
        `;

        ownersContainer.insertAdjacentHTML('beforeend', html);

        ownerIndex++;

    });



    /*
    |--------------------------------------------------------------------------
    | Remove Owner
    |--------------------------------------------------------------------------
    */

    ownersContainer.addEventListener('click', function (event) {

        const removeButton = event.target.closest('.remove-owner');

        if (!removeButton) {
            return;
        }

        const ownerRows = ownersContainer.querySelectorAll('.owner-row');

        if (ownerRows.length <= 1) {

            alert('At least one owner is required.');

            return;
        }

        const row = removeButton.closest('.owner-row');

        row.remove();

    });



    /*
    |--------------------------------------------------------------------------
    | CNIC Check
    |--------------------------------------------------------------------------
    */

    ownersContainer.addEventListener('click', function (event) {

        const checkButton = event.target.closest('.check-cnic');

        if (!checkButton) {
            return;
        }

        const row = checkButton.closest('.owner-row');

        const cnicInput = row.querySelector('.owner-cnic');
        const nameInput = row.querySelector('.owner-name');
        const relativeInput = row.querySelector('.relative-name');
        const contactInput = row.querySelector('.owner-contact');
        const addressInput = row.querySelector('.owner-address');
        const ownerIdInput = row.querySelector('.owner-id');
        const message = row.querySelector('.cnic-message');

        const cnic = cnicInput.value.trim();

        if (!cnic) {

            message.className = 'cnic-message mt-1 d-block text-danger';
            message.textContent = 'Please enter CNIC first.';

            return;
        }


        message.className = 'cnic-message mt-1 d-block text-muted';
        message.textContent = 'Checking CNIC...';

        checkButton.disabled = true;


        fetch(`{{ route('owners.findByCnic') }}?cnic=${encodeURIComponent(cnic)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {

            if (!response.ok) {
                throw new Error('Unable to check CNIC.');
            }

            return response.json();

        })
        .then(data => {

            /*
            |--------------------------------------------------------------------------
            | Existing Owner Found
            |--------------------------------------------------------------------------
            */

            if (data.found && data.owner) {

                const existingName = (data.owner.owner_name || '').trim();
                const enteredName = nameInput.value.trim();


                /*
                |--------------------------------------------------------------------------
                | Name Mismatch
                |--------------------------------------------------------------------------
                */

                if (
                    enteredName &&
                    existingName &&
                    enteredName.toLowerCase() !== existingName.toLowerCase()
                ) {

                    ownerIdInput.value = '';

                    message.className =
                        'cnic-message mt-1 d-block text-danger fw-semibold';

                    message.textContent =
                        `CNIC already belongs to "${existingName}". Please verify the CNIC and owner name.`;

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Fill Existing Owner Details
                |--------------------------------------------------------------------------
                */

                ownerIdInput.value = data.owner.id ?? '';

                nameInput.value = data.owner.owner_name ?? '';
                relativeInput.value = data.owner.relative_name ?? '';
                cnicInput.value = data.owner.cnic ?? cnic;
                contactInput.value = data.owner.contact_no ?? '';
                addressInput.value = data.owner.address ?? '';


                message.className =
                    'cnic-message mt-1 d-block text-success fw-semibold';

                message.textContent =
                    '✓ Existing owner found. Details loaded.';

            }

            /*
            |--------------------------------------------------------------------------
            | Owner Not Found
            |--------------------------------------------------------------------------
            */

            else {

                ownerIdInput.value = '';

                message.className =
                    'cnic-message mt-1 d-block text-warning fw-semibold';

                message.textContent =
                    'Owner not found. Please verify the CNIC or enter a new owner.';

            }

        })
        .catch(error => {

            ownerIdInput.value = '';

            message.className =
                'cnic-message mt-1 d-block text-danger fw-semibold';

            message.textContent =
                'Unable to check CNIC. Please try again.';

            console.error(error);

        })
        .finally(() => {

            checkButton.disabled = false;

        });

    });

});

</script>

@endsection