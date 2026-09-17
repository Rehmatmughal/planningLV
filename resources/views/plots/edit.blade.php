@extends('app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Plot</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        {{-- </div> --}}
        <div id="alert-area"></div>
        <form method="POST" action="{{ route('plots.update', $plot->id) }}">
            @csrf
            <div class="row">
                {{-- left column --}}
                <div class="col-md-6">
                    @method('PUT')

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
                    {{-- Plot Number --}}
                    <div class="mb-3">
                        <label class="form-label">Plot No</label>
                        <input type="text" name="plot_number" class="form-control"
                            value="{{ old('plot_number', $plot->plot_number) }}" required>
                    </div>
                </div>
                {{-- right column --}}
                <div class="col-md-6">
                    {{-- Property Type --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Property Type
                        </label>
                        {{-- <select
                            name="property_type_id"
                            class="form-select"
                            required
                        > --}}
                        <select
                            name="property_type_id"
                            id="propertytype"
                            class="form-select"
                            required
                        >                        
                            @foreach($propertytypes as $propertytype)

                                <option
                                    value="{{ $propertytype->id }}"
                                    {{ old(
                                        'property_type_id',
                                        $plot->property_type_id
                                    ) == $propertytype->id ? 'selected' : '' }} 
                                >
                                    {{ $propertytype->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Size --}}
                    {{-- <div class="mb-3">
                        <label class="form-label">Size</label>
                        <input type="text" name="size_id" class="form-control"
                            value="{{ old('size', $plot->size->title) }}">
                    </div> --}}

                    <div class="mb-3">
                        <label class="form-label">Plot Size</label> 
                        {{-- <select name="size_id" class="form-select" required> --}}
                       <select
                            name="size_id"
                            id="plotsize"
                            class="form-select"
                            required
                        >
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


                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $plot->remarks) }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top"></div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    <i class="fa fa-refresh"></i>
                    Update Plot
                </button>
                <a href="{{ route('plots.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@section('scripts')

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>
$(document).ready(function () {

    const projectID = "{{ $plot->project_id }}";

    const blockID = "{{ $plot->block_id }}";

    const currentSizeID = "{{ old('size_id', $plot->size_id) }}";


    function loadAssignedSizes(propertyTypeID, selectedSizeID = '') {

        $('#plotsize').html(
            '<option value="">Loading assigned sizes...</option>'
        );

        if (!propertyTypeID) {

            $('#plotsize').html(
                '<option value="">-- Select Size --</option>'
            );

            return;
        }

        $.get(
            "{{ url('/admin/get-assigned-sizes') }}/"
            + projectID + '/'
            + blockID + '/'
            + propertyTypeID,

            function (data) {

                $('#plotsize').html(
                    '<option value="">-- Select Size --</option>'
                );

                if (data.length === 0) {

                    $('#plotsize').html(
                        '<option value="">No size assigned</option>'
                    );

                    return;
                }

                $.each(data, function (i, item) {

                    let selected = '';

                    if (String(item.id) === String(selectedSizeID)) {
                        selected = 'selected';
                    }

                    $('#plotsize').append(
                        '<option value="' + item.id + '" '
                        + selected + '>'
                        + item.title
                        + (item.size_area
                            ? ' - ' + item.size_area
                            : '')
                        + '</option>'
                    );

                });

            }
        )
        .fail(function () {

            $('#plotsize').html(
                '<option value="">Error loading sizes</option>'
            );

        });

    }


    // Property Type change hone par sizes reload hongi
    $('#propertytype').on('change', function () {

        let propertyTypeID = $(this).val();

        loadAssignedSizes(propertyTypeID);

    });


    // Page load par current Property Type aur Size restore honge
    let oldPropertyType = "{{ old('property_type_id', $plot->property_type_id) }}";

    loadAssignedSizes(
        oldPropertyType,
        currentSizeID
    );

});
</script>

@endsection
@endsection

