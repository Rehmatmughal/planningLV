@extends('layout')
@section('content')
    <h2 style="text-align: center;">GR Blocks List</h2>
    <div class="container">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project Name</th>
                    <th>Total No of plots</th>
                    {{-- <th>Project</th>
                    <th>Remarks</th>
                    <th>Created At</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($grblock as $block)
                    <tr>
                        {{-- <td>{{ $block->id }}</td> --}}                        
                        <td>{{ $block->project_name ?? 'N/A' }}</td>
                        <td>{{ $block->grblockplots_count }}</td>
                        {{-- <td>{{ $block->remarks }}</td> --}}
                        {{-- <td>{{ $block->created_at }}</td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection




