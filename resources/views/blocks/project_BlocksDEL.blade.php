{{-- @extends('layout') --}}
@extends('app')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            🧱 Blocks –
            <span class="text-primary">{{ $project->project_name }}</span>
        </h4>

        @if(auth()->user()->can('block.excel') || auth()->user()->can('block.create'))

        <div class="d-flex gap-2">
            @can('block.excel')
            <a href="{{ route('projects.blocks.excel', $project->id) }}"
               class="btn btn-success">
                ⬇ Excel Download
            </a>
            @endcan

            @can('block.create')
            <a href="{{ route('blocks.create', ['project_id' => $project->id]) }}"
               class="btn btn-primary">
                + Add New Block
            </a>
            @endcan

        </div>
        @endif
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔍 Filter (NEW – Added, Project locked) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">

                    {{-- <div class="col-md-4">
                        <label class="form-label">Block Name</label>
                        <input type="text"
                               name="block_name"
                               value="{{ request('block_name') }}"
                               class="form-control"
                               placeholder="Search block name">
                    </div> --}}

                    <div class="col-md-4">
                        <label class="form-label">Project</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $project->project_name }}"
                               disabled> 
                    </div>

                    {{-- <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            🔍 Filter
                        </button>
                    </div> --}}

                    {{-- <div class="col-md-2">
                        <a href="{{ route('projects.blocks.index', $project->id) }}"
                           class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div> --}}

                </div>
            </form>
        </div>
    </div>

    {{-- 📋 Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th width="25%">Block Name</th>
                            <th>Remarks</th>
                            @if(auth()->user()->can('street.view') || auth()->user()->can('block.edit') || auth()->user()->can('block.delete'))
                            <th width="25%">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($blocks as $key => $block)
                        <tr>
                            <td class="text-center">
                                {{ $blocks->firstItem() + $key }}
                            </td>

                            <td class="fw-semibold">
                                {{ $block->block_name }}
                            </td>

                            <td>
                                {{ $block->remarks ?? '-' }}
                            </td>

                            @if(auth()->user()->can('street.view') || auth()->user()->can('block.edit') || auth()->user()->can('block.delete'))
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @can('block.edit')
                                    <a href="{{ route('blocks.edit', $block->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    @endcan
                                    @can('street.view')
                                    <a href="{{ route('blocks.plots.index', $block->id) }}"
                                       class="btn btn-sm btn-info">
                                        View Streets
                                    </a>
                                    @endcan
                                    @can('block.delete')
                                    {{-- <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $block->id }}">Delete</button> --}}
                                    <form action="{{ route('blocks.destroy', $block->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this Block?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mb-1">Delete</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No blocks found for this project.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Pagination (filter preserved) --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $blocks->withQueryString()->links() }}
    </div>

</div>
@endsection
