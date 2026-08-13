@extends('layout')

@section('content')
<div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Project : {{ $project_name }}</h2> <br>
            <h2>Block : {{ $block_name }}</h2>
            
            
            {{-- <a href="{{ route('plots.create') }}" class="btn btn-primary">+ Add New Plot</a> --}}
            <a href="" class="btn btn-primary">+ Add New Plot</a>
        </div>
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Filter Section --}}
    {{-- <form method="GET" action="{{ route('plots.index') }}" id="filterForm">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Project</label>
                <select name="project_id" id="projectFilter" class="form-control">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $project_id ? 'selected' : '' }}>
                            {{ $p->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Block</label>
                <select name="block_id" id="blockFilter" class="form-control">
                    <option value="">All Blocks</option>
                    @foreach($blocks as $b)
                        <option value="{{ $b->id }}" {{ $b->id == $block_id ? 'selected' : '' }}>
                            {{ $b->block_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Street</label>
                <select name="street_id" id="streetFilter" class="form-control">
                    <option value="">All Streets</option>
                    @foreach($streets as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $street_id ? 'selected' : '' }}>
                            {{ $s->street_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form> --}}


    <table class="table table-bordered">
        <thead>
            <tr>
                {{-- <th>Project</th> --}}
                {{-- <th>Block</th> --}}
                <th>Street</th>
                <th>Plot No</th>
                <th>Size</th>
                <th>Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody> 
            @forelse($plots as $p)
            <tr id="plot-row-{{ $p->id }}">
                {{-- <td class="col-project">project: {{ $p->project->project_name ?? '-' }}</td> --}}
                {{-- <td class="col-block">block: {{ $p->block->block_name ?? '-' }}</td> --}}
                <td class="col-street">{{ $p->street->street_name ?? '-' }}</td>
                <td class="col-plot-number">{{ $p->plot_number }}</td>
                <td class="col-size">size: {{ $p->psize->title ?? '-' }}</td>
                <td class="col-remarks">{{ $p->remarks }}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $p->id }}">Edit</button>
                    <form action="{{ route('plots.destroy', $p->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this plot?')">Delete</button>
                    </form>
                </td>
            </tr>

            {{-- <tr id="row_{{ $p->id }}">
                <td>{{ $p->project->project_name ?? '—' }}</td>
                <td>{{ $p->block->block_name ?? '—' }}</td>
                <td>{{ $p->street->street_name ?? '—' }}</td>
                <td>{{ $p->plot_number }}</td>
                <td>{{ $p->size }}</td>
                <td>
                    {{-- <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $p->id }}">Edit</button> --}}
                {{-- </td> --}} 
            {{-- </tr> --}}
        @empty
            <tr><td colspan="9" class="text-center text-muted">No plots found.</td></tr>
        @endforelse
        </tbody>
    </table>
        {{-- {{ $plots->links() }} --}}
    {{-- test for live edit --Start--}}
    <!-- Edit Plot Modal -->
    <div class="modal fade" id="editPlotModal" tabindex="-1" aria-labelledby="editPlotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="editPlotModalLabel">Edit Plot</h5>
            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="editPlotForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_plot_id">

            <div class="row mb-3">
                <div class="col-md-4">
                <label>Project</label>
                <select id="edit_project_id" name="project_id" class="form-select" required>
                    <option value="">Select Project</option>
                    {{-- @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach --}}
                </select>
                </div>

                <div class="col-md-4">
                <label>Block</label>
                <select id="edit_block_id" name="block_id" class="form-select" required></select>
                </div>

                <div class="col-md-4">
                <label>Street</label>
                <select id="edit_street_id" name="street_id" class="form-select"></select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                <label>Plot Number</label>
                <input type="text" id="edit_plot_number" name="plot_number" class="form-control" required>
                </div>
                <div class="col-md-4">
                <label>Size</label>
                <input type="text" id="edit_size" name="size" class="form-control">
                </div>
                <div class="col-md-4">
                <label>Numbering Type</label>
                <select id="edit_numbering_type" name="numbering_type" class="form-select" required>
                    <option value="blockwise">Blockwise</option>
                    <option value="streetwise">Streetwise</option>
                </select>
                </div>
            </div>

            <div class="mb-3">
                <label>Remarks</label>
                <textarea id="edit_remarks" name="remarks" class="form-control"></textarea>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
            </form>
        </div>
        </div>
    </div>
    </div>

    {{-- test for live edit --End--}}

</div>

<!-- 🟦 Edit Modal -->
{{-- <div class="modal fade" id="editPlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Plot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editPlotForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_plot_id">

                    <div class="mb-3">
                        <label>Project</label>
                        <select id="edit_project_id" class="form-control" name="project_id">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Block</label>
                        <select id="edit_block_id" class="form-control" name="block_id"></select>
                    </div>

                    <div class="mb-3">
                        <label>Street</label>
                        <select id="edit_street_id" class="form-control" name="street_id"></select>
                    </div>

                    <div class="mb-3">
                        <label>Plot No</label>
                        <input type="text" id="edit_plot_number" class="form-control" name="plot_number">
                    </div>

                    <div class="mb-3">
                        <label>Size</label>
                        <input type="text" id="edit_size" class="form-control" name="size">
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea id="edit_remarks" class="form-control" name="remarks"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Numbering Type</label>
                        <select id="edit_numbering_type" class="form-control" name="numbering_type">
                            <option value="blockwise">Blockwise</option>
                            <option value="streetwise">Streetwise</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Update Plot</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}
@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>
// for filter copy from other page -- start --    

</script>
@endsection
