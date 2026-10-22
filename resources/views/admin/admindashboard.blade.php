
@extends('app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Admin Dashboard</h2>
            <p class="text-muted mb-0">
                Welcome to Town Planning MIS Administration.
            </p>
        </div>
    </div>

    <div class="row g-4">

        {{-- Logged-in User --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-user fa-2x text-primary me-3"></i>
                        <h5 class="card-title mb-0">Current User</h5>
                    </div>

                    <p class="mb-1">
                        <strong>Name:</strong>
                        {{ auth()->user()->name }}
                    </p>

                    <p class="mb-0">
                        <strong>Email:</strong>
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </div>
        </div>

        {{-- User Roles --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-user-shield fa-2x text-success me-3"></i>
                        <h5 class="card-title mb-0">Assigned Roles</h5>
                    </div>

                    <p class="mb-0">
                        {{ auth()->user()->getRoleNames()->join(', ') ?: 'No role assigned' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- User Management --}}
        @can('user.view')
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-users fa-2x text-info me-3"></i>
                        <h5 class="card-title mb-0">User Management</h5>
                    </div>

                    <p class="text-muted">
                        View and manage system users.
                    </p>

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-primary btn-sm">
                        <i class="fa fa-users me-1"></i>
                        Manage Users
                    </a>
                </div>
            </div>
        </div>
        @endcan

    </div>

</div>

@endsection
