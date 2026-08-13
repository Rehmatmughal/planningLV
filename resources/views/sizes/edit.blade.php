@extends('app')

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Size</h4>
        <a href="{{ route('sizes.index') }}" class="btn btn-sm btn-secondary">
            ← Back to Sizes
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('sizes.update', $size) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Size Title --}}
                <div class="mb-3">
                    <label class="form-label">Size Title <span class="text-danger">*</span></label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $size->title) }}"
                           required>
                </div>

                {{-- Size Area --}}
                <div class="mb-3">
                    <label class="form-label">Size Area</label>
                    <input type="number"
                           step="0.01"
                           name="size_area"
                           class="form-control"
                           value="{{ old('size_area', $size->size_area) }}"
                           required>
                </div>

                {{-- Remarks --}}
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <input type="text"
                           name="remarks"
                           class="form-control"
                           value="{{ old('remarks', $size->remarks) }}">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Update Size
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
