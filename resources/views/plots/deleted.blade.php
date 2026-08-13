@extends('app')

@section('content')

<div class="container-fluid mt-3">

    <div class="d-flex justify-content-between mb-3">
        <h4>Deleted Plots</h4>

        <a href="{{ route('plots.index') }}"
           class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">

            <table class="table table-bordered table-hover">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Block</th>
                        <th>Street</th>
                        <th>Plot No</th>
                        <th>Size</th>
                        <th>Deleted At</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($plots as $plot)

                    <tr>

                        <td>{{ $plot->id }}</td>

                        <td>{{ $plot->project->project_name ?? '-' }}</td>

                        <td>{{ $plot->block->block_name ?? '-' }}</td>

                        <td>{{ $plot->street->street_name ?? '-' }}</td>

                        <td>{{ $plot->plot_number }}</td>

                        <td>{{ $plot->size->title ?? '-' }}</td>

                        <td>
                            {{ $plot->deleted_at?->format('d-M-Y h:i A') }}
                        </td>

                        <td>

                            @can('plot.view')
                            {{-- <a href="{{ route('plots.deleted.view',$plot->id) }}" --}}
                            <a href="{{ route('plots.show', $plot->id) }}"
                               class="btn btn-info btn-sm">
                                View
                            </a>
                            @endcan

                            @can('plot.restore')
                            <form method="POST"
                                  action="{{ route('plots.restore',$plot->id) }}"
                                  class="d-inline">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Restore this plot?')">
                                    Restore
                                </button>
                            </form>
                            @endcan

                            @can('plot.force-delete')
                            <form method="POST"
                                  action="{{ route('plots.forceDelete',$plot->id) }}"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Permanent delete? This cannot be undone.')">
                                    Permanent Delete
                                </button>
                            </form>
                            @endcan

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No deleted plots found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-3">
            {{ $plots->links() }}
        </div>
    </div>

</div>

@endsection