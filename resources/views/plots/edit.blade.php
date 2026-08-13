@extends('app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Edit Plot22</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('plots.update', $plot->id) }}">
        @csrf
        @method('PUT')

        {{-- old with dropdown list editable form --}}
        {{-- Project --}}
        {{-- <div class="mb-3">
            <label class="form-label">Project</label>
            <select name="project_id" class="form-select" required>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" 
                        {{ $plot->project_id == $project->id ? 'selected' : '' }}>
                        {{ $project->project_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Block -}}
        <div class="mb-3">
            <label class="form-label">Block</label>
            <select name="block_id" class="form-select" required>
                @foreach($blocks as $block)
                    <option value="{{ $block->id }}"
                        {{ $plot->block_id == $block->id ? 'selected' : '' }}>
                        {{ $block->block_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Numbering Type -}}
        <div class="mb-3">
            <label class="form-label">Numbering Type</label>
            <select name="numbering_type" class="form-select" required>
                <option value="blockwise" {{ $plot->numbering_type == 'blockwise' ? 'selected' : '' }}>
                    Block Wise
                </option>
                <option value="streetwise" {{ $plot->numbering_type == 'streetwise' ? 'selected' : '' }}>
                    Street Wise
                </option>
            </select>
        </div>

        <input type="hidden" name="numbering_type" value="{{ $plot->numbering_type }}">


        {{-- Street -}}
        
        <div class="mb-3">
            <label class="form-label">Street (Optional)</label>
            <select name="street_id" class="form-select" required>
                @foreach($streets as $street)
                    <option value="{{ $street->id }}"
                        {{ old('street_id', $plot->street_id) == $street->id ? 'selected' : '' }}>
                        {{ $street->street_name }}
                    </option>
                @endforeach
            </select>
        </div> --}}

        {{-- Project --}}
        <div class="mb-3">
            <label class="form-label">Project</label>
            <input type="text"
                class="form-control"
                value="{{ $plot->project->project_name }}"
                readonly>

            <input type="hidden" name="project_id" value="{{ $plot->project_id }}">
        </div>
        {{-- Block --}}
        <div class="mb-3">
            <label class="form-label">Block</label>
            <input type="text"
                class="form-control"
                value="{{ $plot->block->block_name }}"
                readonly>

            <input type="hidden" name="block_id" value="{{ $plot->block_id }}">
        </div>

        {{-- Street --}}
        <div class="mb-3">
            <label class="form-label">Street</label>
            <input type="text"
                class="form-control"
                value="{{ $plot->street?->street_name }}"
                readonly>

            <input type="hidden" name="street_id" value="{{ $plot->street_id }}">
        </div>
        {{-- Numbering Type --}}
        <div class="mb-3">
            <label class="form-label">Numbering Type</label>
            <input type="text"
                class="form-control"
                value="{{ ucfirst($plot->numbering_type) }}"
                readonly>
            <input type="hidden"
                name="numbering_type"
                value="{{ $plot->numbering_type }}">
        </div>
        {{-- Plot Number --}}
        <div class="mb-3">
            <label class="form-label">Plot No</label>
            <input type="text" name="plot_number" class="form-control"
                   value="{{ old('plot_number', $plot->plot_number) }}" required>
        </div>

        {{-- Size --}}
        {{-- <div class="mb-3">
            <label class="form-label">Size</label>
            <input type="text" name="size_id" class="form-control"
                   value="{{ old('size', $plot->size->title) }}">
        </div> --}}

        <div class="mb-3">
            <label class="form-label">Plot Size</label>
            {{-- <select name="size_id" class="form-select">
                <option value="">-- Select Size --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}"
                        {{ $plot->size_id == $size->id ? 'selected' : '' }}>
                        {{ $size->title }}
                    </option>
                @endforeach
            </select> --}}
            <select name="size_id" class="form-select" required>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}"
                        {{ old('size_id', $plot->size_id) == $size->id ? 'selected' : '' }}>
                        {{ $size->title }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $plot->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->category_title }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Remarks --}}
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $plot->remarks) }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Update Plot</button>
            <a href="{{ route('plots.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection



{{-- old edit page --}}
{{-- @extends('layout')

@section('content')
<div class="container">
    <h3>Edit Plot</h3>

    <div id="alertBox"></div>

    <form id="plotEditForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Project</label>
                <select name="project_id" id="project" class="form-control">
                    <option value="">Select Project</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $p->id == $plot->project_id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Block</label>
                <select name="block_id" id="block" class="form-control">
                    <option value="">Select Block</option>
                    @foreach($blocks as $b)
                        <option value="{{ $b->id }}" {{ $b->id == $plot->block_id ? 'selected' : '' }}>{{ $b->block_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Street</label>
                <select name="street_id" id="street" class="form-control">
                    <option value="">Select Street</option>
                    @foreach($streets as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $plot->street_id ? 'selected' : '' }}>{{ $s->street_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Plot Number</label>
                <input type="text" name="plot_number" class="form-control" value="{{ $plot->plot_number }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Size</label>
                <input type="text" name="size" class="form-control" value="{{ $plot->size }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Numbering Type</label>
                <select name="numbering_type" class="form-control">
                    <option value="blockwise" {{ $plot->numbering_type == 'blockwise' ? 'selected' : '' }}>Blockwise</option>
                    <option value="streetwise" {{ $plot->numbering_type == 'streetwise' ? 'selected' : '' }}>Streetwise</option>
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control">{{ $plot->remarks }}</textarea>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Update Plot</button>
                <a href="{{ route('plots.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts') 
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> --}}

{{-- <script> --}}
{{-- $(function() {

    // 🟢 Project → Block AJAX
    $('#project').on('change', function() {
        let projectID = $(this).val();
        $('#block').html('<option>Loading...</option>');
        $('#street').html('<option>Select Street</option>');
        if (projectID) {
            $.get('/get-blocks/' + projectID, function(data) {
                $('#block').html('<option value="">Select Block</option>');
                $.each(data, function(_, block) {
                    $('#block').append('<option value="'+block.id+'">'+block.block_name+'</option>');
                });
            });
        }
    });

    // 🟠 Block → Street AJAX
    $('#block').on('change', function() {
        let blockID = $(this).val();
        $('#street').html('<option>Loading...</option>');
        if (blockID) {
            $.get('/get-streets/' + blockID, function(data) {
                $('#street').html('<option value="">Select Street</option>');
                $.each(data, function(_, street) {
                    $('#street').append('<option value="'+street.id+'">'+street.street_name+'</option>');
                });
            });
        }
    });

    // 🧾 AJAX Form Submit
    $('#plotEditForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('plots.update', $plot->id) }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.status === 'success') {
                    $('#alertBox').html('<div class="alert alert-success">'+res.message+'</div>');
                }
            },
            error: function(xhr) {
                let msg = 'An error occurred!';
                if(xhr.responseJSON?.message){
                    msg = xhr.responseJSON.message;
                }
                $('#alertBox').html('<div class="alert alert-danger">'+msg+'</div>');
            }
        });
    });
});
</script>
@endsection  --}}
