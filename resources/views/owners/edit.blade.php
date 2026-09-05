@extends('app')

@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                ✏️ Edit Owner
            </h4>

            <small class="text-muted">
                Update owner information.
            </small>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('owners.index') }}"
               class="btn btn-secondary">

                ← Back

            </a>

        </div>

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


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following errors:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="row">


        {{-- Edit Form --}}
        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">

                    <strong>
                        👤 Owner Information
                    </strong>

                </div>


                <div class="card-body">

                    <form action="{{ route('owners.update', $owner) }}"
                          method="POST">

                        @csrf
                        @method('PUT')


                        <div class="row g-3">


                            {{-- Owner Name --}}
                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Owner Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="owner_name"
                                       class="form-control @error('owner_name') is-invalid @enderror"
                                       value="{{ old('owner_name', $owner->owner_name) }}"
                                       required>

                                @error('owner_name')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- CNIC --}}
                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    CNIC
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="cnic"
                                       class="form-control @error('cnic') is-invalid @enderror"
                                       value="{{ old('cnic', $owner->cnic) }}"
                                       required>

                                @error('cnic')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- Contact --}}
                            <div class="col-md-6">

                                <label class="form-label fw-bold">
                                    Contact No
                                </label>

                                <input type="text"
                                       name="contact_no"
                                       class="form-control @error('contact_no') is-invalid @enderror"
                                       value="{{ old('contact_no', $owner->contact_no) }}"
                                       placeholder="03xx-xxxxxxx">

                                @error('contact_no')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                            {{-- Address --}}
                            <div class="col-md-12">

                                <label class="form-label fw-bold">
                                    Address
                                </label>

                                <textarea name="address"
                                          class="form-control @error('address') is-invalid @enderror"
                                          rows="4">{{ old('address', $owner->address) }}</textarea>

                                @error('address')

                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>


                        </div>


                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between mt-4">


                            {{-- Delete --}}
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    onclick="confirmDelete()">

                                🗑 Delete Owner

                            </button>


                            <div class="d-flex gap-2">

                                <a href="{{ route('owners.index') }}"
                                   class="btn btn-secondary">

                                    Cancel

                                </a>

                                <button type="submit"
                                        class="btn btn-primary">

                                    💾 Update Owner

                                </button>

                            </div>


                        </div>

                    </form>


                    {{-- Delete Form --}}
                    <form id="deleteOwnerForm"
                          action="{{ route('owners.destroy', $owner) }}"
                          method="POST"
                          class="d-none">

                        @csrf
                        @method('DELETE')

                    </form>

                </div>

            </div>

        </div>


        {{-- Owner Summary --}}
        <div class="col-lg-4">

            <div class="card shadow-sm">

                <div class="card-header bg-light">

                    <strong>
                        📋 Owner Summary
                    </strong>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted">
                            Owner ID
                        </small>

                        <div class="fw-bold">
                            #{{ $owner->id }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Owner Name
                        </small>

                        <div class="fw-bold">
                            {{ $owner->owner_name }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            CNIC
                        </small>

                        <div>
                            {{ $owner->cnic }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Possession Cases
                        </small>

                        <div>

                            <span class="badge bg-primary">

                                {{ $owner->possessionCases()->count() }}

                            </span>

                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Added
                        </small>

                        <div>
                            {{ $owner->created_at?->format('d-m-Y') ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>

</div>


<script>

function confirmDelete()
{
    if (confirm(
        'Are you sure you want to delete this owner?\n\nThe owner will be soft deleted.'
    )) {

        document.getElementById('deleteOwnerForm').submit();

    }
}

</script>

@endsection
