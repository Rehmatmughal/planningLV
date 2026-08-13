@extends('layout')

@section('content')
    {{-- <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Add New Block</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addBlockForm">
                    @csrf
                    <div class="mb-3">
                        <label>Project</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Block Name</label>
                        <input type="text" name="block_name" class="form-control" placeholder="Enter block name" required>
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Optional"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Block</button>
                </form>
            </div>
        </div>
    </div> --}}


{{-- NEW store method without ajax --}}
<div class="container mt-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    


    <form action="{{ route('blocks.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif --}}
        <div class="mb-3">
            <label>Project</label>
            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Block Name</label>
            <input type="text" name="block_name" class="form-control @error('block_name') is-invalid @enderror" placeholder="Enter block name">
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional"></textarea>
        </div>


        <button type="submit" class="btn btn-primary">Save Block</button>
        <a href="{{ route('blocks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>


@endsection 
