
@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser && $currentUser->hasRole('super-admin');
    $superAdminCount = \App\Models\User::role('super-admin')->count();
@endphp

<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold mb-1">User Management</h2>
            <p class="text-muted mb-0">
                Manage user accounts, roles and permissions.
            </p>
        </div>

        @can('user.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Create User
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Registered Users</h5>
            <span class="badge bg-secondary">{{ $users->count() }} Users</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3">#</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3 text-end pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse ($users as $user)
                        @php
                            $isSelf = $currentUser && $currentUser->id === $user->id;
                            $targetIsProtected = $user->hasAnyRole(['admin', 'super-admin']);

                            $canEditTarget = $isSuperAdmin
                                || (!$isSelf && !$targetIsProtected);

                            $canDeleteTarget = !$isSelf
                                && (
                                    $isSuperAdmin
                                    || !$targetIsProtected
                                )
                                && (
                                    !$user->hasRole('super-admin')
                                    || $superAdminCount > 1
                                );
                        @endphp

                        <tr>
                            <td class="px-3 text-muted">{{ $loop->iteration }}</td>

                            <td class="fw-semibold">{{ $user->name }}</td>

                            <td>{{ $user->email }}</td>

                            <td>
                                @forelse ($user->getRoleNames() as $roleName)
                                    @php
                                        $roleBadge = match ($roleName) {
                                            'super-admin' => 'bg-danger',
                                            'admin' => 'bg-primary',
                                            'staff' => 'bg-success',
                                            'guest' => 'bg-secondary',
                                            default => 'bg-info text-dark',
                                        };
                                    @endphp

                                    <span class="badge {{ $roleBadge }} me-1">
                                        {{ ucwords(str_replace('-', ' ', $roleName)) }}
                                    </span>
                                @empty
                                    <span class="badge bg-light text-dark border">
                                        No Role
                                    </span>
                                @endforelse
                            </td>

                            <td class="text-end pe-3">
                                <div class="d-inline-flex flex-wrap gap-2 justify-content-end">

                                    @can('user.edit')
                                        @if ($canEditTarget)
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                        @endif
                                    @endcan

                                    @can('user.delete')
                                        @if ($canDeleteTarget)
                                            <form
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @if (
                                        (!$canEditTarget || !auth()->user()->can('user.edit'))
                                        && (!$canDeleteTarget || !auth()->user()->can('user.delete'))
                                    )
                                        <span class="text-muted small">No actions</span>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-people fs-2"></i>
                                </div>
                                <div class="fw-semibold">No users found</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">My Permissions</h5>
            <small class="text-muted">
                Permissions available to your current account.
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3">Permission</th>
                            <th class="py-3 text-center">Access</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td class="px-3">{{ $permission->name }}</td>

                                <td class="text-center">
                                    @can($permission->name)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
