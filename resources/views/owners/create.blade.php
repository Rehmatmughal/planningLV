@extends('app')

@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                ➕ Add New Owner
            </h4>

            <small class="text-muted">
                Add owner information to the master owner database.
            </small>
        </div>

        <a href="{{ route('owners.index') }}"
           class="btn btn-secondary">
            ← Back to Owners
        </a>

    </div>


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


    {{-- Form --}}
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <strong>
                👤 Owner Information
            </strong>

        </div>


        <div class="card-body">

            <form action="{{ route('owners.store') }}"
                  method="POST">

                @csrf


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
                               value="{{ old('owner_name') }}"
                               placeholder="Enter owner name"
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
                               id="cnic"
                               class="form-control @error('cnic') is-invalid @enderror"
                               value="{{ old('cnic') }}"
                               placeholder="xxxxx-xxxxxxx-x"
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
                               value="{{ old('contact_no') }}"
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
                                  rows="4"
                                  placeholder="Enter complete address">{{ old('address') }}</textarea>

                        @error('address')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route('owners.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        💾 Save Owner

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection
