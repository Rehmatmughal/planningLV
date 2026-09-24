@extends('app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Edit Development Status
            </h4>

            <p class="text-muted mb-0">
                Update development status of the selected plot.
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


    {{-- Plot Information --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                <i class="bi bi-geo-alt me-1"></i>
                Plot Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                {{-- Project --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Project
                    </label>

                    <div class="form-control bg-light">
                        {{ $plot->project?->project_name ?? '-' }}
                    </div>

                </div>


                {{-- Block --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Block
                    </label>

                    <div class="form-control bg-light">
                        {{ $plot->block?->block_name ?? '-' }}
                    </div>

                </div>


                {{-- Street --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Street
                    </label>

                    <div class="form-control bg-light">
                        {{ $plot->street?->street_name ?? '-' }}
                    </div>

                </div>


                {{-- Plot Number --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Plot Number
                    </label>

                    <div class="form-control bg-light fw-bold">
                        {{ $plot->plot_number }}
                    </div>

                </div>


                {{-- Property Type --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Property Type
                    </label>

                    <div class="form-control bg-light">
                        {{ $plot->propertyType?->name ?? '-' }}
                    </div>

                </div>


                {{-- Size --}}
                <div class="col-md-3">

                    <label class="form-label text-muted">
                        Size
                    </label>

                    <div class="form-control bg-light">
                        {{ $plot->size?->title ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Development Form --}}
    <form action="{{ route('development.update', $plot) }}"
          method="POST">

        @csrf

        @method('PUT')


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">

                <strong>
                    <i class="bi bi-pencil-square me-1"></i>
                    Development Status
                </strong>

            </div>


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
                                {{ old(
                                    'sewer_manholes',
                                    $plot->developmentStatus?->sewer_manholes
                                ) === 'constructed' ? 'selected' : '' }}>

                                Constructed

                            </option>

                            <option value="not_constructed"
                                {{ old(
                                    'sewer_manholes',
                                    $plot->developmentStatus?->sewer_manholes
                                ) === 'not_constructed' ? 'selected' : '' }}>

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
                                {{ old(
                                    'asphalt_tst',
                                    $plot->developmentStatus?->asphalt_tst
                                ) === 'yes' ? 'selected' : '' }}>

                                Yes

                            </option>

                            <option value="no"
                                {{ old(
                                    'asphalt_tst',
                                    $plot->developmentStatus?->asphalt_tst
                                ) === 'no' ? 'selected' : '' }}>

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
                                {{ old(
                                    'overall_status',
                                    $plot->developmentStatus?->overall_status
                                ) === 'developed' ? 'selected' : '' }}>

                                Developed

                            </option>

                            <option value="under_development"
                                {{ old(
                                    'overall_status',
                                    $plot->developmentStatus?->overall_status
                                ) === 'under_development' ? 'selected' : '' }}>

                                Under Development

                            </option>

                            <option value="not_developed"
                                {{ old(
                                    'overall_status',
                                    $plot->developmentStatus?->overall_status
                                ) === 'not_developed' ? 'selected' : '' }}>

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
                                  placeholder="Enter remarks if required">{{ old(
                                    'remarks',
                                    $plot->developmentStatus?->remarks
                                ) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- Buttons --}}
        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('development.index') }}"
               class="btn btn-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back

            </a>


            <button type="submit"
                    class="btn btn-success">

                <i class="bi bi-check-circle me-1"></i>
                Update Development Status

            </button>

        </div>

    </form>

</div>

@endsection
