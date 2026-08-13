@extends('app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Plot</h4>
        </div>
        <div class="card-body">
            <div id="alert-area"></div>

            <form id="plotForm">
                @csrf
                <div class="row">
                    {{-- left column --}}
                    <div class="col-md-6">

                        {{-- Project --}}
                        <div class="mb-3">
                            <label>Project</label>
                            <select name="project_id" id="project" class="form-control">
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Block --}}
                        <div class="mb-3">
                            <label>Block</label>
                            <select name="block_id" id="block" class="form-control">
                                <option value="">-- Select Block --</option>
                            </select>
                        </div>

                        {{-- Street --}}
                        <div class="mb-3">
                            <label>Street</label>
                            <select name="street_id" id="street" class="form-control">
                                <option value="">-- Select Street --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Plot Number</label>
                            <input type="text" name="plot_number" class="form-control">
                        </div>
                    </div>
                    {{-- right column --}}
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label>Size</label>
                            <select name="size_id" id="plotsize" class="form-control">
                            <option value="">-- Select Size --</option>
                                @foreach($sizes as $s)
                                    <option value="{{ $s->id }}">{{ $s->title }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="size" class="form-control"> --}}
                        </div>
                        <div class="mb-3">
                            <label>Category of plot</label>
                            <select name="category_id" id="plotcategory" class="form-control">
                            <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->id }} - {{ $category->category_title }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="size" class="form-control"> --}}
                        </div>

                        <div class="mb-3">
                            <label>Numbering Type</label>
                            <select name="numbering_type" class="form-control">
                                <option value="blockwise">Blockwise</option>
                                <option value="streetwise">Streetwise</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">Save Plot</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 🟢 Load Blocks based on Project
    $('#project').on('change', function () {
        var projectID = $(this).val();
        $('#block').html('<option>Loading...</option>');
        $('#size').html('<option value="">-- Select size --</option>');
        $('#street').html('<option value="">-- Select Street --</option>');

        if (projectID) {
            $.get('/get-blocks/' + projectID, function (data) {
                $('#block').html('<option value="">-- Select Block --</option>');
                $.each(data, function (i, item) {
                    $('#block').append('<option value="'+item.id+'">'+item.block_name+'</option>');
                });
            });
        } else {
            $('#block').html('<option value="">-- Select Block --</option>');
        }

        // for plotsize
        if (projectID) {
            $.get('/get-sizes/' + projectID, function (data) {
                $('#plotsize').html('<option value="">-- Select Size --</option>');
                $.each(data, function (i, item) {
                    $('#plotsize').append('<option value="'+item.id+'">'+item.title+'</option>');
                });
            });
        } else {
            $('#plotsize').html('<option value="">-- Select Size --</option>');
        }
    });

    // 🟠 Load Streets based on Block
    $('#block').on('change', function () {
        var blockID = $(this).val();
        $('#street').html('<option>Loading...</option>');
        if (blockID) {
            $.get('/get-streets/' + blockID, function (data) {
                $('#street').html('<option value="">-- Select Street --</option>');
                $.each(data, function (i, item) {
                    $('#street').append('<option value="'+item.id+'">'+item.street_name+'</option>');
                });
            });
        } else {
            $('#street').html('<option value="">-- Select Street --</option>');
        }
    });


    // 💾 AJAX Form Submit -- old-- start
    
    // $('#plotForm').on('submit', function (e) {
    //     console.log('Form submit clicked');
    //     e.preventDefault();
    //     $('#alert-area').html('');

    //     $.ajax({
    //         url: "{{ route('plots.store') }}",
    //         method: "POST",
    //         data: $(this).serialize(),
    //         success: function (response) {
    //             console.log('Success:', response);
    //             if (response.status === 'success') {
    //                 $('#alert-area').html('<div class="alert alert-success">'+response.message+'</div>');
    //                 $('#plotForm')[0].reset();
    //             }
    //         },
    //         error: function (xhr) {
    //             console.log('Error:', xhr);
    //             if (xhr.status === 422) {
    //                 let errors = xhr.responseJSON.errors;
    //                 let html = '<div class="alert alert-danger"><ul>';
    //                 $.each(errors, function (key, value) {
    //                     html += '<li>' + value[0] + '</li>';
    //                 });
    //                 html += '</ul></div>';
    //                 $('#alert-area').html(html);
    //             } else if (xhr.status === 409) {
    //                 $('#alert-area').html('<div class="alert alert-danger">'+xhr.responseJSON.message+'</div>');
    //             }
    //         }
    //     });
    // });
//    // 💾 AJAX Form Submit -- old-- END


//    // 💾 AJAX Form Submit -- NEW-- Start
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        console.log('jQuery ready!');

        // Load Blocks
        $('#project').on('change', function () {
            var projectID = $(this).val();
            $('#block').html('<option>Loading...</option>');
            $('#street').html('<option value="">-- Select Street --</option>');

            if (projectID) {
                $.get('/get-blocks/' + projectID, function (data) {
                    $('#block').html('<option value="">-- Select Block --</option>');
                    $.each(data, function (i, item) {
                        $('#block').append('<option value="'+item.id+'">'+item.block_name+'</option>');
                    });
                });
            } else {
                $('#block').html('<option value="">-- Select Block --</option>');
            }
        });

        // Load Streets
        $('#block').on('change', function () {
            var blockID = $(this).val();
            $('#street').html('<option>Loading...</option>');
            if (blockID) {
                $.get('/get-streets/' + blockID, function (data) {
                    $('#street').html('<option value="">-- Select Street --</option>');
                    $.each(data, function (i, item) {
                        $('#street').append('<option value="'+item.id+'">'+item.street_name+'</option>');
                    });
                });
            } else {
                $('#street').html('<option value="">-- Select Street --</option>');
            }
        });

        // Load Size
        $('#block').on('change', function () {
            var blockID = $(this).val();
            $('#street').html('<option>Loading...</option>');
            if (blockID) {
                $.get('/get-streets/' + blockID, function (data) {
                    $('#street').html('<option value="">-- Select Street --</option>');
                    $.each(data, function (i, item) {
                        $('#street').append('<option value="'+item.id+'">'+item.street_name+'</option>');
                    });
                });
            } else {
                $('#street').html('<option value="">-- Select Street --</option>');
            }
        });

        // ✅ AJAX Form Submit
        $('#plotForm').on('submit', function (e) {
            e.preventDefault();
            console.log('Form submit clicked');
            $('#alert-area').html('');

            $.ajax({
                url: "{{ route('plots.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    console.log('Success:', response);
                    if (response.status === 'success') {
                        $('#alert-area').html('<div class="alert alert-success">'+response.message+'</div>');
                        $('#plotForm')[0].reset();
                    }
                },
                error: function (xhr) {
                    console.log('Error:', xhr);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let html = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function (key, value) {
                            html += '<li>' + value[0] + '</li>';
                        });
                        html += '</ul></div>';
                        $('#alert-area').html(html);
                    } else if (xhr.status === 409) {
                        $('#alert-area').html('<div class="alert alert-danger">'+xhr.responseJSON.message+'</div>');
                    } else {
                        $('#alert-area').html('<div class="alert alert-danger">Unexpected error occurred.</div>');
                    }
                }
            });
        });
    });
    // 💾 AJAX Form Submit -- NEW-- END

});
</script>
@endsection
