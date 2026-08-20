{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

@extends('app')

@section('content')

<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-person-circle me-2"></i>
                Profile Settings
            </h3>

            <p class="text-muted mb-0">
                Manage your account information and password.
            </p>
        </div>
    </div>


    {{-- Success Messages --}}
    @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            Profile information updated successfully.
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            Password updated successfully.
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif


    <div class="row g-4">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- Profile Information --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person me-2 text-primary"></i>
                        Profile Information
                    </h5>

                    <small class="text-muted">
                        Update your name and email address.
                    </small>
                </div>

                <div class="card-body">

                    <form method="POST"
                          action="{{ route('profile.update') }}">

                        @csrf
                        @method('PATCH')

                        {{-- Name --}}
                        <div class="mb-3">

                            <label for="name"
                                   class="form-label fw-semibold">
                                Name
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Email --}}
                        <div class="mb-3">

                            <label for="email"
                                   class="form-label fw-semibold">
                                Email Address
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-check-lg me-1"></i>
                            Update Profile

                        </button>

                    </form>

                </div>
            </div>


            {{-- Change Password --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock me-2 text-warning"></i>
                        Change Password
                    </h5>

                    <small class="text-muted">
                        Use a strong password to keep your account secure.
                    </small>

                </div>

                <div class="card-body">

                    <form method="POST"
                          action="{{ route('password.update') }}">

                        @csrf
                        @method('PUT')


                        {{-- Current Password --}}
                        <div class="mb-3">

                            <label for="current_password"
                                   class="form-label fw-semibold">
                                Current Password
                            </label>

                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password"
                                   required>

                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- New Password --}}
                        <div class="mb-3">

                            <label for="password"
                                   class="form-label fw-semibold">
                                New Password
                            </label>

                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password"
                                   required>

                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Confirm Password --}}
                        <div class="mb-3">

                            <label for="password_confirmation"
                                   class="form-label fw-semibold">
                                Confirm New Password
                            </label>

                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password"
                                   required>

                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <button type="submit"
                                class="btn btn-warning">

                            <i class="bi bi-key me-1"></i>
                            Change Password

                        </button>

                    </form>

                </div>
            </div>

        </div>


        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">

            {{-- Account Card --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-badge me-2 text-primary"></i>
                        Account Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="text-center mb-4">

                        <div class="rounded-circle bg-primary text-white
                                    d-inline-flex align-items-center
                                    justify-content-center"
                             style="width:80px;height:80px;font-size:32px;">

                            {{ strtoupper(substr($user->name, 0, 1)) }}

                        </div>

                        <h5 class="mt-3 mb-1">
                            {{ $user->name }}
                        </h5>

                        <small class="text-muted">
                            {{ $user->email }}
                        </small>

                    </div>


                    {{-- Role --}}
                    <div class="border-top pt-3 mb-3">

                        <div class="text-muted small">
                            Role
                        </div>

                        <div class="mt-1">

                            @forelse($user->roles as $role)

                                <span class="badge bg-primary">
                                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                </span>

                            @empty

                                <span class="badge bg-secondary">
                                    No Role
                                </span>

                            @endforelse

                        </div>

                    </div>


                    {{-- Member Since --}}
                    <div class="border-top pt-3">

                        <div class="text-muted small">
                            Member Since
                        </div>

                        <div class="fw-semibold">
                            {{ $user->created_at?->format('d-M-Y') }}
                        </div>

                    </div>

                </div>
            </div>


            {{-- Security Information --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-check me-2 text-success"></i>
                        Security
                    </h5>

                </div>

                <div class="card-body">

                    <div class="d-flex align-items-start mb-3">

                        <i class="bi bi-lock-fill text-success fs-4 me-3"></i>

                        <div>
                            <strong>Password Protected</strong>

                            <div class="small text-muted">
                                Your password is securely hashed.
                            </div>
                        </div>

                    </div>


                    <div class="d-flex align-items-start">

                        <i class="bi bi-person-check-fill text-primary fs-4 me-3"></i>

                        <div>
                            <strong>Role Based Access</strong>

                            <div class="small text-muted">
                                Your system access is controlled by your assigned role and permissions.
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection
