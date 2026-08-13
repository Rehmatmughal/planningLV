@extends('app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h4>Edit Plot Category</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Category Title --}}
                <div class="mb-3">
                    <label class="form-label">Category Title</label>
                    <input type="text"
                           name="category_title"
                           class="form-control @error('category_title') is-invalid @enderror"
                           value="{{ old('category_title', $category->category_title) }}"
                           required>

                    @error('category_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks"
                              class="form-control"
                              rows="3">{{ old('remarks', $category->remarks) }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        ← Back
                    </a>

                    <button type="submit" class="btn btn-success">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
