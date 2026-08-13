@extends('layout')

@section('content')

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
            <label>Project111</label>
            <select name="project_id" class="form-select" required>
                {{-- <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                @endforeach --}}
                <option value="{{ $project->id }}"
                    {{ isset($selected_project_id) && $selected_project_id == $project->id ? 'selected' : '' }}>
                    {{ $project->project_name }}
                </option>
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


        <button type="submit" class="btn btn-primary">Save Project</button>
        <a href="{{ route('blocks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>


@endsection 
