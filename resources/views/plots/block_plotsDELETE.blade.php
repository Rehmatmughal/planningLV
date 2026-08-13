{{-- @extends('layout') --}}
@extends('app')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">
                🧱 Plots in Block:
                <span class="text-primary">{{ $block->block_name }}</span>
            </h4>
            <small class="text-muted">
                Project: {{ $block->project->project_name ?? '-' }}
            </small>
        </div>

        @if(isset($block->project_id))
            <a href="{{ route('projects.blocks.index', $block->project_id) }}"
               class="btn btn-secondary">
                ← Back to Blocks
            </a>
        @endif
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔍 Filter (DO NOT REMOVE) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Street</label>
                        <select name="street_id" class="form-control">
                            <option value="">All Streets</option>
                            @foreach($block->streets as $street)
                                <option value="{{ $street->id }}"
                                    {{ request('street_id') == $street->id ? 'selected' : '' }}>
                                    {{ $street->street_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Plot Number</label>
                        <input type="text"
                               name="plot_number"
                               value="{{ request('plot_number') }}"
                               class="form-control"
                               placeholder="Search plot no">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            🔍 Filter
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('blocks.plots.index', $block->id) }}"
                           class="btn btn-outline-secondary w-100">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Export Button --}}
    @can('plot.excel')
    <div class="mb-3">
        <a href="{{ route('blocks.plots.export', array_merge(
                ['block' => $block->id],
                request()->query()
            )) }}"
           class="btn btn-success">
            ⬇ Export Excel
        </a>
    </div>
    @endcan

    {{-- 📋 Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">#</th>
                            <th>Plot No</th>
                            <th>Street</th>
                            <th>Size</th>
                            <th>Status</th>
                            @if(auth()->user()->can('plot.view') || auth()->user()->can('plot.edit'))
                            <th width="20%">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($plots as $key => $plot)
                        <tr>
                            <td class="text-center">
                                {{ $plots->firstItem() + $key }}
                            </td>

                            <td class="text-center fw-semibold">
                                {{ $plot->plot_number }}
                            </td>

                            <td class="text-center">
                                @if($plot->street)
                                    {{-- <a href="{{ route('streets.plots.index', $plot->street_id) }}"
                                       class="text-decoration-none fw-semibold">
                                        {{ $plot->street->street_name }}
                                    </a> --}}
                                    @if(auth()->user()->can('plot.view'))
                                    <a href="{{ route('streets.plots.index', $plot->street_id) }}"
                                       class="text-decoration-none fw-semibold">
                                        {{ $plot->street->street_name }}
                                    </a>
                                    @else
                                    {{ $plot->street->street_name }}
                                    @endif
                                @else
                                    -
                                @endif

                            </td>

                            <td class="text-center">
                                {{ $plot->plotsize->title ?? '-' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ ucfirst($plot->status ?? 'active') }}
                                </span>
                            </td>
                            @if(auth()->user()->can('plot.view') || auth()->user()->can('plot.edit'))
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @can('plot.view')
                                    <a href="{{ route('plots.show', $plot->id) }}"
                                       class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    @endcan

                                    @can('plot.edit')
                                    <a href="{{ route('plots.edit', $plot->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    @endcan
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No plots found for this block.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Pagination (filters preserved) --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $plots->withQueryString()->links() }}
    </div>

</div>
@endsection
