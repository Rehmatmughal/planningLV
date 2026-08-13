@extends('layout')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0">
                🏘️ Plots in Street:
                <span class="text-primary">{{ $street->street_name }}</span>
            </h4>
            <small class="text-muted">
                Block: {{ $street->block->block_name ?? '-' }}
                | Project: {{ $street->block->project->project_name ?? '-' }}
            </small>
        </div>

        <a href="{{ route('streets.index', $street->block_id) }}"
           class="btn btn-secondary">
            ← Back
        </a>
    </div>

    {{-- 🔍 Filter (DO NOT REMOVE) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">Plot Number</label>
                        <input type="text"
                               name="plot_number"
                               value="{{ request('plot_number') }}"
                               class="form-control"
                               placeholder="Search plot no">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Block</label>
                        <select name="block_id" class="form-control">
                            <option value="">All</option>
                            <option value="{{ $street->block_id }}" selected>
                                {{ $street->block->block_name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            🔍 Filter
                        </button>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('streets.plots.index', $street->id) }}"
                           class="btn btn-outline-secondary w-100">
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
                            <th width="5%">#</th>
                            <th>Plot No</th>
                            <th>Block</th>
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
                                {{ $plot->block->block_name ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $plot->plotsize->title ?? '-' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ ucfirst($plot->status ?? 'active') }}
                                </span>
                            </td>

                            @if(@auth()->user()->can('plot.view') || auth()->user()->can('plot.edit'))
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
                                No plots found for this street.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination (Filter preserved) --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $plots->withQueryString()->links() }}
    </div>

</div>
@endsection
