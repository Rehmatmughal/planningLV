@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Property Type</h4>

        <a href="{{ route('property-types.index') }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Update Property Type</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('property-types.update', $propertyType->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label for="name" class="form-label">
                        Property Type <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{{ old('name', $propertyType->name) }}"
                           required>

                    @error('name')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Update Property Type
                </button>

                <a href="{{ route('property-types.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

@endsection