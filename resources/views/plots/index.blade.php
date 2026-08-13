@extends('app')

@section('content')
<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- <h4 class="fw-bold">Plots Management</h4> --}}
{{-- new heading start --}}
        <div>
            <h4 class="fw-bold mb-0">
                @if($currentStreet)
                    🏘️ Plots in Street -
                    <span class="text-primary">
                        {{ $currentStreet->street_name }}
                    </span>
                @else
                    Plots Management
                @endif
                    @can('plot.delete')
                    <a href="{{ route('plots.deleted') }}"
                    class="btn btn-danger btn-sm">
                        Deleted Plots
                    </a>
                    @endcan

            </h4>

            @if($currentStreet)
                <small class="text-muted">
                    Block:
                    {{ $currentStreet->block->block_name ?? '-' }}

                    |

                    Project:
                    {{ $currentStreet->project->project_name ?? '-' }}
                </small>
            @endif
            {{-- @can('plot.deleted')
            <a href="{{ route('plots.deleted') }}"
            class="btn btn-danger btn-sm">
                Deleted Plots
            </a>
            @endcan --}}
        </div>
        {{-- new heading end --}}
        @can('plot.create')
        <a href="{{ route('plots.create') }}" class="btn btn-primary btn-sm">Add New Plot</a>
        @endcan
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
 
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🔍 Filter Section --}}
    {{-- new filter section --}}

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('plots.index') }}" id="filterForm">
                <div class="row mb-3">

                    {{-- Project --}}
                    <div class="col-md-2">
                        <label>Project</label>
                        <select name="project_id" id="projectFilter" class="form-control">
                            <option value="">All</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Block --}}
                    <div class="col-md-2">
                        <label>Block</label>
                        <select name="block_id" id="blockFilter" class="form-control">
                            <option value="">All</option>
                            @foreach($blocks as $b)
                                <option value="{{ $b->id }}" {{ request('block_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->block_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Street --}}
                    <div class="col-md-2">
                        <label>Street</label>
                        <select name="street_id" id="streetFilter" class="form-control">
                            <option value="">All</option>
                            @foreach($streets as $s)
                                <option value="{{ $s->id }}" {{ request('street_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->street_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Size --}}
                    <div class="col-md-2">
                        <label>Size</label>
                        <select name="size_id" class="form-control">
                            <option value="">All</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>
                                    {{ $size->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Plot No --}}
                    <div class="col-md-2">
                        <label>Plot No</label>
                        <input type="text" name="plot_no" value="{{ request('plot_no') }}" class="form-control" placeholder="e.g. 123">
                    </div>

                    {{-- Universal Search --}}
                    <div class="col-md-2">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Block / Street / Plot / Size">
                    </div>

                    <div class="col-md-12 mt-2 d-flex gap-2">
                        <button class="btn btn-primary btn-sm">Apply Filter</button>
                        <a href="{{ route('plots.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    {{-- <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th>Plot No</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        {{-- <th style="width:100px">Block - Plot</th> --}}
                        <th width="2%">Block-Plot</th>                        
                        <th width="3%">Street</th>
                        <th width="4%">Size</th>
                        <th width="5%">Category</th>
                        @if(auth()->user()->canany(['lop.view','lop.create','lop.update']))
                        <th width="5%">LOP</th>
                        @endif
                        @if(auth()->user()->canany(['development.create','development.view','development.update','development.edit']))
                        <th width="20%">Dev</th>
                        @endcan
                        @can('area.view')
                        {{-- @if(auth()->user()->can('area.view')) --}}
                        <th width="5%">Area</th>
                        {{-- @endif --}}
                        @endcan
                        <th width="5%">Remarks</th>
                        @can('areavariation.create')
                        <th width="4%">Add Areavariation</th>
                        @endcan
                        @if(auth()->user()->can('plot.view') || auth()->user()->can('plot.edit') || auth()->user()->can('plot.delete'))
                        {{-- @if(auth()->user()->canany(['block.view', 'block.edit', 'block.delete']))
                            <th width="25%">Action</th>
                        @endif --}}
                        <th width="20%">Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @php
                        // derive first two projects for coloring (if available)
                        $firstProjectId = $projects->first()->id ?? null;
                        $secondProjectId = $projects->skip(1)->first()->id ?? null;
                    @endphp

                    @foreach($plots as $plot)
                        @php
                            // row class by project
                            $rowClass = '';
                            if($plot->project_id == $firstProjectId) $rowClass = 'table-primary'; // light blue
                            elseif($plot->project_id == $secondProjectId) $rowClass = 'table-secondary'; // light brown/gray
                        @endphp

                        <tr id="plot-row-{{ $plot->id }}" class="{{ $rowClass }}">
                            {{-- Block-PlotNo --}}
                            <td>
                                {{ $plot->block->block_name ?? '-' }}-{{ $plot->plot_number }}
                            </td>
 
                            {{-- Street --}}
                            <td>{{ $plot->street->street_name ?? '-' }}</td>
 
                            {{-- Size (from plotsizes relation) --}}
                            {{-- <td>{{ $plot->plotSize->title ?? '-' }}</td> --}}
                            <td>{{ $plot->size->title ?? '-' }}</td>
 
                            {{-- Category --}}
                            <td>{{ $plot->category->category_title ?? '-' }} -  {{ $plot->pid_lv ?? '-' }}</td>
 
                            {{-- LOP column --}}

                            @if(auth()->user()->canany(['lop.view','lop.edit']))
                            <td class="align-middle">
                                @if(auth()->user()->can('lop.view'))
                                @php $lop = $plot->lopStatus->lop_status ?? null; @endphp
 
                                @if($lop == 'lop')
                                    <span class="badge bg-success">LOP</span>
                                {{-- @elseif($lop == 'mortgaged')
                                    <span class="badge" style="background:#6f42c1;color:#fff">Mortgaged</span> --}}
                                @elseif($lop == 'non_lop')
                                    <span class="badge bg-danger">Non-LOP</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                                @endif 
                                @if(auth()->user()->can('lop.edit'))
                                <button class="btn btn-sm btn-outline-primary ms-2 btn-lop" data-id="{{ $plot->id }}" title="Update LOP">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @endif
                            </td>
                            @endif

                            {{-- Development column: Road + Sewer --}}

                            @if(auth()->user()->canany(['development.view','development.create','development.update']))
                            <td class="align-middle">
                                @if(auth()->user()->can('development.view'))
                                @php
                                    $dev = $plot->developmentStatus ?? null;
                                    $roadStatus = $dev->asphalt_tst ?? null; // 'yes' / 'no' per migration (but you asked complete/not complete)
                                    $sewerStatus = $dev->sewer_manholes ?? null;
                                @endphp

                                {{-- Road badge (blue) --}}
                                @if($roadStatus == 'yes' || $roadStatus == 'complete')
                                    <div><span class="badge" style="background:#0d6efd;color:#fff">Road: Complete</span></div>
                                @elseif($roadStatus)
                                    <div><span class="badge bg-light text-dark">Road: Not Complete</span></div>
                                @else
                                    <div><span class="text-muted">Road: -</span></div>
                                @endif

                                {{-- Sewer badge (brown) --}}
                                @if($sewerStatus == 'constructed')
                                    <div class="mt-1"><span class="badge" style="background:#795548;color:#fff">Sewer: Constructed</span></div>
                                @elseif($sewerStatus)
                                    <div class="mt-1"><span class="badge bg-light text-dark">Sewer: Not Constructed</span></div>
                                @else
                                    <div class="mt-1"><span class="text-muted">Sewer: -</span></div>
                                @endif
                                @endif

                                {{-- @if(auth()->user()->canany(['development.create','development.update','development.edit'])) --}}

                                @can('development.edit')
                                <button class="btn btn-sm btn-outline-secondary mt-1 btn-dev" data-id="{{ $plot->id }}" title="Update Development">
                                    <i class="bi bi-tools"></i>
                                </button>
                                @endcan
                                {{-- @endif --}}
                            </td>
                            @endif

                            {{-- Area: latest measured_area from latestAreavariation --}}
                            @can('area.view')
                            <td class="align-middle">
                                @php $latest = $plot->latestAreavariation ?? null; @endphp
                                <div id="area-value-{{ $plot->id }}">
                                    {{ $latest ? number_format($latest->measured_area, 2) : number_format(0,2) }}
                                </div>

                                {{-- <button class="btn btn-sm btn-warning mt-1 btn-area" data-id="{{ $plot->id }}" title="Add Area Variation">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button> --}}
                            </td>
                            @endcan

                            {{-- Remarks --}}
                            <td>{{ $plot->remarks ?? '-' }}</td>
                            {{-- add area-variation --}}
                            {{-- new add variation --}}
                            @can('areavariation.create')
                            <td><a href="{{ route('area_variations.createnew', $plot->id) }}"
                            class="btn btn-sm btn-warning">
                            Add Variation
                            </a></td>
                            @endcan
                            
                            @if(auth()->user()->canany(['plot.view','plot.edit','plot.delete']))
                            {{-- @if(auth()->user()->can('plot.view') || auth()->user()->can('plot.edit') || auth()->user()->can('plot.delete')) --}}
                            <td class="align-middle">
                                @can('plot.view')
                                <a href="{{ route('plots.show', $plot->id) }}" class="btn btn-sm btn-info mb-1">View</a>
                                @endcan

                                @can('plot.edit')
                                <a href="{{ route('plots.edit', $plot->id) }}" class="btn btn-sm btn-primary mb-1">Edit</a>
                                @endcan

                                {{-- <button class="btn btn-sm btn-primary mb-1 btn-edit" data-id="{{ $plot->id }}">Edit</button> --}}

                                @can('plot.delete')
                                <form action="{{ route('plots.destroy', $plot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this plot?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger mb-1">Delete</button>
                                </form>
                                @endcan
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="p-3">
                {{ $plots->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}

{{-- 1) LOP modal --}}
<div class="modal fade" id="lopModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="lopForm">
        @csrf
        <input type="hidden" id="lop_plot_id" name="plot_id">
        <div class="modal-header">
            <h5 class="modal-title">Update LOP Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <label class="form-label">LOP Status</label>
            <select name="lop_status" id="lop_status" class="form-select" required>
                <option value="">Choose</option>
                <option value="lop">LOP</option>
                <option value="non_lop">Non-LOP</option>
                {{-- <option value="mortgaged">Mortgaged</option> --}}
            </select>

            <div class="mt-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" id="lop_remarks" class="form-control" rows="2"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- 2) Development modal --}}
<div class="modal fade" id="devModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="devForm">
        @csrf
        <input type="hidden" id="dev_plot_id" name="plot_id">
        <div class="modal-header">
            <h5 class="modal-title">Update Development Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <label class="form-label">Road</label>
            <select name="asphalt_tst" id="dev_road" class="form-select" required>
                <option value="">Choose</option>
                <option value="complete">Complete</option>
                <option value="not_complete">Not Complete</option>
            </select>

            <div class="mt-3">
                <label class="form-label">Sewerage Manholes</label>
                <select name="sewer_manholes" id="dev_sewer" class="form-select" required>
                    <option value="">Choose</option>
                    <option value="complete">Constructed</option>
                    <option value="not_complete">Not Constructed</option>
                </select>
            </div>

            <div class="mt-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" id="dev_remarks" class="form-control" rows="2"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- 3) Area Variation modal --}}
{{-- <div class="modal fade" id="areaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="areaForm">
        @csrf
        <input type="hidden" id="area_plot_id" name="plot_id">
        <div class="modal-header">
            <h5 class="modal-title">Add Area Variation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <label class="form-label">Measured Area</label>
            <input type="number" step="0.01" name="measured_area" id="measured_area" class="form-control" required>

            <div class="mt-2">
                <label class="form-label">Measured By</label>
                <input type="text" name="measured_by" id="measured_by" class="form-control">
            </div>

            <div class="mt-2">
                <label class="form-label">Measured Date</label>
                <input type="date" name="measured_date" id="measured_date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>

            <div class="mt-2">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" id="area_remarks" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-text mt-2 text-muted">
                If no previous area exists the row will show 0.00 — you can add the first measurement here.
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-warning" type="submit">Add Area</button>
        </div>
      </form>
    </div>
  </div>
</div> --}}

@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>
// for filter copy from other page -- start --    
$(function() {
    // 🟢 Project change => load blocks
    $('#projectFilter').on('change', function() {
        let projectID = $(this).val();
        $('#blockFilter').html('<option value="">Loading...</option>');
        $('#streetFilter').html('<option value="">All Streets</option>');
        if (projectID) {
            $.get('/get-blocks/' + projectID, function(data) {
                $('#blockFilter').html('<option value="">All Blocks</option>');
                $.each(data, function(_, block) {
                    $('#blockFilter').append('<option value="'+block.id+'">'+block.block_name+'</option>');
                });
            });
        } else {
            $('#blockFilter').html('<option value="">All Blocks</option>');
        }
    });

    // 🟠 Block change => load streets
    $('#blockFilter').on('change', function() {
        let blockID = $(this).val();
        $('#streetFilter').html('<option value="">Loading...</option>');
        if (blockID) {
            $.get('/get-streets/' + blockID, function(data) {
                $('#streetFilter').html('<option value="">All Streets</option>');
                $.each(data, function(_, street) {
                    $('#streetFilter').append('<option value="'+street.id+'">'+street.street_name+'</option>');
                });
            });
        } else {
            $('#streetFilter').html('<option value="">All Streets</option>');
        }
    });
});

document.addEventListener('DOMContentLoaded', function(){

    // Setup global CSRF header for fetch
    const csrfToken = '{{ csrf_token() }}';

    // Open LOP modal
    document.querySelectorAll('.btn-lop').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            // populate modal with current values via AJAX
            fetch(`/admin/plots/${id}/lop`, { headers: {'X-CSRF-TOKEN': csrfToken} })
                .then(r => r.json())
                .then(data => {
                    console.log('LOP DATA:', data);
                    document.getElementById('lop_plot_id').value = id;
                    document.getElementById('lop_status').value = data.lop_status ?? '';
                    document.getElementById('lop_remarks').value = data.remarks ?? '';
                    new bootstrap.Modal(document.getElementById('lopModal')).show();
                // }).catch(()=> {
                //     // if endpoint not available, still show empty modal
                //     document.getElementById('lop_plot_id').value = id;
                //     document.getElementById('lop_status').value = '';
                //     document.getElementById('lop_remarks').value = '';
                //     new bootstrap.Modal(document.getElementById('lopModal')).show();
                });
        });
    });

    // Submit LOP form
    document.getElementById('lopForm').addEventListener('submit', function(e){
        e.preventDefault();
        let id = document.getElementById('lop_plot_id').value;
        let formData = new FormData(this);

        fetch(`/admin/plots/${id}/lop`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            // update LOP badge in row
            const row = document.getElementById('plot-row-'+id);
            const lopCell = row.querySelector('td:nth-child(5)');
            lopCell.innerHTML = ''; // clear then set
            if(res.lop_status === 'lop') lopCell.innerHTML = '<span class="badge bg-success">LOP</span>';
            else if(res.lop_status === 'mortgaged') lopCell.innerHTML = '<span class="badge" style="background:#6f42c1;color:#fff">Mortgaged</span>';
            else if(res.lop_status === 'non_lop') lopCell.innerHTML = '<span class="badge bg-danger">Non-LOP</span>';
            lopCell.innerHTML += ' <button class="btn btn-sm btn-outline-primary ms-2 btn-lop" data-id="'+id+'"><i class="bi bi-pencil-square"></i></button>';
            bootstrap.Modal.getInstance(document.getElementById('lopModal')).hide();
            attachDynamicButtons(); // reattach handlers
        })
        .catch(err => {
            alert('Error saving LOP');
            console.error(err);
        });

    });

    // Open Dev modal
    document.querySelectorAll('.btn-dev').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            fetch(`/admin/plots/${id}/development`, { headers: {'X-CSRF-TOKEN': csrfToken} })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('dev_plot_id').value = id;
                    // note: migration has asphalt_tst ('yes'/'no') + sewer_manholes ('constructed'/'not_constructed')
                    // we map these server-side. Frontend uses 'complete'/'not_complete' for road, so controllers should accept both.
                    document.getElementById('dev_road').value = data.asphalt_tst == 'yes' || data.asphalt_tst == 'complete' ? 'complete' : (data.asphalt_tst == 'no' || data.asphalt_tst == 'Not Complete' ? 'Not Complete' : '');
                    // document.getElementById('dev_sewer').value = data.sewer_manholes ?? '';
                    // document.getElementById('dev_sewer').value = data.sewer_manholes == 'Constructed' || data.sewer_manholes == 'Constructed' ? 'Constructed' : (data.sewer_manholes == 'not_constructed' || data.sewer_manholes == 'Not Constructed' ? 'not constructed' : '');
                    document.getElementById('dev_road').value =
                        data.asphalt_tst == 'yes' ? 'complete' :
                        (data.asphalt_tst == 'no' ? 'not_complete' : '');

                    document.getElementById('dev_sewer').value =
                        data.sewer_manholes == 'constructed' ? 'complete' :
                        (data.sewer_manholes == 'not_constructed' ? 'not_complete' : '');

                    document.getElementById('dev_remarks').value = data.remarks ?? '';
                    new bootstrap.Modal(document.getElementById('devModal')).show();
                // }).catch(()=> {
                //     document.getElementById('dev_plot_id').value = id;
                //     document.getElementById('dev_road').value = '';
                //     document.getElementById('dev_sewer').value = '';
                //     document.getElementById('dev_remarks').value = '';
                //     new bootstrap.Modal(document.getElementById('devModal')).show();
                });
        });
    });

    // Submit Dev form
    document.getElementById('devForm').addEventListener('submit', function(e){
        e.preventDefault();
        let id = document.getElementById('dev_plot_id').value;
        let formData = new FormData(this);

        fetch(`/admin/plots/${id}/development`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            // update dev cell (road + sewer)
            const row = document.getElementById('plot-row-'+id);
            const devCell = row.querySelector('td:nth-child(6)');
            let html = '';
            if(res.asphalt_tst === 'yes' || res.asphalt_tst === 'complete') html += '<div><span class="badge" style="background:#0d6efd;color:#fff">Road: Complete</span></div>';
            else if(res.asphalt_tst) html += '<div><span class="badge bg-light text-dark">Road: Not Complete</span></div>';
            else html += '<div><span class="text-muted">Road: -</span></div>';

            if(res.sewer_manholes === 'constructed') html += '<div class="mt-1"><span class="badge" style="background:#795548;color:#fff">Sewer: Constructed</span></div>';
            else if(res.sewer_manholes) html += '<div class="mt-1"><span class="badge bg-light text-dark">Sewer: Not Constructed</span></div>';
            else html += '<div class="mt-1"><span class="text-muted">Sewer: -</span></div>';

            html += ' <button class="btn btn-sm btn-outline-secondary mt-1 btn-dev" data-id="'+id+'"><i class="bi bi-tools"></i></button>';
            devCell.innerHTML = html;
            bootstrap.Modal.getInstance(document.getElementById('devModal')).hide();
            attachDynamicButtons();
        })
        .catch(err => {
            alert('Error saving development');
            console.error(err);
        });

        
    });

    // Open Area modal
    document.querySelectorAll('.btn-area').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            document.getElementById('area_plot_id').value = id;
            document.getElementById('measured_area').value = '';
            document.getElementById('measured_by').value = '';
            document.getElementById('measured_date').value = new Date().toISOString().slice(0,10);
            document.getElementById('area_remarks').value = '';
            new bootstrap.Modal(document.getElementById('areaModal')).show();
        });
    });

    // Submit Area form
    document.getElementById('areaForm').addEventListener('submit', function(e){
        e.preventDefault();
        let id = document.getElementById('area_plot_id').value;
        let formData = new FormData(this);

        fetch(`/admin/plots/${id}/area-variations`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            body: formData
        })
        .then(r => r.json())
        
        // old but not working
        .then(res => {
            // update area cell
            const areaElem = document.getElementById('area-value-'+id);
            areaElem.innerText = parseFloat(res.measured_area).toFixed(2);
            bootstrap.Modal.getInstance(document.getElementById('areaModal')).hide();
        })
        .catch(err => {
            alert('Error saving area variation');
            console.error(err);
        });

    });

    // helper to reattach handlers for dynamically replaced buttons
    function attachDynamicButtons(){
        // reattach lop buttons
        // document.querySelectorAll('.btn-lop').forEach(btn => {
        //     btn.onclick = function(){
        //         const id = this.dataset.id;
        //         console.log('LOP DATA:', data);
        //         document.getElementById('lop_plot_id').value = id;
        //         document.getElementById('lop_status').value = data.lop_status ?? '';
        //         document.getElementById('lop_remarks').value = data.remarks ?? '';
        //         new bootstrap.Modal(document.getElementById('lopModal')).show();
        //         // document.getElementById('lop_plot_id').value = id;
        //         // document.getElementById('lop_status').value = '';
        //         // document.getElementById('lop_remarks').value = '';
        //         // new bootstrap.Modal(document.getElementById('lopModal')).show();
        //     };
        // });
        document.querySelectorAll('.btn-lop').forEach(btn => {
            btn.onclick = function(){
                const id = this.dataset.id;

                fetch(`/admin/plots/${id}/lop`, {
                    headers: {'X-CSRF-TOKEN': csrfToken}
                })
                .then(r => r.json())
                .then(data => {

                    console.log('LOP DATA:', data);

                    document.getElementById('lop_plot_id').value = id;
                    document.getElementById('lop_status').value = data.lop_status ?? '';
                    document.getElementById('lop_remarks').value = data.remarks ?? '';

                    new bootstrap.Modal(document.getElementById('lopModal')).show();
                });
            };
        });

        // dev buttons
        // document.querySelectorAll('.btn-dev').forEach(btn => {
        //     btn.onclick = function(){
        //         const id = this.dataset.id;
        //         document.getElementById('dev_plot_id').value = id;
        //         document.getElementById('dev_road').value = '';
        //         document.getElementById('dev_sewer').value = '';
        //         document.getElementById('dev_remarks').value = '';
        //         new bootstrap.Modal(document.getElementById('devModal')).show();
        //     };
        // });
        // dev buttons
        document.querySelectorAll('.btn-dev').forEach(btn => {
            btn.onclick = function(){
                const id = this.dataset.id;

                fetch(`/admin/plots/${id}/development`, {
                    headers: {'X-CSRF-TOKEN': csrfToken}
                })
                .then(r => r.json())
                .then(data => {

                    console.log('DEV DATA:', data);

                    document.getElementById('dev_plot_id').value = id;

                    document.getElementById('dev_road').value =
                        data.asphalt_tst == 'yes' || data.asphalt_tst == 'complete'
                            ? 'complete'
                            : (data.asphalt_tst == 'no' || data.asphalt_tst == 'not_complete'
                                ? 'not_complete'
                                : '');

                    document.getElementById('dev_sewer').value =
                        data.sewer_manholes == 'constructed' || data.sewer_manholes == 'complete'
                            ? 'complete'
                            : (data.sewer_manholes == 'not_constructed' || data.sewer_manholes == 'not_complete'
                                ? 'not_complete'
                                : '');

                    document.getElementById('dev_remarks').value = data.remarks ?? '';

                    new bootstrap.Modal(document.getElementById('devModal')).show();
                });
            };
        });
        // area buttons
        document.querySelectorAll('.btn-area').forEach(btn => {
            btn.onclick = function(){
                const id = this.dataset.id;
                document.getElementById('area_plot_id').value = id;
                document.getElementById('measured_area').value = '';
                document.getElementById('measured_by').value = '';
                document.getElementById('measured_date').value = new Date().toISOString().slice(0,10);
                document.getElementById('area_remarks').value = '';
                new bootstrap.Modal(document.getElementById('areaModal')).show();
            };
        });
    }

    attachDynamicButtons();

});
document.getElementById('devModal').addEventListener('hidden.bs.modal', function () {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style = "";
});
document.getElementById('lopModal').addEventListener('hidden.bs.modal', function () {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style = "";
});
document.getElementById('areaModal').addEventListener('hidden.bs.modal', function () {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style = "";
});

</script>
@endsection
