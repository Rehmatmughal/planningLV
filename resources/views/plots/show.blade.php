@extends('layout')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            Plot 11 Details —
            {{ $plot->block->block_name }}-{{ $plot->plot_number }}
        </h3>
        <a href="{{ route('plot.print', $plot->id) }}"
        target="_blank"
        class="btn btn-outline-dark btn-sm">
        🖨 Print
        </a>


        <a href="{{ route('plots.index') }}" class="btn btn-secondary btn-sm">
            ← Back
        </a>
    </div>

    {{-- BASIC INFO --}}
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            Basic Information
        </div>
        <div class="card-body row">
            <div class="col-md-3"><strong>Project:</strong> {{ $plot->project->project_name }}</div>
            <div class="col-md-3"><strong>Block:</strong> {{ $plot->block->block_name }}</div>
            <div class="col-md-3"><strong>Street:</strong> {{ $plot->street->street_name }}</div>
            <div class="col-md-3"><strong>Plot No:</strong> {{ $plot->plot_number }}</div>

            <div class="col-md-3 mt-2"><strong>Size:</strong> {{ $plot->size->title }}</div>
            <div class="col-md-3 mt-2"><strong>Category:</strong> {{ $plot->category->category_title }}</div>
            <div class="col-md-3 mt-2"><strong>Numbering:</strong> {{ ucfirst($plot->numbering_type) }}</div>
        </div>
    </div>

    {{-- LOP & MORTGAGE --}}
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            LOP & Mortgage Status
        </div>
        <div class="card-body row">
            <div class="col-md-4">
                <strong>LOP Status:</strong>
                <span class="badge bg-success">
                    {{ strtoupper($plot->lopStatus->lop_status ?? 'N/A') }}
                </span>
            </div>

            <div class="col-md-4">
                <strong>Mortgage:</strong>
                <span class="badge {{ optional($plot->mortgageStatus)->is_mortgaged == 'yes' ? 'bg-danger' : 'bg-secondary' }}">
                    {{ optional($plot->mortgageStatus)->is_mortgaged ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    {{-- DEVELOPMENT STATUS --}}
    <div class="card mb-3">
        <div class="card-header bg-warning">
            Development Status
        </div>
        <div class="card-body row">
            <div class="col-md-4">
                <strong>Sewer:</strong> {{ $plot->developmentStatus->sewer_manholes ?? 'N/A' }}
            </div>
            <div class="col-md-4">
                <strong>Asphalt TST:</strong> {{ $plot->developmentStatus->asphalt_tst ?? 'N/A' }}
            </div>
            <div class="col-md-4">
                <strong>Overall:</strong>
                <span class="badge bg-dark">
                    {{ $plot->developmentStatus->overall_status ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    {{-- COORDINATES --}}
    
    @if($plot->coordinates)
    <div class="card mb-3">
        <div class="card-header bg-secondary text-white">
            Plot Coordinates
        </div>
        <div class="card-body row">
            <div class="col-md-3"><strong>Easting:</strong> {{ $plot->coordinates->Easting }}</div>
            <div class="col-md-3"><strong>Northing:</strong> {{ $plot->coordinates->Northing }}</div>
            <div class="col-md-3"><strong>Latitude:</strong> {{ $plot->coordinates->latitude }}</div>
            <div class="col-md-3"><strong>Longitude:</strong> {{ $plot->coordinates->longitude }}</div>
            <div class="col-md-3"><strong>Lat,Long:</strong> {{ $plot->coordinates->latitude }},{{ $plot->coordinates->longitude }}</div>

            <a href="{{ route('googlemap.index', $plot->id) }}" class="btn btn-secondary btn-sm">
                view
            </a>
        </div>
    </div>
    @endif

    {{-- AREA VARIATION HISTORY --}}
    <div class="card">
        <div class="card-header bg-dark text-white">
            Area Variation History
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Measured Area</th>
                        <th>Road</th>
                        <th>Sewer</th>
                        <th>LOP</th>
                        <th>Remarks</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areaVariations as $av)
                        <tr>
                            <td>{{ $av->measured_date }}</td>
                            <td>{{ $av->measured_area }}</td>
                            <td>{{ $av->road_status_at_time }}</td>
                            <td>{{ $av->sewer_status_at_time }}</td>
                            <td>{{ strtoupper($av->lop_status_at_time) }}</td>
                            <td>{{ $av->remarks }}</td>
                            <td>
                                <a href="{{ route('area_variations.excel',$av->id) }}"
                                class="btn btn-success btn-sm">
                                Download Excel
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No area variation history found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
