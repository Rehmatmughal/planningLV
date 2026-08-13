@extends('app')

@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Deleted Plot Sizes</h3>

        <a href="{{ route('sizes.index') }}" class="btn btn-primary">
            Back To Sizes
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('sizes.trash') }}">
        <div class="row mb-3">

            <div class="col-md-3">
                <label>Project</label>

                <select name="project_id" class="form-control">
                    <option value="">All Projects</option>

                    @foreach($projects as $p)

                        <option value="{{ $p->id }}"
                            {{ $p->id == $project_id ? 'selected' : '' }}>

                            {{ $p->project_name }}

                        </option>

                    @endforeach

                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Size Title</th>
                <th>Size Area</th>
                <th>Remarks</th>
                <th>Deleted At</th>
                @if(auth()->user()->can('size.restore')|| auth()->user()->can('size.force-delete'))
                <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($sizes as $key => $size)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        {{ $size->project->project_name ?? '-' }}
                    </td>
                    <td>{{ $size->title }}</td>
                    <td>{{ $size->size_area }}</td>
                    <td>{{ $size->remarks ?? '-' }}</td>
                    <td>
                        {{ $size->deleted_at->format('d-M-Y h:i A') }}
                    </td>

                    {{-- <td>

                        <form action="{{ route('sizes.restore', $size->id) }}"
                              method="POST"
                              onsubmit="return confirm('Restore this size?')">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                Restore
                            </button>
                        </form>
                    </td> --}}
                    @if(auth()->user()->can('size.restore')|| auth()->user()->can('size.force-delete'))
                    <td>
                        {{-- Restore --}}
                        @can('size.restore')
                        <form action="{{ route('sizes.restore', $size->id) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Restore this size?')">

                            @csrf

                            <button class="btn btn-success btn-sm">
                                Restore
                            </button>

                        </form>
                        @endcan

                        {{-- Permanent Delete --}}
                        @can('size.force-delete')
                        <form action="{{ route('sizes.forceDelete', $size->id) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Permanently delete this size? This action cannot be undone.')">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                Permanent Delete
                            </button>

                        </form>
                        @endcan
                    </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        No deleted sizes found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $sizes->links() }}

</div>

@endsection