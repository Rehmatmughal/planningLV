@extends('layout')
@section('content')
<div>
    
</div>
    <h2 style="text-align: center;">Blocks List</h2>
    <div class="container">
        <a href="{{ route('addblock') }}" class="btn btn-success btn-sm mb-3">Add Block</a>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr align="center">
                    <th>ID</th>
                    <th>Block Name</th>
                    <th>Project</th>
                    <th>Remarks</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blocks as $block)
                    <tr>
                        <td align="center">{{ $block->id }}</td>
                        <td align="center">{{ $block->block_name }}</td>
                        <td align="center">{{ $block->project_name ?? 'N/A' }}</td>
                        <td>{{ $block->remarks }}</td>
                        <td>{{ $block->created_at }}</td>
                        <td><a href="" class="btn btn-danger btn-sm"> Delete </a></td>
                        {{-- <td><a href="{{ route('delete.ggblock',$block->id) }}" class="btn btn-danger btn-sm"> Delete </a></td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection




