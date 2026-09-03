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

                <div class="row g-3">

                    {{-- Plot --}}
                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Plot <span class="text-danger">*</span>
                        </label>

                        <select name="plot_id"
                                id="plot_id"
                                class="form-select @error('plot_id') is-invalid @enderror"
                                required>

                            <option value="">
                                -- Select Plot --
                            </option>

                            @foreach($plots as $plot)

                                <option value="{{ $plot->id }}"
                                    {{ old('plot_id', $selectedPlot?->id) == $plot->id ? 'selected' : '' }}>

                                    Plot {{ $plot->plot_number }}

                                    @if($plot->project)
                                        - {{ $plot->project->project_name }}
                                    @endif

                                    @if($plot->block)
                                        - {{ $plot->block->block_name }}
                                    @endif

                                    @if($plot->street)
                                        - {{ $plot->street->street_name }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                        @error('plot_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


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
                    <div class="col-md-4">

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
                    <div class="col-md-4">

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
                    <div class="col-md-8">

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
                                               name="owners[{{ $index }}][owner_name]"
                                               class="form-control"
                                               value="{{ $owner['owner_name'] ?? '' }}"
                                               required>

                                    </div>


                                    <div class="col-md-4">

                                        <label class="form-label">
                                            CNIC
                                        </label>

                                        <input type="text"
                                               name="owners[{{ $index }}][cnic]"
                                               class="form-control"
                                               value="{{ $owner['cnic'] ?? '' }}"
                                               placeholder="xxxxx-xxxxxxx-x">

                                    </div>


                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Contact No
                                        </label>

                                        <input type="text"
                                               name="owners[{{ $index }}][contact_no]"
                                               class="form-control"
                                               value="{{ $owner['contact_no'] ?? '' }}">

                                    </div>


                                    <div class="col-md-8">

                                        <label class="form-label">
                                            Address
                                        </label>

                                        <textarea name="owners[{{ $index }}][address]"
                                                  class="form-control"
                                                  rows="2">{{ $owner['address'] ?? '' }}</textarea>

                                    </div>


                                    <div class="col-md-4">

                                        <label class="form-label">
                                            Ownership %
                                        </label>

                                        <div class="input-group">

                                            <input type="number"
                                                   name="owners[{{ $index }}][ownership_percentage]"
                                                   class="form-control"
                                                   value="{{ $owner['ownership_percentage'] ?? '' }}"
                                                   min="0"
                                                   max="100"
                                                   step="0.01">

                                            <span class="input-group-text">
                                                %
                                            </span>

                                        </div>

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

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Owner Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="owners[0][owner_name]"
                                           class="form-control"
                                           required>

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        CNIC
                                    </label>

                                    <input type="text"
                                           name="owners[0][cnic]"
                                           class="form-control"
                                           placeholder="xxxxx-xxxxxxx-x">

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Contact No
                                    </label>

                                    <input type="text"
                                           name="owners[0][contact_no]"
                                           class="form-control">

                                </div>


                                <div class="col-md-8">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea name="owners[0][address]"
                                              class="form-control"
                                              rows="2"></textarea>

                                </div>


                                <div class="col-md-4">

                                    <label class="form-label">
                                        Ownership %
                                    </label>

                                    <div class="input-group">

                                        <input type="number"
                                               name="owners[0][ownership_percentage]"
                                               class="form-control"
                                               min="0"
                                               max="100"
                                               step="0.01">

                                        <span class="input-group-text">
                                            %
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>


                <div class="alert alert-info mb-0">

                    <small>
                        <strong>Note:</strong>
                        If a plot has multiple owners, use
                        <strong>+ Add Owner</strong> to add additional owners.
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
     JAVASCRIPT - ADD / REMOVE OWNERS
========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('ownersContainer');
    const addButton = document.getElementById('addOwnerBtn');

    let ownerIndex = {{ old('owners') ? count(old('owners')) : 1 }};


    addButton.addEventListener('click', function () {

        const ownerNumber = ownerIndex + 1;

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

                        <input type="text"
                               name="owners[${ownerIndex}][cnic]"
                               class="form-control"
                               placeholder="xxxxx-xxxxxxx-x">

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


                    <div class="col-md-4">

                        <label class="form-label">
                            Ownership %
                        </label>

                        <div class="input-group">

                            <input type="number"
                                   name="owners[${ownerIndex}][ownership_percentage]"
                                   class="form-control"
                                   min="0"
                                   max="100"
                                   step="0.01">

                            <span class="input-group-text">
                                %
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        `;

        container.insertAdjacentHTML('beforeend', ownerHtml);

        ownerIndex++;

    });


    // Remove owner
    container.addEventListener('click', function (event) {

        if (event.target.classList.contains('remove-owner')) {

            const rows = container.querySelectorAll('.owner-row');

            // At least one owner must remain
            if (rows.length <= 1) {

                alert('At least one owner is required.');

                return;
            }

            event.target.closest('.owner-row').remove();

        }

    });

});

</script>

@endsection
