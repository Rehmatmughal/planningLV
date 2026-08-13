@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- <h3>Project : {{ $project_name }}</h3>
        <a href="{{ route('blocks.createpsproject', ['project_id' => $project_id]) }}" class="btn btn-primary">+ Add New Block</a> --}}

        <h3>Project : {{ $project_name }}</h3>

        <a href="{{ route('blocks.createpsproject', ['project_id' => $project_id]) }}" class="btn btn-primary">
            + Add New Block
        </a>


        {{-- <a href="{{ route('blocks.create') }}" class="btn btn-primary">+ Add New Block</a> --}}
           
    </div>
    <div id="alertArea" class="mt-2"></div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Block Name</th>
                <th>Remarks</th>
                <th>View Plots</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blocks as $key => $block)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $block->block_name }}</td>
                    {{-- <td>{{ $block->project->project_name ?? '-' }}</td> --}}
                    <td>{{ $block->remarks ?? '-' }}</td>
                    <td>
                        {{-- <button class="btn btn-info btn-sm view-plots-btn" data-id="{{ $block->id }}">View Plots</button> --}}
                        <a href="{{ route('getplots', $block->id) }}" class="btn btn-info btn-sm view-plots-btn">View Plots</a>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $block->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- {{ $blocks->links() }} --}}
</div>


@endsection

{{-- @push('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

@endpush --}}
