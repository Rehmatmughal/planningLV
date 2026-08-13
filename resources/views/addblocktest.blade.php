




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add plot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
    
</body>
</html>