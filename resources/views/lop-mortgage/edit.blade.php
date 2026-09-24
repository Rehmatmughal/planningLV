@extends('app')

@section('content')

<div class="container-fluid">

```
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit LOP & Mortgage Status</h4>
        <p class="text-muted mb-0">
            Update LOP and Mortgage status for the selected plot.
        </p>
    </div>

    <a href="{{ route('lop-mortgage.index') }}"
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
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="card shadow-sm">

    <div class="card-header">
        <strong>Plot Information</strong>
    </div>

    <div class="card-body">

        {{-- Plot Information --}}
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <label class="form-label">Project</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->project?->project_name }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Property Type</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->propertyType?->name }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Block</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->block?->block_name }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Street</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->street?->street_name ?? 'N/A' }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Plot No.</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->plot_number }}"
                       readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Plot Size</label>
                <input type="text"
                       class="form-control"
                       value="{{ $plot->size?->title ?? 'N/A' }}"
                       readonly>
            </div>

        </div>


        <hr>


        {{-- Update Form --}}
        <form action="{{ route('lop-mortgage.update', $plot) }}"
              method="POST">

            @csrf
            @method('PUT')


            <div class="row g-3">

                {{-- LOP Status --}}
                <div class="col-md-6">

                    <label for="lop_status" class="form-label">
                        LOP Status <span class="text-danger">*</span>
                    </label>

                    <select name="lop_status"
                            id="lop_status"
                            class="form-select"
                            required>

                        <option value="">Select LOP Status</option>

                        <option value="lop"
                            {{ old('lop_status', $plot->lopStatus?->lop_status) === 'lop' ? 'selected' : '' }}>
                            LOP
                        </option>

                        <option value="non_lop"
                            {{ old('lop_status', $plot->lopStatus?->lop_status) === 'non_lop' ? 'selected' : '' }}>
                            Non-LOP
                        </option>

                    </select>

                </div>


                {{-- Mortgage --}}
                <div class="col-md-6">

                    <label for="is_mortgaged" class="form-label">
                        Mortgage <span class="text-danger">*</span>
                    </label>

                    <select name="is_mortgaged"
                            id="is_mortgaged"
                            class="form-select"
                            required>

                        <option value="">Select Mortgage Status</option>

                        <option value="yes"
                            {{ old('is_mortgaged', $plot->mortgageStatus?->is_mortgaged) === 'yes' ? 'selected' : '' }}>
                            Yes
                        </option>

                        <option value="no"
                            {{ old('is_mortgaged', $plot->mortgageStatus?->is_mortgaged) === 'no' ? 'selected' : '' }}>
                            No
                        </option>

                    </select>

                    <small class="text-muted">
                        Mortgage can be YES only when LOP status is LOP.
                    </small>

                </div>


                {{-- Remarks --}}
                <div class="col-12">

                    <label for="remarks" class="form-label">
                        Remarks
                    </label>

                    <textarea name="remarks"
                              id="remarks"
                              rows="4"
                              class="form-control"
                              placeholder="Enter remarks...">{{ old('remarks', $plot->lopStatus?->remarks ?? $plot->mortgageStatus?->remarks) }}</textarea>

                </div>

            </div>


            {{-- Buttons --}}
            <div class="mt-4">

                <button type="submit"
                        class="btn btn-primary">
                    Update Status
                </button>

                <a href="{{ route('lop-mortgage.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>
```

</div>

@endsection
