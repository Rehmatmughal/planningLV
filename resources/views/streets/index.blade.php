{{-- @extends('layout') --}}
@extends('app')

@section('content')
<div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            {{-- <h2>List of streets</h2> --}}
            {{-- <h4 class="fw-bold mb-0">
                🛣 Streets
                @if($block)
                    -
                    <span class="text-primary">
                        {{ $block->block_name }}
                    </span>
                @endif
            </h4> --}}
            @can('street.create')
            <a href="{{ route('streets.create') }}" class="btn btn-primary">+ Add New Street</a>
            @endcan
        </div>
        {{-- new header test --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-0">
                        🛣 Streets
                        @if($block)
                            in Block -
                            <span class="text-primary">
                                {{ $block->block_name }}
                            </span>
                        @endif
                    </h4>
                    <small class="text-muted">
                        Project: {{ $block->project->project_name ?? '-' }}
                    </small>
                </div>

                @if($block_id)
                    <a href="{{ route('blocks.index', ['project_id' => $block?->project_id]) }}"
                    class="btn btn-secondary">
                        ← Back to Blocks
                    </a>
                @endif
                {{-- @if(isset($block->project_id))
                    <a href="{{ route('projects.blocks.index', $block->project_id) }}"
                    class="btn btn-secondary">
                        ← Back to Blocks
                    </a>
                @endif --}}
            </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    {{-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif --}}


<div class="card shadow-sm mb-3">
    <div class="card-body">

        <form method="GET" action="{{ route('streets.index') }}">

            <div class="row g-3 align-items-end">

                {{-- Project --}}
                <div class="col-md-4">
                    <label class="form-label">Project</label>

                    <select name="project_id"
                            id="projectFilter"
                            class="form-select">

                        <option value="">All Projects</option>

                        @foreach($projects as $p)
                            <option value="{{ $p->id }}"
                                {{ $p->id == $project_id ? 'selected' : '' }}>
                                {{ $p->project_name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Block --}}
                <div class="col-md-4">
                    <label class="form-label">Block</label>

                    <select name="block_id"
                            id="blockFilter"
                            class="form-select">
                        <option value="">All Blocks</option>

                        @foreach($blocks as $b)
                            <option value="{{ $b->id }}"
                                {{ $b->id == $block_id ? 'selected' : '' }}>
                                {{ $b->block_name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Filter --}}
                <div class="col-md-2">
                    <button type="submit"
                            class="btn btn-primary w-100">
                        🔍 Filter
                    </button>
                </div>

                {{-- Reset --}}
                <div class="col-md-2">
                    <a href="{{ route('streets.index') }}"
                       class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>

            </div>

        </form>

    </div>
</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Project</th>
                <th>Block</th>
                <th>Street</th>
                @if(auth()->user()->canany(['street.edit','street.delete','plot.view']))
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($streets as $st)
            <tr id="plot-row-{{ $st->id }}">
                <td class="col-project">
                    {{ $st->project->project_name ?? '-' }}
                </td>
                <td class="col-block">
                    {{ $st->block->block_name ?? '-' }}
                </td>
                <td class="col-street">
                    {{ $st->street_name ?? '-' }}
                </td>
                @if(auth()->user()->canany(['street.edit','street.delete', 'plots.view']))
                <td>
                    @can('street.edit')
                    <a href="{{ route('streets.edit', $st->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    @endcan 
                    @can('plot.view')
                    {{-- <a href="{{ route('streets.plots.index', $st->id) }}"
                    class="btn btn-sm btn-info">
                        View Plots
                    </a> --}}
                    <a href="{{ route('plots.index', [
                        'project_id' => $st->project_id,
                        'block_id'   => $st->block_id,
                        'street_id'  => $st->id,
                    ]) }}"
                    class="btn btn-sm btn-info">
                        View Plots
                    </a>
                    @endcan
                    {{-- <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $st->id }}">Edit</button> --}}
                    @can('street.delete')
                    <form action="{{ route('streets.destroy', $st->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this street?')">Delete</button>
                    </form>
                    @endcan
                </td>
                @endif
            </tr>


        @empty
            <tr><td colspan="9" class="text-center text-muted">No streets found.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{-- test for live edit --Start--}}
    <!-- Edit Plot Modal -->

</div>
@endsection
<!-- 🟦 update filter Modal -->
@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>
$(function() {

    // Load blocks when project changes
    $('#projectFilter').on('change', function() {
        let projectID = $(this).val();
        $('#blockFilter').html('<option value="">Loading...</option>');

        if (projectID) {
            $.get('/get-blocks/' + projectID, function(data) {
                $('#blockFilter').html('<option value="">All Blocks</option>');
                data.forEach(function(block) {
                    $('#blockFilter').append(
                        `<option value="${block.id}">${block.block_name}</option>`
                    );
                });
            });
        } else {
            $('#blockFilter').html('<option value="">All Blocks</option>');
        }
    });

});
</script>
@endsection
