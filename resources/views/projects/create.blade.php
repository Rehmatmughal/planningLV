@extends('layout')

@section('content')
{{-- NEW store method without ajax --}}
<div class="container mt-4">
    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="mb-3">
            <label>Project Name</label>
            <input type="text" name="project_name" class="form-control">
        </div> 
        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="project_remarks" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Project</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

{{-- old store method with AJAX --}}
{{-- <div class="container mt-4">
    <h3>Add New Project</h3>

    <div id="alertBox"></div>

    <form id="projectForm">
        @csrf
        <div class="mb-3">
            <label>Project Name</label>
            <input type="text" name="project_name" class="form-control">
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Project</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#projectForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('projects.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#alertBox').html('<div class="alert alert-success">'+response.message+'</div>');
                $('#projectForm')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorList = '<ul>';
                    $.each(errors, function(key, val) {
                        errorList += '<li>'+val[0]+'</li>';
                    });
                    errorList += '</ul>';
                    $('#alertBox').html('<div class="alert alert-danger">'+errorList+'</div>');
                } else {
                    $('#alertBox').html('<div class="alert alert-danger">Something went wrong!</div>');
                }
            }
        });
    });
});
</script> --}}
@endsection 
