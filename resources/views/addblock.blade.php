@extends('layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h1>Add New Block</h1>
                @if ($errors->any())
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- {{ route('addUser') }} --}}
                <form action="{{ route('saveblock') }}" method="POST">
                    {{-- <form action="{{ route('saveblock') }}" method="POST"> --}}
                    @csrf
                    <div class="mb-3">
                        <label for="postproject">Select Project:</label>
                        <select class="form-control" name="postproject" required>
                            <option value="">-- Select Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                        <br><br>

                        {{-- <label for="postproject">Project:</label>
                        <input type="number" class="form-control" name="postproject" ><br><br> --}}
                        <label for="postblock">Block:</label>
                        <input type="text" class="form-control" name="postblock"><br><br>

                        {{-- </div> --}}
                    {{-- <div class="mb-3"> --}}
                        <label for="postremarks">remarks:</label>
                        <input type="text" class="form-control" name="postremarks"><br><br>
                    {{-- </div> --}}
                    {{-- <div class="mb-3"> --}}
                        <button type="submit" class="btn btn-primary">Submit</button>

                </form>
            </div>
        </div>
    </div>

@endsection




