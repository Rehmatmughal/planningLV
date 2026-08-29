{{-- @extends('layout') --}}
@extends('app')

@section('content')
<!-- <div class="container-fluid py-4">  -->
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">🏠 Planning Management Dashboard</h2>
    <div>
        <ul>

        </ul>
    </div>
 
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        {{-- <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-primary text-black">
                <div class="card-body">
                    <h6>Total Projects</h6>
                    <h3>{{ $totalProjects }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-success text-black">
                <div class="card-body">
                    <h6>Total Blocks</h6>
                    <h3>{{ $totalBlocks }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-warning text-black">
                <div class="card-body">
                    <h6>Total Streets</h6>
                    <h3>{{ $totalStreets }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-info text-black">
                <div class="card-body">
                    <h6>Total Plots</h6>
                    <h3>{{ $totalPlots }}</h3>
                </div>
            </div>
        </div> --}}

{{-- new way of cards --}}
        <div class="col-md-4">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card dashboard-card p-4 text-center h-100">
                    <i class="fas fa-city fa-2x text-secondary mb-3"></i>
                    <h5>Projects</h5>
                    <h3>{{ $totalProjects }}</h3>
                </div>
            </a> 
        </div>

        <div class="col-md-4">
            <a href="{{ route('blocks.index') }}" class="text-decoration-none">
                <div class="card dashboard-card p-4 text-center h-100">
                    <i class="fas fa-border-all fa-2x text-secondary mb-3"></i>
                    <h5>Blocks</h5>
                    <h3>{{ $totalBlocks }}</h3>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('streets.index') }}" class="text-decoration-none">
                <div class="card dashboard-card p-4 text-center h-100">
                    <i class="fas fa-draw-polygon fa-2x text-secondary mb-3"></i>
                    <h5>Streets</h5>
                    <h3>{{ $totalStreets }}</h3>
                </div>
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <a href="{{ route('plots.index') }}" class="text-decoration-none">
            <div class="card dashboard-card p-4 text-center h-100">
                <i class="fas fa-draw-polygon fa-2x text-secondary mb-3"></i>
                <h5>Plots</h5>
                <h3>{{ $totalPlots }}</h3>
            </div>
        </a>
    </div>

    <!-- Chart -->
    {{-- <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-secondary mb-3">Plots by Project</h5>
            <canvas id="plotsChart" height="120"></canvas>
        </div>
    </div> --}}
    {{-- for summary card --}}
    <div class="row">

        <div class="col-md-4">
            <div class="card bg-primary text-white p-3">
                <h5>Total Activities</h5>
                <h3>{{ $totalActivities }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white p-3">
                <h5>Today's Activities</h5>
                <h3>{{ $todayActivities }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h5>Most Active User</h5>
                <h3>{{ $topUserName }}</h3>
                {{-- <h3>{{ optional($topUser->causer)->name ?? 'N/A' }}</h3> --}}
                {{-- <h3>{{ $topUser ? optional($topUser->causer)->name : 'N/A' }}</h3> --}}
            </div>
        </div>

    </div>

    <!-- Quick Links -->
    <div class="mt-4">
        <h5 class="fw-bold text-secondary">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-3 mt-2">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">Manage Projects</a>
            <a href="{{ route('blocks.index') }}" class="btn btn-outline-success">Manage Blocks</a>
            <a href="{{ route('streets.index') }}" class="btn btn-outline-warning">Manage Streets</a>
            <a href="{{ route('plots.index') }}" class="btn btn-outline-info">Manage Plots</a>
        </div>
    </div>
</div>

<!-- Chart.js -->
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('plotsChart').getContext('2d');
const plotsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($plotsPerProject->pluck('project_name')) !!},
        datasets: [{
            label: 'Number of Plots',
            data: {!! json_encode($plotsPerProject->pluck('plots_count')) !!},
            borderWidth: 1,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script> --}}
@endsection
