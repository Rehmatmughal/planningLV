@extends('app')

@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="fw-bold mb-1">Edit Possession Case</h3>

            <p class="text-muted mb-0">
                Case #{{ $possessionCase->case_no }}
            </p>
        </div>

        <div>
            <a href="{{ route('possession-cases.show', $possessionCase) }}"
               class="btn btn-secondary">
                ← Back
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


    <form action="{{ route('possession-cases.update', $possessionCase) }}"
          method="POST">

        @csrf
        @method('PUT')


        {{-- =========================
             CASE INFORMATION
        ========================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">
                <strong>Case Information</strong>
            </div>

            <div class="card-body">

                <div class="row">


                    {{-- Plot --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Plot <span class="text-danger">*</span>
                        </label>

                        <select name="plot_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Plot
                            </option>

                            @foreach($plots as $plot)

                                <option value="{{ $plot->id }}"
                                    {{ old('plot_id', $possessionCase->plot_id) == $plot->id ? 'selected' : '' }}>

                                    {{ $plot->project?->project_name }}
                                    -
                                    {{ $plot->block?->block_name }}
                                    -
                                    Plot {{ $plot->plot_number }}

                                    @if($plot->size)
                                        ({{ $plot->size->title }})
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Case Number --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Case No <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="case_no"
                               class="form-control"
                               value="{{ old('case_no', $possessionCase->case_no) }}"
                               required>

                    </div>


                    {{-- Need Approval --}}
                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold d-block">
                            Need Approval?
                        </label>

                        <div class="form-check form-switch mt-2">

                            <input type="hidden"
                                   name="need_approval"
                                   value="0">

                            <input type="checkbox"
                                   name="need_approval"
                                   value="1"
                                   class="form-check-input"
                                   id="need_approval"
                                   {{ old('need_approval', $possessionCase->need_approval) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="need_approval">
                                Yes
                            </label>

                        </div>

                    </div>

                </div>


                <div class="row">


                    {{-- Current Holder Type --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Current Holder Type
                        </label>

                        <input type="text"
                               name="current_holder_type"
                               class="form-control"
                               value="{{ old('current_holder_type', $possessionCase->current_holder_type) }}"
                               placeholder="e.g. Staff / Officer">

                    </div>


                    {{-- Current Holder ID --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Current Holder ID
                        </label>

                        <input type="number"
                               name="current_holder_id"
                               class="form-control"
                               value="{{ old('current_holder_id', $possessionCase->current_holder_id) }}">

                    </div>


                    {{-- Current Holder Name --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Current Holder Name
                        </label>

                        <input type="text"
                               name="current_holder_name"
                               class="form-control"
                               value="{{ old('current_holder_name', $possessionCase->current_holder_name) }}">

                    </div>

                </div>


                <div class="row">


                    {{-- Received Date --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Received Date
                        </label>

                        <input type="date"
                               name="received_at"
                               class="form-control"
                               value="{{ old('received_at', optional($possessionCase->received_at)->format('Y-m-d')) }}">

                    </div>

                </div>


                {{-- Remarks --}}
                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Remarks
                    </label>

                    <textarea name="remarks"
                              class="form-control"
                              rows="3"
                              placeholder="Enter remarks">{{ old('remarks', $possessionCase->remarks) }}</textarea>

                </div>

            </div>

        </div>



        {{-- =========================
             OWNERS
        ========================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">

                <strong>Owners</strong>

                <button type="button"
                        class="btn btn-light btn-sm"
                        id="addOwner">

                    + Add Owner

                </button>

            </div>


            <div class="card-body">

                <div id="ownersContainer">


                    @foreach($possessionCase->owners as $index => $owner)

                        <div class="owner-row border rounded p-3 mb-3">


                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <strong>
                                    Owner #{{ $index + 1 }}
                                </strong>

                                <button type="button"
                                        class="btn btn-danger btn-sm removeOwner">

                                    Remove

                                </button>

                            </div>


                            {{-- Owner ID --}}
                            <input type="hidden"
                                   name="owners[{{ $index }}][id]"
                                   value="{{ $owner->id }}">


                            <div class="row">


                                {{-- Owner Name --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Owner Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][owner_name]"
                                           class="form-control"
                                           value="{{ old('owners.' . $index . '.owner_name', $owner->owner_name) }}"
                                           required>

                                </div>


                                {{-- CNIC --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        CNIC
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][cnic]"
                                           class="form-control"
                                           value="{{ old('owners.' . $index . '.cnic', $owner->cnic) }}"
                                           placeholder="xxxxx-xxxxxxx-x">

                                </div>


                                {{-- Contact --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Contact No
                                    </label>

                                    <input type="text"
                                           name="owners[{{ $index }}][contact_no]"
                                           class="form-control"
                                           value="{{ old('owners.' . $index . '.contact_no', $owner->contact_no) }}">

                                </div>


                                {{-- Ownership Percentage --}}
                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">
                                        Ownership %
                                    </label>

                                    <input type="number"
                                           name="owners[{{ $index }}][ownership_percentage]"
                                           class="form-control"
                                           value="{{ old('owners.' . $index . '.ownership_percentage', $owner->ownership_percentage) }}"
                                           step="0.01"
                                           min="0"
                                           max="100">

                                </div>


                                {{-- Address --}}
                                <div class="col-md-12 mb-3">

                                    <label class="form-label fw-bold">
                                        Address
                                    </label>

                                    <textarea name="owners[{{ $index }}][address]"
                                              class="form-control"
                                              rows="2">{{ old('owners.' . $index . '.address', $owner->address) }}</textarea>

                                </div>

                            </div>

                        </div>

                    @endforeach


                </div>

            </div>

        </div>



        {{-- =========================
             BUTTONS
        ========================== --}}

        <div class="d-flex justify-content-end gap-2 mb-5">

            <a href="{{ route('possession-cases.show', $possessionCase) }}"
               class="btn btn-secondary">

                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary">

                Update Possession Case

            </button>

        </div>


    </form>

</div>



{{-- =========================
     JAVASCRIPT
========================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('ownersContainer');

    const addOwnerButton = document.getElementById('addOwner');

    let ownerIndex = {{ $possessionCase->owners->count() }};


    // Add New Owner
    addOwnerButton.addEventListener('click', function () {

        const row = document.createElement('div');

        row.className = 'owner-row border rounded p-3 mb-3';


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
                    </label>

                    <input type="text"
                           name="owners[${ownerIndex}][cnic]"
                           class="form-control"
                           placeholder="xxxxx-xxxxxxx-x">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label fw-bold">
                        Contact No
                    </label>

                    <input type="text"
                           name="owners[${ownerIndex}][contact_no]"
                           class="form-control">

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label fw-bold">
                        Ownership %
                    </label>

                    <input type="number"
                           name="owners[${ownerIndex}][ownership_percentage]"
                           class="form-control"
                           step="0.01"
                           min="0"
                           max="100">

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

    });


    // Remove Owner
    container.addEventListener('click', function (event) {

        if (event.target.classList.contains('removeOwner')) {

            const rows = container.querySelectorAll('.owner-row');


            if (rows.length > 1) {

                event.target.closest('.owner-row').remove();

            } else {

                alert('At least one owner is required.');

            }

        }

    });

});

</script>

@endsection
