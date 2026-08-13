@extends('layout')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>
            {{ isset($av->id) ? 'Edit Area Variation' : 'Create Area Variation' }}
        </h4>
        <a href="{{ route('area_variations.index') }}" class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM START --}}
    <form action="{{ isset($av->id) 
        ? route('area_variations.update', $av->id) 
        : route('area_variations.store') }}" method="POST">

        @csrf

        @if(isset($av->id))
            @method('PUT')
        @else
            {{-- Create ke liye hidden fields --}}
            <input type="hidden" name="plot_id" value="{{ $av->plot->id }}">
            <input type="hidden" name="previous_area" value="{{ $av->previous_area }}">
        @endif

        {{-- ================= Plot Info ================= --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                Plot Information
            </div>
            <div class="card-body row g-2">

                <div class="col-md-3">
                    <label>Project</label>
                    <input class="form-control" readonly
                        value="{{ $av->plot->project->project_name ?? '-' }}">
                </div>

                <div class="col-md-3">
                    <label>Block</label>
                    <input class="form-control" readonly
                        value="{{ $av->plot->block->block_name ?? '-' }}">
                </div>

                <div class="col-md-3">
                    <label>Street</label>
                    <input class="form-control" readonly
                        value="{{ $av->plot->street->street_name ?? '-' }}">
                </div>

                <div class="col-md-3">
                    <label>Plot No</label>
                    <input class="form-control" readonly
                        value="{{ $av->plot->plot_number }}">
                </div>

            </div>
        </div>

        {{-- ================= Area Measurement ================= --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                Area Measurement
            </div>

            <div class="card-body row g-2">

                <div class="col-md-3">
                    <label>Previous Area</label>
                    <input type="text" class="form-control" readonly
                        value="{{ $av->previous_area ?? '-' }}">
                </div>

                <div class="col-md-3">
                    <label>Measured Area</label>
                    <input type="number" step="0.01"
                        name="measured_area"
                        class="form-control"
                        value="{{ old('measured_area', $av->measured_area ?? '') }}"
                        required>
                </div>

                <div class="col-md-3">
                    <label>Measured Date</label>
                    <input type="date"
                        name="measured_date"
                        class="form-control"
                        value="{{ old('measured_date', 
                            isset($av->measured_date) 
                                ? \Carbon\Carbon::parse($av->measured_date)->format('Y-m-d')
                                : now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label>Measured By</label>

                    {{-- <input type="text"
                        name="measured_by"
                        class="form-control"
                        value="{{ old('measured_by', $av->measured_by ?? 'system') }}"> --}}
                    <input type="text"
                        class="form-control"
                        value="{{ auth()->user()->name }}"
                        readonly>

                    <input type="hidden"
                        name="measured_by"
                        value="{{ auth()->user()->name }}">
                </div>

                <div class="col-md-3">
                    <label>Remarks</label>
                    <input type="text"
                        name="remarks"
                        class="form-control"
                        value="{{ old('remarks', $av->remarks ?? '') }}">
                </div>

            </div>
        </div>

        {{-- ================= Status Updates ================= --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                Update Related Statuses (Optional)
            </div>

            <div class="card-body row g-2">

                {{-- Sewer --}}
                <div class="col-md-4">
                    <label>Sewer Manholes</label>
                    <select name="sewer_manholes" class="form-select">
                        <option value="">-- keep unchanged --</option>
                        <option value="constructed"
                            @selected(optional($av->plot->developmentStatus)->sewer_manholes == 'constructed')>
                            Constructed
                        </option>
                        <option value="not_constructed"
                            @selected(optional($av->plot->developmentStatus)->sewer_manholes == 'not_constructed')>
                            Not Constructed
                        </option>
                    </select>
                </div>

                {{-- Road --}}
                <div class="col-md-4">
                    <label>Road / Asphalt</label>
                    <select name="asphalt_tst" class="form-select">
                        <option value="">-- keep unchanged --</option>
                        <option value="yes"
                            @selected(optional($av->plot->developmentStatus)->asphalt_tst == 'yes')>
                            Yes
                        </option>
                        <option value="no"
                            @selected(optional($av->plot->developmentStatus)->asphalt_tst == 'no')>
                            No
                        </option>
                    </select>
                </div>

                {{-- Overall --}}
                <div class="col-md-4">
                    <label>Overall Status</label>
                    <select name="overall_status" class="form-select">

                        <option value="developed"
                            @selected(optional($av->plot->developmentStatus)->overall_status == 'developed')>
                            Developed
                        </option>

                        <option value="under_development"
                            @selected(optional($av->plot->developmentStatus)->overall_status == 'under_development')>
                            Under Development
                        </option>
 
                        {{-- <option value="not_developed"
                            @selected(optional($av->plot->developmentStatus)->overall_status == 'not_developed')>
                            Not Developed
                        </option> --}}
                        <option value="not_developed"
                            @selected(
                                optional($av->plot->developmentStatus)->overall_status == 'not_developed' ||
                                is_null(optional($av->plot->developmentStatus)->overall_status)
                            )>
                            Not Developed
                        </option>
                    </select>
                </div>

                {{-- LOP --}}
                <div class="col-md-4">
                    <label>LOP Status</label>
                    <select name="lop_status" class="form-select">
                        <option value="">-- keep unchanged --</option>
                        <option value="lop"
                            @selected(optional($av->plot->lopStatus)->lop_status == 'lop')>
                            LOP
                        </option>
                        <option value="non_lop"
                            @selected(optional($av->plot->lopStatus)->lop_status == 'non_lop')>
                            Non-LOP
                        </option>
                    </select>
                </div>

                {{-- Mortgage --}}
                <div class="col-md-4">
                    <label>Mortgage</label>
                    <select name="is_mortgaged" class="form-select">
                        <option value="">-- keep unchanged --</option>
                        <option value="yes"
                            @selected(optional($av->plot->mortgageStatus)->is_mortgaged == 'yes')>
                            Yes
                        </option>
                        <option value="no"
                            @selected(optional($av->plot->mortgageStatus)->is_mortgaged == 'no')>
                            No
                        </option>
                    </select>
                </div>

                {{-- defaul value set --}}
                @php
                    $status = optional($av->plot->possessionStatus)->possession_status ?? 'not_possessionable';
                @endphp
                {{-- Possession --}}
                <div class="col-md-4">
                    <label>Possession</label>
                    <select name="possession_status" class="form-select">
                        <option value="possessionable" @selected($status == 'possessionable')>
                            Possessionable
                        </option>

                        <option value="non_lop_possessionable" @selected($status == 'non_lop_possessionable')>
                            Non-LOP Possessionable
                        </option>

                        <option value="under_development_possessionable" @selected($status == 'under_development_possessionable')>
                            Under Development Possessionable
                        </option>

                        <option value="not_possessionable" @selected($status == 'not_possessionable')>
                            Not Possessionable
                        </option>
                    </select>
                    {{-- old select options --}}
                    {{-- <select name="possession_status" class="form-select">
                        <option value="possessionable"
                            @selected(optional($av->plot->possessionStatus)->possession_status == 'possessionable')>
                            Possessionable
                        </option>

                        <option value="non_lop_possessionable"
                            @selected(optional($av->plot->possessionStatus)->possession_status == 'non_lop_possessionable')>
                            Non-LOP Possessionable
                        </option>

                        <option value="under_development_possessionable"
                            @selected(optional($av->plot->possessionStatus)->possession_status == 'under_development_possessionable')>
                            Under Development Possessionable
                        </option>

                        <option value="not_possessionable"
                            @selected(optional($av->plot->possessionStatus)->possession_status == 'not_possessionable')>
                            Not Possessionable
                        </option>
                    </select> --}}
                </div>

            </div>
        </div>

        <div class="text-end">
            <button class="btn btn-primary">
                {{ isset($av->id) ? 'Update Area Variation' : 'Save Area Variation' }}
            </button>
        </div>

    </form>
</div>
@endsection