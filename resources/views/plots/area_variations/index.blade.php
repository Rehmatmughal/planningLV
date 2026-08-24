@extends('app')

@section('content')
{{-- {{ dd($areaVariations) }} --}}

<div class="container mt-4">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">📐 Area Variations</h4>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔍 Filter Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('area_variations.index') }}"> {{--class="card card-body mb-3" --}}
                <div class="row g-3 align-items-end">

                    {{-- Date From --}}
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date') }}">
                    </div>

                    {{-- Project --}}
                    <div class="col-md-2">
                        <label class="form-label">Project</label>
                        <select name="project_id" id="project_id" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Block --}}
                    <div class="col-md-2">
                        <label class="form-label">Block</label>
                        <select name="block_id" id="block_id" class="form-select">
                            <option value="">All Blocks</option>
                            {{-- blocks AJAX se fill honge --}}
                        </select>
                    </div>

                    {{-- Project --}}
                    {{-- <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    {{-- Block --}}
                    {{-- <div class="col-md-2">
                        <label class="form-label">Block</label>
                        <select name="block_id" class="form-select">
                            <option value="">All Blocks</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}"
                                    {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->block_name }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}

                    {{-- Plot --}}
                    <div class="col-md-2">
                        <label class="form-label">Plot No</label>
                        <input type="text" name="plot_number" class="form-control"
                            value="{{ request('plot_number') }}">
                    </div>

                    {{-- Submit --}}
                    {{-- <div class="col-md-1"> --}}
                        {{-- <button class="btn btn-primary w-100">Filter</button> --}}
                            {{-- <button type="submit" class="btn btn-primary">
                                Filter
                            </button>
                            {{-- RESET BUTTON --}}
                            {{-- <a href="{{ route('area_variations.index') }}" class="btn btn-secondary">Reset</a> --}}
                    {{-- </div> --}} 
                    {{-- Buttons --}}
                    <div class="col-md-2 mt-3 d-flex gap-3">
                        <button class="btn btn-primary btn-sm">Apply Filter</button>
                        <a href="{{ route('area_variations.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light]">
                        <tr class="text-center">
                            <th width="5%" >#</th>
                            <th width="5%" >Project</th>
                            <th width="5%" >Block-Plot</th>
                            <th width="5%" >Street</th>
                            {{-- <th width="5%" >Plot No</th> --}}
                            <th width="5%" >Size</th>
                            <th width="5%" >Sewer</th>
                            <th width="5%" >Road</th>
                            <th width="5%" >LOP</th>
                            <th width="5%" >Mortgage</th>
                            <th width="5%" >Possession</th>
                            <th width="8%">Workflow</th>
                            <th width="5%" >Measured Area</th>
                            <th width="5%" >Measured Date</th>
                            <th width="18%" >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($areaVariations as $av)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}-{{ $av->id }}</td>
                            <td>{{ $av->plot->project->project_name ?? '-' }}</td>
                            <td>{{ $av->plot->block->block_name ?? '-' }}-{{ $av->plot->plot_number ?? 'n/a' }}</td>
                            <td>{{ $av->plot->street->street_name ?? '-' }}</td>
                            {{-- <td>{{ $av->plot->plot_number ?? 'n/a' }}</td> --}}
                            <td>{{ $av->plot->size->title ?? '-' }}</td>
                            {{-- <td>{{ $av->plot->plotsize->title ?? '-' }}</td> --}}
                            <td>{{ ucfirst(str_replace('_',' ', $av->sewer_status_at_time)) }}</td>
                            {{-- <td>{{ $av->plot->developmentStatus->sewer_manholes ?? '-' }}</td> --}}
                            <td>{{ ucfirst(str_replace('_',' ', $av->road_status_at_time)) }}</td>
                            {{-- <td>{{ $av->plot->developmentStatus->asphalt_tst ?? '-' }}</td> --}}
                            <td>{{ strtoupper($av->lop_status_at_time) }}</td>
                            {{-- <td>{{ $av->plot->lopStatus->lop_status ?? '-' }}</td> --}}
                            <td>{{ $av->plot->mortgageStatus->is_mortgaged ?? '-' }}</td>
                            <td>{{ $av->plot->possessionStatus->possession_status ?? '-' }}</td>
                            <td class="text-center">
                                {{-- @if($av->workflow_status === 'pending') --}}
                                @if($av->workflow_status === 1)
                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>
                                {{-- @elseif($av->workflow_status === 'ready_for_print') --}}
                                @elseif($av->workflow_status === 2)                                
                                    <span class="badge bg-info text-dark">
                                        Ready for Print
                                    </span>
                                {{-- @elseif($av->workflow_status === 'printed') --}}
                                @elseif($av->workflow_status === 3)
                                    <span class="badge bg-success">
                                        Printed
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td>{{ $av->measured_area }}</td>
                            <td>{{ $av->measured_date ?? $av->created_at->format('d-M-Y') }}</td>
                           
                            <td style="white-space:nowrap;">
                                <a href="{{ route('plots.show', $av->plot->id) }}" class="btn btn-sm btn-info mb-1">View Plot</a>

                                <!-- Edit button opens modal and passes data-* -->
                                {{-- <button class="btn btn-sm btn-warning mb-1 edit-av-btn"
                                    data-id="{{ $av->id }}"
                                    data-plot-id="{{ $av->plot->id }}"
                                    data-plot-size="{{ $av->plot->plotsize->title }}"
                                    data-measured-area="{{ $av->measured_area }}"
                                    data-measured-by="{{ $av->measured_by }}"
                                    data-measured-date="{{ $av->measured_date }}"
                                    data-remarks="{{ $av->remarks }}"
                                    data-sewer="{{ $av->plot->developmentStatus->sewer_manholes ?? '' }}"
                                    data-road="{{ $av->plot->developmentStatus->asphalt_tst ?? '' }}"
                                    data-overall="{{ $av->plot->developmentStatus->overall_status ?? '' }}"
                                    data-lop="{{ $av->plot->lopStatus->lop_status ?? '' }}"
                                    data-mortgage="{{ $av->plot->mortgageStatus->is_mortgaged ?? '' }}"
                                    data-possession="{{ $av->plot->possessionStatus->possession_status ?? '' }}"
                                >Edit</button> --}}

                                <a href="{{ route('area_variations.edit', $av->id) }}" class="btn btn-sm btn-warning mb-1">Edit2</a>
                                <a href="{{ route('area_variations.print', $av->id) }}" class="btn btn-sm btn-primary mb-1">Print</a>

                                <form action="{{ route('area_variations.destroy', $av->id) }}" method="POST" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this measurement?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        {{-- <div class="p-3">
            {{ $areaVariations->links() }}
        </div> --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $areaVariations->links() }}
        </div>

    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAVModal" tabindex="-1" aria-labelledby="editAVModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editAVForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Area Measurement & Statuses</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="plot_id" id="modal_plot_id">
              <div class="row g-2">
                  <div class="col-md-4">
                      <label>Plot Size (nominal)</label>
                      <input type="text" id="modal_plot_size" class="form-control" readonly>
                  </div>
                  <div class="col-md-4">
                      <label>Measured Area</label>
                      <input type="number" step="0.01" name="measured_area" id="modal_measured_area" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                      <label>Difference</label>
                      <input type="text" id="modal_difference" class="form-control" readonly>
                  </div>

                  <div class="col-md-4">
                      <label>Measured By</label>
                      <input type="text" name="measured_by" id="modal_measured_by" class="form-control">
                  </div>

                  <div class="col-md-4">
                      <label>Measured Date</label>
                      <input type="date" name="measured_date" id="modal_measured_date" class="form-control">
                  </div>

                  <div class="col-md-4">
                      <label>Remarks</label>
                      <input type="text" name="remarks" id="modal_remarks" class="form-control">
                  </div>
              </div>

              <hr>
              <h6>Update Related Statuses (optional)</h6>

              <div class="row g-2">
                  <div class="col-md-4">
                      <label>Sewer Manholes</label>
                      <select name="sewer_manholes" id="modal_sewer" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="constructed">Constructed</option>
                          <option value="not_constructed">Not Constructed</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label>Asphalt / Road</label>
                      <select name="asphalt_tst" id="modal_road" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label>Overall Dev Status</label>
                      <select name="overall_status" id="modal_overall" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="developed">Developed</option>
                          <option value="under_development">Under Development</option>
                          <option value="not_developed">Not Developed</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label>LOP Status</label>
                      <select name="lop_status" id="modal_lop" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="lop">LOP</option>
                          <option value="non_lop">Non-LOP</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label>Mortgage</label>
                      <select name="is_mortgaged" id="modal_mortgage" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label>Possession Status</label>
                      <select name="possession_status" id="modal_possession" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="possessionable">Possessionable</option>
                          <option value="non_lop_possessionable">Non-LOP Possessionable</option>
                          <option value="under_development_possessionable">Under Development Possessionable</option>
                          <option value="not_possessionable">Not Possessionable</option>
                      </select>
                  </div>
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-av-btn');
    const modal = new bootstrap.Modal(document.getElementById('editAVModal'));
    const form = document.getElementById('editAVForm');

    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const plotId = btn.dataset.plotId;
            const plotSize = btn.dataset.plotSize || '';
            const measuredArea = btn.dataset.measuredArea || '';
            const measuredBy = btn.dataset.measuredBy || '';
            const measuredDate = btn.dataset.measuredDate || '';
            const remarks = btn.dataset.remarks || '';

            // statuses
            const sewer = btn.dataset.sewer || '';
            const road = btn.dataset.road || '';
            const overall = btn.dataset.overall || '';
            const lop = btn.dataset.lop || '';
            const mortgage = btn.dataset.mortgage || '';
            const possession = btn.dataset.possession || '';

            // set form action
            form.action = `/area-variations/${id}`;

            // fill fields
            document.getElementById('modal_plot_id').value = plotId;
            document.getElementById('modal_plot_size').value = plotSize;
            document.getElementById('modal_measured_area').value = measuredArea;
            document.getElementById('modal_measured_by').value = measuredBy;
            document.getElementById('modal_measured_date').value = measuredDate ? measuredDate.substring(0,10) : '';
            document.getElementById('modal_remarks').value = remarks;

            // statuses (select)
            document.getElementById('modal_sewer').value = sewer;
            document.getElementById('modal_road').value = road;
            document.getElementById('modal_overall').value = overall;
            document.getElementById('modal_lop').value = lop;
            document.getElementById('modal_mortgage').value = mortgage;
            document.getElementById('modal_possession').value = possession;

            // calculate difference
            calculateDifference();

            modal.show();
        });
    });

    // difference calc
    const measuredEl = document.getElementById('modal_measured_area');
    const plotSizeEl = document.getElementById('modal_plot_size');
    const diffEl = document.getElementById('modal_difference');

    function calculateDifference() {
        const p = parseFloat(plotSizeEl.value) || 0;
        const m = parseFloat(measuredEl.value) || 0;
        const d = m - p;
        if (!isNaN(d)) {
            diffEl.value = d.toFixed(2);
            diffEl.style.color = d > 0 ? 'green' : (d < 0 ? 'red' : 'black');
        } else {
            diffEl.value = '';
        }
    }

    measuredEl.addEventListener('input', calculateDifference);

    // Ensure difference recalculates when modal shown (in case measured value unchanged)
    document.getElementById('editAVModal').addEventListener('shown.bs.modal', calculateDifference);
});
// filter section depended dropdown
document.addEventListener('DOMContentLoaded', function () {
    const projectSelect = document.getElementById('project_id');
    const blockSelect = document.getElementById('block_id');
    const selectedBlock = "{{ request('block_id') }}";

    function loadBlocks(projectId) {
        blockSelect.innerHTML = '<option value="">Loading...</option>';

        if (!projectId) {
            blockSelect.innerHTML = '<option value="">All Blocks</option>';
            return;
        }

        fetch(`/ajax/blocks-by-project/${projectId}`)
            .then(res => res.json())
            .then(blocks => {
                blockSelect.innerHTML = '<option value="">All Blocks</option>';

                blocks.forEach(block => {
                    const opt = document.createElement('option');
                    opt.value = block.id;
                    opt.textContent = block.block_name;

                    if (block.id == selectedBlock) {
                        opt.selected = true;
                    }

                    blockSelect.appendChild(opt);
                });
            });
    }

    // On page load (edit / filter preserve)
    if (projectSelect.value) {
        loadBlocks(projectSelect.value);
    }

    // On project change
    projectSelect.addEventListener('change', function () {
        loadBlocks(this.value);
    });
});
</script>
@endpush
