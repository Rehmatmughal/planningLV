@extends('app')

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

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif --}}
        {{-- <div class="mb-3">
            <label>Project</label>
            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                <option value="">Select Project</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                @endforeach
            </select>
        </div> --}}
        <div class="mb-3">
            <label>Category Type</label>
            <input type="text" name="category_title" class="form-control @error('category_title') is-invalid @enderror" placeholder="Enter category name">
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Category</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection
