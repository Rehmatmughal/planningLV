@extends('layout')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Project</h4>
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">
            ← Back to Projects
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Project Name --}}
                <div class="mb-3">
                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="project_name"
                           class="form-control"
                           value="{{ old('name', $project->project_name) }}"
                           required>
                </div>

                {{-- Project Code --}}
                {{-- <div class="mb-3">
                    <label class="form-label">Project Code</label>
                    <input type="text"
                           name="code"
                           class="form-control"
                           value="{{ old('code', $project->code) }}">
                </div> --}}

                {{-- Location --}}
                {{-- <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text"
                           name="location"
                           class="form-control"
                           value="{{ old('location', $project->location) }}">
                </div> --}}

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">Remarks / Description</label>
                    <textarea name="project_remarks"
                              class="form-control"
                              rows="3">{{ old('remarks', $project->project_remarks) }}</textarea>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label">Status (not in use)</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ old('status', $project->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Update Project
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
