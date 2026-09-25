@extends('app')

@section('content')

<div class="container-fluid py-4">


{{-- =========================================================
    DASHBOARD HEADER
========================================================== --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fas fa-chart-pie text-primary me-2"></i>
            Planning Management Dashboard
        </h2>

        <p class="text-muted mb-0">
            Overview of current planning, possession and area variation activities
        </p>
    </div>

    <div class="mt-3 mt-md-0">
        <span class="dashboard-date">
            <i class="fas fa-calendar-day text-primary me-2"></i>
            {{ now()->format('d M Y') }}
        </span>
    </div>

</div>


{{-- =========================================================
    POSSESSION STATUS + ATTENTION REQUIRED
========================================================== --}}
<div class="row g-4 mb-4">

    {{-- POSSESSION STATUS --}}
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-primary-subtle text-primary me-3">
                        <i class="fas fa-file-signature"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Possession Status
                        </h5>

                        <small class="text-muted">
                            Current status of possession cases
                        </small>
                    </div>

                </div>


                <div class="row g-3">

                    {{-- Received --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-primary-subtle text-primary">
                                <i class="fas fa-inbox"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Received
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['received'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Prepared --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-info-subtle text-info">
                                <i class="fas fa-file-circle-check"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Prepared
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['prepared'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Signing --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-warning-subtle text-warning">
                                <i class="fas fa-pen-nib"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Signing
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['signing'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Approval --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-purple-subtle text-purple">
                                <i class="fas fa-stamp"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Approval
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['approval'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Receive Back --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-secondary-subtle text-secondary">
                                <i class="fas fa-rotate-left"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Receive Back
                                </span>

                                <h4 class="fw-bold mb-0">
                                    0
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Handed Over --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-success-subtle text-success">
                                <i class="fas fa-hand-holding"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Handed Over
                                </span>

                                <h4 class="fw-bold mb-0">
                                    0
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Completed --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-success-subtle text-success">
                                <i class="fas fa-circle-check"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Completed
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['completed'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>


                    {{-- Cancelled --}}
                    <div class="col-md-6">
                        <div class="status-box">

                            <div class="status-icon bg-danger-subtle text-danger">
                                <i class="fas fa-ban"></i>
                            </div>

                            <div class="flex-grow-1">
                                <span class="text-muted small">
                                    Cancelled
                                </span>

                                <h4 class="fw-bold mb-0">
                                    {{ number_format($possessionStatus['cancelled'] ?? 0) }}
                                </h4>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ATTENTION REQUIRED --}}
    <div class="col-lg-5">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-danger-subtle text-danger me-3">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Attention Required
                        </h5>

                        <small class="text-muted">
                            Cases requiring attention
                        </small>
                    </div>

                </div>


                {{-- Pending Cases --}}
                <div class="attention-item">

                    <div class="attention-icon bg-danger-subtle text-danger">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div class="flex-grow-1">
                        <strong>
                            Pending Possession Cases
                        </strong>

                        <small class="text-muted d-block">
                            Cases require further action
                        </small>
                    </div>

                    <span class="badge bg-danger">
                        {{ number_format($attention['pending_possession'] ?? 0) }}
                    </span>

                </div>


                {{-- Approval --}}
                <div class="attention-item">

                    <div class="attention-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>

                    <div class="flex-grow-1">
                        <strong>
                            Awaiting Approval
                        </strong>

                        <small class="text-muted d-block">
                            Cases waiting for approval
                        </small>
                    </div>

                    <span class="badge bg-warning text-dark">
                        {{ number_format($attention['awaiting_approval'] ?? 0) }}
                    </span>

                </div>


                {{-- Area Variations --}}
                <div class="attention-item">

                    <div class="attention-icon bg-info-subtle text-info">
                        <i class="fas fa-chart-area"></i>
                    </div>

                    <div class="flex-grow-1">
                        <strong>
                            Area Variations
                        </strong>

                        <small class="text-muted d-block">
                            Variations requiring attention
                        </small>
                    </div>

                    <span class="badge bg-info">
                        {{ number_format($attention['area_variations'] ?? 0) }}
                    </span>

                </div>


                {{-- Ready for Handover --}}
                <div class="attention-item">

                    <div class="attention-icon bg-success-subtle text-success">
                        <i class="fas fa-hand-holding-hand"></i>
                    </div>

                    <div class="flex-grow-1">
                        <strong>
                            Ready for Handover
                        </strong>

                        <small class="text-muted d-block">
                            Cases ready for next action
                        </small>
                    </div>

                    <span class="badge bg-success">
                        {{ number_format($attention['ready_for_handover'] ?? 0) }}
                    </span>

                </div>


                <div class="mt-4">

                    <button type="button"
                            class="btn btn-outline-primary w-100">
                        <i class="fas fa-list-check me-2"></i>
                        View Pending Work
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    AREA VARIATION STATUS + TODAY'S OVERVIEW
========================================================== --}}
<div class="row g-4 mb-4">

    {{-- AREA VARIATION STATUS --}}
    <div class="col-lg-5">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-info-subtle text-info me-3">
                        <i class="fas fa-chart-area"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Area Variation Status
                        </h5>

                        <small class="text-muted">
                            Current area variation workload
                        </small>
                    </div>

                </div>


                {{-- Received / Updated --}}
                <div class="area-status-row">

                    <div class="d-flex align-items-center">

                        <span class="status-dot bg-primary me-3"></span>

                        <span>
                            Received / Updated
                        </span>

                    </div>

                    <strong>
                        {{ number_format($areaVariationStatus['received_updated'] ?? 0) }}
                    </strong>

                </div>


                {{-- Ready for Print --}}
                <div class="area-status-row">

                    <div class="d-flex align-items-center">

                        <span class="status-dot bg-warning me-3"></span>

                        <span>
                            Ready for Print
                        </span>

                    </div>

                    <strong>
                        {{ number_format($areaVariationStatus['ready_for_print'] ?? 0) }}
                    </strong>

                </div>


                {{-- Printed --}}
                <div class="area-status-row">

                    <div class="d-flex align-items-center">

                        <span class="status-dot bg-success me-3"></span>

                        <span>
                            Printed
                        </span>

                    </div>

                    <strong>
                        {{ number_format($areaVariationStatus['printed'] ?? 0) }}
                    </strong>

                </div>


                {{-- Pending --}}
                <div class="area-status-row border-0">

                    <div class="d-flex align-items-center">

                        <span class="status-dot bg-danger me-3"></span>

                        <span>
                            Pending
                        </span>

                    </div>

                    <strong>
                        {{ number_format($areaVariationStatus['pending'] ?? 0) }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- TODAY'S OVERVIEW --}}
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-success-subtle text-success me-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Today's Overview
                        </h5>

                        <small class="text-muted">
                            Today's possession and area variation activity
                        </small>
                    </div>

                </div>


                <div class="row g-4">

                    {{-- TODAY POSSESSION --}}
                    <div class="col-md-6">

                        <div class="today-box">

                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-file-signature text-primary me-2"></i>
                                Possession
                            </h6>


                            <div class="today-row">
                                <span>
                                    Received
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['received'] ?? 0) }}
                                </strong>
                            </div>


                            <div class="today-row">
                                <span>
                                    Prepared
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['prepared'] ?? 0) }}
                                </strong>
                            </div>


                            <div class="today-row">
                                <span>
                                    Sent for Approval
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['sent_for_approval'] ?? 0) }}
                                </strong>
                            </div>


                            <div class="today-row">
                                <span>
                                    Signing
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['signing'] ?? 0) }}
                                </strong>
                            </div>


                            <div class="today-row">
                                <span>
                                    Approval
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['approval'] ?? 0) }}
                                </strong>
                            </div>


                            <div class="today-row border-0">
                                <span>
                                    Ready for Handover
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['possession']['ready_for_handover'] ?? 0) }}
                                </strong>
                            </div>

                        </div>

                    </div>


                    {{-- TODAY AREA VARIATION --}}
                    <div class="col-md-6">

                        <div class="today-box">

                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-chart-area text-info me-2"></i>
                                Area Variation
                            </h6>


                            <div class="today-row">

                                <span>
                                    Received / Updated
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['area_variation']['received_updated'] ?? 0) }}
                                </strong>

                            </div>


                            <div class="today-row">

                                <span>
                                    Printed
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['area_variation']['printed'] ?? 0) }}
                                </strong>

                            </div>


                            <div class="today-row">

                                <span>
                                    Ready for Print
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['area_variation']['ready_for_print'] ?? 0) }}
                                </strong>

                            </div>


                            <div class="today-row border-0">

                                <span>
                                    Pending
                                </span>

                                <strong>
                                    {{ number_format($todayOverview['area_variation']['pending'] ?? 0) }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    PROJECT WISE OVERVIEW
========================================================== --}}
<div class="card border-0 shadow-sm dashboard-card mb-4">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div class="d-flex align-items-center">

                <div class="section-icon bg-primary-subtle text-primary me-3">
                    <i class="fas fa-building"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        Project-wise Possession Summary
                    </h5>

                    <small class="text-muted">
                        First possessions and subsequent repossessions by project
                    </small>
                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>Project</th>

                        <th class="text-center">
                            Possession
                        </th>

                        <th class="text-center">
                            Repossession
                        </th>

                        <th class="text-center">
                            Total
                        </th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($projectOverview as $project)

                        <tr>

                            <td>
                                <strong>
                                    {{ $project['project_name'] }}
                                </strong>
                            </td>


                            <td class="text-center">

                                <span class="badge bg-primary px-3 py-2">
                                    {{ number_format($project['possession'] ?? 0) }}
                                </span>

                            </td>


                            <td class="text-center">

                                <span class="badge bg-warning text-dark px-3 py-2">
                                    {{ number_format($project['repossession'] ?? 0) }}
                                </span>

                            </td>


                            <td class="text-center fw-bold">

                                {{ number_format($project['total'] ?? 0) }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted py-4">

                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>

                                No project data available.

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                @if($projectOverview->count())

                    <tfoot class="table-light">

                        <tr>

                            <th>
                                Total
                            </th>


                            <th class="text-center">

                                {{ number_format($projectOverview->sum('possession')) }}

                            </th>


                            <th class="text-center">

                                {{ number_format($projectOverview->sum('repossession')) }}

                            </th>


                            <th class="text-center">

                                {{ number_format($projectOverview->sum('total')) }}

                            </th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
    POSSESSION TREND
========================================================== --}}
<div class="card border-0 shadow-sm dashboard-card mb-4">

    <div class="card-body p-4">

        <div class="d-flex align-items-center mb-4">

            <div class="section-icon bg-warning-subtle text-warning me-3">
                <i class="fas fa-chart-line"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-1">
                    Possession Trend
                </h5>

                <small class="text-muted">
                    Monthly possession activity
                </small>
            </div>

        </div>


        <div class="chart-container">

            <canvas id="possessionTrendChart"></canvas>

        </div>

    </div>

</div>


{{-- =========================================================
    RECENT ACTIVITY + RECENTLY ADDED
========================================================== --}}
<div class="row g-4">

    {{-- RECENT ACTIVITY --}}
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-dark-subtle text-dark me-3">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Recent Activity
                        </h5>

                        <small class="text-muted">
                            Latest activities in the system
                        </small>
                    </div>

                </div>


                @forelse($recentActivity as $activity)

                    <div class="activity-item">

                        <div class="activity-icon
                            @if($activity['type'] === 'possession')
                                bg-primary-subtle text-primary
                            @else
                                bg-info-subtle text-info
                            @endif
                        ">

                            @if($activity['type'] === 'possession')

                                <i class="fas fa-file-signature"></i>

                            @else

                                <i class="fas fa-chart-area"></i>

                            @endif

                        </div>


                        <div>

                            <strong>
                                {{ $activity['title'] }}
                            </strong>

                            <small class="text-muted d-block">
                                {{ $activity['description'] }}
                            </small>

                            <small class="text-muted">
                                {{ $activity['created_at']->diffForHumans() }}
                            </small>

                        </div>

                    </div>

                @empty

                    <div class="text-center text-muted py-4">

                        <i class="fas fa-clock-rotate-left fa-2x mb-2"></i>

                        <p class="mb-0">
                            No recent activity available.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- RECENTLY ADDED --}}
    <div class="col-lg-5">

        <div class="card border-0 shadow-sm dashboard-card h-100">

            <div class="card-body p-4">

                <div class="d-flex align-items-center mb-4">

                    <div class="section-icon bg-success-subtle text-success me-3">
                        <i class="fas fa-plus-circle"></i>
                    </div>

                    <div>
                        <h5 class="fw-bold mb-1">
                            Recently Added
                        </h5>

                        <small class="text-muted">
                            Latest records added to the system
                        </small>
                    </div>

                </div>


                {{-- Owners --}}
                <div class="recent-item">

                    <div class="recent-number bg-primary-subtle text-primary">

                        {{ number_format($recentlyAdded['owners'] ?? 0) }}

                    </div>

                    <div>
                        <strong>
                            New Owners
                        </strong>

                        <small class="text-muted d-block">
                            Recently registered owners
                        </small>
                    </div>

                </div>


                {{-- Possession Cases --}}
                <div class="recent-item">

                    <div class="recent-number bg-success-subtle text-success">

                        {{ number_format($recentlyAdded['possession_cases'] ?? 0) }}

                    </div>

                    <div>
                        <strong>
                            Possession Cases
                        </strong>

                        <small class="text-muted d-block">
                            Recently received cases
                        </small>
                    </div>

                </div>


                {{-- Area Variations --}}
                <div class="recent-item">

                    <div class="recent-number bg-info-subtle text-info">

                        {{ number_format($recentlyAdded['area_variations'] ?? 0) }}

                    </div>

                    <div>
                        <strong>
                            Area Variations
                        </strong>

                        <small class="text-muted d-block">
                            Recently received / updated
                        </small>
                    </div>

                </div>


                {{-- Plot Updates --}}
                <div class="recent-item border-0">

                    <div class="recent-number bg-warning-subtle text-warning">

                        {{ number_format($recentlyAdded['plot_updates'] ?? 0) }}

                    </div>

                    <div>
                        <strong>
                            Plot Updates
                        </strong>

                        <small class="text-muted d-block">
                            Recently updated plots
                        </small>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


</div>

{{-- =============================================================
CHART.JS
============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    const ctx = document.getElementById('possessionTrendChart');

    new Chart(ctx, {

        type: 'line',

        data: {

            labels: @json($trendLabels),

            datasets: [{

                label: 'Possession Cases',

                data: @json($trendData),

                borderWidth: 3,

                tension: 0.4,

                fill: false,

                pointRadius: 4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: true
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                }

            }

        }

    });

</script>

{{-- =============================================================
DASHBOARD CSS
============================================================= --}}

<style>

    .dashboard-card {
        border-radius: 14px;
    }


    .dashboard-date {

        display: inline-flex;
        align-items: center;

        background: #fff;

        border: 1px solid #e9ecef;

        border-radius: 10px;

        padding: 10px 15px;

        font-size: 14px;

        color: #495057;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }


    .section-icon {

        width: 46px;
        height: 46px;

        border-radius: 11px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 18px;

        flex-shrink: 0;
    }


    .status-box {

        display: flex;
        align-items: center;

        padding: 14px;

        border: 1px solid #edf0f2;

        border-radius: 11px;

        background: #fff;

        transition: all 0.2s ease;
    }


    .status-box:hover {

        transform: translateY(-2px);

        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
    }


    .status-icon {

        width: 42px;
        height: 42px;

        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-right: 12px;

        flex-shrink: 0;
    }


    .bg-purple-subtle {
        background-color: #eee5ff;
    }


    .text-purple {
        color: #6f42c1;
    }


    .attention-item {

        display: flex;
        align-items: center;

        padding: 15px 0;

        border-bottom: 1px solid #edf0f2;
    }


    .attention-icon {

        width: 42px;
        height: 42px;

        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-right: 12px;

        flex-shrink: 0;
    }


    .area-status-row {

        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 17px 0;

        border-bottom: 1px solid #edf0f2;
    }


    .status-dot {

        width: 10px;
        height: 10px;

        border-radius: 50%;

        display: inline-block;
    }


    .today-box {

        background: #f8f9fa;

        border: 1px solid #edf0f2;

        border-radius: 12px;

        padding: 18px;
    }


    .today-row {

        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 11px 0;

        border-bottom: 1px solid #e9ecef;

        font-size: 14px;
    }


    .today-row strong {

        font-size: 15px;

        color: #212529;
    }


    .chart-container {

        position: relative;

        height: 320px;
    }


    .activity-item {

        display: flex;
        align-items: flex-start;

        padding: 15px 0;

        border-bottom: 1px solid #edf0f2;
    }


    .activity-icon {

        width: 42px;
        height: 42px;

        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-right: 13px;

        flex-shrink: 0;
    }


    .recent-item {

        display: flex;
        align-items: center;

        padding: 15px 0;

        border-bottom: 1px solid #edf0f2;
    }


    .recent-number {

        width: 45px;
        height: 45px;

        border-radius: 10px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-weight: 700;

        margin-right: 13px;

        flex-shrink: 0;
    }


    @media (max-width: 767px) {

        .dashboard-date {

            width: 100%;

            justify-content: center;
        }


        .chart-container {

            height: 250px;
        }

    }

</style>

@endsection
