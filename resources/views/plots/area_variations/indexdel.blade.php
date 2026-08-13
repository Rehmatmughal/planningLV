@extends('layout')

@section('content')
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
            <form method="GET" action="{{ route('area_variations.index') }}">
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

                    {{-- Plot --}}
                    <div class="col-md-2">
                        <label class="form-label">Plot No</label>
                        <input type="text" name="plot_number" class="form-control"
                               value="{{ request('plot_number') }}">
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary btn-sm w-100">Apply Filter</button>
                        <a href="{{ route('area_variations.index') }}"
                           class="btn btn-outline-secondary btn-sm w-100">
                            Reset
                        </a>
                    </div>

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
                            <th>#</th>
                            <th>Project</th>
                            <th>Block</th>
                            <th>Street</th>
                            <th>Plot No</th>
                            <th>Size</th>
                            <th>Sewer</th>
                            <th>Road</th>
                            <th>LOP</th>
                            <th>Mortgage</th>
                            <th>Possession</th>
                            <th>Measured Area</th>
                            <th>Measured Date</th>
                            <th width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($areaVariations as $av)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}-{{ $av->id }}</td>
                            <td>{{ $av->plot->project->project_name ?? '-' }}</td>
                            <td>{{ $av->plot->block->block_name ?? '-' }}</td>
                            <td>{{ $av->plot->street->street_name ?? '-' }}</td>
                            <td>{{ $av->plot->plot_number }}</td>
                            <td>{{ $av->plot->plotsize->title ?? '-' }}</td>
                            <td>{{ ucfirst(str_replace('_',' ', $av->sewer_status_at_time)) }}</td>
                            <td>{{ ucfirst(str_replace('_',' ', $av->road_status_at_time)) }}</td>
                            <td>{{ strtoupper($av->lop_status_at_time) }}</td>
                            <td>{{ $av->plot->mortgageStatus->is_mortgaged ?? '-' }}</td>
                            <td>{{ $av->plot->possessionStatus->possession_status ?? '-' }}</td>
                            <td>{{ $av->measured_area }}</td>
                            <td>{{ $av->measured_date ?? $av->created_at->format('d-M-Y') }}</td>
                            <td class="text-center" style="white-space:nowrap;">

                            <a href="{{ route('plots.show', $av->plot->id) }}"
                                class="btn btn-sm btn-info mb-1">
                                View Plot
                            </a>

                            <button class="btn btn-sm btn-warning mb-1 edit-av-btn"
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
                                data-possession="{{ $av->plot->possessionStatus->possession_status ?? '' }}">
                                Edit
                            </button>

                            <a href="{{ route('area_variations.edit', $av->id) }}"
                                class="btn btn-sm btn-warning mb-1">
                                Edit2
                            </a>

                            <a href="{{ route('area_variations.print', $av->id) }}"
                                class="btn btn-sm btn-primary mb-1">
                                Print
                            </a>

                            <form action="{{ route('area_variations.destroy', $av->id) }}"
                                    method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this measurement?')">
                                    Delete
                                </button>
                            </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $areaVariations->links() }}
    </div>

</div>

{{-- 🔽 MODAL & SCRIPTS SAME AS YOUR ORIGINAL --}}
@include('plots.area_variations._edit_modal') optional include if you want
@endsection
