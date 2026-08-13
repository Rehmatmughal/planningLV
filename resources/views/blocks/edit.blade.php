@extends('layout')

@section('content')
<div class="container mt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Edit Block</h4>
        <a href="{{ route('blocks.index') }}" class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('blocks.update', $block->id) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Project --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Project</label>
                <select name="project_id" class="form-control" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ $project->id == $block->project_id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Block Name --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Block Name</label>
                <input type="text"
                       name="block_name"
                       class="form-control"
                       value="{{ old('block_name', $block->block_name) }}"
                       required>
            </div>

            {{-- Remarks --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Remarks</label>
                <input type="text"
                       name="remarks"
                       class="form-control"
                       value="{{ old('remarks', $block->remarks) }}">
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                Update Block
            </button>
        </div>

    </form>
</div>
@endsection
