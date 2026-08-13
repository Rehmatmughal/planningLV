@extends('app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Plot Size Management</h3>
        {{-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSizeModal">
            + Add New Size
        </button> --}}
        @can('size.trashview')
        <a href="{{ route('sizes.trash') }}" class="btn btn-danger">
            Deleted Sizes
        </a>
        @endcan

        @can('size.create')
        <a href="{{ route('sizes.create') }}" class="btn btn-primary">
            + Add New Size
        </a>
        @endcan
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
 
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif --}}
    <div id="alertArea" class="mt-2"></div>
    
    {{-- 🔍 Filter Section -- Start--}}
    <form method="GET" action="{{ route('sizes.index') }}" id="filterForm">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Project</label>
                <select name="project_id" id="projectFilter" class="form-control">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $project_id ? 'selected' : '' }}>
                            {{ $p->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    {{-- project filter-- End --}}


    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr align="center">
                <th>#</th>
                <th>Project</th>
                <th>Size Title</th>
                {{-- <th>Project</th> --}}
                <th>Remarks</th>
                @if(auth()->user()->can('size.edit') || auth()->user()->can('size.delete'))
                <th>Action</th>
                @endif

            </tr>
        </thead>
        <tbody>
            @foreach ($sizes as $key => $size)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $size->project->project_name ?? '-' }}</td>
                    <td>{{ $size->title }}</td>
                    {{-- <td>{{ $size->project->project_name ?? '-' }}</td> --}}
                    <td>{{ $size->remarks ?? '-' }}</td>
                    
                    @if(auth()->user()->can('size.edit') || auth()->user()->can('size.delete'))
                    <td>
                        {{-- <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-sm btn-info mb-1">Edit</a> --}}
                        @can('size.edit')
                        <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        @endcan


                        @can('size.delete')
                        <form action="{{ route('sizes.destroy', $size->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this size?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger mb-1">Delete</button>
                        </form>
                        @endcan
                        {{-- <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $size->id }}">Delete</button> --}}
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $sizes->links() }}
</div>

<!-- 🧱 Add size Modal -->
{{-- <div class="modal fade" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Add New Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSizeForm">
                    @csrf
                    <div class="mb-3">
                        <label>Project</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Size</label>
                        <input type="text" name="size_title" class="form-control" placeholder="Enter size name" required>
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Optional"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Size</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@endsection 

@push('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>
$(document).ready(function () {

//     // ✅ Add size via AJAX
//     $('#addSizeForm').on('submit', function (e) {
//         e.preventDefault();

//         $.ajax({
//             url: "{{ route('sizes.store') }}",
//             type: "POST",
//             data: $(this).serialize(),
//             success: function (response) {
//                 if (response.status === 'success') {
//                     showAlert('success', response.message);
//                     $('#addSizeModal').modal('hide');
//                     $('#addsizeForm')[0].reset();
//                     setTimeout(() => location.reload(), 6000);
//                 }
//             },
//             error: function (xhr) {
//                 if (xhr.status === 422) {
//                     let errors = xhr.responseJSON.errors;
//                     let message = Object.values(errors).flat().join('<br>');
//                     showAlert('danger', message);
//                 } else {
//                     showAlert('danger', 'Error saving size.');
//                 }
//             }
//         });
//     });

    // 🗑️ Delete Size
    $('.delete-btn').on('click', function () {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete size?')) {
            $.ajax({
                url: '/sizes/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                },
                error: function () {
                    showAlert('danger', 'Error deleting size.');
                }
            });
        }
    });

    // 🌟 Function to show Bootstrap alert
    function showAlert(type, message) {
        $('#alertArea').html(`
            <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }

});
</script>



{{-- <script>
$(function() {

    // 🧱 Create size AJAX
    $('#addsizeForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('sizes.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    $('#addSizeModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let messages = Object.values(errors).flat().join('\n');
                    alert(messages);
                } else {
                    alert('Something went wrong while saving the size.');
                }
            }
        });
    });

    // 🗑️ Delete size
    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this size?')) {
            $.ajax({
                url: '/sizes/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error deleting size.');
                    console.error(xhr.responseText);
                }
            });
        }
    });

});
</script> --}}
@endpush
