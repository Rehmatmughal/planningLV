
@extends('app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="mb-1">Possession Cases Report</h3>
            <p class="text-muted mb-0">
                Project, Block, Property Type aur Status ke mutabiq report
            </p>
        </div>

        <a href="{{ route('possession-cases.index') }}"
           class="btn btn-outline-secondary">
            Back to Possession Cases
        </a>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Report Filters</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('possession-cases.report') }}">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="project_id" class="form-label">Project</label>
                        <select name="project_id" id="project_id" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    @selected(request('project_id') == $project->id)>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="block_id" class="form-label">Block</label>
                        <select name="block_id" id="block_id" class="form-select">
                            <option value="">All Blocks</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}"
                                    @selected(request('block_id') == $block->id)>
                                    {{ $block->block_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="property_type_id" class="form-label">
                            Property Type
                        </label>
                        <select name="property_type_id"
                                id="property_type_id"
                                class="form-select">
                            <option value="">All Property Types</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}"
                                    @selected(request('property_type_id') == $type->id)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Apply Filters
                        </button>

                        <a href="{{ route('possession-cases.report') }}"
                        class="btn btn-outline-secondary">
                            Reset
                        </a>

                        <button type="submit"
                                name="export"
                                value="excel"
                                class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>

                        <button type="button"
                                class="btn btn-outline-dark ms-auto"
                                onclick="window.print()">
                            <i class="bi bi-printer"></i> Print Report
                        </button>
                    </div>


                </div>
            </form>
        </div>
    </div>

    {{-- Report Heading and Selected Filters --}}
    <div class="card shadow-sm mb-4 report-summary">
        <div class="card-body">
            <h4 class="text-center mb-2">Possession Summary Report</h4>

            <p class="text-center text-muted mb-3">
                Report Date: {{ now()->format('d-m-Y') }}
            </p>

            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Project:</strong>
                    @if(request('project_id'))
                        {{ optional($projects->firstWhere('id', request('project_id')))->project_name ?? 'Selected Project' }}
                    @else
                        All Projects
                    @endif
                </div>

                <div class="col-md-4">
                    <strong>Property Type:</strong>
                    @if(request('property_type_id'))
                        {{ optional($propertyTypes->firstWhere('id', request('property_type_id')))->name ?? 'Selected Property Type' }}
                    @else
                        All Property Types
                    @endif
                </div>

                <div class="col-md-4">
                    <strong>Block:</strong>
                    @if(request('block_id'))
                        {{ optional($blocks->firstWhere('id', request('block_id')))->block_name ?? 'Selected Block' }}
                    @else
                        All Blocks
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Four Detailed Summaries --}}
    @php
        $summarySections = [
            'residential_possessions' => 'Residential Possessions',
            'residential_repossessions' => 'Residential Re-Possessions',
            'commercial_possessions' => 'Commercial Possessions',
            'commercial_repossessions' => 'Commercial Re-Possessions',
        ];
    @endphp

    @foreach($summarySections as $sectionKey => $sectionTitle)
        @if(!empty($detailedSummaries[$sectionKey]))
            @foreach($detailedSummaries[$sectionKey] as $section)
                <div class="card shadow-sm mb-4 report-summary">
                    <div class="card-header bg-white">
                        <strong>{{ $sectionTitle }}</strong>
                        <div class="small text-muted mt-1">
                            Project: {{ $section['project_name'] }}
                            |
                            Property Type: {{ $section['property_type_name'] }}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Block</th>

                                    @foreach($section['sizes'] as $size)
                                        <th class="text-end">{{ $size }}</th>
                                    @endforeach

                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $sizeTotals = array_fill_keys(
                                        $section['sizes'],
                                        0
                                    );
                                    $grandTotal = 0;
                                @endphp

                                @forelse($section['blocks'] as $blockIndex => $block)
                                    <tr>
                                        <td>{{ $blockIndex + 1 }}</td>
                                        <td>{{ $block['block_name'] }}</td>

                                        @foreach($section['sizes'] as $size)
                                            @php
                                                $count = (int) ($block['counts'][$size] ?? 0);
                                                $sizeTotals[$size] += $count;
                                            @endphp

                                            <td class="text-end">
                                                {{ number_format($count) }}
                                            </td>
                                        @endforeach

                                        <td class="text-end fw-semibold">
                                            {{ number_format($block['total']) }}
                                        </td>
                                    </tr>

                                    @php
                                        $grandTotal += $block['total'];
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="{{ count($section['sizes']) + 3 }}"
                                            class="text-center text-muted">
                                            No cases found.
                                        </td>
                                    </tr>
                                @endforelse

                                <tr class="table-light fw-bold">
                                    <td colspan="2">Grand Total</td>

                                    @foreach($section['sizes'] as $size)
                                        <td class="text-end">
                                            {{ number_format($sizeTotals[$size]) }}
                                        </td>
                                    @endforeach

                                    <td class="text-end">
                                        {{ number_format($grandTotal) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    {{-- Two Overall Summaries --}}

    {{-- Project-wise Residential and Commercial Overall Summaries --}}

    @php
        $projectGroups = collect($overallSummaries['residential'])
            ->concat($overallSummaries['commercial'])
            ->unique('project_name')
            ->pluck('project_name')
            ->sort()
            ->values();
    @endphp

    @foreach($projectGroups as $projectName)

        {{-- Current Project: Residential Overall Summary --}}
        @foreach([
            'residential' => 'Residential Overall Summary',
            'commercial' => 'Commercial Overall Summary',
        ] as $category => $heading)

            @php
                $projectRows = collect($overallSummaries[$category])
                    ->where('project_name', $projectName)
                    ->values();

                $overallPossession = 0;
                $overallRePossession = 0;
                $overallTotal = 0;
            @endphp

            <div class="card shadow-sm mb-4 report-summary">
                <div class="card-header bg-white">
                    <strong>{{ $heading }}</strong>
                    <div class="small text-muted mt-1">
                        Project: {{ $projectName }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sr. No.</th>
                                <th>Block</th>
                                <th class="text-end">Possession</th>
                                <th class="text-end">Re-possession</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($projectRows as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['block_name'] }}</td>

                                    <td class="text-end">
                                        {{ number_format($row['possession']) }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($row['re_possession']) }}
                                    </td>

                                    <td class="text-end fw-semibold">
                                        {{ number_format($row['total']) }}
                                    </td>
                                </tr>

                                @php
                                    $overallPossession += $row['possession'];
                                    $overallRePossession += $row['re_possession'];
                                    $overallTotal += $row['total'];
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No cases found.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="table-light fw-bold">
                                <td colspan="2">Grand Total</td>

                                <td class="text-end">
                                    {{ number_format($overallPossession) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($overallRePossession) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($overallTotal) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        @endforeach
    @endforeach

    {{-- @foreach([
        'residential' => 'Residential Overall Summary',
        'commercial' => 'Commercial Overall Summary',
    ] as $category => $heading)
        <div class="card shadow-sm mb-4 report-summary">
            <div class="card-header bg-white">
                <strong>{{ $heading }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sr. No.</th>
                            <th>Block</th>
                            <th class="text-end">Possession</th>
                            <th class="text-end">Re-possession</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $overallPossession = 0;
                            $overallRePossession = 0;
                            $overallTotal = 0;
                        @endphp

                        @forelse($overallSummaries[$category] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $row['block_name'] }}
                                    @if(!request('project_id'))
                                        <small class="text-muted">
                                            ({{ $row['project_name'] }})
                                        </small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ number_format($row['possession']) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($row['re_possession']) }}
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($row['total']) }}
                                </td>
                            </tr>

                            @php
                                $overallPossession += $row['possession'];
                                $overallRePossession += $row['re_possession'];
                                $overallTotal += $row['total'];
                            @endphp
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No cases found.
                                </td>
                            </tr>
                        @endforelse

                        <tr class="table-light fw-bold">
                            <td colspan="2">Grand Total</td>
                            <td class="text-end">
                                {{ number_format($overallPossession) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($overallRePossession) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($overallTotal) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach --}}

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-3">
            <div class="card shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="text-muted small">Total Cases</div>
                    <h3 class="mb-0">
                        {{ number_format($summary->total ?? 0) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="text-muted small">Completed Possessions</div>
                    <h3 class="mb-0 text-success">
                        {{ number_format($summary->completed ?? 0) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small">In-Process Cases</div>
                    <h3 class="mb-0">
                        {{ number_format($summary->in_process ?? 0) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card shadow-sm h-100 border-start border-4 border-danger">
                <div class="card-body">
                    <div class="text-muted small">Cancelled Cases</div>
                    <h3 class="mb-0 text-danger">
                        {{ number_format($summary->cancelled ?? 0) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Original and Re-possession Summary --}}
    <div class="alert alert-light border mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <strong>Original Possessions:</strong>
                {{ number_format($summary->original_possessions ?? 0) }}
            </div>
            <div class="col-md-6">
                <strong>Re-possessions:</strong>
                {{ number_format($summary->re_possessions ?? 0) }}
            </div>
        </div>
        <small class="text-muted">
            Original/re-possession counts are based on possession_sequence.
            These counts include cases in all statuses.
        </small>
    </div>

    {{-- Status Summary --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Status-wise Summary</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th class="text-end">Total Cases</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statusSummary as $status)
                        <tr>
                            <td>{{ $status->label }}</td>
                            <td class="text-end fw-semibold">
                                {{ number_format($status->total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Project-wise Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Project-wise Report</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Completed</th>
                        <th class="text-end">In Process</th>
                        <th class="text-end">Cancelled</th>
                        <th class="text-end">Original</th>
                        <th class="text-end">Re-possession</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectReport as $row)
                        <tr>
                            <td>{{ $row->project_name }}</td>
                            <td class="text-end">{{ number_format($row->total) }}</td>
                            <td class="text-end">{{ number_format($row->completed) }}</td>
                            <td class="text-end">{{ number_format($row->in_process) }}</td>
                            <td class="text-end">{{ number_format($row->cancelled) }}</td>
                            <td class="text-end">{{ number_format($row->original_possessions) }}</td>
                            <td class="text-end">{{ number_format($row->re_possessions) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                No possession cases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Block-wise Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Project and Block-wise Report</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th>Block</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Completed</th>
                        <th class="text-end">In Process</th>
                        <th class="text-end">Cancelled</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blockReport as $row)
                        <tr>
                            <td>{{ $row->project_name }}</td>
                            <td>{{ $row->block_name }}</td>
                            <td class="text-end">{{ number_format($row->total) }}</td>
                            <td class="text-end">{{ number_format($row->completed) }}</td>
                            <td class="text-end">{{ number_format($row->in_process) }}</td>
                            <td class="text-end">{{ number_format($row->cancelled) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No possession cases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Property Type-wise Report --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>Project, Block and Property Type-wise Report</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th>Block</th>
                        <th>Property Type</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Completed</th>
                        <th class="text-end">In Process</th>
                        <th class="text-end">Cancelled</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($propertyTypeReport as $row)
                        <tr>
                            <td>{{ $row->project_name }}</td>
                            <td>{{ $row->block_name }}</td>
                            <td>{{ $row->property_type_name }}</td>
                            <td class="text-end">{{ number_format($row->total) }}</td>
                            <td class="text-end">{{ number_format($row->completed) }}</td>
                            <td class="text-end">{{ number_format($row->in_process) }}</td>
                            <td class="text-end">{{ number_format($row->cancelled) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                No possession cases found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    @media print {
        nav,
        .sidebar,
        .btn,
        form,
        footer {
            display: none !important;
        }

        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            break-inside: avoid;
        }

        body {
            font-size: 10px;
        }

        table {
            font-size: 9px;
        }
    }
</style>
@endsection
