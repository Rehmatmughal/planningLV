@extends('app')

@section('content')
<div class="container mt-3">
    <h2>Add New Street</h2>

    <div id="alert-area"></div>
    {{-- error dispay area --}}
    <div class="container mt-4">

        {{-- for ERROR display --}}

        {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    {{-- error display area end --}}


    
    {{-- <form id="plotForm"> --}}
    {{-- <form id="blockForm"> --}}
    <form action="{{ route('streets.store') }}" method="POST" enctype="multipart/form-data">

        @csrf

        {{-- Project --}}
        <div class="mb-3">
            <label>Project</label>
            <select name="project_id" id="project" class="form-control @error('project_id') is-invalid @enderror">
                <option value="">-- Select Project --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                @endforeach
            </select>
        </div>
        {{-- <div class="mb-3">            
            @error('project_id')
                <span class="alert alert-danger" mt-5>
                    {{ $message }}
                    </span>
            @enderror
        </div> --}}

        {{-- Block --}}
        <div class="mb-3">
            <label>Block</label>
            <select name="block_id" id="block" class="form-control @error('block_id') is-invalid @enderror">
                <option value="">-- Select Block --</option>
            </select>
        </div>
        {{-- <div class="mb-3">            
                @error('block_id')
                    <span class="alert alert-danger" mt-5>
                        {{ $message }}
                     </span>
                @enderror
        </div> --}}

        {{-- Street --}}
        <div class="mb-3">
            <label>Road / Street No</label>
            <input type="text" name="street_name" class="form-control @error('street_name') is-invalid @enderror">
        </div>
        {{-- <div class="mb-3">            
            @error('street_name')
                <span class="alert alert-danger" mt-5>
                    {{ $message }}
                    </span>
            @enderror
        </div> --}}

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

        <button type="submit" class="btn btn-primary">Save street</button>
    </form>
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

    

});
</script>
@endsection
