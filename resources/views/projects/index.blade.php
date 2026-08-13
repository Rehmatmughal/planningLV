    {{-- Sidebar --}}
    {{-- @include('partials.sidebar') --}}

{{-- @extends('layout') --}}
@extends('app')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">📁 Projects Management</h4>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            + Add New Project
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="25%">Project Name</th>
                            <th>Remarks</th>
                            <th width="15%">Created At</th>
                            @if(auth()->user()->can('project.edit') || auth()->user()->can('project.delete'))
                                <th width="20%">Action</th>
                            @endif
                            {{-- <th width="20%">Action</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($projects as $key => $project)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>

                                <td>
                                    <a href="{{ route('projects.blocks.index', $project->id) }}"
                                       class="fw-semibold text-decoration-none">
                                        {{ $project->project_name }}
                                    </a>
                                </td>

                                <td>{{ $project->project_remarks ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $project->created_at->format('d M Y') }}
                                </td>

                                @if(auth()->user()->can('project.edit') || auth()->user()->can('project.delete'))
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('project.edit')
                                        <a href="{{ route('projects.edit', $project->id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        @endcan
                                        @can('project.delete')
                                        <form action="{{ route('projects.destroy', $project->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this project?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $projects->links() }}
    </div>
</div>
@endsection
