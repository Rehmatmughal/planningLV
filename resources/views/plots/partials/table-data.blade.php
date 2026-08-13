@foreach($plots as $plot)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $plot->project->project_name ?? '-' }}</td>
    <td>{{ $plot->block->block_name ?? '-' }}</td>
    <td>{{ $plot->street->street_name ?? '-' }}</td>
    <td>{{ $plot->plot_number }}</td>
    <td>{{ $plot->size->title ?? '-' }}</td>
    <td>{{ $plot->category ?? '-' }}</td>

    {{-- LOP Status --}}
    <td>
        @if($plot->lop_status == 'approved')
            <span class="badge bg-success">Approved</span>
        @elseif($plot->lop_status == 'submitted')
            <span class="badge bg-warning text-dark">Submitted</span>
        @elseif($plot->lop_status == 'not_applied')
            <span class="badge bg-danger">Not Applied</span>
        @else
            -
        @endif
    </td>

    {{-- Development Status --}}
    <td>
        @if($plot->development_status == 'complete')
            <span class="badge bg-success">Complete</span>
        @elseif($plot->development_status == 'in_progress')
            <span class="badge bg-info text-dark">In Progress</span>
        @elseif($plot->development_status == 'not_started')
            <span class="badge bg-secondary">Not Started</span>
        @else
            -
        @endif
    </td>

    <td>{{ $plot->remarks }}</td>

    <td>

        {{-- Edit --}}
        <button class="btn btn-sm btn-primary editBtn"
            data-id="{{ $plot->id }}">
            Edit
        </button>

        {{-- Print --}}
        <a href="{{ route('plots.print', $plot->id) }}"
           class="btn btn-sm btn-secondary">
            Print
        </a>

        {{-- Delete --}}
        <form action="{{ route('plots.destroy', $plot->id) }}"
              method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger"
                    onclick="return confirm('Are you sure?')">
                Delete
            </button>
        </form>

    </td>
</tr>
@endforeach

@if($plots->count() == 0)
<tr>
    <td colspan="11" class="text-center text-muted">
        No plots found
    </td>
</tr>
@endif
