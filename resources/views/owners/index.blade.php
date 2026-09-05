@extends('app')

@section('content')

<div class="container-fluid mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                👥 Owners
            </h4>

            <small class="text-muted">
                Manage owner information and CNIC records
            </small>
        </div>

        <a href="{{ route('owners.create') }}"
           class="btn btn-primary">

            + Add Owner

        </a>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Error Message --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Search --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('owners.index') }}">

                <div class="row g-2 align-items-end">

                    <div class="col-md-10">

                        <label class="form-label fw-bold">
                            Search Owner
                        </label>

                        <input type="text"
                               name="search"
                               class="form-control"
                               value="{{ request('search') }}"
                               placeholder="Search by name, CNIC or contact number">

                    </div>


                    <div class="col-md-2">

                        <button type="submit"
                                class="btn btn-primary w-100">

                            🔍 Search

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Owners Table --}}
    <div class="card shadow-sm">

        <div class="card-header bg-light d-flex justify-content-between align-items-center">

            <strong>
                Owner Records
            </strong>

            <span class="badge bg-secondary">

                {{ $owners->total() }}

            </span>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width: 60px;">
                                #
                            </th>

                            <th>
                                Owner Name
                            </th>

                            <th>
                                CNIC
                            </th>

                            <th>
                                Contact No
                            </th>

                            <th>
                                Address
                            </th>

                            <th class="text-center">
                                Possession Cases
                            </th>

                            <th class="text-center"
                                style="width: 150px;">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($owners as $owner)

                            <tr>

                                {{-- Number --}}
                                <td>

                                    {{ $owners->firstItem() + $loop->index }}

                                </td>


                                {{-- Name --}}
                                <td>

                                    <strong>
                                        {{ $owner->owner_name }}
                                    </strong>

                                </td>


                                {{-- CNIC --}}
                                <td>

                                    <span class="font-monospace">
                                        {{ $owner->cnic }}
                                    </span>

                                </td>


                                {{-- Contact --}}
                                <td>

                                    {{ $owner->contact_no ?? '-' }}

                                </td>


                                {{-- Address --}}
                                <td>

                                    {{ $owner->address ?? '-' }}

                                </td>


                                {{-- Possession Cases --}}
                                <td class="text-center">

                                    @php
                                        $caseCount = $owner->possessionCases()->count();
                                    @endphp

                                    @if($caseCount > 0)

                                        <span class="badge bg-primary">

                                            {{ $caseCount }}

                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            0

                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-1">


                                        {{-- Edit --}}
                                        <a href="{{ route('owners.edit', $owner) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit Owner">

                                            ✏️

                                        </a>


                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('owners.destroy', $owner) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this owner?');">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete Owner">

                                                🗑️

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-5 text-muted">

                                    @if(request('search'))

                                        No owner found for:

                                        <strong>
                                            "{{ request('search') }}"
                                        </strong>

                                    @else

                                        No owner records found.

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($owners->hasPages())

            <div class="card-footer">

                <div class="d-flex justify-content-center">

                    {{ $owners->links() }}

                </div>

            </div>

        @endif

    </div>

</div>

@endsection
