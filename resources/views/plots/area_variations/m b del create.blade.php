@extends('layout')

@section('content')
<div class="container-fluid mt-3">
    {{-- <div class="card shadow-sm"> --}}
        {{-- <div class="container-flud mt-5"> --}}
            <div class="card-body table-responsive p-0">
                <div class="mt-3">
                    <form action="{{ route('area_variations.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="plot_id" value="{{ $plot->id }}">
                        {{-- <input type="hidden" name="previous_area" value="{{ $latestArea }}"> --}}
                        @php
                            $previousArea = $plot->latestAreavariation
                                                ? $plot->latestAreavariation->measured_area
                                                : $plot->plotsize?->size_area;
                        @endphp

                        <input type="hidden" name="previous_area" value="{{ $previousArea }}">


                        <div class="mb-2">
                            <label>Measured Area</label>
                            <input type="number" step="0.01" name="measured_area" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Measurement Date</label>
                            {{-- <input type="date" name="measured_date" class="form-control" required> --}}
                            <input type="date" name="measured_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            

                        </div>

                        {{-- Temporary solution --}}
                        <input type="hidden" name="measured_by" value="system">

                        <button class="btn btn-success">Save Variation</button>
                    </form>
                </div>
            </div>
        {{-- </div> --}}
    {{-- </div> --}}
</div>


@endsection