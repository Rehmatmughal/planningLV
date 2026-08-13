@extends('layout')

@section('content')
<div class="container mt-4">
    <table class="table table-bordered">
        <thead>
             <tr align="center">
                <th>#</th>
                <th>Project</th>
                <th>Block</th>
                <th>Street</th>
                <th>Plot No</th>
                <th>Size</th>
                <th>Sewer Development status</th>
                <th>Road Development Status</th>
                <th>LOP Status</th>
                <th>Mortgage Status</th>
                <th>Possession status</th>
                <th>Area of plot</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($areaVariations as $av)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $av->plot->project->project_name ?? '-' }}</td>
                    <td>{{ $av->plot->block->block_name ?? '-' }}</td>
                    <td>{{ $av->plot->street->street_name ?? '-' }}</td>

                    <td>{{ $av->plot->plot_number }}</td>
                    <td>{{ $av->plot->size }}</td>

                    <td>{{ $av->plot->developmentStatus->sewer_manholes ?? '-' }}</td>
                    <td>{{ $av->plot->developmentStatus->asphalt_tst ?? '-' }}</td>

                    <td>{{ $av->plot->lopStatus->lop_status ?? '-' }}</td>
                    <td>{{ $av->plot->mortgageStatus->is_mortgaged ?? '-' }}</td>
                    <td>{{ $av->plot->possessionStatus->possession_status ?? '-' }}</td>

                    <td>{{ $av->measured_area }}</td>

                    <td>
                        {{-- <a href="{{ route('area_variations.print', $av->id) }}" class="btn btn-sm btn-primary">Print</a> --}}
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>
</div>
@endsection