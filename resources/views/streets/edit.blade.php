@extends('app')

@section('content')
<div class="container mt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Edit Street</h4>
        <a href="{{ route('streets.index') }}" class="btn btn-secondary btn-sm">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('streets.update', $street->id) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Project --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-control" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ $project->id == $street->project_id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Block --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Block</label>
                <select name="block_id" id="block_id" class="form-control" required>
                    <option value="">Select Block</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}"
                            {{ $block->id == $street->block_id ? 'selected' : '' }}>
                            {{ $block->block_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Street Name --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">Street Name</label>
                <input type="text"
                       name="street_name"
                       class="form-control"
                       value="{{ old('street_name', $street->street_name) }}"
                       required>
            </div>

            {{-- Remarks --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">
                {{ old('remarks', $street->remarks) }}
                </textarea>
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">
                Update Street
            </button>
        </div>

    </form>
</div>
@endsection
{{-- for project wise load steets --}}
@section('scripts')
<script>
$('#project_id').on('change', function () {
    let projectId = $(this).val();
    $('#block_id').html('<option>Loading...</option>');

    $.get('/get-blocks/' + projectId, function (data) {
        $('#block_id').html('<option value="">Select Block</option>');
        data.forEach(block => {
            $('#block_id').append(
                `<option value="${block.id}">${block.block_name}</option>`
            );
        });
    });
});
</script>
@endsection

